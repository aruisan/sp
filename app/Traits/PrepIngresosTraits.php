<?php
namespace App\Traits;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\Rubro;
use Carbon\Carbon;

Class PrepIngresosTraits
{

    public function prepIngresos($vigencia, $inicio = null, $final = null ){

        $V = $vigencia->id;
        $vigencia_id = $V;
        if ($inicio != null) $comprobanteIng = ComprobanteIngresos::where('vigencia_id',$vigencia_id)->where('estado','3')
            ->whereBetween('ff',array($inicio, $final))->get();
        else  $comprobanteIng = ComprobanteIngresos::where('vigencia_id',$vigencia_id)->where('estado','3')->get();

        foreach ($comprobanteIng as $comp){
            foreach ($comp->movs as $mov){
                if(isset($mov->rubro_font_ingresos_id)) {
                    if ($inicio != null) {
                        if (date('Y-m-d', strtotime($mov->fechaComp)) <= $final and date('Y-m-d', strtotime($mov->fechaComp)) >= $inicio) {
                            $totComIng[] = $mov->debito;
                        }
                    }else $totComIng[] = $mov->debito;
                }
            }
        }
        if (!isset($totComIng)) $totComIng[] = 0;
        $plantillaIng = PlantillaCuipoIngresos::all();
        foreach ($plantillaIng as $data){
            if ($data->id == 1){
                if ($inicio != null) $adiciones = RubrosMov::where('font_vigencia_id', $vigencia->id)->where('movimiento','2')
                    ->whereBetween('created_at',array($inicio, $final))->get();
                else  $adiciones = RubrosMov::where('font_vigencia_id', $vigencia->id)->where('movimiento','2')->get();

                if ($inicio != null) $reducciones = RubrosMov::where('font_vigencia_id', $vigencia->id)->where('movimiento','3')
                    ->whereBetween('created_at',array($inicio, $final))->get();
                else  $reducciones = RubrosMov::where('font_vigencia_id', $vigencia->id)->where('movimiento','3')->get();

                $definitivo = $adiciones->sum('valor') - $reducciones->sum('valor') + $vigencia->presupuesto_inicial;
                //if ($data->code == '1.1.01.02.200') dd("FIRST",$data, array_sum($totComIng), $totComIng);
                $prepIng[] = collect(['id' => $data->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $vigencia->presupuesto_inicial, 'adicion' => $adiciones->sum('valor'), 'reduccion' => $reducciones->sum('valor'),
                    'anulados' => 0, 'recaudado' => array_sum($totComIng) , 'porRecaudar' => $definitivo - array_sum($totComIng), 'definitivo' => $definitivo,
                    'hijo' => 0, 'cod_fuente' => '', 'name_fuente' => '']);
                unset($totComIng);
            } else {
                $hijos1 = PlantillaCuipoIngresos::where('padre_id', $data->id)->get();
                if (count($hijos1) > 0){
                    foreach ($hijos1 as $h1){
                        if ($data->name == 'INGRESOS CORRIENTES' and $h1->id == 48){
                            dd($prepIng, $h1, $data, $sum, $compIngValue);
                        }
                        $hijos2 = PlantillaCuipoIngresos::where('padre_id', $h1->id)->get();
                        if (count($hijos2) > 0){
                            foreach ($hijos2 as $h2){
                                $hijos3 = PlantillaCuipoIngresos::where('padre_id', $h2->id)->get();
                                if (count($hijos3) > 0){
                                    foreach ($hijos3 as $h3){
                                        $hijos4 = PlantillaCuipoIngresos::where('padre_id', $h3->id)->get();
                                        if (count($hijos4) > 0){
                                            foreach ($hijos4 as $h4){
                                                $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h4->id)->get();
                                                if (count($rubro) > 0){
                                                    if (count($rubro) == 1){

                                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                        if (isset($adicionesH)) unset($adicionesH);
                                                        if (isset($reduccionesH)) unset($reduccionesH);

                                                        foreach ($rubro[0]->fontsRubro as $font) {
                                                            // VALIDACION PARA LAS ADICIONES
                                                            if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                                            else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                            if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                            else $hijosAdicion[] = 0;

                                                            // VALIDACION PARA LAS REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                            if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                                            else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                            if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                            else $hijosReduccion[] = 0;

                                                            if (count($font->compIng) > 0) {
                                                                foreach ($font->compIng as $compI){
                                                                    if ($inicio != null) {
                                                                        if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                            $civ[] = $compI->debito;
                                                                        }
                                                                    }else $civ[] = $compI->debito;
                                                                }
                                                            }
                                                        }

                                                        if($rubro[0]->cod == '1.1.01.02.300.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 2)->get();
                                                        elseif ($rubro[0]->cod == '1.1.01.02.218') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 3)->get();
                                                        elseif ($rubro[0]->cod == '1.1.01.02.200.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 5)->get();

                                                        if (isset($OPDes)){
                                                            foreach ($OPDes as $descuento){
                                                                $op = OrdenPagos::find($descuento->orden_pagos_id);
                                                                if ($op and $op->estado == '1' and Carbon::parse($op->created_at)->year == $vigencia->vigencia){
                                                                    if ($inicio != null) {
                                                                        if (date('Y-m-d', strtotime($op->created_at)) <= $final and date('Y-m-d', strtotime($op->created_at)) >= $inicio) {
                                                                            $descOPs[] = $descuento->valor;
                                                                        }
                                                                    }else $descOPs[] = $descuento->valor;
                                                                }
                                                            }
                                                            if (isset($descOPs)) {
                                                                $civ[] = array_sum($descOPs);
                                                                //if (array_sum($descOPs) == 996370884) dd("First",$descOPs, $rubro[0]);
                                                                unset($descOPs);
                                                            }
                                                        }

                                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                        $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                                        unset($OPDes);

                                                    } else {
                                                        foreach ($rubro as $rb){

                                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                            if (isset($adicionesH)) unset($adicionesH);
                                                            if (isset($reduccionesH)) unset($reduccionesH);

                                                            // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                            foreach ($rb->fontsRubro as $font) {
                                                                if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                                                else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                                if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                                else $hijosAdicion[] = 0;

                                                                if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                                                else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                                if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                                else $hijosReduccion[] = 0;

                                                                if (count($font->compIng) > 0) {
                                                                    foreach ($font->compIng as $compI){
                                                                        if ($inicio != null) {
                                                                            if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                                $civ[] = $compI->debito;
                                                                            }
                                                                        }else $civ[] = $compI->debito;
                                                                    }
                                                                }
                                                            }

                                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                            $sum[] = $rb->fontsRubro->sum('valor');
                                                        }
                                                    }
                                                }

                                            }
                                        } else{
                                            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h3->id)->get();
                                            if (count($rubro) > 0){
                                                if (count($rubro) == 1){

                                                    //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                    if (isset($adicionesH)) unset($adicionesH);
                                                    if (isset($reduccionesH)) unset($reduccionesH);

                                                    // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                    foreach ($rubro[0]->fontsRubro as $font) {
                                                        if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                            ->whereBetween('created_at',array($inicio, $final))->get();
                                                        else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                        if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                        else $hijosAdicion[] = 0;

                                                        if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                            ->whereBetween('created_at',array($inicio, $final))->get();
                                                        else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                        if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                        else $hijosReduccion[] = 0;

                                                        if (count($font->compIng) > 0) {
                                                            foreach ($font->compIng as $compI){
                                                                if ($inicio != null) {
                                                                    if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                        $civ[] = $compI->debito;
                                                                    }
                                                                }else $civ[] = $compI->debito;
                                                            }
                                                        }
                                                    }

                                                    if($rubro[0]->cod == '1.1.01.02.300.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 2)->get();
                                                    elseif ($rubro[0]->cod == '1.1.01.02.218') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 3)->get();
                                                    elseif ($rubro[0]->cod == '1.1.01.02.200.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 5)->get();

                                                    if (isset($OPDes)){
                                                        foreach ($OPDes as $descuento){
                                                            $op = OrdenPagos::find($descuento->orden_pagos_id);
                                                            if ($op and $op->estado == '1' and Carbon::parse($op->created_at)->year == $vigencia->vigencia){
                                                                if ($inicio != null) {
                                                                    if (date('Y-m-d', strtotime($op->created_at)) <= $final and date('Y-m-d', strtotime($op->created_at)) >= $inicio) {
                                                                        $descOPs[] = $descuento->valor;
                                                                    }
                                                                }else $descOPs[] = $descuento->valor;
                                                            }
                                                        }
                                                        if (isset($descOPs)) {
                                                            $civ[] = array_sum($descOPs);
                                                            //if (array_sum($descOPs) == 996370884) dd("Second",$descOPs);
                                                            unset($descOPs);
                                                        }
                                                    }

                                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                    $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                                    unset($OPDes);
                                                } else {
                                                    foreach ($rubro as $rb){

                                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                        if (isset($adicionesH)) unset($adicionesH);
                                                        if (isset($reduccionesH)) unset($reduccionesH);

                                                        // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                        foreach ($rb->fontsRubro as $font) {
                                                            if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                                            else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                            if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                            else $hijosAdicion[] = 0;

                                                            if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                                            else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                            if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                            else $hijosReduccion[] = 0;

                                                            if (count($font->compIng) > 0) {
                                                                foreach ($font->compIng as $compI){
                                                                    if ($inicio != null) {
                                                                        if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                            $civ[] = $compI->debito;
                                                                        }
                                                                    }else $civ[] = $compI->debito;
                                                                }
                                                            }
                                                        }

                                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                        $sum[] = $rb->fontsRubro->sum('valor');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h2->id)->get();
                                    if (count($rubro) > 0){
                                        if (count($rubro) == 1){

                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                            if (isset($adicionesH)) unset($adicionesH);
                                            if (isset($reduccionesH)) unset($reduccionesH);

                                            // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                            foreach ($rubro[0]->fontsRubro as $font) {
                                                if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                                else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                else $hijosAdicion[] = 0;

                                                if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                                else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                else $hijosReduccion[] = 0;

                                                if (count($font->compIng) > 0) {
                                                    foreach ($font->compIng as $compI){
                                                        if ($inicio != null) {
                                                            if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                $civ[] = $compI->debito;
                                                            }
                                                        }else $civ[] = $compI->debito;
                                                    }
                                                }
                                            }

                                            if($rubro[0]->cod == '1.1.01.02.300.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 2)->get();
                                            elseif ($rubro[0]->cod == '1.1.01.02.218') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 3)->get();
                                            elseif ($rubro[0]->cod == '1.1.01.02.200.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 5)->get();

                                            if (isset($OPDes)){
                                                foreach ($OPDes as $descuento){
                                                    $op = OrdenPagos::find($descuento->orden_pagos_id);
                                                    if ($op and $op->estado == '1' and Carbon::parse($op->created_at)->year == $vigencia->vigencia){
                                                        if ($inicio != null) {
                                                            if (date('Y-m-d', strtotime($op->created_at)) <= $final and date('Y-m-d', strtotime($op->created_at)) >= $inicio) {
                                                                $descOPs[] = $descuento->valor;
                                                            }
                                                        }else $descOPs[] = $descuento->valor;
                                                    }
                                                }
                                                if (isset($descOPs)) {
                                                    $civ[] = array_sum($descOPs);
                                                    //if (array_sum($descOPs) == 996370884) dd("third",$descOPs);
                                                    unset($descOPs);
                                                }
                                            }

                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                            $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                            unset($OPDes);
                                        } else {
                                            foreach ($rubro as $rb){
                                                //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                if (isset($adicionesH)) unset($adicionesH);
                                                if (isset($reduccionesH)) unset($reduccionesH);

                                                // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                foreach ($rb->fontsRubro as $font) {
                                                    if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                        ->whereBetween('created_at',array($inicio, $final))->get();
                                                    else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                                    if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                                    else $hijosAdicion[] = 0;

                                                    if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                        ->whereBetween('created_at',array($inicio, $final))->get();
                                                    else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                                    if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                                    else $hijosReduccion[] = 0;

                                                    if (count($font->compIng) > 0) {
                                                        foreach ($font->compIng as $compI){
                                                            if ($inicio != null) {
                                                                if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                                    $civ[] = $compI->debito;
                                                                }
                                                            }else $civ[] = $compI->debito;
                                                        }
                                                    }
                                                }

                                                if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                $sum[] = $rb->fontsRubro->sum('valor');
                                            }
                                        }
                                    }
                                }
                            }
                        } else{
                            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h1->id)->get();
                            if (count($rubro) > 0){
                                if (count($rubro) == 1){
                                    //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                    if (isset($adicionesH)) unset($adicionesH);
                                    if (isset($reduccionesH)) unset($reduccionesH);

                                    // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                    foreach ($rubro[0]->fontsRubro as $font) {
                                        if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                            ->whereBetween('created_at',array($inicio, $final))->get();
                                        else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                        if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                        else $hijosAdicion[] = 0;

                                        if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                            ->whereBetween('created_at',array($inicio, $final))->get();
                                        else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                        if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                        else $hijosReduccion[] = 0;

                                        if (count($font->compIng) > 0) {
                                            foreach ($font->compIng as $compI){
                                                if ($inicio != null) {
                                                    if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                        $civ[] = $compI->debito;
                                                    }
                                                }else $civ[] = $compI->debito;
                                            }
                                        }
                                    }

                                    if($rubro[0]->cod == '1.1.01.02.300.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 2)->get();
                                    elseif ($rubro[0]->cod == '1.1.01.02.218') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 3)->get();
                                    elseif ($rubro[0]->cod == '1.1.01.02.200.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 5)->get();

                                    if (isset($OPDes)){
                                        foreach ($OPDes as $descuento){
                                            $op = OrdenPagos::find($descuento->orden_pagos_id);
                                            if ($op and $op->estado == '1' and Carbon::parse($op->created_at)->year == $vigencia->vigencia){
                                                if ($inicio != null) {
                                                    if (date('Y-m-d', strtotime($op->created_at)) <= $final and date('Y-m-d', strtotime($op->created_at)) >= $inicio) {
                                                        $descOPs[] = $descuento->valor;
                                                    }
                                                }else $descOPs[] = $descuento->valor;
                                            }
                                        }
                                        if (isset($descOPs)) {
                                            $civ[] = array_sum($descOPs);
                                            $descFromOPs[] = array_sum($descOPs);
                                            //if (array_sum($descOPs) == 996370884) dd("fourth",$descOPs);
                                            unset($descOPs);
                                        }
                                    }

                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                    // VALIDACION PARA EL VALOR INICIAL DE LOS RUBROS PADRES
                                    $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                    unset($OPDes);
                                } else {
                                    foreach ($rubro as $rb){
                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                        if (isset($adicionesH)) unset($adicionesH);
                                        if (isset($reduccionesH)) unset($reduccionesH);

                                        // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                        foreach ($rb->fontsRubro as $font) {
                                            if ($inicio != null) $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                            else  $adds = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->get();
                                            if ($adds) foreach ($adds as $add) $hijosAdicion[] = $add->valor;
                                            else $hijosAdicion[] = 0;

                                            if ($inicio != null) $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                                ->whereBetween('created_at',array($inicio, $final))->get();
                                            else $reds = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->get();
                                            if ($reds)  foreach ($reds as $red) $hijosReduccion[] = $red->valor;
                                            else $hijosReduccion[] = 0;

                                            if (count($font->compIng) > 0) {
                                                foreach ($font->compIng as $compI){
                                                    if ($inicio != null) {
                                                        if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                            $civ[] = $compI->debito;
                                                        }
                                                    }else $civ[] = $compI->debito;
                                                }
                                            }
                                        }

                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                        // VALIDACION PARA EL VALOR INICIAL DE LOS RUBROS PADRES
                                        $sum[] = $rb->fontsRubro->sum('valor');
                                    }
                                }
                            }
                        }
                    }
                    if (isset($sum)){
                        $compIngValue = 0;
                        $adicionesTot = 0;
                        $reduccionesTot = 0;
                        if (isset($civ)) $compIngValue = array_sum($civ);
                        if (isset($adicionesH)) $adicionesTot = array_sum($adicionesH);
                        if (isset($reduccionesH)) $reduccionesTot = array_sum($reduccionesH);

                        $definitivo = $adicionesTot - $reduccionesTot + array_sum($sum);

                        if (!isset($descFromOPs)) $descFromOPs[] = 0;

                        if ($data->name == 'INGRESOS CORRIENTES') dd($prepIng, $sum, $compIngValue);

                        $prepIng[] = collect(['id' => $data->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => array_sum($sum), 'adicion' => $adicionesTot, 'reduccion' => $reduccionesTot,
                            'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo,
                            'hijo' => $data->hijo, 'cod_fuente' => '', 'name_fuente' => '']);

                        unset($sum);
                        if (isset($civ)) unset($civ);
                        if (isset($adicionesH)) unset($adicionesH);
                        if (isset($reduccionesH)) unset($reduccionesH);
                        if (isset($hijosAdicion)) unset($hijosAdicion);
                        if (isset($hijosReduccion)) unset($hijosReduccion);
                    }
                } else {
                    //AL NO TENER HIJOS SE TOMA COMO SI FUERA YA EL RUBRO HIJO CON LOS VALORES
                    $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
                    if (count($rubro) > 0){
                        if (count($rubro) == 1){
                            if (count($rubro[0]->fontsRubro) > 1){
                                foreach ($rubro[0]->fontsRubro as $font){
                                    if ($inicio != null) $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                    if ($add) $adicion = $add->valor;
                                    else $adicion = 0;

                                    if ($inicio != null) $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                    if ($red) $reduccion = $red->valor;
                                    else $reduccion = 0;

                                    $definitivo = $adicion - $reduccion + $font->valor;

                                    if (count($font->compIng) > 0) {
                                        foreach ($font->compIng as $comprobante){
                                            if ($comprobante->rubro_font_ingresos_id == $font->id) {
                                                if ($inicio != null) {
                                                    if (date('Y-m-d', strtotime($comprobante->ff)) <= $final and date('Y-m-d', strtotime($comprobante->ff)) >= $inicio) {
                                                        $compIngValueArray[] = $comprobante->debito;
                                                    }
                                                }else $compIngValueArray[] = $comprobante->debito;
                                            }
                                        }
                                    }

                                    if (isset($compIngValueArray)) $compIngValue = array_sum($compIngValueArray);
                                    else $compIngValue = 0;
                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name,
                                        'inicial' => $font->valor, 'adicion' => $adicion, 'reduccion' => $reduccion, 'anulados' => 0,
                                        'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo,'hijo' => $data->hijo,
                                        'cod_fuente' => $font->sourceFunding->code, 'name_fuente' => $font->sourceFunding->description]);

                                    if (isset($compIngValueArray)) unset($compIngValueArray);
                                }
                            } else {
                                $compIngValue = 0;
                                if (count($rubro[0]->fontsRubro) > 0){
                                    if ($inicio != null) $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                    if ($add) $adicion = $add->valor;
                                    else $adicion = 0;

                                    if ($inicio != null) $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                    if ($red) $reduccion = $red->valor;
                                    else $reduccion = 0;

                                    $definitivo = $adicion - $reduccion + $rubro[0]->fontsRubro->sum('valor');
                                    if (count($rubro[0]->fontsRubro[0]->compIng) > 0) {

                                        if ($inicio != null) {
                                            foreach ($rubro[0]->fontsRubro[0]->compIng as $comprobante) {
                                                if (date('Y-m-d', strtotime($comprobante->ff)) <= $final and date('Y-m-d', strtotime($comprobante->ff)) >= $inicio) {
                                                    $compIngValueArray[] = $comprobante->debito;
                                                }
                                            }
                                            if (isset($compIngValueArray)) $compIngValue = array_sum($compIngValueArray);
                                            else $compIngValue = 0;
                                        } else $compIngValue = $rubro[0]->fontsRubro[0]->compIng->sum('debito');
                                    }

                                    if($rubro[0]->cod == '1.1.01.02.300.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 2)->get();
                                    elseif ($rubro[0]->cod == '1.1.01.02.218') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 3)->get();
                                    elseif ($rubro[0]->cod == '1.1.01.02.200.01') $OPDes = OrdenPagosDescuentos::where('desc_municipal_id', 5)->get();

                                    if (isset($OPDes)){
                                        foreach ($OPDes as $descuento){
                                            $op = OrdenPagos::find($descuento->orden_pagos_id);
                                            if ($op and $op->estado == '1' and Carbon::parse($op->created_at)->year == $vigencia->vigencia){
                                                if ($inicio != null) {
                                                    if (date('Y-m-d', strtotime($op->created_at)) <= $final and date('Y-m-d', strtotime($op->created_at)) >= $inicio) {
                                                        $descOPs[] = $descuento->valor;
                                                    }
                                                }else $descOPs[] = $descuento->valor;
                                            }
                                        }
                                        if (isset($descOPs)) {
                                            $compIngValue = $compIngValue + array_sum($descOPs);
                                            unset($descOPs);
                                        }
                                    }
                                    //if ($data->code == '1.1.01.02.200.01') dd("FOURTH",$data, $compIngValue, $civ, array_sum($civ), $rubro[0]);
                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $rubro[0]->fontsRubro->sum('valor'), 'adicion' => $adicion, 'reduccion' => $reduccion,
                                        'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' =>  $definitivo,
                                        'hijo' => $data->hijo, 'cod_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->code, 'name_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->description]);

                                    if (isset($compIngValueArray)) unset($compIngValueArray);
                                    unset($OPDes);
                                }

                            }
                            } else {
                            //MAS DE UN RUBRO ASIGNADO A LA MISMA PLANTILLA
                            foreach ($rubro as $rb){
                                //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                if (isset($adicionesH)) unset($adicionesH);
                                if (isset($reduccionesH)) unset($reduccionesH);

                                foreach ($rb->fontsRubro as $font) {
                                    if ($inicio != null) RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                    if ($add) {
                                        if ($add->fonts_rubro_id == $font->id) $hijosAdicion[] = $add->valor;
                                        else $hijosAdicion[] = 0;
                                    }
                                    else $hijosAdicion[] = 0;

                                    if ($inicio != null) $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)
                                        ->whereBetween('created_at',array($inicio, $final))->first();
                                    else  $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                    if ($red) {
                                        if ($red->fonts_rubro_id == $font->id) $hijosReduccion[] = $red->valor;
                                        else $hijosReduccion[] = 0;
                                    }
                                    else $hijosReduccion[] = 0;

                                    if (count($font->compIng) > 0) {
                                        foreach ($font->compIng as $compI){
                                            if ($inicio != null) {
                                                if (date('Y-m-d', strtotime($compI->fechaComp)) <= $final and date('Y-m-d', strtotime($compI->fechaComp)) >= $inicio) {
                                                    $civHijo[] = $compI->debito;
                                                }
                                            }else $civHijo[] = $compI->debito;
                                        }
                                    }
                                }

                                if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                if (isset($adicionesH)) $adicionesTot = array_sum($adicionesH);
                                if (isset($reduccionesH)) $reduccionesTot = array_sum($reduccionesH);

                                $compIngValue = 0;
                                if (isset($civHijo)) $compIngValue = array_sum($civHijo);
                                elseif (count($rb->compIng) > 0) $compIngValue = $rb->compIng->sum('valor');
                                $sum[] = $rb->fontsRubro->sum('valor');
                                $definitivo = $adicionesTot - $reduccionesTot + $rb->fontsRubro->sum('valor');
                                $prepIng[] = collect(['id' => $rb->id, 'code' => $data->code, 'name' => $rb->name, 'inicial' => $rb->fontsRubro->sum('valor'), 'adicion' => $adicionesTot, 'reduccion' => $reduccionesTot,
                                    'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo  - $compIngValue, 'definitivo' => $definitivo,
                                    'hijo' => $data->hijo, 'cod_fuente' => $rb->fontsRubro[0]->sourceFunding->code, 'name_fuente' => $rb->fontsRubro[0]->sourceFunding->description]);

                                unset($sum);
                                if (isset($adicionesH)) unset($adicionesH);
                                if (isset($reduccionesH)) unset($reduccionesH);
                                if (isset($hijosAdicion)) unset($hijosAdicion);
                                if (isset($hijosReduccion)) unset($hijosReduccion);
                                if (isset($civHijo)) unset($civHijo);
                            }
                        }
                    }
                }
            }
        }

        return $prepIng;
    }


}