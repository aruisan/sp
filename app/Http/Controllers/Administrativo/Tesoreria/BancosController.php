<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\bancos;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas;
use App\Model\Persona;
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
        $fechaFin = Carbon::parse($request->fechaFinal);

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

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
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

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    $persona = Persona::find($compCont->comprobante->persona_id);
                    $tercero = $persona->nombre;
                    $numIdent = $persona->num_dc;
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

        return $result;
    }

    public function conciliacion(){

        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $result[] = $cuenta;
        }
        $conciliaciones = ConciliacionBancaria::where('año', Carbon::today()->format('Y') )->get();

        return view('administrativo.tesoreria.bancos.conciliacion',compact('result','conciliaciones'));
    }

    public function movAccount(Request $request){

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

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->fechaComp)->format('Y') == Carbon::today()->format('Y')) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $request->mes) {
                        $persona = Persona::find($compCont->comprobante->persona_id);
                        $tercero = $persona->nombre;
                        $numIdent = $persona->num_dc;
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

        $rubroPUC = PucAlcaldia::find($request->cuentaPUC);
        $añoActual = Carbon::today()->format('Y');
        $mesFind = $request->mes;
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $totCredAll = 0;
        $totBank = 0;

        if($request->mes >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$request->mes."-01", $rubroPUC);
            $totalLastMonth = $newSaldo['total'];
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
                        if ($pago->type_pay == "CHEQUE") $referencia = "Pago #".$pago->code." - # Cheque ".$pago->num;
                        else $referencia = "Pago #".$pago->code;
                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                            'modulo' => $referencia, 'debito' => 0, 'credito' => $pagoBank->valor, 'tercero' => $tercero,
                            'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                            'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                            'referencia' => $referencia, 'pago_estado' => $pago->estado]);
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
                        $persona = Persona::find($compCont->comprobante->persona_id);
                        $tercero = $persona->nombre;
                        $numIdent = $persona->num_dc;
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
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
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
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

        return view('administrativo.tesoreria.bancos.conciliacionmake',compact('result', 'rubroPUC'
            ,'añoActual','mesFind','totDeb','totCred', 'totCredAll','totBank','totalLastMonth'));
    }

    public function saveConciliacion(Request $request){

        $conciliacion = new ConciliacionBancaria();
        $conciliacion->año = $request->año;
        $conciliacion->mes = $request->mes;
        $conciliacion->puc_id = $request->cuenta;
        $conciliacion->subTotBancoInicial = $request->subTotBancoInicial;
        $conciliacion->subTotBancoFinal = $request->subTotBancoFinal;
        $conciliacion->sumaIgualBank = $request->sumaIgualBank;
        $conciliacion->responsable_id = auth()->user()->id;
        $conciliacion->save();

        if (isset($request->fecha)){
            for ($i = 0; $i < count($request->fecha); $i++) {
                $find = array_search($i, $request->check);
                if ($find === false){
                    $conciliacionCuentas = new ConciliacionBancariaCuentas();
                    $conciliacionCuentas->conciliacion_id = $conciliacion->id;
                    $conciliacionCuentas->fecha = $request->fecha[$i];
                    $conciliacionCuentas->referencia = $request->referencia[$i];
                    $conciliacionCuentas->debito = $request->debito[$i];
                    $conciliacionCuentas->credito = $request->credito[$i];
                    $conciliacionCuentas->valor = $request->banco[$i];
                    $conciliacionCuentas->aprovado = "OFF";
                    $conciliacionCuentas->save();
                }else{
                    $conciliacionCuentas = new ConciliacionBancariaCuentas();
                    $conciliacionCuentas->conciliacion_id = $conciliacion->id;
                    $conciliacionCuentas->fecha = $request->fecha[$i];
                    $conciliacionCuentas->referencia = $request->referencia[$i];
                    $conciliacionCuentas->debito = $request->debito[$i];
                    $conciliacionCuentas->credito = $request->credito[$i];
                    $conciliacionCuentas->valor = $request->banco[$i];
                    $conciliacionCuentas->aprovado = "ON";
                    $conciliacionCuentas->save();
                }
            }
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
        return redirect('administrativo/tesoreria/bancos/conciliacion');
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

        if($mesFind >= 2) {
            $newSaldo = $this->validateBeforeMonths(Carbon::today()->format('Y').'-'.$mesFind."-01", $rubroPUC);
            $totalLastMonth = $newSaldo['total'];
            $total = $newSaldo['total'];
        }

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if (Carbon::parse($pagoBank->created_at)->format('Y') == $añoActual) {
                    if (Carbon::parse($pagoBank->created_at)->format('m') == $mesFind){
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
                        if ($pago->type_pay == "CHEQUE") $referencia = "Pago #".$pago->code." - # Cheque ".$pago->num;
                        else $referencia = "Pago #".$pago->code;
                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                            'modulo' => $referencia, 'debito' => 0, 'credito' => $pagoBank->valor, 'tercero' => $tercero,
                            'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                            'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                            'referencia' => $referencia, 'pago_estado' => $pago->estado]);
                    }
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->fechaComp)->format('Y') == $añoActual) {
                    if (Carbon::parse($compCont->fechaComp)->format('m') == $mesFind) {
                        $persona = Persona::find($compCont->comprobante->persona_id);
                        $tercero = $persona->nombre;
                        $numIdent = $persona->num_dc;
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito;
                            $total = $total - $compCont->credito;
                            $totDeb = $totDeb + $compCont->debito;
                            $totCred = $totCred + $compCont->credito;
                            $totCredAll = $totCred + $compCont->credito;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
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
                            $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
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

        $fecha = Carbon::createFromTimeString($conciliacion->created_at);
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $pdf = PDF::loadView('administrativo.tesoreria.bancos.pdf', compact('conciliacion',  'dias', 'meses', 'fecha',
        'cuentas','rubroPUC','totDeb','totCred','totCredAll','result','totBank','totalLastMonth'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function validateBeforeMonths($lastDate, $rubroPUC){
        $lastDate = Carbon::parse($lastDate." 23:59:59");
        $fechaFin = $lastDate->subDays(1);
        $today = Carbon::today();
        $fechaIni = Carbon::parse($today->year."-01-01");
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $mes = $fechaFin->month.'-'.$today->year;

        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                $total = $total - $pagoBank->valor;
                $totDeb = $totDeb + 0;
                $totCred = $totCred + $pagoBank->valor;
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
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
}
