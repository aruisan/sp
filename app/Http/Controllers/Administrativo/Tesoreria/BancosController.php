<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\ComprobanteIngresoTemporalConciliacion;
use App\ComprobanteIngresoTemporal;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\bancos;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas;
use App\Model\Persona;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use PDF;

class BancosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = bancos::all();
        return view('administrativo.bancos.index',compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

        foreach ($R1 as $r1) {
            $codigoEnd = $r1->code;
            $codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
            foreach ($r1->codes as $data1){
                $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                $codigo = $reg0->code;
                $codigoEnd = "$r1->code$codigo";
                $codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                if ($reg0->codes){
                    foreach ($reg0->codes as $data3){
                        $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                        $codigo = $reg->code;
                        $codigoF = "$codigoEnd$codigo";
                        $codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                        foreach ($reg->codes as $data4){
                            $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                            $codigo = $reg1->code;
                            $code = "$codigoF$codigo";
                            $codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
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

        return view('administrativo.bancos.create', compact('codigos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bank = new bancos();
        $bank->numero_cuenta = $request->num;
        $bank->descripcion = $request->descripcion;
        $bank->valor_inicial = $request->value;
        $bank->valor_actual = $request->value;
        $bank->estado = "0";
        $bank->rubros_puc_id = $request->PUC;
        $bank->save();

        Session::flash('success','El banco se ha almacenado exitosamente');
        return redirect('administrativo/bancos');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = bancos::findOrFail($id);

        $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

        foreach ($R1 as $r1) {
            $codigoEnd = $r1->code;
            $codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
            foreach ($r1->codes as $data1){
                $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                $codigo = $reg0->code;
                $codigoEnd = "$r1->code$codigo";
                $codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                if ($reg0->codes){
                    foreach ($reg0->codes as $data3){
                        $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                        $codigo = $reg->code;
                        $codigoF = "$codigoEnd$codigo";
                        $codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                        foreach ($reg->codes as $data4){
                            $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                            $codigo = $reg1->code;
                            $code = "$codigoF$codigo";
                            $codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
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

        return view('administrativo.bancos.show',compact('item','codigos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = bancos::findOrFail($id);
        $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

        foreach ($R1 as $r1) {
            $codigoEnd = $r1->code;
            $codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
            foreach ($r1->codes as $data1){
                $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                $codigo = $reg0->code;
                $codigoEnd = "$r1->code$codigo";
                $codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                if ($reg0->codes){
                    foreach ($reg0->codes as $data3){
                        $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                        $codigo = $reg->code;
                        $codigoF = "$codigoEnd$codigo";
                        $codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                        foreach ($reg->codes as $data4){
                            $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                            $codigo = $reg1->code;
                            $code = "$codigoF$codigo";
                            $codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
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

        return view('administrativo.bancos.edit',compact('item','codigos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bank = bancos::findOrFail($id);
        $bank->numero_cuenta = $request->num;
        $bank->descripcion = $request->descripcion;
        $bank->rubros_puc_id = $request->PUC;
        $bank->save();

        Session::flash('success','El banco se ha actualizado exitosamente');
        return redirect('administrativo/bancos/'.$bank->id);
    }


    public function libros(){
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $result[] = $cuenta;
        }

        return view('administrativo.tesoreria.bancos.libros',compact('result'));
    }

    public function movAccountLibros(Request $request){

        $rubroPUC = PucAlcaldia::find($request->id);
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $fechaIni = Carbon::parse($request->fechaInicial);
        $fechaFin = Carbon::parse($request->fechaFinal.'23:59:59');

        if($fechaIni->month >= 2) {
            $newSaldo = $this->validateBeforeMonths($request->fechaInicial, $rubroPUC);
            $total = $newSaldo['total'];
            $result[] = collect(['fecha' => $newSaldo['fecha'],
                'modulo' => '', 'debito' => '',
                'credito' => '', 'tercero' => '',
                'CC' => '', 'concepto' => 'SALDO HASTA EL MES '.$newSaldo['fecha'], 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => '', 'pago_estado' => '']);
        }

        if (strlen($rubroPUC->code) == 4) return $this->findlvl3($rubroPUC, $fechaIni, $fechaFin, $total, $totDeb, $totCred);
        elseif (strlen($rubroPUC->code) == 6) return $this->findlvl4($rubroPUC, $fechaIni, $fechaFin, $total, $totDeb, $totCred);
        elseif (strlen($rubroPUC->code) == 10){
            if ($rubroPUC->id == 765){
                //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                foreach ($pagosFin as $pagoF){
                    foreach ($pagoF->orden_pago->descuentos as $descuento){
                        if ($descuento->valor > 0){
                            if ($descuento->desc_municipal_id != null){
                                //DESCUENTOS MUNICIPALES
                                if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                    $total = $total + $descuento->valor;
                                    $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                    $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                    $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                        'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                        'total' => '$'.number_format($total,0), 'from' => 5,
                                        'totDeb' => $totDeb, 'totCred' => $totCred]);
                                    //return $descuento->descuento_mun;
                                }
                            }
                        }
                    }
                }
            } else {
                //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                if (count($ordenPagosPUC) > 0) {
                    foreach ($ordenPagosPUC as $op_puc) {
                        if ($op_puc->ordenPago->estado == '1') {
                            if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                $total = $total + $op_puc->valor_debito;
                                $total = $total - $op_puc->valor_credito;
                                $totDeb = $totDeb + $op_puc->valor_debito;
                                $totCred = $totCred + $op_puc->valor_credito;
                                if (isset($op_puc->ordenPago->registros->persona)) {
                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                } else {
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                    'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                    'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6]);
                            }
                        }
                    }
                }

                // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                if (count($pagoBanks) > 0) {
                    foreach ($pagoBanks as $pagoBank) {
                        if ($pagoBank->pago->estado == 1) {
                            $total = $total - $pagoBank->valor;
                            $pago = Pagos::find($pagoBank->pagos_id);
                            if (isset($pago->orden_pago->registros->persona)) {
                                $tercero = $pago->orden_pago->registros->persona->nombre;
                                $numIdent = $pago->orden_pago->registros->persona->num_dc;
                            } else {
                                $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                $numIdent = 800197268;
                            }

                            $totDeb = $totDeb + 0;
                            $totCred = $totCred + $pagoBank->valor;
                            if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                            else $referencia = "Pago #" . $pago->code;
                            $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                'modulo' => $referencia, 'debito' => '$' . number_format(0, 0),
                                'credito' => '$' . number_format($pagoBank->valor, 0), 'tercero' => $tercero,
                                'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                        }
                    }
                }

                //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                if (count($compsCont) > 0) {
                    foreach ($compsCont as $compCont) {
                        if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                            if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                $user = User::find($compCont->comprobante->persona_id);
                                $tercero = $user->name;
                                $numIdent = $user->email;
                            } else {
                                $persona = Persona::find($compCont->comprobante->persona_id);
                                $tercero = $persona->nombre;
                                $numIdent = $persona->num_dc;
                            }
                            if ($compCont->cuenta_banco == $rubroPUC->id) {
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                    'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                            } else {
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                    'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                            }
                        }
                    }
                }
            }

            return $result;
        }
    }

    public function findlvl4($cuenta, $fechaIni, $fechaFin, $total, $totDeb, $totCred){
        $rubrosPUC = PucAlcaldia::where('padre_id',$cuenta->id)->get();

        if ($rubrosPUC->count() >= 1) {
            foreach ($rubrosPUC as $rubroPUC) {
                if ($rubroPUC->id == 765){
                    //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                    $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                    foreach ($pagosFin as $pagoF){
                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                            if ($descuento->valor > 0){
                                if ($descuento->desc_municipal_id != null){
                                    //DESCUENTOS MUNICIPALES
                                    if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                        $total = $total + $descuento->valor;
                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 5,
                                            'totDeb' => $totDeb, 'totCred' => $totCred]);
                                        //return $descuento->descuento_mun;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                    $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                    if (count($ordenPagosPUC) > 0) {
                        foreach ($ordenPagosPUC as $op_puc) {
                            if ($op_puc->ordenPago->estado == '1') {
                                if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                    $total = $total + $op_puc->valor_debito;
                                    $total = $total - $op_puc->valor_credito;
                                    $totDeb = $totDeb + $op_puc->valor_debito;
                                    $totCred = $totCred + $op_puc->valor_credito;
                                    if (isset($op_puc->ordenPago->registros->persona)) {
                                        $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                        $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                    } else {
                                        $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                        $numIdent = 800197268;
                                    }
                                    $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                        'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                        'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                        'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6,
                                        'totDeb' => $totDeb, 'totCred' => $totCred]);
                                }
                            }
                        }
                    }

                    // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                    $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                    if (count($pagoBanks) > 0) {
                        foreach ($pagoBanks as $pagoBank) {
                            if ($pagoBank->pago->estado == 1) {
                                $total = $total - $pagoBank->valor;
                                $pago = Pagos::find($pagoBank->pagos_id);
                                if (isset($pago->orden_pago->registros->persona)) {
                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                } else {
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }

                                $totDeb = $totDeb + 0;
                                $totCred = $totCred + $pagoBank->valor;
                                if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                                else $referencia = "Pago #" . $pago->code;
                                $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                    'modulo' => $referencia, 'debito' => '$' . number_format(0, 0),
                                    'credito' => '$' . number_format($pagoBank->valor, 0), 'tercero' => $tercero,
                                    'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                    'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                            }
                        }
                    }

                    //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                    $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                    if (count($compsCont) > 0) {
                        foreach ($compsCont as $compCont) {
                            if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                                if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                    $user = User::find($compCont->comprobante->persona_id);
                                    $tercero = $user->name;
                                    $numIdent = $user->email;
                                } else {
                                    $persona = Persona::find($compCont->comprobante->persona_id);
                                    $tercero = $persona->nombre;
                                    $numIdent = $persona->num_dc;
                                }
                                if ($compCont->cuenta_banco == $rubroPUC->id) {
                                    $total = $total + $compCont->debito;
                                    $total = $total - $compCont->credito;
                                    $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                        'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                        'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                        'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                        'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                } else {
                                    $total = $total + $compCont->debito;
                                    $total = $total - $compCont->credito;
                                    $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                        'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                        'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                        'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                        'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function findlvl3($cuenta, $fechaIni, $fechaFin, $total, $totDeb, $totCred){
        $lv4 = PucAlcaldia::where('padre_id', $cuenta->id)->get();
        foreach ($lv4 as $item) {
            $rubrosPUC = PucAlcaldia::where('padre_id', $item->id)->get();
            if ($rubrosPUC->count() >= 1) {
                foreach ($rubrosPUC as $rubroPUC) {
                    if ($rubroPUC->id == 765){
                        //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                        $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                        foreach ($pagosFin as $pagoF){
                            foreach ($pagoF->orden_pago->descuentos as $descuento){
                                if ($descuento->valor > 0){
                                    if ($descuento->desc_municipal_id != null){
                                        //DESCUENTOS MUNICIPALES
                                        if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                            $total = $total + $descuento->valor;
                                            $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                            $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                            $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                'total' => '$'.number_format($total,0), 'from' => 5,
                                                'totDeb' => $totDeb, 'totCred' => $totCred]);
                                            //return $descuento->descuento_mun;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                        $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                        if (count($ordenPagosPUC) > 0) {
                            foreach ($ordenPagosPUC as $op_puc) {
                                if ($op_puc->ordenPago->estado == '1') {
                                    if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        $totDeb = $totDeb + $op_puc->valor_debito;
                                        $totCred = $totCred + $op_puc->valor_credito;
                                        if (isset($op_puc->ordenPago->registros->persona)) {
                                            $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                            $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                        } else {
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                            'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                            'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6]);
                                    }
                                }
                            }
                        }

                        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                        if (count($pagoBanks) > 0) {
                            foreach ($pagoBanks as $pagoBank) {
                                if ($pagoBank->pago->estado == 1) {
                                    $total = $total - $pagoBank->valor;
                                    $pago = Pagos::find($pagoBank->pagos_id);
                                    if (isset($pago->orden_pago->registros->persona)) {
                                        $tercero = $pago->orden_pago->registros->persona->nombre;
                                        $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                    } else {
                                        $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                        $numIdent = 800197268;
                                    }

                                    $totDeb = $totDeb + 0;
                                    $totCred = $totCred + $pagoBank->valor;
                                    if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                                    else $referencia = "Pago #" . $pago->code;
                                    $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                        'modulo' => $referencia, 'debito' => '$' . number_format(0, 0),
                                        'credito' => '$' . number_format($pagoBank->valor, 0), 'tercero' => $tercero,
                                        'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                        'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                                }
                            }
                        }

                        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                        if (count($compsCont) > 0) {
                            foreach ($compsCont as $compCont) {
                                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                                    if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                        $user = User::find($compCont->comprobante->persona_id);
                                        $tercero = $user->name;
                                        $numIdent = $user->email;
                                    } else {
                                        $persona = Persona::find($compCont->comprobante->persona_id);
                                        $tercero = $persona->nombre;
                                        $numIdent = $persona->num_dc;
                                    }
                                    if ($compCont->cuenta_banco == $rubroPUC->id) {
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                            'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                            'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                    } else {
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                            'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                            'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function conciliacion(){
        $conciliacion_id = NULL;
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $result[] = $cuenta;
        }
        $conciliaciones = ConciliacionBancaria::where('año', Carbon::today()->format('Y') )->where('finalizar', 1)->get();

        return view('administrativo.tesoreria.bancos.conciliacion',compact('result','conciliaciones', 'conciliacion_id'));
    }

    public function conciliacion_pdf($conciliacion_id){
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $result[] = $cuenta;
        }
        $conciliaciones = ConciliacionBancaria::where('año', Carbon::today()->format('Y') )->get();

        return view('administrativo.tesoreria.bancos.conciliacion',compact('result','conciliaciones', 'conciliacion_id'));
    }

    public function movAccount(Request $request){

        $result = collect();
        $rubroPUC = PucAlcaldia::find($request->id);
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;

        if($request->mes >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$request->mes."-01", $rubroPUC);
            $total = $newSaldo['total'];
            $result[] = collect(['fecha' => $newSaldo['fecha'],
                'modulo' => '', 'debito' => '',
                'credito' => '', 'tercero' => '',
                'CC' => '', 'concepto' => 'SALDO HASTA EL MES '.$newSaldo['fecha'], 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => '', 'pago_estado' => '']);
        }

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                        if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                            $total = $total - $pagoBank->valor;
                            $pago = Pagos::find($pagoBank->pagos_id);
                            if (isset($pago->orden_pago->registros->persona)){
                                $tercero = $pago->orden_pago->registros->persona->nombre;
                                $numIdent = $pago->orden_pago->registros->persona->num_dc;
                            } else{
                                $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                $numIdent = 800197268;
                            }
                            $totDeb = $totDeb + 0;
                            $totCred = $totCred + $pagoBank->valor;
                            if ($pago->type_pay == "CHEQUE") $referencia = "Pago #".$pago->code." - # Cheque ".$pago->num;
                            else $referencia = "Pago #".$pago->code;
                            $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                'modulo' => $referencia, 'debito' => '$'.number_format(0,0),
                                'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero,
                                'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                                'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                        }
                    }
                }
            }
        }

        

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->fechaComp)->format('Y') == Carbon::today()->format('Y')) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $request->mes) {
                        if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                            $user = User::find($compCont->comprobante->persona_id);
                            $tercero = $user->name;
                            $numIdent = $user->email;
                        } else{
                            $persona = Persona::find($compCont->comprobante->persona_id);
                            $tercero = $persona->nombre;
                            $numIdent = $persona->num_dc;
                        }
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                        } else{
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function makeConciliacion(Request $request){
        $result = collect();
        $rubroPUC = PucAlcaldia::find($request->cuentaPUC);
        $añoActual = Carbon::today()->format('Y');
        $mesFind = $request->mes;
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $totCredAll = 0;
        $totBank = 0;

        $conciliacion = ConciliacionBancaria::where('año', $añoActual)->where('mes', $request->mes)->where('puc_id', $request->cuentaPUC)->first();
        if(is_null($conciliacion)){
            $conciliacion = new ConciliacionBancaria();
            $conciliacion->año = $añoActual;
            $conciliacion->mes = $request->mes;
            $conciliacion->puc_id = $request->cuentaPUC;
        }
        $conciliacion->subTotBancoInicial = 0;
        $conciliacion->subTotBancoFinal = 0;
        $conciliacion->partida_sin_conciliacion_libros = 0;
        $conciliacion->partida_sin_conciliacion_bancos = 0;
        $conciliacion->finalizar = 0;
        $conciliacion->sumaIgualBank = 0;
        $conciliacion->responsable_id = auth()->user()->id;
        $conciliacion->save();

        if(!is_null($conciliacion)){
            if($conciliacion->cuentas_temporales->count() > 0){
                $conciliacion->cuentas_temporales()->delete();
            }
        }

        $conciliaciones_anteriores = ConciliacionBancaria::where('puc_id', $rubroPUC->id)->get();
        $conciliacion_anterior = NULL;
        $total_cheque_mano = 0;
        $total_cheque_cobrados = 0;

        //dd([$conciliacion->conciliacion_anterior->saldo_libros, $conciliacion_anterior]);

        if($conciliaciones_anteriores->count() > 0):
            
            foreach($conciliaciones_anteriores as $anterior):
                $conciliacion_anterior = $anterior->mes == $mesFind-1 ? $anterior : $conciliacion_anterior; 
                $total_cheque_mano += $anterior->mes < $mesFind && $anterior->cheques_mano->count() > 0 
                                    ? $anterior->cheques_mano->filter(function($c){ return $c->aprobado == "ON";})->sum('total') : 0;
                $total_cheque_cobrados += $anterior->mes < $mesFind && $anterior->cuentas_temporales->count() > 0 
                                        ? $anterior->cuentas_temporales->filter(function($e){ return $e->check;})->sum('comprobante_ingreso_temporal.valor') : 0 ;
            endforeach;
        endif;

        //dd([$conciliaciones_anteriores[0]->cheques_mano, $total_cheque_mano, $total_cheque_cobrados]);

       // dd([$conciliaciones_anteriores[0]->mes, $request->mes]);
        /*
        $conciliacion_anterior = NULL;
        $total_cheque_mano = 0;
        $total_cheque_cobrados = 0;

        if($rubroPUC->conciliaciones->count() > 0):
            $conciliacion_anterior = $rubroPUC->conciliaciones->filter(function($c) use($mesFind){return $c->mes == $mesFind-1; })->last();
            if(!is_null($conciliacion_anterior)):
                //dd($conciliacion_anterior);
                $total_cheque_mano = $conciliacion_anterior->cheques_mano->count() > 0 ? $conciliacion_anterior->cheques_mano->filter(function($c){ return $c->aprobado == "ON";})->sum('total') : 0;
                $total_cheque_cobrados = $conciliacion_anterior->cuentas_temporales->count() > 0 ? $conciliacion_anterior->cuentas_temporales->filter(function($e){ return $e->check;})->sum('comprobante_ingreso_temporal.valor') : 0 ;
            endif;
        endif;
    */

        if($request->mes >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$request->mes."-01", $rubroPUC);//2023-2-1
            $totalLastMonth = $newSaldo['total'];
            $total = $newSaldo['total'];
        } else $totalLastMonth = $total;

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        
        if (count($pagoBanks) > 0){
            //dd($pagoBanks->map(function($e){return $e->pago;}));
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    if (Carbon::parse($pagoBank->created_at)->format('Y') == $añoActual) {
                        if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                            $total = $total - $pagoBank->valor;
                            $pago = Pagos::find($pagoBank->pagos_id);
                            if (isset($pago->orden_pago->registros->persona)){
                                $tercero = $pago->orden_pago->registros->persona->nombre;
                                $numIdent = $pago->orden_pago->registros->persona->num_dc;
                            } else{
                                $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                $numIdent = 800197268;
                            }
                            $totDeb = $totDeb + 0;
                            $totCredAll = $totCredAll + $pagoBank->valor;
                            if ($pago->estado == 1) {
                                $totCred = $totCred + $pagoBank->valor;
                                $totBank = $totBank - $pagoBank->valor;
                            }
                            $tipo_pago = $pago->type_pay == "CHEQUE" ? 'cheque' : 'referencia';
                            $result[] = collect(["numero" => "#{$tipo_pago} {$pago->num}", 'fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                'modulo' => "Pago #{$pago->code}", 'debito' => 0, 'credito' => $pagoBank->valor, 'tercero' => $tercero,
                                'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => "Pago #{$pago->code}", 'pago_estado' => $pago->estado]);
                        }
                    }
                }
            }
        }
       
        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->fechaComp)->format('Y') == $añoActual) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $request->mes) {
                        if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                            $user = User::find($compCont->comprobante->persona_id);
                            $tercero = $user->name;
                            $numIdent = $user->email;
                        } else{
                            $persona = Persona::find($compCont->comprobante->persona_id);
                            $tercero = $persona->nombre;
                            $numIdent = $persona->num_dc;
                        }
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                            $result[] = collect(["numero" => "", 'id' => $compCont->id,'fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => $compCont->debito,
                                'credito' => $compCont->credito, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->comprobante->id, 'pago_estado' => '1']);
                        } else{
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                            $result[] = collect(["numero" => "",'fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => $compCont->debito,
                                'credito' => $compCont->credito, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->comprobante->id, 'pago_estado' => '1']);
                        }
                    }
                }
            }
        }

        $periodo = \Carbon\Carbon::parse("01-{$mesFind}-{$añoActual}");
        $periodo_inicial = $periodo->format('d-m-Y');
        $periodo_final = $periodo->addMonth(1)->subDay(1)->format('d-m-Y');

        //dd($result);
        //dd($rubroPUC);
        $comprobantes_old = ComprobanteIngresoTemporal::where('code', $rubroPUC->code)->get()->filter(function($e){ return !$e->check;})->values();
        //dd($comprobantes_old);
        //$comprobantes_old = ComprobanteIngresoTemporal::where('code', $rubroPUC->code)->get();
        return view('administrativo.tesoreria.bancos.conciliacionmake',compact('conciliacion', 'result', 'rubroPUC', 'conciliacion_anterior', 'total_cheque_mano', 'total_cheque_cobrados'
            ,'añoActual','mesFind','totDeb','totCred', 'totCredAll','totBank','totalLastMonth', 'comprobantes_old','periodo_inicial','periodo_final'));
    }

    public function saveAndSeePdf(Request $request){
        //return $request->cobros_select;
        $conciliacion = ConciliacionBancaria::where('año', $request->año)->where('mes', $request->mes)->where('puc_id', $request->cuenta)->first();
        if(is_null($conciliacion)){
            $conciliacion = new ConciliacionBancaria();
            $conciliacion->año = $request->año;
            $conciliacion->mes = $request->mes;
            $conciliacion->puc_id = $request->cuenta;
        }
        $conciliacion->subTotBancoInicial = $request->inicio;
        $conciliacion->subTotBancoFinal = $request->final;
        $conciliacion->partida_sin_conciliacion_libros = $request->libros;
        $conciliacion->partida_sin_conciliacion_bancos = $request->bancos;
        $conciliacion->finalizar = 0;
        $conciliacion->sumaIgualBank = 0;
        $conciliacion->responsable_id = auth()->user()->id;
        $conciliacion->save();

        foreach($request->cobros_select as $cobro_s):
            $conciliacion_cobro_s = ComprobanteIngresoTemporalConciliacion::where('conciliacion_id', $conciliacion->id)->where('comprobante_ingreso_temporal_id', $cobro_s['id'])->first();
            if(is_null($conciliacion_cobro_s)):
                $conciliacion_cobro_s = new ComprobanteIngresoTemporalConciliacion;
                $conciliacion_cobro_s->conciliacion_id = $conciliacion->id;
                $conciliacion_cobro_s->comprobante_ingreso_temporal_id = $cobro_s['id'];
            endif;
            $conciliacion_cobro_s->check = '1';
            $conciliacion_cobro_s->save();
        endforeach;
        
        
        foreach($request->cobros_no_select as $cobro_s):
            $conciliacion_cobro_s = ComprobanteIngresoTemporalConciliacion::where('conciliacion_id', $conciliacion->id)->where('comprobante_ingreso_temporal_id', $cobro_s['id'])->first();
            if(is_null($conciliacion_cobro_s)):
                $conciliacion_cobro_s = new ComprobanteIngresoTemporalConciliacion;
                $conciliacion_cobro_s->conciliacion_id = $conciliacion->id;
                $conciliacion_cobro_s->comprobante_ingreso_temporal_id = $cobro_s['id'];
            endif;
            $conciliacion_cobro_s->check = '0';
            $conciliacion_cobro_s->save();
        endforeach;
        
        
        ConciliacionBancariaCuentas::where('conciliacion_id', $conciliacion->id)->delete();
        
        foreach($request->mano_select as $mano_s):
            $fecha = Carbon::parse($mano_s['fecha'])->format('Y-m-d');
            $conciliacionCuentas = new ConciliacionBancariaCuentas();
            $conciliacionCuentas->conciliacion_id = $conciliacion->id;
            $conciliacionCuentas->fecha = $fecha;
            $conciliacionCuentas->referencia = $mano_s['referencia']." - ".$mano_s['CC']." - ".$mano_s['tercero'];
            $conciliacionCuentas->debito = $mano_s['debito'];
            $conciliacionCuentas->credito = $mano_s['credito'];
            $conciliacionCuentas->valor = $mano_s['debito'] == 0 ? $mano_s['credito'] : $mano_s['debito'];
            $conciliacionCuentas->aprobado = "ON";
            $conciliacionCuentas->save();
        endforeach;
        
        foreach($request->mano_no_select as $mano_s):
            $fecha = Carbon::parse($mano_s['fecha'])->format('Y-m-d');
            $conciliacionCuentas = new ConciliacionBancariaCuentas();
            $conciliacionCuentas->conciliacion_id = $conciliacion->id;
            $conciliacionCuentas->fecha = $fecha;
            $conciliacionCuentas->referencia = $mano_s['referencia']." - ".$mano_s['CC']." - ".$mano_s['tercero'];
            $conciliacionCuentas->debito = $mano_s['debito'];
            $conciliacionCuentas->credito = $mano_s['credito'];
            $conciliacionCuentas->valor = $mano_s['debito'] == 0 ? $mano_s['credito'] : $mano_s['debito'];
            $conciliacionCuentas->aprobado = "OFF";
            $conciliacionCuentas->save();
        endforeach;
        
        //return $request->cobros_no_select;
        return ['url' => route('conciliacion.pdf', $conciliacion->id)];
    }

    public function saveConciliacion(Request $request){
        
        //dd($request->all());
        $conciliacion = ConciliacionBancaria::where('año', $request->año)->where('mes', $request->mes)->where('puc_id', $request->cuenta)->first();
        if(is_null($conciliacion)){
            $conciliacion = new ConciliacionBancaria();
            $conciliacion->año = $request->año;
            $conciliacion->mes = $request->mes;
            $conciliacion->puc_id = $request->cuenta;
        }
        $conciliacion->subTotBancoInicial = $request->saldo_inicial;
        $conciliacion->subTotBancoFinal = $request->saldo_final;
        $conciliacion->partida_sin_conciliacion_libros = $request->partida_sin_conciliar_libros;
        $conciliacion->partida_sin_conciliacion_bancos = $request->partida_sin_conciliar_bancos;
        $conciliacion->finalizar = $request->finalizar;
        $conciliacion->sumaIgualBank = $request->sumaIgualBank;
        $conciliacion->responsable_id = auth()->user()->id;
        $conciliacion->save();

        $cobros_select = json_decode($request->data_cobro_select);
        $cobros_no_select = json_decode($request->data_cobro_no_select);

        //dd($cobros_select);
        foreach($cobros_select as $cobro_s):
            $conciliacion_cobro_s = ComprobanteIngresoTemporalConciliacion::where('conciliacion_id', $conciliacion->id)->where('comprobante_ingreso_temporal_id', $cobro_s->id)->first();
            if(is_null($conciliacion_cobro_s)):
                $conciliacion_cobro_s = new ComprobanteIngresoTemporalConciliacion;
                $conciliacion_cobro_s->conciliacion_id = $conciliacion->id;
                $conciliacion_cobro_s->comprobante_ingreso_temporal_id = $cobro_s->id;
            endif;
            $conciliacion_cobro_s->check = '1';
            $conciliacion_cobro_s->save();
        endforeach;
        
        
        foreach($cobros_no_select as $cobro_s):
            $conciliacion_cobro_s = ComprobanteIngresoTemporalConciliacion::where('conciliacion_id', $conciliacion->id)->where('comprobante_ingreso_temporal_id', $cobro_s->id)->first();
            if(is_null($conciliacion_cobro_s)):
                $conciliacion_cobro_s = new ComprobanteIngresoTemporalConciliacion;
                $conciliacion_cobro_s->conciliacion_id = $conciliacion->id;
                $conciliacion_cobro_s->comprobante_ingreso_temporal_id = $cobro_s->id;
            endif;
            $conciliacion_cobro_s->check = '0';
            $conciliacion_cobro_s->save();
        endforeach;
        
        $result = collect();
        $rubroPUC = PucAlcaldia::find($request->cuenta);
        $añoActual = $request->año;
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $totCredAll = 0;
        $totBank = 0;

        if($request->mes >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$request->mes."-01", $rubroPUC);
            $total = $newSaldo['total'];
        }

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if (Carbon::parse($pagoBank->created_at)->format('Y') == $añoActual) {
                    if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                        $total = $total - $pagoBank->valor;
                        $pago = Pagos::find($pagoBank->pagos_id);
                        //dd($pago);
                        if (isset($pago->orden_pago->registros->persona)){
                            $tercero = $pago->orden_pago->registros->persona->nombre;
                            $numIdent = $pago->orden_pago->registros->persona->num_dc;
                        } else{
                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                            $numIdent = 800197268;
                        }
                        $totDeb = $totDeb + 0;
                        $totCredAll = $totCredAll + $pagoBank->valor;
                        if ($pago->estado == 1) {
                            $totCred = $totCred + $pagoBank->valor;
                            $totBank = $totBank - $pagoBank->valor;
                        }
                        $tipo_pago = $pago->type_pay == "CHEQUE" ? 'cheque' : 'referencia';
                        $result[] = collect(["numero" => "#{$tipo_pago} {$pago->num}", 'fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                            'modulo' => "Pago #{$pago->code}", 'debito' => 0, 'credito' => $pagoBank->valor, 'tercero' => $tercero,
                            'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                            'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                            'referencia' => "Pago #{$pago->code}", 'pago_estado' => $pago->estado]);
                    }
                }
            }
        }

        

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                
                if (Carbon::parse($compCont->fechaComp)->format('Y') == $añoActual) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $request->mes) {
                        if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                            $user = User::find($compCont->comprobante->persona_id);
                            $tercero = $user->name;
                            $numIdent = $user->email;
                        } else{
                            $persona = Persona::find($compCont->comprobante->persona_id);
                            $tercero = $persona->nombre;
                            $numIdent = $persona->num_dc;
                        }
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $result[] = collect(["numero" => "", 'fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => $compCont->debito,
                                'credito' => $compCont->credito, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->comprobante->id, 'pago_estado' => '1']);
                        } else{
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $result[] = collect(["numero" => "", 'fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => $compCont->debito,
                                'credito' => $compCont->credito, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->comprobante->id, 'pago_estado' => '1']);
                        }
                    }
                }
            }
        }
        ConciliacionBancariaCuentas::where('conciliacion_id', $conciliacion->id)->delete();
        for ($i = 0; $i < count($result); $i++) {
            $fecha = Carbon::parse($result[$i]['fecha'])->format('Y-m-d');

            $conciliacionCuentas = new ConciliacionBancariaCuentas();
            $conciliacionCuentas->conciliacion_id = $conciliacion->id;
            $conciliacionCuentas->fecha = $fecha;
            $conciliacionCuentas->referencia = $result[$i]['referencia']." - ".$result[$i]['CC']." - ".$result[$i]['tercero'];
            $conciliacionCuentas->debito = $result[$i]['debito'];
            $conciliacionCuentas->credito = $result[$i]['credito'];
            $conciliacionCuentas->valor = $result[$i]['debito'] == 0 ? $result[$i]['credito'] : $result[$i]['debito'];

            $find = array_search($i, $request->check);
            if ($find === false):
                $conciliacionCuentas->aprobado = "OFF";

                $new = new ComprobanteIngresoTemporal;
                $new->code = $rubroPUC->code;
                $new->fecha = $fecha;
                $new->referencia = $result[$i]['referencia']." - ".$result[$i]['CC']." - ".$result[$i]['tercero']." - ".$result[$i]['numero'];
                $new->cc = $result[$i]['CC'];
                $new->tercero = $result[$i]['tercero'];
                $new->valor = $result[$i]['debito'] == 0 ? $result[$i]['credito'] : $result[$i]['debito'];
                $new->concepto = $result[$i]['referencia']." - ".$result[$i]['CC']." - ".$result[$i]['tercero'];
                $new->save();
            else:
                $conciliacionCuentas->aprobado = "ON";
            endif;

            $conciliacionCuentas->save();
        }

        if (isset($request->ref)){
            for ($i = 0; $i < count($request->ref); $i++) {
                $conciliacionCuentas = new ConciliacionBancariaCuentas();
                $conciliacionCuentas->conciliacion_id = $conciliacion->id;
                $conciliacionCuentas->referencia = $request->ref[$i];
                $conciliacionCuentas->valor = $request->banco[$i];
                $conciliacionCuentas->save();
            }
        }

        Session::flash('success','Se ha realizado la conciliación bancaria exitosamente.');
        return redirect()->route('conciliacion.guardar.pdf', $conciliacion->id);
    }

    public function pdf($id){

        $conciliacion = ConciliacionBancaria::findOrFail($id);
        $cuentas = ConciliacionBancariaCuentas::where('conciliacion_id', $id)->get();

        $rubroPUC = PucAlcaldia::find($conciliacion->puc_id);
        $añoActual = $conciliacion->año;
        $mesFind = $conciliacion->mes;
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $totCredAll = 0;
        $totBank = 0;
        $conciliaciones_anteriores = ConciliacionBancaria::where('año', $añoActual)->where('puc_id', $conciliacion->puc_id)->get();
        $conciliacion_anterior = NULL;
        $total_cheque_mano = 0;
        $total_cheque_cobrados = 0;

        
        if($conciliaciones_anteriores->count() > 0):
            foreach($conciliaciones_anteriores as $anterior):
                $conciliacion_anterior = $anterior->mes == $mesFind-1 ? $anterior : $conciliacion_anterior; 
                $total_cheque_mano += $anterior->mes < $mesFind && $anterior->cheques_mano->count() > 0 
                                    ? $anterior->cheques_mano->filter(function($c){ return $c->aprobado == "ON";})->sum('total') : 0;
                $total_cheque_cobrados += $anterior->mes < $mesFind && $anterior->cuentas_temporales->count() > 0 
                                        ? $anterior->cuentas_temporales->filter(function($e){ return $e->check;})->sum('comprobante_ingreso_temporal.valor') : 0 ;
            endforeach;
        endif;

        if($mesFind >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$mesFind."-01", $rubroPUC);
            $totalLastMonth = $newSaldo['total'];
            $total = $newSaldo['total'];
        } else $totalLastMonth = $total;

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereYear('created_at', $añoActual)->whereMonth('created_at', $mesFind)->get()->filter(function($e){
            return $e->pago->estado == 1;
        });

        if ($pagoBanks->count() > 0){
            $total = $pagoBanks->sum('valor');
            $totDeb = $totDeb + 0;
            $totCredAll = $pagoBanks->sum('valor');
            $totCred = $pagoBanks->sum('valor');
            $totBank =$pagoBanks->sum('valor');
        }

        
        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->fechaComp)->format('Y') == $añoActual) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $mesFind) {
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                        } else{
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                        }
                    }
                }
            }
        }
        //dd($pagoBanks);
        $fecha = Carbon::createFromTimeString($conciliacion->created_at);
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        
        $periodo = \Carbon\Carbon::parse("01-{$mesFind}-{$añoActual}");
        $periodo_inicial = $periodo->format('d-m-Y');
        $periodo_final = $periodo->addMonth(1)->subDay(1)->format('d-m-Y');

        //dd($conciliacion->cuentas_temporales);
        $pdf = PDF::loadView('administrativo.tesoreria.bancos.pdf', compact('conciliacion',  'dias', 'meses', 'fecha','conciliacion_anterior', 'total_cheque_mano', 'total_cheque_cobrados',
   
        'cuentas','rubroPUC','totDeb','totCred','totCredAll','totBank','totalLastMonth', 'periodo_inicial', 'periodo_final'))
            ->setPaper('a3', 'landscape')
            ->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function validateBeforeMonths($lastDate, $rubroPUC){
        $today = Carbon::today();//2023-04-11
        $lastDate = Carbon::parse($lastDate." 23:59:59");//2023-2-1 23:59:59
        $fechaIni = Carbon::parse($today->year."-01-01");//2023-01-01
        $fechaFin = $lastDate->subDays(1);//2023-31-1 23:59:59
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $mes = $fechaFin->month.'-'.$today->year;//1-2023

        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        //select * from pago_banks where rubros_puc_id == 28 and created_at >= 2023-01-01 and created_at <= 2023-31-1
        //trae todos los pagos de bancos hechos entre una fecha inicial que seria el primero de enero del año actual a la fecha final
        // que seria el dia que hagan el descargue de el informe
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    $total = $total - $pagoBank->valor;
                    $totDeb = $totDeb + 0;
                    $totCred = $totCred + $pagoBank->valor;
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
        //aca coge todos los movimientos de las cuenta de bancos poara generar los libros y al igual que pahgo de bancos tiene una fecha final y una fecha inicial
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    if ($compCont->cuenta_banco == $rubroPUC->id){
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    } else{
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    }
                }
            }
        }

        return collect(['total' => $total, 'fecha' => $mes]);
    }

    public function eliminar_conciliacion(ConciliacionBancaria $conciliacion){
        //dd($conciliacion);
        $conciliacion->cheques_mano()->delete(); //conciliacion a la mano
        $conciliacion->cuentas_temporales()->delete();//conciliacion viejos
        $conciliacion->delete();//conciliacion

        return back();
    }
}
