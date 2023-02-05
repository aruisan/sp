<?php

namespace App\Http\Controllers\Administrativo\OrdenPago\RetencionFuente;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Hacienda\Presupuesto\Terceros;
use App\Model\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class RetencionFuenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = RetencionFuente::all();
        return view('administrativo.contabilidad.retencionfuente.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrativo.contabilidad.retencionfuente.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reteF = new RetencionFuente();
        $reteF->concepto = $request->concept;
        $reteF->uvt = $request->uvt;
        $reteF->base = $request->base;
        $reteF->tarifa = $request->tarifa;
        $reteF->codigo = $request->codigo;
        $reteF->cuenta = $request->cuenta;
        $reteF->save();

        Session::flash('success','La retención en la fuente '.$request->concept.' se ha almacenado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function show(RetencionFuente $retencionFuente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $retens = RetencionFuente::findOrFail($id);
        return view('administrativo.contabilidad.retencionfuente.edit', compact('retens'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reteF = RetencionFuente::findOrFail($id);
        $reteF->concepto = $request->concept;
        $reteF->uvt = $request->uvt;
        $reteF->base = $request->base;
        $reteF->tarifa = $request->tarifa;
        $reteF->codigo = $request->codigo;
        $reteF->cuenta = $request->cuenta;
        $reteF->save();

        Session::flash('success','La retención en la fuente '.$request->concept.' se ha actualizado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $retenF = RetencionFuente::findOrFail($id);
        $retenF->delete();

        Session::flash('error','La retención en la fuente se ha eliminado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Display a form to create the declaracion.
     *
     * @return \Illuminate\Http\Response
     */
    public function declaracion()
    {
        return view('administrativo.contabilidad.retencionfuente.declaracion');
    }

    /**
     * Display a form to create the declaracion.
     *
     * @return \Illuminate\Http\Response
     */
    public function certificado()
    {
        $personas = Persona::all();
        return view('administrativo.contabilidad.retencionfuente.certificado', compact('personas'));
    }

    public function pagoRetefuente($vigencia_id, $mes){

        $cuentaPUC = PucAlcaldia::where('padre_id',660)->get();
        foreach ($cuentaPUC as $cuenta){

            //CUENTA CORRESPONDIENTE AL DEBITO
            if ($cuenta->code == '243603') $idPadreDeb = 868;
            elseif ($cuenta->code == '243605') $idPadreDeb = 1029;
            else $idPadreDeb = 869;
            $padreDeb = PucAlcaldia::find($idPadreDeb);
            $hijosDeb = PucAlcaldia::where('padre_id', $idPadreDeb)->get();

            //SE INGRESA EL PADRE
            $tableRT[] = collect(['code' => $cuenta->code, 'concepto' => $cuenta->concepto,
                'valorDesc' => 0, 'cc' => '',
                'nameTer' => '', 'codeDeb' => $padreDeb->code,
                'conceptoDeb' => $padreDeb->concepto, 'valorDeb' => 0]);


            $hijos = PucAlcaldia::where('padre_id', $cuenta->id)->get();
            foreach ($hijos as $hijo){
                $retefuenteCode = RetencionFuente::where('codigo', $hijo->code)->first();
                if ($retefuenteCode){
                    $descuentosOP = OrdenPagosDescuentos::where('retencion_fuente_id', $retefuenteCode->id)->get();
                    foreach ($descuentosOP as $descuento){
                        $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')->first();
                        if ($ordenPago){
                            if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $vigencia_id){
                                //SE RECORRE EL PUC PARA OBTENER LOS VALORES DE LA OP
                                dd($ordenPago->pucs);
                                foreach ($ordenPago->pucs as $puc){
                                    //SE RECORRE EL PADRE CORRESPONDIENTE AL DEBITO PARA SABER SI UN HIJO CORRESPONDE
                                    if (count($hijosDeb) > 0){
                                        foreach ($hijosDeb as $hDeb){
                                            if ($hDeb->id == $puc->rubros_puc_id ){
                                                //dd($hDeb, $puc);
                                                $tableRT[] = collect(['code' => $retefuenteCode->codigo, 'concepto' => $retefuenteCode->concepto,
                                                    'valorDesc' => $descuento->valor, 'cc' => $ordenPago->registros->persona->num_dc,
                                                    'nameTer' => $ordenPago->registros->persona->nombre, 'codeDeb' => $hDeb->code,
                                                    'conceptoDeb' => $hDeb->concepto, 'valorDeb' => $puc->valor_debito]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($tableRT)){
            return view('administrativo.tesoreria.retefuente.pago', compact('tableRT'));
        } else {
            Session::flash('error','Para el mes escogido no hay ordenes de pago finalizadas. Seleccione un mes distinto.');
            return back();
        }

    }
}
