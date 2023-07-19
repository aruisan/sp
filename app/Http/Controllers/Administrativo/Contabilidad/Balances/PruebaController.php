<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use App\InformeContableMensual;
use App\InformeContableMensualData;

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


    public function reload_informe(InformeContableMensual $informe){
        $informe->finalizar = FALSE;
        $informe->save();
        $pucs = PucAlcaldia::get();
        return view('administrativo.contabilidad.balances.pre_prueba',compact('informe', 'pucs'));
    }

    public function pre_informe($mes){
        
        Session::put(auth()->id().'-mes-informe-contable-mes', $mes);
        $fecha = "2023-{$mes}-01";
        $informe = InformeContableMensual::where('fecha', $fecha)->first();
        $pucs = PucAlcaldia::get();
        if(is_null($informe)):
            $informe = InformeContableMensual::create(['fecha' => $fecha]);
        endif;

        if($informe->finalizar){
            return redirect()->route('balance.prueba-informe', $informe->id);
        }
        //Session::flash('error','Actualmente no existe un PUC para poder ver los informes. Se recomienda crearlo.');
        return view('administrativo.contabilidad.balances.pre_prueba',compact('informe', 'pucs'));
    }

    public function generar_informe(InformeContableMensual $informe, PucAlcaldia $puc){
        $mes = Session::get(auth()->id().'-mes-informe-contable-mes');
        $mes_integer = intval($mes)-1;
        $mes_anterior = $mes_integer > 9 ? $mes_integer : "0{$mes_integer}";
        $fecha_anterior = "2023-{$mes_anterior}-01";
        $pucs_count = PucAlcaldia::count();
        $puc_mensual = InformeContableMensualData::where('informe_contable_mensual_id', $informe->id)->where('puc_alcaldia_id', $puc->id)->first();

        if(is_null($puc_mensual)):
            $puc_mensual = new InformeContableMensualData;
        endif;

        if($mes_integer == 0){
            $i_debito = $puc->naturaleza  == 'DEBITO' ? $puc->v_inicial: 0;
            $i_credito = $puc->naturaleza == 'CREDITO' ? $puc->v_inicial: 0;
        }else{
            $informe_anterior = InformeContableMensual::where('fecha', $fecha_anterior)->first();
            //dd([$informe_anterior, $fecha_anterior]);
            if(is_null($informe_anterior)){
                $informe->delete();
                return "no se ha generado el mes anterior";
            }
            $puc_informe_contable_mensual_anterior = InformeContableMensualData::where('informe_contable_mensual_id', $informe_anterior->id)->where('puc_alcaldia_id', $puc->id)->first();

            $i_debito = $puc_informe_contable_mensual_anterior->s_debito;
            $i_credito = $puc_informe_contable_mensual_anterior->s_credito;
        }

        $m_credito = $puc->m_credito;
        $m_debito = $puc->m_debito;
        $s_debito = $puc->naturaleza == "DEBITO" ? $i_debito + $m_debito - $m_credito : 0;
        $s_credito = $puc->naturaleza == "CREDITO" ?  $i_credito + $m_credito - $m_debito : 0;
        
        $puc_mensual->informe_contable_mensual_id = $informe->id;
        $puc_mensual->puc_alcaldia_id = $puc->id;
        $puc_mensual->m_credito = $m_credito;
        $puc_mensual->m_debito = $m_debito;
        $puc_mensual->i_credito = $i_credito;
        $puc_mensual->i_debito = $i_debito;
        $puc_mensual->s_credito = $s_credito;
        $puc_mensual->s_debito = $s_debito;    
        $puc_mensual->save();
        

        return response()->json($pucs_count == $informe->datos->count() ? TRUE : FALSE);
    }

    public function generar_informe_relaciones(InformeContableMensual $informe){
        $pucs = PucAlcaldia::get();

        foreach($informe->datos->filter(function($d){ return is_null($d->padre_id); }) as $puc_mensual):
            if($puc_mensual->puc_alcaldia->hijos->count()):
                $hijos = $informe->datos->filter(function($e)use($puc_mensual){ return in_array($e->puc_alcaldia_id, $puc_mensual->puc_alcaldia->hijos->pluck('id')->toArray()); });
                foreach($hijos as $puc_data):
                    $puc_data->update(['padre_id' => $puc_mensual->id]); 
                endforeach;
            endif;
        endforeach;

        $informe->finalizar = TRUE;
        $informe->save();
        return response()->json(TRUE);
    }

    public function informe(InformeContableMensual $informe){
        $meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio'];
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->take(3)->get();
        $pucs = $informe->datos->filter(function($p){ return is_null($p->padre); })->sortBy('puc_alcaldia.code');

        //dd($pucs->first()->hijos->map(function($e){ return $e->puc_alcaldia->code; }));
        //dd($pucs);
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        return view('administrativo.contabilidad.balances.prueba',compact('añoActual', 'mesActual', 'diaActual', 'pucs', 'meses', 'informe'));
    }
}
