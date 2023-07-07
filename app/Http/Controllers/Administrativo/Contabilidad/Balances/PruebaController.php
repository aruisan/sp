<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class PruebaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = 0;
        $result = collect();
        $lv1 = PucAlcaldia::where('padre_id',0)->get();
        foreach ($lv1 as $item){
            $lv2 = PucAlcaldia::where('padre_id', $item->id )->get();
            foreach ($lv2 as $dato){
                $lv3 = PucAlcaldia::where('padre_id', $dato->id )->get();
                foreach ($lv3 as $cuentaslvl3){
                    $lv4 = PucAlcaldia::where('padre_id',$cuentaslvl3->id)->get();
                    if ($lv4->count() > 0){
                        $savedDad = false;
                        foreach ($lv4 as $hijo){
                            $saveHijo = false;
                            $rubrosPUC = PucAlcaldia::where('padre_id',$hijo->id)->get();
                            if ($rubrosPUC->count() >= 1){
                                $cuentaslvl3->name = $cuentaslvl3->concepto;
                                $cuentaslvl3->credito = 0;
                                $cuentaslvl3->debito = 0;
                                $cuentaslvl3->total = 0;
                                $cuentaslvl3->lvl = "nivel3";
                                $hijo->name = $hijo->concepto;
                                $hijo->credito = 0;
                                $hijo->debito = 0;
                                $hijo->total = 0;
                                $hijo->lvl = "nivel4";
                                foreach ($rubrosPUC as $rubroPUC) {
                                    $saveRubroPUC = false;
                                    if ($rubroPUC->orden_pagos->count() > 0){
                                        $count = 0;
                                        $totalRubros = 0;
                                        $totalRubrosCred = 0;
                                        $totalRubrosDeb = 0;
                                        foreach ($rubroPUC->orden_pagos as $op_puc) {
                                            if (Carbon::parse($op_puc->ordenPago->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                                $count++;
                                                $rubroPUC->name = $rubroPUC->concepto;
                                                $rubroPUC->credito = 0;
                                                $rubroPUC->debito = 0;
                                                $rubroPUC->total = 0;
                                                $rubroPUC->lvl = "nivel5";
                                                $total = $total + $op_puc->valor_debito;
                                                $total = $total - $op_puc->valor_credito;
                                                $totalRubros = $totalRubros + $op_puc->valor_debito;
                                                $totalRubros = $totalRubros - $op_puc->valor_credito;

                                                if ($totalRubros != 0) {
                                                    $totalRubrosCred = $totalRubrosCred + $op_puc->valor_credito;
                                                    $totalRubrosDeb = $totalRubrosDeb + $op_puc->valor_debito;
                                                    if ($savedDad == false) {
                                                        $result->push($cuentaslvl3);
                                                        $savedDad = true;
                                                    }
                                                    if ($saveHijo == false) {
                                                        $result->push($hijo);

                                                        $saveHijo = true;
                                                    }
                                                    if ($saveRubroPUC == false) {
                                                        $result->push($rubroPUC);
                                                        $codeRubroPUC = $rubroPUC->code;

                                                        $saveRubroPUC = true;
                                                    }
                                                    $key = $result->where('lvl', 'nivel6');
                                                    if ($key->count() == 0 or $result->last()['lvl'] == "nivel5") {
                                                        $result->push(['fecha' => Carbon::parse($op_puc->ordenPago->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago',
                                                            'debito' => $op_puc->valor_debito, 'credito' => $op_puc->valor_credito,
                                                            'name' => $op_puc->ordenPago->registros->persona->nombre, 'code' => $rubroPUC->code . '' . $count, 'total' => $total,
                                                            'lvl' => "nivel6", 'code_padre' => $rubroPUC->code]);

                                                    } else {
                                                        $key2 = $result->where('code_padre', $codeRubroPUC);
                                                        $keyRepetidos = $key2->where('name', $op_puc->ordenPago->registros->persona->nombre);
                                                        if ($keyRepetidos->count() > 0) {
                                                            $keyResult = $keyRepetidos->keys()[0];
                                                            $result[$keyResult] = collect($result[$keyResult]);
                                                            $result[$keyResult]['debito'] = $result[$keyResult]['debito'] + $op_puc->valor_debito;
                                                            $result[$keyResult]['credito'] = $result[$keyResult]['credito'] + $op_puc->valor_credito;
                                                            $result[$keyResult]['total'] = $total;
                                                        } else {
                                                            $result->push(['fecha' => Carbon::parse($op_puc->ordenPago->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago',
                                                                'debito' => $op_puc->valor_debito, 'credito' => $op_puc->valor_credito,
                                                                'name' => $op_puc->ordenPago->registros->persona->nombre, 'code' => $rubroPUC->code . '' . $count, 'total' => $total,
                                                                'lvl' => "nivel6", 'code_padre' => $rubroPUC->code]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ($result->count() > 0){
                                            $key = $result->where('code', $codeRubroPUC);

                                            $keyResult = $key->keys()[0];
                                            $keyHijo = $result->where('lvl', 'nivel6')->where('code_padre', $key[$keyResult]->code);

                                            $result[$keyResult]->debito = $keyHijo->sum('debito');
                                            $result[$keyResult]->credito = $keyHijo->sum('credito');
                                            $result[$keyResult]->total = $keyHijo->last()['total'];
                                        }
                                    }
                                }
                                if ($result->count() > 0){
                                    $keyHijo = $result->where('lvl', 'nivel4');
                                    $keyResult = $keyHijo->keys()->last();
                                    $idHijo = $result[$keyResult]->id;

                                    $key = $result->where('padre_id', $idHijo);
                                    $credHijo = 0;
                                    $debHijo = 0;
                                    $totHijo = 0;

                                    //dd($keyHijo, $keyResult, $key, $result);


                                    foreach ($key as $data){
                                        $credHijo = $credHijo + $data->credito;
                                        $debHijo = $debHijo + $data->debito;
                                        if ($data->total != 0){
                                            $totHijo = $data->total;
                                        }
                                    }

                                    $result[$keyResult]->debito = $debHijo;
                                    $result[$keyResult]->credito = $credHijo;
                                    $result[$keyResult]->total = $totHijo;
                                }
                            }
                        }
                        if ($result->count() > 0){
                            $keyLVL3 = $result->where('lvl', 'nivel3');
                            $keyResult = $keyLVL3->keys()->last();
                            $idLVL3 = $result[$keyResult]->id;

                            $key = $result->where('padre_id', $idLVL3);
                            $credLVL3 = 0;
                            $debLVL3 = 0;
                            $totLVL3 = 0;

                            foreach ($key as $data){
                                $credLVL3 = $credLVL3 + $data->credito;
                                $debLVL3 = $debLVL3 + $data->debito;
                                $totLVL3 = $data->total;
                            }

                            $result[$keyResult]->debito = '$'.number_format($debLVL3, 0);
                            $result[$keyResult]->credito = '$'.number_format($credLVL3, 0);
                            $result[$keyResult]->total = '$'.number_format($totLVL3, 0);
                        }
                    }
                }
            }
        }

        return view('administrativo.contabilidad.balances.prueba', compact('result'));

        $cuentasPUClvl3 = RegistersPuc::where('level_puc_id',3)->get();
        foreach ($cuentasPUClvl3 as $cuentaslvl3){
            $cuentaslvl3->code = $cuentaslvl3->code_padre->registers->code_padre->registers->code.$cuentaslvl3->code_padre->registers->code.$cuentaslvl3->code;
            $hijos = RegistersPuc::where('register_puc_id', $cuentaslvl3->id)->get();
            if ($hijos->count() >= 1){
                $savedDad = false;
                foreach ($hijos as $hijo){
                    $saveHijo = false;
                    $hijo->code = $cuentaslvl3->code.$hijo->code;
                    $rubrosPUC = RubrosPuc::where('registers_puc_id',$hijo->id)->get();
                    if ($rubrosPUC->count() >= 1){
                        $cuentaslvl3->credito = 0;
                        $cuentaslvl3->debito = 0;
                        $cuentaslvl3->total = 0;
                        $cuentaslvl3->lvl = "nivel3";
                        $hijo->credito = 0;
                        $hijo->debito = 0;
                        $hijo->total = 0;
                        $hijo->lvl = "nivel4";
                        foreach ($rubrosPUC as $rubroPUC){
                            $saveRubroPUC = false;
                            if ($rubroPUC->op_puc->count() >= 1){
                                $count = 0;
                                $totalRubros = 0;
                                $totalRubrosCred = 0;
                                $totalRubrosDeb = 0;
                                foreach ($rubroPUC->op_puc as $op_puc){
                                    if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                        $count++;
                                        $rubroPUC->code = $hijo->code . $rubroPUC->codigo;
                                        $rubroPUC->name = $rubroPUC->nombre_cuenta;
                                        $rubroPUC->credito = 0;
                                        $rubroPUC->debito = 0;
                                        $rubroPUC->total = 0;
                                        $rubroPUC->lvl = "nivel5";
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        $totalRubros = $totalRubros + $op_puc->valor_debito;
                                        $totalRubros = $totalRubros - $op_puc->valor_credito;

                                        if ($totalRubros != 0)
                                            $totalRubrosCred = $totalRubrosCred + $op_puc->valor_credito;
                                            $totalRubrosDeb= $totalRubrosDeb + $op_puc->valor_debito;
                                            if ($savedDad == false){
                                                $result->push($cuentaslvl3);
                                                $savedDad = true;
                                            }
                                            if ($saveHijo == false){
                                                $result->push($hijo);

                                                $saveHijo = true;
                                            }
                                            if ($saveRubroPUC == false) {
                                                $result->push($rubroPUC);
                                                $codeRubroPUC = $rubroPUC->code;

                                                $saveRubroPUC = true;
                                            }
                                            $key = $result->where('lvl', 'nivel6');
                                            if ($key->count() == 0 or $result->last()['lvl'] == "nivel5"){
                                                $result->push(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago',
                                                    'debito' => $op_puc->valor_debito, 'credito' => $op_puc->valor_credito,
                                                    'name' => $op_puc->ordenPago->registros->persona->nombre, 'code' => $rubroPUC->code . '' . $count, 'total' => $total,
                                                    'lvl' => "nivel6", 'code_padre' => $rubroPUC->code]);
                                            } else {
                                                $key2 = $result->where('code_padre', $codeRubroPUC);
                                                $keyRepetidos = $key2->where('name',$op_puc->ordenPago->registros->persona->nombre);
                                                if ($keyRepetidos->count() > 0){
                                                    $keyResult = $keyRepetidos->keys()[0];
                                                    $result[$keyResult] = collect($result[$keyResult]);
                                                    $result[$keyResult]['debito'] = $result[$keyResult]['debito'] + $op_puc->valor_debito;
                                                    $result[$keyResult]['credito'] = $result[$keyResult]['credito'] + $op_puc->valor_credito;
                                                    $result[$keyResult]['total'] = $total;
                                                } else {
                                                    $result->push(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago',
                                                        'debito' => $op_puc->valor_debito, 'credito' => $op_puc->valor_credito,
                                                        'name' => $op_puc->ordenPago->registros->persona->nombre, 'code' => $rubroPUC->code . '' . $count, 'total' => $total,
                                                        'lvl' => "nivel6", 'code_padre' => $rubroPUC->code]);
                                                }
                                            }
                                    }
                                }
                                if ($result->count() > 0){
                                    $key = $result->where('code', $codeRubroPUC);
                                    $keyResult = $key->keys()[0];
                                    if ($result[$keyResult]->debito == 0){
                                        $result[$keyResult]->debito = $totalRubrosDeb;
                                    }
                                    if ($result[$keyResult]->credito == 0){
                                        $result[$keyResult]->credito = $totalRubrosCred;
                                    }
                                    if ($result[$keyResult]->total == 0){
                                        $result[$keyResult]->total = $total;
                                    }
                                }
                            }
                        }
                        if ($result->count() > 0){
                            $keyHijo = $result->where('lvl', 'nivel4');
                            $keyResult = $keyHijo->keys()->last();
                            $idHijo = $result[$keyResult]->id;

                            $key = $result->where('registers_puc_id', $idHijo);
                            $credHijo = 0;
                            $debHijo = 0;
                            $totHijo = 0;

                            foreach ($key as $data){
                                $credHijo = $credHijo + $data->credito;
                                $debHijo = $debHijo + $data->debito;
                                if ($data->total != 0){
                                    $totHijo = $data->total;
                                }
                            }

                            $result[$keyResult]->debito = $debHijo;
                            $result[$keyResult]->credito = $credHijo;
                            $result[$keyResult]->total = $totHijo;
                        }
                    }
                }
                if ($result->count() > 0){
                    $keyLVL3 = $result->where('lvl', 'nivel3');
                    $keyResult = $keyLVL3->keys()->last();
                    $idLVL3 = $result[$keyResult]->id;

                    $key = $result->where('register_puc_id', $idLVL3);
                    $credLVL3 = 0;
                    $debLVL3 = 0;
                    $totLVL3 = 0;

                    foreach ($key as $data){
                        $credLVL3 = $credLVL3 + $data->credito;
                        $debLVL3 = $debLVL3 + $data->debito;
                        $totLVL3 = $data->total;
                    }

                    $result[$keyResult]->debito = '$'.number_format($debLVL3, 0);
                    $result[$keyResult]->credito = '$'.number_format($credLVL3, 0);
                    $result[$keyResult]->total = '$'.number_format($totLVL3, 0);

                    //dd($result);

                }
            }
        }
        return view('administrativo.contabilidad.balances.prueba', compact('result'));
    }

    public function informe($mes){
        Session::put('mes-informe-inicial', $mes);
        //dd();
        $meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio'];
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->take(3)->get();
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->where('id', 1)->get();
        //dd($pucs);
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        return view('administrativo.contabilidad.balances.prueba',compact('añoActual', 'mesActual', 'diaActual', 'pucs', 'meses'));
    }
}
