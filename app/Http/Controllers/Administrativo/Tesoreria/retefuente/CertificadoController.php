<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\retefuente;

use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Tesoreria\retefuente\Certificado;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;
use PDF;

class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personas = Persona::all();
        return view('administrativo.tesoreria.retefuente.certificado', compact('personas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCert(Request $request)
    {
        if ($request->persona_id == 1257){
            $pathtoFile = public_path().'/file_public/MOYA.pdf';
            return response()->download($pathtoFile);
        } else {
            $Descuentos = OrdenPagosDescuentos::where('valor','>',0)->get();
            $añoActual = 2023;
            $vigencia = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->first();
            $persona = Persona::find($request->persona_id);

            foreach ($Descuentos as $descuento){
                $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')
                    ->where('saldo', 0)->first();
                if(isset($ordenPago)){
                    if (isset($ordenPago->pago) and isset($ordenPago->registros)){
                        if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $vigencia->id and
                        $ordenPago->pago->persona_id == $request->persona_id){
                            if ($descuento->retencion_fuente_id != null){
                                $descuento->concepto = $descuento->descuento_retencion->concepto;
                                $descuento->cuenta = $descuento->descuento_retencion->codigo;
                            } else if ($descuento->desc_municipal_id != null){
                                $descuento->concepto = $descuento->descuento_mun->concepto;
                                $descuento->cuenta = $descuento->descuento_mun->codigo;
                            } else{
                                $descuento->concepto = $descuento->puc->concepto;
                                $descuento->cuenta = $descuento->puc->code;
                            }
                            $fechaPago = Carbon::parse($ordenPago->pago->created_at)->format('d-m-Y');
                            $valoresDesc[] = $descuento->valor;
                            $values[] = collect(['CEcode' => $ordenPago->pago->code, 'CEconcepto' => $ordenPago->pago->concepto,
                                'OPvalor' => $ordenPago->valor, 'DESvalor' => $descuento->valor, 'DESconcepto' =>  $descuento->concepto,
                                'DEScuenta' => $descuento->cuenta, 'Pvalor' => $ordenPago->pago->valor, 'Pfecha' => $fechaPago]);
                        }
                    }
                }
            }

            if (isset($values)){
                $hoy = Carbon::now();
                $fecha = Carbon::createFromTimeString($hoy);
                $totDes = array_sum($valoresDesc);

                $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

                $pdf = \PDF::loadView('administrativo.tesoreria.retefuente.pdfCertificado', compact('values',
                    'fecha','dias','meses','persona','añoActual','totDes'))
                    ->setOptions(['images' => true,'isRemoteEnabled' => true]);
                return $pdf->stream();
            } else{
                Session::flash('warning','No se detectan pagos efectuados a ordenes de pagos con descuentos del tercero'.$persona->nombre);
                return back();
            }
        }
    }
}
