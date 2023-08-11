<?php

namespace App\Http\Controllers\Administrativo\Pago;

use App\BPin;
use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use PDF;

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
            if (isset($data->orden_pago->registros)) {
                if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id) {
                    $pagosTarea[] = collect(['info' => $data, 'cc' => $data->persona->num_dc, 'persona' => $data->persona->nombre]);
                }
            } else {
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->orden_pago_id)->first();
                if ($tesoreriaRetefuentePago){
                    if ($tesoreriaRetefuentePago->vigencia_id == $id){
                        $pagosTarea[] = collect(['info' => $data, 'cc' => 800197268, 'persona' => 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN']);
                    }
                }
            }
        }

        $p = Pagos::where('estado','!=', '0')->get();
        foreach ($p as $data){
            if (isset($data->orden_pago->registros)){
                if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                    $pagos[] = collect(['info' => $data]);
                }
            } else{
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->orden_pago_id)->first();
                if ($tesoreriaRetefuentePago){
                    if ($tesoreriaRetefuentePago->vigencia_id == $id){
                        $pagos[] = collect(['info' => $data]);
                    }
                }
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
            $data->maxValuePay = $data->pucs->sum('valor_credito');

            if (isset($data->registros->cdpsRegistro)){
                if ($data->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                    $ordenPagos[] = collect(['info' => $data]);
                }
            } else {
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->id)->first();
                if (isset($tesoreriaRetefuentePago)) {
                    if ($tesoreriaRetefuentePago->vigencia_id == $id){
                        $ordenPagos[] = collect(['info' => $data, 'cc' => 800197268, 'persona' => 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN']);
                    }
                }
            }

        }
        $pagos = Pagos::orderBy('code','ASC')->get();
        foreach ($pagos as $data){
            if (isset($data->orden_pago->registros)) {
                if ($data->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id == $id) {
                    $pagosAll[] = collect(['info' => $data, 'cc' =>  $data->orden_pago->registros->persona->num_dc
                        , 'persona' => $data->orden_pago->registros->persona->nombre]);
                }
            } else{
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $data->orden_pago_id)->first();
                if (isset($tesoreriaRetefuentePago)) {
                    if ($tesoreriaRetefuentePago->vigencia_id == $id) {
                        $pagosAll[] = collect(['info' => $data, 'cc' => 800197268, 'persona' => 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN']);
                    }
                }
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
        $OrdenPago = OrdenPagos::findOrFail($request->IdOP);
        $vigencia = Vigencia::find($request->vigencia_id);

        if ($request->Monto > $OrdenPago->saldo){

            Session::flash('warning','El valor que va a pagar: $'.$request->Monto.' es mayor al valor disponible de la orden de pago: $'.$OrdenPago->saldo);
            return back();

        } else {

            //SE REALIZA LA BUSQUEDA DEL CODIGO CORRESPONDIENTE AL NUEVO PAGO
            $pago = Pagos::orderBy('code','DESC')->first();
            $añoPago = Carbon::parse($pago->created_at)->format('Y');
            if (intval($añoPago) == $vigencia->vigencia) $codePago = $pago->code + 1;
            else $codePago = 1;

            $Pago = new Pagos();
            $Pago->code = $codePago;
            $Pago->concepto = trim(preg_replace('/\s+/', ' ', $request->Objeto));
            if (isset($OrdenPago->registros->cdpsRegistro)) $Pago->persona_id = $OrdenPago->registros->persona_id;
            else $Pago->persona_id = 75;
            $Pago->orden_pago_id = $request->IdOP;
            $Pago->valor = $request->Monto;
            $Pago->estado = "0";
            $Pago->responsable_id = auth()->user()->id;
            //$Pago->created_at = '2023-06-30 12:00:00';
            $Pago->save();

            //BUSQUEDA DEL ID DEL RUBRO
            //$Pago->orden_pago->rubros[0]->cdps_registro->rubro_id = $rubroid->fontRubro->rubro_id;

            if (count($Pago->orden_pago->rubros) == 1){
                $pagoRubros = new PagoRubros();
                $pagoRubros->pago_id = $Pago->id;

                // SI EL PAGO NO ES DE RETEFUENTE
                if (isset($Pago->orden_pago->rubros[0]->cdps_registro)){
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
        $lv1 = PucAlcaldia::where('padre_id', 618 )->get();
        foreach ($lv1 as $dato){
            //$result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) {
                //$result[] = $cuenta;
                $lv3 = PucAlcaldia::where('padre_id', $cuenta->id )->get();
                foreach ($lv3 as $level3) {
                    //$result[] = $level3;
                    $hijos = PucAlcaldia::where('padre_id', $level3->id )->get();
                    foreach ($hijos as $hijo)  $cuentas[] = $hijo;
                }
            }
        }
        $personas = Persona::all();
        $pago = Pagos::findOrFail($id);
        if (count($pago->rubros) > 0){
            $PUCS = RubrosPuc::where('naturaleza','1')->get();
            $hijosPUC = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();

            $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
            foreach ($lv1 as $dato){
                $cuentasBanc[] = $dato;
                $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
                foreach ($lv2 as $cuenta) $cuentasBanc[] = $cuenta;
            }

            if (isset($pago->orden_pago->rubros[0]->cdps_registro)) $vigencia_id = $pago->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id;
            else {
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $pago->orden_pago_id)->first();
                if (isset($tesoreriaRetefuentePago)){
                    $vigencia_id = $tesoreriaRetefuentePago->vigencia_id;
                }
            }

            return view('administrativo.pagos.createBanks', compact('pago','PUCS', 'hijosPUC',
            'cuentas', 'personas', 'vigencia_id','cuentasBanc'));
        } else {
            Session::flash('warning','El pago no ha recibido la asignación del monto, por favor realizarla');
            return redirect('administrativo/pagos/asignacion/'.$pago->id);
        }
    }

    public function bankStore(Request $request){

        $valReceived =array_sum($request->val);
        $pago = Pagos::findOrFail($request->pago_id);

        if ($request->terceroRetefuente){
            $reteFValidate = false;
            foreach ($request->terceroRetefuente as $reteFuente){
                if ($reteFuente != 0){
                    $reteFValidate = true;
                    $tercero = Persona::find($reteFuente);
                    $pago->concepto = $pago->concepto." - ".$tercero->num_dc." ".$tercero->nombre;
                }
            }
            if ($reteFValidate) $pago->reteFuente = '1';
        }

        if ($request->adultoMayor != 0){
            $pago->persona_id = $request->adultoMayor;
            $adultoMayor = Persona::find($request->adultoMayor);
            $pago->concepto = $pago->concepto." - ".$adultoMayor->num_dc." ".$adultoMayor->nombre;
            $pago->adultoMayor = '1';
        }

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

                //SE REGISTRA SI EL PAGO ES POR EMBARGO
                if (isset($request->embargo) and $request->embargo == "1") $pago->embargo = '1';

                $pago->estado = "1";
                $pago->ff_fin = today()->format("Y-m-d");
                //$pago->ff_fin = "2023-06-30";
                $pago->save();

                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $pago->orden_pago_id)->first();

                if (isset($tesoreriaRetefuentePago)){
                    $tesoreriaRetefuentePago->comp_egreso_id = $pago->id;
                    $tesoreriaRetefuentePago->save();
                }

                if ($pago->adultoMayor == '1' OR $pago->embargo == '1'){
                    foreach ($pago->orden_pago->pucs as $pucOP){
                        if ($pucOP->valor_credito > 0){
                            $newPagoBank = new PagoBanksNew();
                            $newPagoBank->pagos_id = $pago->id;
                            $newPagoBank->rubros_puc_id = $pucOP->rubros_puc_id;
                            $newPagoBank->debito = $pago->valor;
                            $newPagoBank->credito = 0;
                            $newPagoBank->persona_id = $pago->persona_id;
                            $newPagoBank->created_at = $pago->created_at;
                            $newPagoBank->save();
                        }
                    }
                } elseif ($pago->reteFuente == '1'){
                    //SI ES UN PAGO DE RETEFUENTE
                    $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $pago->orden_pago_id)->first();
                    foreach ($tesoreriaRetefuentePago->contas as $debPay){
                        if ($debPay->debito > 0){
                            $newPagoBank = new PagoBanksNew();
                            $newPagoBank->pagos_id = $pago->id;
                            $newPagoBank->rubros_puc_id = $debPay->cuenta_puc_id;
                            $newPagoBank->debito = $debPay->debito;
                            $newPagoBank->credito = 0;
                            $newPagoBank->persona_id = $debPay->persona_id;
                            $newPagoBank->created_at = $pago->created_at;
                            $newPagoBank->save();
                        }
                    }
                } else{
                    foreach ($pago->orden_pago->pucs as $pucOP){
                        if ($pucOP->valor_credito > 0){
                            $newPagoBank = new PagoBanksNew();
                            $newPagoBank->pagos_id = $pago->id;
                            $newPagoBank->rubros_puc_id = $pucOP->rubros_puc_id;
                            $newPagoBank->debito = $pucOP->valor_credito;
                            $newPagoBank->credito = 0;
                            $newPagoBank->persona_id = $pago->persona_id;
                            $newPagoBank->created_at = $pago->created_at;
                            $newPagoBank->save();
                        }
                    }
                }

                for($i=0;$i< count($request->banco); $i++){
                    $bank = new PagoBanksNew();
                    $bank->pagos_id = $request->pago_id;
                    $bank->rubros_puc_id = $request->banco[$i];
                    $bank->debito = 0;
                    $bank->credito = $request->val[$i];
                    $bank->persona_id = $pago->persona_id;
                    $bank->created_at = $pago->created_at;
                    if ($request->terceroRetefuente and $i > 0 and $reteFValidate) $bank->persona_id = $request->terceroRetefuente[$i-1];
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
        $banks = PagoBanksNew::where('pagos_id', $pago->id)->get();
        $ordenPago = OrdenPagos::findOrFail($pago->orden_pago_id);

        if (isset($pago->orden_pago->rubros[0]->cdps_registro)) $vigencia_id = $pago->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id;
        else {
            $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $pago->orden_pago_id)->first();
            if (isset($tesoreriaRetefuentePago)){
                $vigencia_id = $tesoreriaRetefuentePago->vigencia_id;
            }
        }

        $rol = auth()->user()->roles->first()->id;

        return view('administrativo.pagos.show', compact('pago','ordenPago','banks','vigencia_id',
        'rol'));
    }


    public function anular($id, Request $request){
        $pago = Pagos::find($id);
        $pago->observacion = $request->observacion;
        $pago->estado = '2';
        $pago->save();

        $ordenPago = OrdenPagos::find($pago->orden_pago_id);
        $ordenPago->saldo = $ordenPago->saldo + $pago->valor;
        $ordenPago->save();

        Session::flash('error','El pago ha sido anulado');
        return redirect('/administrativo/pagos/show/'.$id);
    }

    public function changeCheque($id,Request $request){
        $pago = Pagos::find($id);
        $pago->num = $request->cheque;
        $pago->save();
        return $pago;
    }

    public function delete($id, $vigencia){

        $banks = PagoBanksNew::where('pagos_id', $id)->get();
        foreach ($banks as $bank) $bank->delete();

        $rubros = PagoRubros::where('pago_id', $id)->get();
        foreach ($rubros as $rubro) $rubro->delete();

        $pago = Pagos::find($id);
        $pago->delete();

        Session::flash('error','Pago borrado correctamente');
        return redirect('../administrativo/pagos/'.$vigencia);
    }

    public function getCheque($id){
        $pago = Pagos::find($id);
        $pdf = PDF::loadView('administrativo.pagos.pdfCheque', compact('pago'))->setOptions(['images' => true,
            'isRemoteEnabled' => true]);

        return $pdf->stream('Cheque Pago #'.$pago->code.'.pdf');
    }
}
