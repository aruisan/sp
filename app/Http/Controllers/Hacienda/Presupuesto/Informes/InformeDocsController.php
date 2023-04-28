<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\Exports\InfOrdenPagosExcExport;
use App\Exports\InfPagosExcExport;
use App\Exports\InfPrepIngExcExport;
use App\Exports\InfPrepEgrExcExport;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\InformePresupuestosExport;
use Illuminate\Http\Request;
use Session;
use PDF;

class InformeDocsController extends Controller
{
    public function generatePagos($año){
        $vigencia = Vigencia::where('vigencia', $año)->where('tipo', 0)->where('estado', '0')->first();
        $p = Pagos::where('estado', '1')->get();

        foreach ($p as $data){
            if (isset($data->orden_pago->registros)){
                if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $vigencia->id){
                    $banks = PagoBanks::where('pagos_id', $data->id)->get();
                    if (isset($codes)) unset($codes);
                    if (isset($values)) unset($values);
                    foreach ($data->orden_pago->pucs as $puc){
                        if ($puc->valor_credito > 0){
                            $codes[] = $puc->data_puc->code.' - '.$puc->data_puc->concepto;
                            $values[] = $puc->valor_credito;
                        }
                    }
                    if (isset($codes)) $data->cuentaOP = $codes;
                    if (isset($values)) $data->credOP = $values;
                    if (count($banks) == 0) dd($data, "FALLO");
                    $data->cuentaBanco = $banks[0]->data_puc->code.' - '.$banks[0]->data_puc->concepto;
                    $pagos[] = collect(['info' => $data]);
                }
            } else{
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->orden_pago->id)->first();
                if ($tesoreriaRetefuentePago->vigencia_id == $vigencia->id){
                    $banks = PagoBanks::where('pagos_id', $data->id)->get();
                    if (isset($codes)) unset($codes);
                    if (isset($values)) unset($values);
                    if (isset($personas)) unset($personas);
                    foreach ($tesoreriaRetefuentePago->contas as  $contabilizacion){
                        $codes[] = $contabilizacion->puc->code.' - '.$contabilizacion->puc->concepto;
                        $personas[] = $contabilizacion->persona->num_dc.' - '.$contabilizacion->persona->nombre;
                        $values[] = $contabilizacion->debito;
                    }
                    if (isset($codes)) $data->cuentaOP = $codes;
                    if (isset($values)) $data->credOP = $values;
                    if (isset($personas)) $data->perOP = $personas;
                    if (count($banks) == 0) dd($data, "FALLO");
                    $data->cuentaBanco = $banks[0]->data_puc->code.' - '.$banks[0]->data_puc->concepto;
                    $pagos[] = collect(['info' => $data]);

                }
            }
        }
        return $pagos;
    }

    public function generateOrdenPagos($año){
        $vigencia = Vigencia::where('vigencia', $año)->where('tipo', 0)->where('estado', '0')->first();
        $oPH = OrdenPagos::where('estado', '1')->get();
        foreach ($oPH as $data){
            if (isset($data->registros->cdpsRegistro)){
                if ($data->registros->cdpsRegistro[0]->cdp->vigencia_id == $vigencia->id){
                    $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $data->id)->where('valor', '>', 0)->get();
                    $ordenPagos[] = collect(['info' => $data, 'tercero' => $data->registros->persona->nombre,
                        'ccH' => $data->registros->persona->num_dc, 'descuentos' => $OrdenPagoDescuentos, 'pucs' => $data->pucs, 'pucV' => $data->pucs->sum('debito'),
                        'descV' => $OrdenPagoDescuentos->sum('valor')]);
                    dd($ordenPagos, $OrdenPagoDescuentos, $data->pucs);
                }
            } else{
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->id)->first();
                if (isset($tesoreriaRetefuentePago)) {
                    if ($tesoreriaRetefuentePago->vigencia_id == $vigencia->id) {
                        $ordenPagos[] = collect(['info' => $data, 'tercero' => 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN',
                            'ccH' => 800197268, 'descuentos' => [], 'pucs' => $tesoreriaRetefuentePago->contas, 'pucV' => $tesoreriaRetefuentePago->contas->sum('debito'),
                            'descV' => 0]);
                    }
                }
            }

        }

        return $ordenPagos;
    }

    public function makePagosEXCEL()
    {
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
        $pagos = $this->generatePagos($añoActual);

        return Excel::download(new InfPagosExcExport($pagos),
            'Informe de Pagos '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');
    }

    public function makeOrdenPagosEXCEL()
    {
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
        $ordenPagos = $this->generateOrdenPagos($añoActual);

        return Excel::download(new InfOrdenPagosExcExport($ordenPagos),
            'Informe de Ordenes de Pago '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');
    }

    public function makeEgresosEjecucion(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepEgresos($inicio, $final);

        return Excel::download(new InfPrepEgrExcExport($presupuesto),
            'Ejecucion Presupuesto de Egresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeIngresosEXCEL()
    {
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
        $prepIng = $this->prepIngresos();

        return Excel::download(new InfPrepIngExcExport($prepIng),
            'Presupuesto de Ingresos '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');

    }

    public function makeIngresosEjecucion(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepIngresos($inicio, $final);

        return Excel::download(new InfPrepIngExcExport($presupuesto),
            'Ejecucion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeEgresosPDF(){
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $dia = Carbon::now()->day;
        $presupuesto = $this->prepEgresos();

        $pdf = PDF::loadView('hacienda.presupuesto.informes.pdfEgresos', compact('añoActual','mesActual','dia','presupuesto'))
            ->setPaper('a3', 'landscape')
            ->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function makeIngresosPDF(){
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $dia = Carbon::now()->day;
        $presupuesto = $this->prepIngresos();

        $pdf = PDF::loadView('hacienda.presupuesto.informes.pdfIngresos', compact('añoActual','mesActual','dia','presupuesto'))
            ->setPaper('a3', 'landscape')
            ->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

}