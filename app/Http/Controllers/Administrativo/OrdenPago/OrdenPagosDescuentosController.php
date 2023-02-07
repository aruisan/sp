<?php

namespace App\Http\Controllers\Administrativo\OrdenPago;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Persona;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;


class OrdenPagosDescuentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $retenF = RetencionFuente::all();
        $ordenPago = OrdenPagos::findOrFail($id);
        $vigencia = $ordenPago->registros->cdpsRegistro[0]->cdp->vigencia_id;
        $cuentas24 = PucAlcaldia::where('id','>=',622)->where('id','<=',711)->where('hijo','1')->get();
        $personas = Persona::all();
        if ($ordenPago->rubros->count() == 0){
            Session::flash('warning',' Se debe realizar primero la asignación del monto antes de realizar los descuentos.');
            return redirect('administrativo/ordenPagos/monto/create/'.$ordenPago->id);
        } else {
            if ($ordenPago->iva > 0){
                $desMun = DescMunicipales::all();
            } else {
                $desMun = DescMunicipales::where('id','!=','4')->get();
            }
            return view('administrativo.ordenpagos.createDescuentos', compact('ordenPago','retenF','desMun','vigencia','cuentas24',
            'personas'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ordenPago_id = $request->ordenPago_id;

        if ($request->retencion_fuente != "Selecciona un Concepto de Descuento"){
            $retenFuente = RetencionFuente::findOrFail($request->retencion_fuente);
            $valor = $request->valor;
            $nombre = $retenFuente->concepto;
            $porcentaje = $retenFuente->tarifa;
            $base = $retenFuente->base;

            $ordenPagoDes = new OrdenPagosDescuentos();
            $ordenPagoDes->nombre = $nombre;
            $ordenPagoDes->porcent = $porcentaje;
            $ordenPagoDes->base = $base;
            $ordenPagoDes->valor = $valor;
            $ordenPagoDes->orden_pagos_id = $ordenPago_id;
            $ordenPagoDes->retencion_fuente_id = $request->retencion_fuente;
            $ordenPagoDes->save();
        }

        if ($request->idDes != null){
            for($i=0;$i< count($request->idDes); $i++){
                $descMunicipales = DescMunicipales::findOrFail($request->idDes[$i]);
                $descuento = new OrdenPagosDescuentos();
                $descuento->nombre = $descMunicipales->concepto;
                $descuento->base = 0;
                $descuento->porcent = $descMunicipales->tarifa;
                $descuento->valor = $request->valorMuni[$i];
                $descuento->orden_pagos_id = $ordenPago_id;
                $descuento->desc_municipal_id = $request->idDes[$i];
                $descuento->save();
            }
        }

        //SE ALMACENAN LOS NUEVOS DESCUENTOS MUNICIPALES
        if ($request->cuentaDesc != null){
            for($x=0;$x< count($request->cuentaDesc); $x++){
                $cuenta = PucAlcaldia::find($request->cuentaDesc[$x]);
                $descuento = new OrdenPagosDescuentos();
                $descuento->nombre = $cuenta->concepto;
                $descuento->base = 0;
                $descuento->porcent = 0;
                $descuento->valor = $request->valorDesc[$x];
                $descuento->orden_pagos_id = $ordenPago_id;
                $descuento->cuenta_puc_id = $request->cuentaDesc[$x];
                $descuento->persona_id = $request->tercero[$x];
                $descuento->save();
            }
        }

        Session::flash('success','Los Descuentos se han Almacenado Exitosamente');
        return redirect('/administrativo/ordenPagos/liquidacion/create/'.$ordenPago_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\orden_pagos_descuentos  $orden_pagos_descuentos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = OrdenPagosDescuentos::where('orden_pagos_id', $id)->get();

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\orden_pagos_descuentos  $orden_pagos_descuentos
     * @return \Illuminate\Http\Response
     */
    public function edit(OrdenPagosDescuentos $orden_pagos_descuentos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\orden_pagos_descuentos  $orden_pagos_descuentos
     * @return \Illuminate\Http\Response
     */
    public function update($id, $nombre, $valor, $orden_id, $porc, $base)
    {
        $ordenPago = OrdenPagos::findOrFail($orden_id);
        $Descuento = OrdenPagosDescuentos::findOrFail($id);
        $registro = Registro::findOrFail($ordenPago->registros_id);

        if ($registro->saldo < $base){
            Session::flash('warning','El valor de base no puede ser superior al valor disponible del registro: '.$registro->saldo);
        } else{
            $x = $base * $porc;
            $y = $x/100;
            $Descuento->valor = $y;
            $Descuento->porcent = $porc;
            $Descuento->base = $base;
            $Descuento->nombre = $nombre;
            $Descuento->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\orden_pagos_descuentos  $orden_pagos_descuentos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $descuento = OrdenPagosDescuentos::findOrFail($id);
        $descuento->delete();
        Session::flash('error','Descuento eliminado correctamente de la orden de pago');

    }
}
