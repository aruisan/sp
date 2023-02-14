<?php

namespace App\Http\Controllers\Administrativo\Pago;

use App\BPin;
use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Persona;
use Illuminate\Http\Request;
use Session;

class PagosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $pT = Pagos::where('estado', '0')->get();
        foreach ($pT as $data){
            if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $pagosTarea[] = collect(['info' => $data, 'persona' => $data->persona->nombre]);
            }
        }

        $p = Pagos::where('estado','!=', '0')->get();
        foreach ($p as $data){
            if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $pagos[] = collect(['info' => $data]);
            }
        }
        if (!isset($pagosTarea)){
            $pagosTarea[] = null;
            unset($pagosTarea[0]);
        }
        if (!isset($pagos)){
            $pagos[] = null;
            unset($pagos[0]);
        }

        return view('administrativo.pagos.index', compact('pagos','pagosTarea','id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $oP = OrdenPagos::where([['estado', '1'], ['saldo', '>', 0]])->get();
        foreach ($oP as $data){
            if ($data->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $ordenPagos[] = collect(['info' => $data]);
            }
        }
        $pagos = Pagos::orderBy('code','ASC')->get();
        foreach ($pagos as $data){
            if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $pagosAll[] = collect(['info' => $data, 'persona' => $data->orden_pago->registros->persona->nombre]);
            }
        }
        if (!isset($ordenPagos)){
            Session::flash('warning', 'No hay ordenes de pago disponibles para crear el pago. ');
            return redirect('/administrativo/pagos/'.$id);
        } else {
            if (isset($pagosAll)){
                $last2 = array_last($pagosAll);
                $numP = $last2['info']->code;
            }else{
                $numP = 0;
            }
            return view('administrativo.pagos.create', compact('ordenPagos','id','numP'));
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
        if ($request->Monto > $request->SaldoOP){

            Session::flash('warning','El valor que va a pagar: $'.$request->Monto.' es mayor al valor disponible de la orden de pago: $'.$request->SaldoOP);
            return back();

        } else {

            $OrdenPago = OrdenPagos::findOrFail($request->IdOP);

            //SE REALIZA LA BUSQUEDA DEL CODIGO CORRESPONDIENTE AL NUEVO PAGO
            $pagos = Pagos::orderBy('code','ASC')->get();
            foreach ($pagos as $pago){
                if ($pago->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $OrdenPago->registros->cdpsRegistro[0]->cdp->vigencia_id){
                    $PagoArray[] = collect(['info' => $pago]);
                }
            }
            if (isset($PagoArray)) {
                $last = array_last($PagoArray);
                $codePago = $last['info']->code + 1;
            }
            else $codePago = 0;

            $Pago = new Pagos();
            $Pago->code = $codePago;
            $Pago->concepto = $request->Objeto;
            $Pago->persona_id = $OrdenPago->registros->persona_id;
            $Pago->orden_pago_id = $request->IdOP;
            $Pago->valor = $request->Monto;
            $Pago->estado = "0";
            $Pago->responsable_id = auth()->user()->id;
            $Pago->save();

            //BUSQUEDA DEL ID DEL RUBRO
            //$Pago->orden_pago->rubros[0]->cdps_registro->rubro_id = $rubroid->fontRubro->rubro_id;

            if (count($Pago->orden_pago->rubros) == 1){
                $pagoRubros = new PagoRubros();
                $pagoRubros->pago_id = $Pago->id;

                if ($Pago->orden_pago->rubros[0]->cdps_registro->cdps->tipo == "Inversion"){
                    $codActiv = $Pago->orden_pago->rubros[0]->cdps_registro->cdps->bpinsCdpValor->first()->cod_actividad;
                    $bin = BPin::where('cod_actividad', $codActiv )->first();
                    $bPinVig = bpinVigencias::where('bpin_id',$bin->id)->where('vigencia_id', $OrdenPago->registros->cdpsRegistro[0]->cdp->vigencia_id)->first();
                    $depRub = DependenciaRubroFont::find($bPinVig->dep_rubro_id);
                    $rubroIDInv = $depRub->fontRubro->rubro_id;

                    $pagoRubros->rubro_id = $rubroIDInv;

                } else{

                    $rubroid = DependenciaRubroFont::find($Pago->orden_pago->registros->cdpRegistroValor[0]->cdps->rubrosCdpValor[0]->fontsDep_id);
                    $pagoRubros->rubro_id = $rubroid->fontRubro->rubro_id;
                }
                $pagoRubros->valor = $Pago->valor;
                $pagoRubros->save();

                return redirect('/administrativo/pagos/banks/'.$Pago->id);
            } else {
                return redirect('/administrativo/pagos/asignacion/'.$Pago->id);
            }
        }
    }

    public function asignacion($id){
        $pago = Pagos::findOrFail($id);
        $rubros = $pago->orden_pago->rubros;
        if (count($pago->orden_pago->rubros) == 1){
            return redirect('/administrativo/pagos/banks/'.$pago->id);
        } else {
            foreach ($rubros as $rubro){
                $valC[] = $rubro->valor;
            }
            $vOP = $pago->valor;
            foreach ($valC as $value){
                if ($vOP == 0){
                    $distri[] = 0;
                }else {
                    if ($vOP >= $value){
                        $vOP = $vOP - $value;
                        $distri[] = $value;
                        $a[] = $vOP;
                    } else {
                        $distri[] = $vOP;
                        $vOP = 0;
                    }
                }
            }
            return view('administrativo.pagos.createRubros', compact('pago','distri'));
        }
    }

    public function asignacionStore(Request $request){
        $pago = Pagos::findOrFail($request->pago_id);
        $valR = array_sum($request->valor);
        if ($valR == $pago->valor){
            for($i=0;$i< count($request->valor); $i++){
                $pagoRubros = new PagoRubros();
                $pagoRubros->pago_id = $pago->id;
                $pagoRubros->rubro_id = $request->idR[$i];
                $pagoRubros->valor = $request->valor[$i];
                $pagoRubros->save();
            }
            return redirect('/administrativo/pagos/banks/'.$pago->id);
        } else {
            Session::flash('warning','El valor tomado de los rubros debe ser igual al valor a pagar: $'.$pago->valor);
            return back();
        }
    }

    public function asignacionDelete(Request $request){
        $pagosRubro = PagoRubros::where('pago_id',$request->id)->get();
        foreach ($pagosRubro as $data){
            $delete = PagoRubros::findOrFail($data->id);
            $delete->delete();
        }
        Session::flash('warning','Los valores se han reiniciado');
        return redirect('administrativo/pagos/asignacion/'.$request->id);
    }

    public function bank($id){
        $cuentas24 = PucAlcaldia::where('id','>=',622)->where('id','<=',711)->where('hijo','1')->get();
        $personas = Persona::all();
        $pago = Pagos::findOrFail($id);
        if (count($pago->rubros) > 0){
            $PUCS = RubrosPuc::where('naturaleza','1')->get();
            $hijosPUC = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();

            return view('administrativo.pagos.createBanks', compact('pago','PUCS', 'hijosPUC',
            'cuentas24', 'personas'));
        } else {
            Session::flash('warning','El pago no ha recibido la asignación del monto, por favor realizarla');
            return redirect('administrativo/pagos/asignacion/'.$pago->id);
        }
    }

    public function bankStore(Request $request){

        $valReceived =array_sum($request->val);
        $pago = Pagos::findOrFail($request->pago_id);
        $valTotal = $pago->valor;


        if ($request->referenciaPago != null){
            $pago->referenciaPago = $request->referenciaPago;
        }

        if ($request->cuentaDesc) {
            for ($x = 0; $x < count($request->cuentaDesc); $x++) {
                $descuentos[] = $request->valorDesc[$x];
            }
            $valReceived = $valReceived + array_sum($descuentos);
        }

        $valR =number_format($valReceived,0);
        $valT = number_format($valTotal,0);

        if ($valReceived == $valTotal){

            $OP = OrdenPagos::findOrFail($request->ordenPago_id);

            if ($OP->saldo >= $valTotal){

                if ($request->type_pay == "1"){
                    $pago->type_pay = "CHEQUE";
                    $pago->num = $request->num_cheque;
                }elseif ($request->type_pay == "2"){
                    $pago->type_pay = "ACCOUNT";
                    $pago->num = $request->num_cuenta;
                }

                $pago->estado = "1";
                $pago->ff_fin = today()->format("Y-m-d");
                $pago->save();

                for($i=0;$i< count($request->banco); $i++){
                    $bank = new PagoBanks();
                    $bank->pagos_id = $request->pago_id;
                    $bank->rubros_puc_id = $request->banco[$i];
                    $bank->valor = $request->val[$i];
                    $bank->save();
                }

                //SE ALMACENAN LOS NUEVOS DESCUENTOS MUNICIPALES
                if ($request->cuentaDesc){
                    for($x=0;$x< count($request->cuentaDesc); $x++){
                        $cuenta = PucAlcaldia::find($request->cuentaDesc[$x]);
                        $descuento = new OrdenPagosDescuentos();
                        $descuento->nombre = $cuenta->concepto;
                        $descuento->base = 0;
                        $descuento->porcent = 0;
                        $descuento->valor = $request->valorDesc[$x];
                        $descuento->orden_pagos_id = $request->ordenPago_id;
                        $descuento->cuenta_puc_id = $request->cuentaDesc[$x];
                        $descuento->persona_id = $request->tercero[$x];
                        $descuento->save();
                    }
                }

                $OP->saldo = $OP->saldo -  $valTotal;
                $OP->save();

                Session::flash('success','El pago se ha finalizado exitosamente');
                return redirect('/administrativo/pagos/show/'.$pago->id);

            } else {
                Session::flash('warning','El valor que va a pagar: $'.$valTotal.' es mayor al valor disponible de la orden de pago: $'.$OP->saldo);
                return back();
            }
        } elseif ($valReceived > $valTotal){
            Session::flash('warning','El valor que va a pagar: $'.$valR.' es mayor al valor correspondiente del pago: $'.$valT);
            return back();
        } else{
            Session::flash('warning','El valor que va a pagar: $'.$valR.' es menor al valor correspondiente del pago: $'.$valT);
            return back();
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pago = Pagos::findOrFail($id);
        $banks = PagoBanks::where('pagos_id', $pago->id)->get();
        $ordenPago = OrdenPagos::findOrFail($pago->orden_pago_id);

        return view('administrativo.pagos.show', compact('pago','ordenPago','banks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function edit(Pagos $pagos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pagos $pagos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pagos $pagos)
    {
        //
    }
}
