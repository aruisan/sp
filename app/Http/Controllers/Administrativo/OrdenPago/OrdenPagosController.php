<?php

namespace App\Http\Controllers\Administrativo\OrdenPago;

use App\Model\Admin\ConfigGeneral;
use App\Model\Administrativo\Contabilidad\LevelPUC;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagosPayments;
use App\Model\Administrativo\OrdenPago\OrdenPagosEgresos;
use App\Model\Administrativo\Contabilidad\Puc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\RadCuentas\RadCuentas;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Persona;
use App\Traits\ConteoTraits;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Registro\Registro;
use Session;
use PDF;
use Carbon\Carbon;

class OrdenPagosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = null)
    {
        if ($id == null){
            $añoActual = Carbon::now()->year;
            $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->first();
            $id = $vigens->id;
        }
        $ordenPagoTarea = OrdenPagos::where('estado', '0')->where('vigencia_id', $id )->get();
        $oPH = OrdenPagos::where('estado','!=', '0')->where('vigencia_id', $id )->orderBy('code','DESC')->paginate(500);
        if (!isset($ordenPagoTarea)){
            $ordenPagoTarea[] = null;
            unset($ordenPagoTarea[0]);
        }

        return view('administrativo.ordenpagos.index', compact('oPH','ordenPagoTarea','id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $Reg= Registro::where([['secretaria_e', '3'], ['saldo', '>', 0]])->get();
        foreach ($Reg as $reg){
            if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $Registros[] = collect(['info' => $reg]);
            }
        }
        $PUCs = Puc::all();

        $radCuentas = RadCuentas::where('estado_elabor','1')->where('estado_rev','1')->where('saldo','>',0)->where('vigencia_id', $id)->with('persona')->get();
        if (!isset($Registros)) {
            Session::flash('warning', 'No hay registros disponibles para crear la orden de pago, debe crear un nuevo registro. ');
            return redirect('/administrativo/ordenPagos/'.$id);
        }elseif ($PUCs == null){
            Session::flash('warning', 'No hay un PUC alamcenado en el software o finalizado, debe disponer de uno para poder realizar una orden de pago. ');
            return redirect('/administrativo/ordenPagos');
        }else{
            return view('administrativo.ordenpagos.create', compact('Registros','id','radCuentas'));
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
        if ($request->IdR == 0){
            //ORDEN DE PAGO DE RADICACION DE CUENTA

            $radCuenta = RadCuentas::find($request->IdRadCuenta);
            if ($radCuenta->saldo < $request->ValTOP){
                Session::flash('warning','El valor no puede ser superior al valor disponible del radicación seleccionada: '.$radCuenta->saldo.' Rectifique el valor de la orden de pago y el iva.');
                return redirect('/administrativo/ordenPagos/create/'.$request->vigencia);
            } else {
                $ordenPago = new OrdenPagos();
                $ordenPago->nombre = trim(preg_replace('/\s+/', ' ', $request->concepto));
                $ordenPago->valor = $request->ValTOP;
                $ordenPago->saldo = $request->ValTOP;
                $ordenPago->iva = $request->ValIOP;
                $ordenPago->estado = $request->estado;
                $ordenPago->vigencia_id = $request->vigencia;
                $ordenPago->rad_cuenta_id = $radCuenta->id;
                $ordenPago->user_id = auth()->user()->id;
                //$ordenPago->created_at = '2023-06-30 12:00:00';
                $ordenPago->save();

                $ordenPago->code = $ordenPago->id;
                $ordenPago->save();

                Session::flash('success','La orden de pago se ha creado exitosamente');
                return redirect('/administrativo/ordenPagos/monto/create/'.$ordenPago->id);

            }

        } else{
            //AL SER UNA ORDEN DE PAGO DE RP
            $registro_id = Registro::findOrFail($request->IdR);

            if ($request->ValTOP > $registro_id->saldo){
                Session::flash('warning','El valor no puede ser superior al valor disponible del registro seleccionado: '.$registro_id->saldo.' Rectifique el valor de la orden de pago y el iva.');
                return redirect('/administrativo/ordenPagos/create/'.$request->vigencia);
            } else {

                $oP = OrdenPagos::orderBy('code','ASC')->get();
                foreach ($oP as $data){
                    if (isset($data->registros->cdpsRegistro)) {
                        if ($registro_id->cdpsRegistro[0]->cdp->vigencia_id == $data->registros->cdpsRegistro[0]->cdp->vigencia_id) {
                            $ordenPago[] = collect(['info' => $data, 'persona' => $data->registros->persona->nombre]);
                        }
                    } else $ordenPago[] = collect(['info' => $data, 'persona' => 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN	']);
                }
                if (isset($ordenPago)){
                    $last = array_last($ordenPago);
                    $numOP = $last['info']->code + 1;
                }else $numOP = 0;

                $findCodeOPs = OrdenPagos::where('code', $numOP)->get();
                if (count($findCodeOPs) > 0 ){
                    $vigencia = Vigencia::find($request->vigencia);
                    foreach ($findCodeOPs as $find){
                        if (Carbon::parse($find->created_at)->format('Y') == $vigencia->vigencia) $numOP = $numOP + 1 ;
                    }
                }

                $ordenPago = new OrdenPagos();
                $ordenPago->nombre = trim(preg_replace('/\s+/', ' ', $request->concepto));
                $ordenPago->valor = $request->ValTOP;
                $ordenPago->saldo = $request->ValTOP;
                $ordenPago->iva = $request->ValIOP;
                $ordenPago->estado = $request->estado;
                $ordenPago->registros_id = $request->IdR;
                $ordenPago->user_id = auth()->user()->id;
                //$ordenPago->created_at = '2023-06-30 12:00:00';
                $ordenPago->save();

                $ordenPago->code = $ordenPago->id;
                $ordenPago->save();

                Session::flash('success','La orden de pago se ha creado exitosamente');
                return redirect('/administrativo/ordenPagos/monto/create/'.$ordenPago->id);
            }
        }
    }

    public function liquidacion($id)
    {
        $ordenPago = OrdenPagos::findOrfail($id);
        if ($ordenPago->rad_cuenta_id != 0) {
            $radCuenta = RadCuentas::find($ordenPago->rad_cuenta_id);
            $vigencia = $radCuenta->vigencia_id;
        } else $vigencia = $ordenPago->registros->cdpsRegistro[0]->cdp->vigencia_id;

        if ($ordenPago->descuentos->count() == 0){
            Session::flash('warning',' Se deben realizar primero los descuentos para poder hacer la contabilización de la orden de pago.');
            return redirect('administrativo/ordenPagos/descuento/create/'.$ordenPago->id);
        }else{
            $Usuarios = Persona::all();
            $data = Puc::all()->first();
            $puc_id = $data->id;
            $ultimoLevel = LevelPUC::where('puc_id', $puc_id)->get()->last();

            global $lastLevel;
            $lastLevel = $ultimoLevel->id;

            $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

            $hijosPUC = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();

            foreach ($R1 as $r1) {
                $codigoEnd = $r1->code;
                //$codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                foreach ($r1->codes as $data1){
                    $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                    $codigo = $reg0->code;
                    $codigoEnd = "$r1->code$codigo";
                    //$codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                    if ($reg0->codes){
                        foreach ($reg0->codes as $data3){
                            $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                            $codigo = $reg->code;
                            $codigoF = "$codigoEnd$codigo";
                            //$codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                            foreach ($reg->codes as $data4){
                                $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                                $codigo = $reg1->code;
                                $code = "$codigoF$codigo";
                                //$codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                                foreach ($reg1->rubro as $rubro){
                                    $codigo = $rubro->codigo;
                                    $code1 = "$code$codigo";
                                    $codigos[] = collect(['id' => $rubro->id, 'codigo' => $code1, 'name' => $rubro->nombre_cuenta, 'code' => $rubro->codigo, 'code_N' =>  $rubro->codigo_NIPS, 'name_N' => $rubro->nombre_NIPS, 'naturaleza' => $rubro->naturaleza,'per_id' => $rubro->persona_id, 'register_id' => $rubro->registers_puc_id]);
                                }
                            }
                        }
                    }
                }
            }
            $ordenPagoDesc = OrdenPagosDescuentos::where('orden_pagos_id',$id)->get();
            if ($ordenPago->rad_cuenta_id != 0) $registro = $radCuenta->registro;
            else $registro = Registro::findOrFail($ordenPago->registros_id);
            $Pagos = OrdenPagos::where('estado','=',1);
            $SumPagos = $Pagos->sum('valor');

            return view('administrativo.ordenpagos.createLiquidacion', compact('ordenPago',
                'registro','SumPagos','ordenPagoDesc','Usuarios','codigos','vigencia','hijosPUC'));
        }
    }

    public function liquidar(Request $request)
    {

        if (count($request->PUC) > 200){
            Session::flash('warning','Es posible que se hayan replicado las cuentas del PUC. Por favor seleccionelas nuevamente. ');
            return back();
        }
        $ordenPago = OrdenPagos::findOrFail($request->ordenPago_id);
        for ($i=0;$i< count($request->PUC); $i++){
            if ($request->PUC[$i] == "Selecciona un PUC"){
                Session::flash('warning','Recuerde seleccionar un PUC antes de continuar');
                return back();
            }else{
                $totalDeb = array_sum($request->valorPucD);
                $totalCred = array_sum($request->valorPucC);
                $totalDes = $ordenPago->descuentos->sum('valor');
                if ( $totalCred + $totalDes == $totalDeb){

                    if($ordenPago->rad_cuenta_id != 0){
                        $ordenPago->radCuenta->saldo = $ordenPago->radCuenta->saldo - $request->valorPucD[$i];
                        if ($ordenPago->radCuenta->saldo < 0){
                            Session::flash('warning','Es posible que se hayan replicado las cuentas del PUC y la Orden de Pago ya este finalizada. Revice la orden de pago '.$request->ordenPago_id);
                            return redirect('/administrativo/ordenPagos/show/'.$request->ordenPago_id);
                        }
                        $ordenPago->radCuenta->save();

                    } else {
                        $registro = Registro::findOrFail($ordenPago->registros_id);
                        $registro->saldo = $registro->saldo - $request->valorPucD[$i];
                        if ($registro->saldo < 0){
                            Session::flash('warning','Es posible que se hayan replicado las cuentas del PUC y la Orden de Pago ya este finalizada. Revice la orden de pago '.$request->ordenPago_id);
                            return redirect('/administrativo/ordenPagos/show/'.$request->ordenPago_id);
                        }
                        $registro->save();
                    }

                    $oPP = new OrdenPagosPuc();
                    $oPP->rubros_puc_id = $request->PUC[$i];
                    $oPP->orden_pago_id = $request->ordenPago_id;
                    $oPP->valor_debito = $request->valorPucD[$i];
                    $oPP->valor_credito = $request->valorPucC[$i];
                    $oPP->save();

                } else {
                    Session::flash('warning','Recuerde que los totales del credito y debito deben dar sumas iguales');
                    return back();
                }
            }
        }

        foreach ($ordenPago->descuentos as $descuento){
            if ($descuento->desc_municipal_id != null) $ordenPago->saldo = $ordenPago->saldo - $descuento->valor;
            if ($descuento->retencion_fuente_id != null) $ordenPago->saldo = $ordenPago->saldo - $descuento->valor;
        }
        $ordenPago->estado = "1";
        $ordenPago->save();

        Session::flash('success','La orden de pago se ha finalizado exitosamente');
        return redirect('/administrativo/ordenPagos/show/'.$request->ordenPago_id);
    }

    public function paySave(Request $request){
        $valReceived =array_sum($request->val);
        $valTotal = $request->Pay;
        $valR =number_format($valReceived,0);
        $valT = number_format($valTotal,0);

        if ($valReceived == $valTotal){
            $OPE = new OrdenPagosEgresos();
            if ($request->type_pay == "1"){
                $OPE->type_pay = "CHEQUE";
                $OPE->num = $request->num_cheque;
            }elseif ($request->type_pay == "2"){
                $OPE->type_pay = "ACCOUNT";
                $OPE->num = $request->num_cuenta;
            }
            $OPE->save();
            for($i=0;$i< count($request->banco); $i++){
                $OPPay = new OrdenPagosPayments();
                $OPPay->orden_pago_id = $request->OP;
                $OPPay->rubros_puc_id = $request->banco[$i];
                if ($request->type_pay == "1"){
                    $OPPay->num = $request->num_cheque;
                    $OPPay->type_pay = "CHEQUE";
                }elseif ($request->type_pay == "2"){
                    $OPPay->num = $request->num_cuenta;
                    $OPPay->type_pay = "ACCOUNT";
                }
                $OPPay->valor = $request->val[$i];
                $OPPay->orden_pago_egreso_id = $OPE->id;
                $OPPay->save();
            }

            $OP = OrdenPagos::findOrFail($request->OP);
            $OP->estado = "1";
            $OP->saldo = $OP->saldo - $valReceived;
            $OP->save();

            Session::flash('success','La orden de pago se ha finalizado exitosamente');
            return redirect('/administrativo/ordenPagos/'.$request->OP);
        } elseif ($valReceived > $valTotal){
            Session::flash('warning','El valor que va a pagar: $'.$valR.' es mayor al valor correspondiente del pago: $'.$valT);
            return back();
        } else{
            Session::flash('warning','El valor que va a pagar: $'.$valR.' es menor al valor correspondiente del pago: $'.$valT);
            return back();
        }
    }

    public function pay($id){
        $OP = OrdenPagos::findOrFail($id);
        $OPP = $OP->pucs;
        $PUCS = RubrosPuc::where('naturaleza','1')->get();

        return view('administrativo.ordenpagos.createPay', compact('OPP','OP','PUCS'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\orden_pagos  $orden_pagos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $OrdenPago = OrdenPagos::findOrFail($id);

        if ($OrdenPago->rad_cuenta_id != 0) {
            $radCuenta = RadCuentas::find($OrdenPago->rad_cuenta_id);
            $vigenc = $radCuenta->vigencia_id;
            $R = $OrdenPago->radCuenta->registro;
        } else {
            $vigenc = $OrdenPago->registros->cdpsRegistro[0]->cdp->vigencia_id;
            $R = Registro::findOrFail($OrdenPago->registros_id);
        }
        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $id)->get();

        $all_rubros = Rubro::where('vigencia_id', $vigenc);
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $vigens = Vigencia::findOrFail($vigenc);
        $V = $vigens->id;
        $vigencia_id = $V;

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if ($data->id < '324') {
            } elseif (count($rubro) > 0) $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
        }

        $rol = auth()->user()->roles->first()->id;

        $pagos = Pagos::where('orden_pago_id', $id)->get();

        return view('administrativo.ordenpagos.show', compact('OrdenPago','OrdenPagoDescuentos','R','infoRubro','vigencia_id','rol','pagos'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\orden_pagos  $orden_pagos
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Registros = Registro::where('secretaria_e', '3')->get();
        $ordenPago = OrdenPagos::findOrFail($id);
        $vigenc = $ordenPago->registros->cdpsRegistro[0]->cdp->vigencia_id;
        $hijosPUC = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();
        $retenF = RetencionFuente::all();
        $cuentas24 = PucAlcaldia::where('id','>=',622)->where('id','<=',711)->where('hijo','1')->get();
        $personas = Persona::all();
        if ($ordenPago->iva > 0) $desMun = DescMunicipales::all();
        else $desMun = DescMunicipales::where('id','!=','4')->get();

        $pagos = Pagos::where('orden_pago_id', $id)->get();
        return view('administrativo.ordenpagos.edit', compact('Registros','ordenPago',
            'vigenc','hijosPUC', 'retenF', 'desMun','cuentas24','personas','pagos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\orden_pagos  $orden_pagos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $ordenPago = OrdenPagos::findOrFail($id);
        $vigenc = $ordenPago->registros->cdpsRegistro[0]->cdp->vigencia_id;
        $ordenPago->nombre = $request->nombre;
        $ordenPago->save();

        //SE ACTUALIZAN LAS CUENTAS DEL PUC
        foreach ($ordenPago->pucs as $index => $puc){
            $puc->rubros_puc_id = $request->PUC[$index];
            $puc->save();
        }

        Session::flash('success','La orden de pago se ha actualizado exitosamente');
        return redirect('/administrativo/ordenPagos/'.$vigenc);


    }

    public function deleteRF($id){

        $retenF = OrdenPagosDescuentos::findOrFail($id);
        $retenF->delete();
        Session::flash('error','Descuento de la Retención de Fuente eliminado de la Orden de Pago');
    }

    public function deleteM($id){

        $municipal = OrdenPagosDescuentos::findOrFail($id);
        $municipal->delete();
        Session::flash('error','Descuento Municipal eliminado de la Orden de Pago');
    }

    public function deleteP($id){
        $puc = OrdenPagosPuc::findOrFail($id);
        $puc->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\orden_pagos  $orden_pagos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orden = OrdenPagos::findOrFail($id);
        $vigenc = $orden->registros->cdpsRegistro[0]->cdp->vigencia_id;
        if ($orden->estado != 0){
            Session::flash('warning', 'La orden de pago ya ha sido finalizada y no se puede eliminar.');
            return redirect('/administrativo/ordenPagos/'.$vigenc);
        }else{
            $descuentos = OrdenPagosDescuentos::where('orden_pagos_id','=',$id)->get();
            foreach ($descuentos as $descuento) $descuento->delete();

            $rubros = OrdenPagosRubros::where('orden_pagos_id','=',$id)->get();
            foreach ($rubros as $rubro) $rubro->delete();

            $pucs = OrdenPagosPuc::where('orden_pago_id','=',$id)->get();
            foreach ($pucs as $puc) $puc->delete();

            $orden->delete();
            Session::flash('error','Orden de pago eliminada correctamente');
            return redirect('/administrativo/ordenPagos/'.$vigenc);
        }
    }

    public function pdf_OP($id)
    {
        $OrdenPago = OrdenPagos::findOrFail($id);
        $OrdenPago->responsable = User::find($OrdenPago->user_id);

        if (isset($OrdenPago->registros->cdpsRegistro)) {
            $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $id)->where('valor', '>', 0)->get();
            $R = Registro::findOrFail($OrdenPago->registros_id);

            $all_rubros = Rubro::all();
            foreach ($all_rubros as $rubro) {
                if ($rubro->fontsRubro->sum('valor_disp') != 0) {
                    $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                    $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                    $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
                }
            }

            //codigo de rubros

            $vigens = Vigencia::findOrFail($R->cdpsRegistro[0]->cdp->vigencia_id);
            $V = $vigens->id;
            $vigencia_id = $V;

            //NEW PRESUPUESTO
            $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
            foreach ($plantilla as $data) {
                $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
                if ($data->id < '324') {
                } elseif (count($rubro) > 0) $infoRubro[] = ['id_rubro' => $rubro->first()->id, 'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
            }

            $fecha = Carbon::createFromTimeString($OrdenPago->created_at);
            $fechaR = Carbon::createFromTimeString($R->created_at);
            $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

            $presidentes = ConfigGeneral::where('tipo', 'PRESIDENTE')->get();

            foreach ($presidentes as $president) {
                if ($president->fecha_inicio <= $fecha) {
                    if ($president->fecha_fin >= $fecha) {
                        $name_pres = $president->nombres;
                    }
                }
            }

            if (!isset($name_pres)) {
                $name_pres = "POR DEFINIR";
            }

            $contadores = ConfigGeneral::where('tipo', 'CONTADOR')->get();

            foreach ($contadores as $contador) {
                if ($contador->fecha_inicio <= $fecha) {
                    if ($contador->fecha_fin >= $fecha) {
                        $name_contador = $contador->nombres;
                    }
                }
            }

            if (!isset($name_contador)) {
                $name_contador = "POR DEFINIR";
            }

            $pdf = PDF::loadView('administrativo.ordenpagos.pdfOP', compact('OrdenPago',
                'OrdenPagoDescuentos', 'R', 'infoRubro', 'dias', 'meses', 'fecha', 'fechaR', 'name_pres',
                'name_contador'))->setOptions(['images' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream();
        }else{
            $fecha = Carbon::createFromTimeString($OrdenPago->created_at);
            $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

            $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $id)->first();

            $pdf = PDF::loadView('administrativo.ordenpagos.pdfOPRF', compact('OrdenPago',
                'dias', 'meses', 'fecha','tesoreriaRetefuentePago'))->setOptions(['images' => true, 'isRemoteEnabled' => true]);

            return $pdf->stream();
        }
    }

    public function pdf_CE($id)
    {
        $Pago = Pagos::findOrFail($id);
        $banks = PagoBanksNew::where('pagos_id', $Pago->id)->get();
        $Egreso_id = $Pago->code;
        $OrdenPago = OrdenPagos::findOrFail($Pago->orden_pago_id);
        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $OrdenPago->id)->where('valor','>',0)->get();

        if ($Pago->responsable_id) $Pago->responsable = User::find($Pago->responsable_id);

        $all_rubros = Rubro::all();
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $vigens = Vigencia::where('id', '>',0)->get();
        foreach ($vigens as $vigen) {
            $V = $vigen->id;
        }
        $vigencia_id = $V;

        $conteoTraits = new ConteoTraits;

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if ($data->id < '324') {
            } elseif (count($rubro) > 0) {
                if($rubro[0]->fontsRubro and $rubro[0]->tipo == "Funcionamiento"){
                    //SE VALIDA QUE EL RUBRO TENGA DINERO DISPONIBLE
                    foreach ($rubro[0]->fontsRubro as $fuentes){
                        foreach ($fuentes->dependenciaFont as $fontDep){
                            if (auth()->user()->dependencia_id == $fontDep->dependencia_id) $valDisp[] = $fontDep->saldo;
                        }
                    }
                    if (isset($valDisp) and array_sum($valDisp) > 0){
                        $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
                        unset($valDisp);
                    }
                }
            }
        }

        if (!isset($infoRubro)) $infoRubro = [];

        $fecha = Carbon::createFromTimeString($Pago->created_at);
        $fechaO = Carbon::createFromTimeString($OrdenPago->created_at);
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $contadores = ConfigGeneral::where('tipo','CONTADOR')->get();

        foreach ($contadores as $contador){
            if ($contador->fecha_inicio <= $fecha){
                if ($contador->fecha_fin >= $fecha){
                    $name_contador = $contador->nombres;
                }
            }
        }

        if (!isset($name_contador)){
            $name_contador = "POR DEFINIR";
        }

        $pdf = PDF::loadView('administrativo.ordenpagos.pdfCE', compact('OrdenPago','OrdenPagoDescuentos','infoRubro',
            'dias', 'meses', 'fecha','fechaO','Egreso_id','name_contador','banks','Pago'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();

    }

    public function embargos($id){

        $cuentas24 = PucAlcaldia::where('id','>=',622)->where('id','<=',711)->where('hijo','1')->get();
        $personas = Persona::all();
        $oPH = OrdenPagos::where('estado','1')->where('saldo','>',0)->get();
        $vigencia = Vigencia::find($id);
        foreach ($oPH as $data){
            if ($data->registros->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $ordenPagos[] = collect(['info' => $data, 'tercero' => $data->registros->persona->nombre]);
            }
        }

        if (!isset($ordenPagos)){
            $ordenPagos[] = null;
            unset($ordenPagos[0]);
        }

        return view('administrativo.ordenpagos.embargo', compact('ordenPagos','id','vigencia',
        'cuentas24','personas'));
    }

    public function getOPEmbargo(Request $request){
        $OP = OrdenPagos::where('id', $request->id)->with('descuentos','pucs','rubros')->first();
        return $OP;
    }

    public function getEmbargo(Request $request){
        dd($request);
    }

    public function anular($id, Request $request){
        $ordenPago = OrdenPagos::find($id);
        $ordenPago->saldo = 0;
        $ordenPago->observacion = $request->observacion;
        $ordenPago->estado = '2';
        $ordenPago->user_anulacion = auth()->id();
        $ordenPago->ff_anulacion = today();
        $ordenPago->save();

        $registro = Registro::find($ordenPago->registros_id);
        $registro->saldo = $registro->saldo + $ordenPago->valor;
        $registro->save();

        Session::flash('error','La orden de pago ha sido anulada');
        return redirect('/administrativo/ordenPagos/show/'.$id);
    }

    public function deleteRFFinished($id){
        $retenF = OrdenPagosDescuentos::findOrFail($id);
        $retenF->delete();

        //SE ACTUALIZA EL VALOR CREDITO DE LA ORDEN DE PAGO CON LOS NUEVOS DESCUENTOS
        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $retenF->pago->id)->where('valor', '>', 0)->get();
        $ordenP = OrdenPagos::find($retenF->pago->id);
        foreach ($ordenP->pucs as $puc){
            if ($puc->valor_debito == 0){
                $puc->valor_credito = $ordenP->valor - $OrdenPagoDescuentos->sum('valor');
                $puc->save();
            }
        }

        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $retenF->pago->id)->where('valor', '>', 0)->get();
        $retenF->pago->saldo = $retenF->pago->valor - $OrdenPagoDescuentos->sum('valor');
        $retenF->pago->save();
        Session::flash('error','Descuento de la Retención de Fuente eliminado de la Orden de Pago');
    }

    public function deleteMFinished($id){
        $municipal = OrdenPagosDescuentos::findOrFail($id);
        $municipal->delete();

        //SE ACTUALIZA EL VALOR CREDITO DE LA ORDEN DE PAGO CON LOS NUEVOS DESCUENTOS
        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $municipal->pago->id)->where('valor', '>', 0)->get();
        $ordenP = OrdenPagos::find($municipal->pago->id);
        foreach ($ordenP->pucs as $puc){
            if ($puc->valor_debito == 0){
                $puc->valor_credito = $ordenP->valor - $OrdenPagoDescuentos->sum('valor');
                $puc->save();
            }
        }

        $OrdenPagoDescuentos = OrdenPagosDescuentos::where('orden_pagos_id', $municipal->pago->id)->where('valor', '>', 0)->get();
        $municipal->pago->saldo = $municipal->pago->valor - $OrdenPagoDescuentos->sum('valor');
        $municipal->pago->save();
        Session::flash('error','Descuento Municipal eliminado de la Orden de Pago');
    }
}
