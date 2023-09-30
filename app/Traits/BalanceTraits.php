<?php
namespace App\Traits;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Pago\Pagos;
use Carbon\Carbon;

Class BalanceTraits
{
    public function balance($mes1, $mes2 = null){
        $año = Carbon::today()->year;
        $count = PucAlcaldia::where('padre_id',0)->get();
        foreach ($count as $first){
            $result2[] = $first;
            $lv2 = PucAlcaldia::where('padre_id', $first->id )->get();
            foreach ($lv2 as $dato){
                $result2[] = $dato;
                $lv3 = PucAlcaldia::where('padre_id', $dato->id )->get();
                foreach ($lv3 as $object){
                    $result2[] = $object;
                    $lv4 = PucAlcaldia::where('padre_id', $object->id )->get();
                    foreach ($lv4 as $data){
                        $result2[] = $data;
                        $hijos = PucAlcaldia::where('padre_id', $data->id )->get();
                        foreach ($hijos as $hijo){
                            $result2[] = $hijo;

                            //DESC RETENCION EN LAS FUENTES
                            $OPRets = RetencionFuente::where('codigo', $hijo->code)->get();
                            foreach ($OPRets as $retFuen){
                                $OPD = OrdenPagosDescuentos::where('retencion_fuente_id', $retFuen->id)->get();
                                foreach ($OPD as $descRet){
                                    $OP = OrdenPagos::find($descRet->orden_pagos_id);
                                    if ($OP){
                                        if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                            if (Carbon::parse($OP->created_at)->month >= $mes1 and
                                                Carbon::parse($OP->created_at)->month <= $mes2){
                                                $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' => 0,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->id, 'from' => 1,
                                                    'padre_id' => $hijo->padre_id];
                                            }
                                        }
                                    }
                                }

                            }
                            //OP DESC MUNI

                            $OPDescMunicipal = DescMunicipales::where('codigo', $hijo->code)->get();
                            foreach ($OPDescMunicipal as $OPDescMuni){
                                $OPD = OrdenPagosDescuentos::where('desc_municipal_id', $OPDescMuni->id)->get();
                                foreach ($OPD as $descRet){
                                    $OP = OrdenPagos::find($descRet->orden_pagos_id);
                                    if ($OP){
                                        if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                            if (Carbon::parse($OP->created_at)->month >= $mes1 and
                                                Carbon::parse($OP->created_at)->month <= $mes2){
                                                $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->id, 'from' => 2,
                                                    'padre_id' => $hijo->padre_id];
                                            }
                                        }
                                    }
                                }
                            }
                            //OP DESC

                            $OPD = OrdenPagosDescuentos::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($OPD as $descRet){
                                $OP = OrdenPagos::find($descRet->orden_pagos_id);
                                if ($OP){
                                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                        if (Carbon::parse($OP->created_at)->month >= $mes1 and
                                            Carbon::parse($OP->created_at)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->id, 'from' => 3,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }
                            //OP PUCS

                            $OPD = OrdenPagosPuc::where('rubros_puc_id', $hijo->id)->get();
                            foreach ($OPD as $descRet){
                                $OP = OrdenPagos::find($descRet->orden_pago_id);
                                if ($OP){
                                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                        if (Carbon::parse($OP->created_at)->month >= $mes1 and
                                            Carbon::parse($OP->created_at)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->id, 'from' => 4,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }
                            //PAGOS PUCS

                            $PagosPUCs = PagoBanksNew::where('rubros_puc_id', $hijo->id)->get();
                            foreach ($PagosPUCs as $descRet){
                                $pago = Pagos::find($descRet->pagos_id);
                                if ($pago){
                                    if ($pago->estado == "1" and Carbon::parse($pago->created_at)->year == $año ){
                                        if (Carbon::parse($pago->created_at)->month >= $mes1 and
                                            Carbon::parse($pago->created_at)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->id, 'from' => 5,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //COMP CONTABLES cuenta_puc_id

                            $compContMovs = ComprobanteIngresosMov::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($compContMovs as $descRet){
                                $compCont = ComprobanteIngresos::find($descRet->comp_id);
                                if ($compCont){
                                    if (Carbon::parse($compCont->ff)->year == $año ){
                                        if (Carbon::parse($compCont->ff)->month >= $mes1 and
                                            Carbon::parse($compCont->ff)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->id, 'from' => 6,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //COMP CONTABLES cuenta_banco

                            $compContMovsCuentaBanco = ComprobanteIngresosMov::where('cuenta_banco', $hijo->id)->get();
                            foreach ($compContMovsCuentaBanco as $compBanco){
                                $compCont = ComprobanteIngresos::find($compBanco->comp_id);
                                if ($compCont){
                                    if (Carbon::parse($compCont->ff)->year == $año ){
                                        if (Carbon::parse($compCont->ff)->month >= $mes1 and
                                            Carbon::parse($compCont->ff)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($compCont->ff)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $compBanco->debito ,
                                                'credito' =>  $compBanco->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->id, 'from' => 6,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }
                        }
                        //PADRES LVL 2
                        $totD = 0;
                        $totC = 0;
                        if (isset($hijosResult)){
                            foreach ($hijosResult as $item){
                                if($item['padre_id'] == $data->id){
                                    $totD = $totD + $item['debito'];
                                    $totC = $totC + $item['credito'];
                                }
                            }
                            $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $result[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                    }
                    //PADRES LVL 3
                    $totD = 0;
                    $totC = 0;
                    foreach ($result as $item){
                        if($item['padre_id'] == $object->id){
                            $totD = $totD + $item['debito'];
                            $totC = $totC + $item['credito'];
                        }
                    }
                    $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' => $object->code,
                        'concepto' => $object->concepto, 'padre_id' => $object->padre_id, 'cuenta_id' => $object->id]);

                }
                //PADRES LVL 2
                $totD = 0;
                $totC = 0;
                foreach ($result as $item){
                    if($item['padre_id'] == $dato->id){
                        $totD = $totD + $item['debito'];
                        $totC = $totC + $item['credito'];
                    }
                }
                $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' =>  $dato->code,
                    'concepto' =>  $dato->concepto, 'padre_id' =>  $dato->padre_id, 'cuenta_id' => $dato->id]);
            }
            //PADRES LVL 1
            $totD = 0;
            $totC = 0;
            foreach ($result as $item) {
                if ($item['padre_id'] == $first->id) {
                    $totD = $totD + $item['debito'];
                    $totC = $totC + $item['credito'];
                }
            }
            $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' =>  $first->code,
                'concepto' =>  $first->concepto, 'padre_id' =>  $first->padre_id, 'cuenta_id' => $first->id]);
        }
        $result[] = collect([ 'hijos' => $hijosResult]);

        return $result;
    }

}