<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\bancos;
use App\Model\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function destroy(bancos $bancos)
    {
        //
    }

    public function conciliacion(){

        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $result[] = $cuenta;
        }

        return view('administrativo.tesoreria.bancos.conciliacion',compact('result'));
    }

    public function movAccount(Request $request){

        $rubroPUC = PucAlcaldia::find($request->id);
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                    if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                        $total = $total - $pagoBank->valor;
                        $pago = Pagos::find($pagoBank->pagos_id);
                        $tercero = $pago->orden_pago->registros->persona->nombre;
                        $numIdent = $pago->orden_pago->registros->persona->num_dc;
                        $totDeb = $totDeb + 0;
                        $totCred = $totCred + $pagoBank->valor;
                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                            'modulo' => 'Pago #'.$pago->code, 'debito' => '$'.number_format(0,0),
                            'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero,
                            'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                            'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                            'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                    }
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresos::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->ff)->format('Y') == Carbon::today()->format('Y')) {
                    if (Carbon::parse($compCont->ff)->format('m') == $request->mes) {
                        $persona = Persona::find($compCont->persona_id);
                        $tercero = $persona->nombre;
                        $numIdent = $persona->num_dc;
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito_banco;
                            $total = $total - $compCont->credito_banco;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' => '$'.number_format($compCont->debito_banco,0),
                                'credito' => '$'.number_format($compCont->credito_banco,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->id]);
                        } else{
                            $total = $total + $compCont->debito_puc;
                            $total = $total - $compCont->credito_puc;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' => '$'.number_format($compCont->debito_puc,0),
                                'credito' => '$'.number_format($compCont->credito_puc,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->id]);
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

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if (Carbon::parse($pagoBank->created_at)->format('Y') == $añoActual) {
                    if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                        $total = $total - $pagoBank->valor;
                        $pago = Pagos::find($pagoBank->pagos_id);
                        $tercero = $pago->orden_pago->registros->persona->nombre;
                        $numIdent = $pago->orden_pago->registros->persona->num_dc;
                        $totDeb = $totDeb + 0;
                        $totCredAll = $totCredAll + $pagoBank->valor;
                        if ($pago->estado == 1) {
                            $totCred = $totCred + $pagoBank->valor;
                            $totBank = $totBank - $pagoBank->valor;
                        }
                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                            'modulo' => 'Pago #'.$pago->code, 'debito' => 0, 'credito' => $pagoBank->valor, 'tercero' => $tercero,
                            'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                            'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                            'referencia' => 'Pago #'.$pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                    }
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresos::where('cuenta_banco', $rubroPUC->id)->orwhere('cuenta_puc_id', $rubroPUC->id)->get();
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if (Carbon::parse($compCont->ff)->format('Y') == $añoActual) {
                    if (Carbon::parse($compCont->ff)->format('m') == $request->mes) {
                        $persona = Persona::find($compCont->persona_id);
                        $tercero = $persona->nombre;
                        $numIdent = $persona->num_dc;
                        if ($compCont->cuenta_banco == $rubroPUC->id){
                            $total = $total + $compCont->debito_banco;
                            $total = $total - $compCont->credito_banco;
                            $totDeb = $totDeb + $compCont->debito_banco;
                            $totCred = $totCred + $compCont->credito_banco;
                            $totCredAll = $totCred + $compCont->credito_banco;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' => $compCont->debito_banco,
                                'credito' => $compCont->credito_banco, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->id, 'pago_estado' => '1']);
                        } else{
                            $total = $total + $compCont->debito_puc;
                            $total = $total - $compCont->credito_puc;
                            $totDeb = $totDeb + $compCont->debito_puc;
                            $totCred = $totCred + $compCont->credito_puc;
                            $totCredAll = $totCred + $compCont->credito_puc;
                            $result[] = collect(['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' => $compCont->debito_puc,
                                'credito' => $compCont->credito_puc, 'tercero' => $tercero, 'CC' => $numIdent,
                                'concepto' => $compCont->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => $total, 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                'referencia' => 'CC #'.$compCont->id, 'pago_estado' => '1']);
                        }
                    }
                }
            }
        }

        return view('administrativo.tesoreria.bancos.conciliacionmake',compact('result', 'rubroPUC'
            ,'añoActual','mesFind','totDeb','totCred', 'totCredAll','totBank'));
    }

    public function saveConciliacion(Request $request){
        dd($request);
    }
}
