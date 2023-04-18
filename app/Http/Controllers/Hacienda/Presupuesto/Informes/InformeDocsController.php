<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\Exports\InfPagosExcExport;
use App\Exports\InfPrepIngExcExport;
use App\Exports\InfPrepEgrExcExport;
use App\Http\Controllers\Controller;
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
        $p = Pagos::where('estado','!=', '0')->get();

        foreach ($p as $data){
            if (isset($data->orden_pago->registros)){
                if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $vigencia->id){
                    if (!isset($data->banks->data_puc)) dd($data, $data->banks);
                    $banks = PagoBanks::where('pagos_id', $data->id)->first();
                    if (!isset($banks)) dd($data);
                    $data->cuentaBanco = $banks->data_puc->code.' - '.$banks->data_puc->concepto;
                    $pagos[] = collect(['info' => $data]);
                }
            } else{
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->orden_pago->id)->first();
                if ($tesoreriaRetefuentePago->vigencia_id == $vigencia->id){
                    $banks = PagoBanks::where('pagos_id', $data->id)->first();
                    if (!isset($banks)) dd($data);
                    $data->cuentaBanco = $banks->data_puc->code.' - '.$banks->data_puc->concepto;
                    $pagos[] = collect(['info' => $data]);
                }
            }
        }
        return $pagos;
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