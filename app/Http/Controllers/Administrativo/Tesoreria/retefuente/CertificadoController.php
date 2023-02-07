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
        $Descuentos = OrdenPagosDescuentos::where('valor','>',0)->get();
        $añoActual = Carbon::today()->year;
        $vigencia = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->first();

        foreach ($Descuentos as $descuento){
            $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')
                ->where('saldo', 0)->first();
            if ($ordenPago and $ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $vigencia->id and
                $ordenPago->registros->persona_id == $request->persona_id){
                dd($ordenPago, $descuento, $ordenPago->registros);
            }
        }


        $registros = Registro::where('persona_id', $request->persona_id)->get();

        foreach ($registros as $registro){
            if ($registro->jefe_e == '3' ){
                if ($registro->cdpsRegistro->first()->cdp->vigencia_id == $vigencia->id){
                    foreach ($registro->ordenPagos as $ordenPago){
                        if ($ordenPago->estado == 1){
                            foreach ($ordenPago->descuentos as $descuento){
                                if ($descuento->valor > 0){
                                    dd($descuento, $ordenPago);
                                    $values[] = collect(['']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
