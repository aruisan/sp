<?php

namespace App\Http\Controllers\Administrativo\Contabilidad;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\CompContMov;
use App\Model\Administrativo\Contabilidad\Puc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use App\Model\Persona;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;
use Session;

class LibrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lv1 = PucAlcaldia::where('padre_id',0)->get();
        foreach ($lv1 as $item){
            $lv2 = PucAlcaldia::where('padre_id', $item->id )->get();
            foreach ($lv2 as $dato){
                $lv3 = PucAlcaldia::where('padre_id', $dato->id )->get();
                foreach ($lv3 as $object){
                    $result[] = $object;
                    $lv4 = PucAlcaldia::where('padre_id', $object->id )->get();
                    foreach ($lv4 as $data){
                        $result[] = $data;
                        $hijos = PucAlcaldia::where('padre_id', $data->id )->get();
                        foreach ($hijos as $hijo){
                            $result[] = $hijo;
                        }
                    }
                }
            }
        }

        return view('administrativo.contabilidad.libros.index',compact('result'));
    }

    public function balanceALL(){
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
                                            if (Carbon::parse($OP->created_at)->month == 1){
                                                $enero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' => 0,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            } elseif (Carbon::parse($OP->created_at)->month == 2){
                                                $febrero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 3){
                                                $marzo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 4){
                                                $abril[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 5){
                                                $mayo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 6){
                                                $junio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 7){
                                                $julio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 8){
                                                $agosto[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 9){
                                                $septiembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 10){
                                                $octubre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 11){
                                                $noviembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 12){
                                                $diciembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
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
                                            if (Carbon::parse($OP->created_at)->month == 1){
                                                $enero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            } elseif (Carbon::parse($OP->created_at)->month == 2){
                                                $febrero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 3){
                                                $marzo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 4){
                                                $abril[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 5){
                                                $mayo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 6){
                                                $junio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 7){
                                                $julio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 8){
                                                $agosto[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 9){
                                                $septiembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 10){
                                                $octubre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 11){
                                                $noviembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }elseif (Carbon::parse($OP->created_at)->month == 12){
                                                $diciembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1, 'padre_id' => $hijo->padre_id];
                                            }
                                        }
                                    }
                                }
                            }

                            //OP DESC

                            $OPD = OrdenPagosDescuentos::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($OPD as $descRet){
                                $OP = OrdenPagos::find($descRet->orden_pagos_id);
                                $from = 2;
                                if ($OP){
                                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                        if (Carbon::parse($OP->created_at)->month == 1){
                                            $enero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        } elseif (Carbon::parse($OP->created_at)->month == 2){
                                            $febrero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 3){
                                            $marzo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 4){
                                            $abril[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 5){
                                            $mayo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 6){
                                            $junio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 7){
                                            $julio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 8){
                                            $agosto[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 9){
                                            $septiembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 10){
                                            $octubre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 11){
                                            $noviembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 12){
                                            $diciembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }


                            //OP PUCS

                            $OPD = OrdenPagosPuc::where('rubros_puc_id', $hijo->id)->get();
                            foreach ($OPD as $descRet){
                                $OP = OrdenPagos::find($descRet->orden_pago_id);
                                $from = 3;
                                if ($OP){
                                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == $año ){
                                        if (Carbon::parse($OP->created_at)->month == 1){
                                            $enero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        } elseif (Carbon::parse($OP->created_at)->month == 2){
                                            $febrero[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 3){
                                            $marzo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 4){
                                            $abril[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 5){
                                            $mayo[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 6){
                                            $junio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 7){
                                            $julio[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' => $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 8){
                                            $agosto[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 9){
                                            $septiembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 10){
                                            $octubre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 11){
                                            $noviembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($OP->created_at)->month == 12){
                                            $diciembre[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito, 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //PAGOS PUCS

                            $PagosPUCs = PagoBanksNew::where('rubros_puc_id', $hijo->id)->get();
                            foreach ($PagosPUCs as $descRet){
                                $pago = Pagos::find($descRet->pagos_id);
                                $from = 4;
                                if ($pago){
                                    if ($pago->estado == "1" and Carbon::parse($pago->created_at)->year == $año ){
                                        if (Carbon::parse($pago->created_at)->month == 1){
                                            $enero[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        } elseif (Carbon::parse($pago->created_at)->month == 2){
                                            $febrero[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 3){
                                            $marzo[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 4){
                                            $abril[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 5){
                                            $mayo[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 6){
                                            $junio[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 7){
                                            $julio[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' => $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 8){
                                            $agosto[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 9){
                                            $septiembre[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 10){
                                            $octubre[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 11){
                                            $noviembre[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($pago->created_at)->month == 12){
                                            $diciembre[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //COMP CONTABLES

                            $compContMovs = ComprobanteIngresosMov::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($compContMovs as $descRet){
                                $compCont = ComprobanteIngresos::find($descRet->comp_id);
                                $from = 5;
                                if ($compCont){
                                    if (Carbon::parse($compCont->created_at)->year == $año ){
                                        if (Carbon::parse($compCont->created_at)->month == 1){
                                            $enero[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        } elseif (Carbon::parse($compCont->created_at)->month == 2){
                                            $febrero[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 3){
                                            $marzo[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 4){
                                            $abril[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 5){
                                            $mayo[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 6){
                                            $junio[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 7){
                                            $julio[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' => $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 8){
                                            $agosto[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 9){
                                            $septiembre[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 10){
                                            $octubre[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 11){
                                            $noviembre[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }elseif (Carbon::parse($compCont->created_at)->month == 12){
                                            $diciembre[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito, 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => $from, 'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }
                        }
                        $totEneroD = 0;
                        $totEneroC = 0;
                        if (isset($enero)){
                            foreach ($enero as $item){
                                if($item['padre_id'] == $data->id){
                                    $totEneroD = $totEneroD + $item['debito'];
                                    $totEneroC = $totEneroC + $item['credito'];
                                }
                            }
                            $resultEne[] = collect(['debito' => $totEneroD, 'credito' => $totEneroC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultEne[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totFebD = 0;
                        $totFebC = 0;
                        if (isset($febrero)){
                            foreach ($febrero as $item){
                                if($item['padre_id'] == $data->id){
                                    $totFebD = $totFebD + $item['debito'];
                                    $totFebC = $totFebC + $item['credito'];
                                }
                            }
                            $resultFeb[] = collect(['debito' => $totFebD, 'credito' => $totFebC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultFeb[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totMarD = 0;
                        $totMarC = 0;
                        if (isset($marzo)){
                            foreach ($marzo as $item){
                                if($item['padre_id'] == $data->id){
                                    $totMarD = $totMarD + $item['debito'];
                                    $totMarC = $totMarC + $item['credito'];
                                }
                            }
                            $resultMar[] = collect(['debito' => $totMarD, 'credito' => $totMarC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultMar[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totAbrD = 0;
                        $totAbrC = 0;
                        if (isset($abril)){
                            foreach ($abril as $item){
                                if($item['padre_id'] == $data->id){
                                    $totAbrD = $totAbrD + $item['debito'];
                                    $totAbrC = $totAbrC + $item['credito'];
                                }
                            }
                            $resultAbr[] = collect(['debito' => $totAbrD, 'credito' => $totAbrC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultAbr[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totMayD = 0;
                        $totMayC = 0;
                        if (isset($mayo)){
                            foreach ($mayo as $item){
                                if($item['padre_id'] == $data->id){
                                    $totMayD = $totMayD + $item['debito'];
                                    $totMayC = $totMayC + $item['credito'];
                                }
                            }
                            $resultMay[] = collect(['debito' => $totMayD, 'credito' => $totMayC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultMay[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totJunD = 0;
                        $totJunC = 0;
                        if (isset($junio)){
                            foreach ($junio as $item){
                                if($item['padre_id'] == $data->id){
                                    $totJunD = $totJunD + $item['debito'];
                                    $totJunC = $totJunC + $item['credito'];
                                }
                            }
                            $resultJun[] = collect(['debito' => $totJunD, 'credito' => $totJunC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultJun[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code, 'concepto' => $data->concepto,
                            'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        $totJulD = 0;
                        $totJulC = 0;
                        if (isset($julio)){
                            foreach ($julio as $item){
                                if($item['padre_id'] == $data->id){
                                    $totJulD = $totJulD + $item['debito'];
                                    $totJulC = $totJulC + $item['credito'];
                                }
                            }
                            $resultJul[] = collect(['debito' => $totJulD, 'credito' => $totJulC, 'code' => $data->code,
                                'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                        } else $resultJul[] = collect(['debito' => 0, 'credito' => 0, 'code' => $data->code,
                            'concepto' => $data->concepto, 'padre_id' => $data->padre_id, 'cuenta_id' => $data->id]);
                    }

                    //PADRES LVL 3

                    $totEneroD = 0;
                    $totEneroC = 0;
                    foreach ($resultEne as $item){
                        if($item['padre_id'] == $object->id){
                            $totEneroD = $totEneroD + $item['debito'];
                            $totEneroC = $totEneroC + $item['credito'];
                        }
                    }
                    $resultEne[] = collect(['debito' => $totEneroD, 'credito' => $totEneroC, 'code' => $object->code,
                        'concepto' => $object->concepto, 'padre_id' => $object->padre_id, 'cuenta_id' => $object->id]);
                    $totFebD = 0;
                    $totFebC = 0;
                    foreach ($resultFeb as $item){
                        if($item['padre_id'] == $object->id){
                            $totFebD = $totFebD + $item['debito'];
                            $totFebC = $totFebC + $item['credito'];
                        }
                    }
                    $resultFeb[] = collect(['debito' => $totFebD, 'credito' => $totFebC, 'code' => $object->code,
                        'concepto' => $object->concepto, 'padre_id' => $object->padre_id, 'cuenta_id' => $object->id]);
                    $totMarD = 0;
                    $totMarC = 0;
                    foreach ($resultMar as $item){
                        if($item['padre_id'] ==  $object->id){
                            $totMarD = $totMarD + $item['debito'];
                            $totMarC = $totMarC + $item['credito'];
                        }
                    }
                    $resultMar[] = collect(['debito' => $totMarD, 'credito' => $totMarC, 'code' =>  $object->code,
                        'concepto' =>  $object->concepto, 'padre_id' =>  $object->padre_id, 'cuenta_id' => $object->id]);
                    $totAbrD = 0;
                    $totAbrC = 0;
                    foreach ($resultAbr as $item){
                        if($item['padre_id'] ==  $object->id){
                            $totAbrD = $totAbrD + $item['debito'];
                            $totAbrC = $totAbrC + $item['credito'];
                        }
                    }
                    $resultAbr[] = collect(['debito' => $totAbrD, 'credito' => $totAbrC, 'code' =>  $object->code,
                        'concepto' =>  $object->concepto, 'padre_id' =>  $object->padre_id, 'cuenta_id' => $object->id]);
                    $totMayD = 0;
                    $totMayC = 0;
                    foreach ($resultMay as $item){
                        if($item['padre_id'] ==  $object->id){
                            $totMayD = $totMayD + $item['debito'];
                            $totMayC = $totMayC + $item['credito'];
                        }
                    }
                    $resultMay[] = collect(['debito' => $totMayD, 'credito' => $totMayC, 'code' =>  $object->code,
                        'concepto' =>  $object->concepto, 'padre_id' =>  $object->padre_id, 'cuenta_id' => $object->id]);
                    $totJunD = 0;
                    $totJunC = 0;
                    foreach ($resultJun as $item){
                        if($item['padre_id'] ==  $object->id){
                            $totJunD = $totJunD + $item['debito'];
                            $totJunC = $totJunC + $item['credito'];
                        }
                    }
                    $resultJun[] = collect(['debito' => $totJunD, 'credito' => $totJunC, 'code' =>  $object->code,
                        'concepto' =>  $object->concepto, 'padre_id' =>  $object->padre_id, 'cuenta_id' => $object->id]);
                    $totJulD = 0;
                    $totJulC = 0;
                    foreach ($resultJul as $item){
                        if($item['padre_id'] ==  $object->id){
                            $totJulD = $totJulD + $item['debito'];
                            $totJulC = $totJulC + $item['credito'];
                        }
                    }
                    $resultJul[] = collect(['debito' => $totJulD, 'credito' => $totJulC, 'code' =>  $object->code,
                        'concepto' =>  $object->concepto, 'padre_id' =>  $object->padre_id, 'cuenta_id' => $object->id]);

                }

                //PADRES LVL 2

                $totEneroD = 0;
                $totEneroC = 0;
                foreach ($resultEne as $item){
                    if($item['padre_id'] == $dato->id){
                        $totEneroD = $totEneroD + $item['debito'];
                        $totEneroC = $totEneroC + $item['credito'];
                    }
                }
                $resultEne[] = collect(['debito' => $totEneroD, 'credito' => $totEneroC, 'code' =>  $dato->code,
                    'concepto' =>  $dato->concepto, 'padre_id' =>  $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totFebD = 0;
                $totFebC = 0;
                foreach ($resultFeb as $item){
                    if($item['padre_id'] == $dato->id){
                        $totFebD = $totFebD + $item['debito'];
                        $totFebC = $totFebC + $item['credito'];
                    }
                }
                $resultFeb[] = collect(['debito' => $totFebD, 'credito' => $totFebC, 'code' => $dato->code,
                    'concepto' => $dato->concepto, 'padre_id' => $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totMarD = 0;
                $totMarC = 0;
                foreach ($resultMar as $item){
                    if($item['padre_id'] ==  $dato->id){
                        $totMarD = $totMarD + $item['debito'];
                        $totMarC = $totMarC + $item['credito'];
                    }
                }
                $resultMar[] = collect(['debito' => $totMarD, 'credito' => $totMarC, 'code' =>  $dato->code,
                    'concepto' =>  $dato->concepto, 'padre_id' =>  $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totAbrD = 0;
                $totAbrC = 0;
                foreach ($resultAbr as $item){
                    if($item['padre_id'] ==   $dato->id){
                        $totAbrD = $totAbrD + $item['debito'];
                        $totAbrC = $totAbrC + $item['credito'];
                    }
                }
                $resultAbr[] = collect(['debito' => $totAbrD, 'credito' => $totAbrC, 'code' =>   $dato->code,
                    'concepto' =>   $dato->concepto, 'padre_id' =>   $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totMayD = 0;
                $totMayC = 0;
                foreach ($resultMay as $item){
                    if($item['padre_id'] ==   $dato->id){
                        $totMayD = $totMayD + $item['debito'];
                        $totMayC = $totMayC + $item['credito'];
                    }
                }
                $resultMay[] = collect(['debito' => $totMayD, 'credito' => $totMayC, 'code' =>   $dato->code,
                    'concepto' =>   $dato->concepto, 'padre_id' =>   $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totJunD = 0;
                $totJunC = 0;
                foreach ($resultJun as $item){
                    if($item['padre_id'] ==   $dato->id){
                        $totJunD = $totJunD + $item['debito'];
                        $totJunC = $totJunC + $item['credito'];
                    }
                }
                $resultJun[] = collect(['debito' => $totJunD, 'credito' => $totJunC, 'code' =>   $dato->code,
                    'concepto' =>   $dato->concepto, 'padre_id' =>   $dato->padre_id, 'cuenta_id' => $dato->id]);
                $totJulD = 0;
                $totJulC = 0;
                foreach ($resultJul as $item){
                    if($item['padre_id'] ==   $dato->id){
                        $totJulD = $totJulD + $item['debito'];
                        $totJulC = $totJulC + $item['credito'];
                    }
                }

                $resultJul[] = collect(['debito' => $totJulD, 'credito' => $totJulC, 'code' =>   $dato->code,
                    'concepto' =>   $dato->concepto, 'padre_id' =>  $dato->padre_id, 'cuenta_id' => $dato->id]);
            }
            //PADRES LVL 1

            $totEneroD = 0;
            $totEneroC = 0;
            foreach ($resultEne as $item){
                if($item['padre_id'] == $first->id){
                    $totEneroD = $totEneroD + $item['debito'];
                    $totEneroC = $totEneroC + $item['credito'];
                }
            }
            $resultEne[] = collect(['debito' => $totEneroD, 'credito' => $totEneroC, 'code' =>  $first->code,
                'concepto' =>  $first->concepto, 'padre_id' =>  $first->padre_id, 'cuenta_id' => $first->id]);
        }

        return view('administrativo.contabilidad.libros.balance',compact('result2', 'resultEne',
            'enero', 'resultFeb','febrero', 'resultMar', 'marzo'));
    }

    public function balance($mes){
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
                                            if (Carbon::parse($OP->created_at)->month == $mes){
                                                $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' => 0,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1,
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
                                            if (Carbon::parse($OP->created_at)->month == $mes){
                                                $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                    'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                    'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 2,
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
                                        if (Carbon::parse($OP->created_at)->month == $mes){
                                            $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  0 ,
                                                'credito' =>  $descRet->valor , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 3,
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
                                        if (Carbon::parse($OP->created_at)->month == $mes){
                                            $hijosResult[] = ['fecha' => Carbon::parse($OP->created_at)->format('d-m-Y'),
                                                'modulo' => 'Orden de Pago #'.$OP->code, 'debito' =>  $descRet->valor_debito ,
                                                'credito' =>  $descRet->valor_credito , 'concepto' => $OP->nombre,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 4,
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
                                        if (Carbon::parse($pago->created_at)->month == $mes){
                                            $hijosResult[] = ['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                                'modulo' => 'Pago #'.$pago->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $pago->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 5,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //COMP CONTABLES

                            $compContMovs = ComprobanteIngresosMov::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($compContMovs as $descRet){
                                $compCont = ComprobanteIngresos::find($descRet->comp_id);
                                if ($compCont){
                                    if (Carbon::parse($compCont->created_at)->year == $año ){
                                        if (Carbon::parse($compCont->created_at)->month == $mes){
                                            $hijosResult[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 6,
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
            foreach ($result as $item){
                if($item['padre_id'] == $first->id){
                    $totD = $totD + $item['debito'];
                    $totC = $totC + $item['credito'];
                }
            }
            $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' =>  $first->code,
                'concepto' =>  $first->concepto, 'padre_id' =>  $first->padre_id, 'cuenta_id' => $first->id]);
        }

        if ($mes == 1) $mes = "Enero";
        elseif ($mes == 2) $mes = "Febrero";
        elseif ($mes == 3) $mes = "Marzo";

        $trim = 0;

        return view('administrativo.contabilidad.libros.balance',compact('result2', 'result',
            'hijosResult','mes','trim'));
    }


    public function balanceTrim($mes1, $mes2){
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
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 1,
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
                                                    'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 2,
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
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 3,
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
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 4,
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
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 5,
                                                'padre_id' => $hijo->padre_id];
                                        }
                                    }
                                }
                            }

                            //COMP CONTABLES

                            $compContMovs = ComprobanteIngresosMov::where('cuenta_puc_id', $hijo->id)->get();
                            foreach ($compContMovs as $descRet){
                                $compCont = ComprobanteIngresos::find($descRet->comp_id);
                                if ($compCont){
                                    if (Carbon::parse($compCont->created_at)->year == $año ){
                                        if (Carbon::parse($compCont->created_at)->month >= $mes1 and
                                            Carbon::parse($compCont->created_at)->month <= $mes2){
                                            $hijosResult[] = ['fecha' => Carbon::parse($compCont->created_at)->format('d-m-Y'),
                                                'modulo' => 'Comprobante Contable #'.$compCont->code, 'debito' =>  $descRet->debito ,
                                                'credito' =>  $descRet->credito , 'concepto' => $compCont->concepto,
                                                'cuenta' => $hijo->code.' - '.$hijo->concepto, 'from' => 6,
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
            foreach ($result as $item){
                if($item['padre_id'] == $first->id){
                    $totD = $totD + $item['debito'];
                    $totC = $totC + $item['credito'];
                }
            }
            $result[] = collect(['debito' => $totD, 'credito' => $totC, 'code' =>  $first->code,
                'concepto' =>  $first->concepto, 'padre_id' =>  $first->padre_id, 'cuenta_id' => $first->id]);
        }

        if ($mes1 == 1) $mes1 = "Enero";
        elseif ($mes1 == 2) $mes1 = "Febrero";
        elseif ($mes1 == 3) $mes1 = "Marzo";

        if ($mes2 == 1) $mes2 = "Enero";
        elseif ($mes2 == 2) $mes2 = "Febrero";
        elseif ($mes2 == 3) $mes2 = "Marzo";

        $trim = $mes1.'-'.$mes2;
        $mes = 0;

        return view('administrativo.contabilidad.libros.balance',compact('result2', 'result',
            'hijosResult','trim','mes'));
    }

    /**
     * Al seleccionar una cuenta se entra a esa funcion para traer los valores y llenarlo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRubrosPUC(Request $request)
    {
        $cuenta = PucAlcaldia::find($request->id);
        $mes = intval($request->mes);

        if (strlen($cuenta->code) == 10) return $this->findHijo($cuenta, $mes);
        elseif (strlen($cuenta->code) == 6) return $this->findlvl4($cuenta, $mes);
        elseif (strlen($cuenta->code) == 4) {

            $lv4 = PucAlcaldia::where('padre_id', $request->id)->get();
            $total = 0;
            foreach ($lv4 as $item){
                $rubrosPUC = PucAlcaldia::where('padre_id',$item->id)->get();
                if ($rubrosPUC->count() >= 1){
                    foreach ($rubrosPUC as $rubroPUC){
                        if ($request->id == 765){
                            //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                            $pagosFin = Pagos::where('estado','1')->get();
                            foreach ($pagosFin as $pagoF){
                                $añoPago = Carbon::parse($pagoF->ff_fin)->format('Y');
                                $añoActual = Carbon::today()->format('Y');
                                if ($añoPago == $añoActual){
                                    if ($mes == 0 ){
                                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                                            if ($descuento->valor > 0){
                                                if ($descuento->desc_municipal_id != null){
                                                    //DESCUENTOS MUNICIPALES
                                                    if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                                        $total = $total + $descuento->valor;
                                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->ff_fin)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                            'total' => '$'.number_format($total,0), 'from' => 1]);
                                                        //return $descuento->descuento_mun;

                                                    }
                                                }
                                            }
                                        }
                                    } elseif ($mes == Carbon::parse($pagoF->ff_fin)->format('m')){
                                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                                            if ($descuento->valor > 0){
                                                if ($descuento->desc_municipal_id != null){
                                                    //DESCUENTOS MUNICIPALES
                                                    if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                                        $total = $total + $descuento->valor;
                                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->ff_fin)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                            'total' => '$'.number_format($total,0), 'from' => 1]);
                                                        //return $descuento->descuento_mun;

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else{
                            //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                            $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                            if (count($ordenPagosPUC) > 0){
                                foreach ($ordenPagosPUC as $op_puc){
                                    if ($op_puc->ordenPago->estado == '1'){
                                        if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                            if ($mes == 0 ){
                                                $total = $total + $op_puc->valor_debito;
                                                $total = $total - $op_puc->valor_credito;
                                                if (isset($op_puc->ordenPago->registros->persona)){
                                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                                } else{
                                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                                    $numIdent = 800197268;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                                    'debito' => '$'.number_format($op_puc->valor_debito,0),'credito' => '$'.number_format($op_puc->valor_credito,0),
                                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 2]);

                                                //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                                if ($op_puc->ordenPago->saldo == 0){
                                                    $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                                    foreach ($pagosOP as $pay){
                                                        if ($op_puc->valor_debito > 0){
                                                            $debito = 0;
                                                            $credito = $pay->valor;
                                                            $total = $total - $credito;
                                                        } else{
                                                            $debito = $pay->valor;
                                                            $credito = 0;
                                                            $total = $total + $debito;
                                                        }
                                                        $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                                            'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                            'total' => '$'.number_format($total,0), 'from' => 2]);
                                                    }
                                                }
                                            } elseif ($mes == Carbon::parse($op_puc->ordenPago->created_at)->format('m')){
                                                $total = $total + $op_puc->valor_debito;
                                                $total = $total - $op_puc->valor_credito;
                                                if (isset($op_puc->ordenPago->registros->persona)){
                                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                                } else{
                                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                                    $numIdent = 800197268;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                                    'debito' => '$'.number_format($op_puc->valor_debito,0),'credito' => '$'.number_format($op_puc->valor_credito,0),
                                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 2]);
                                                //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                                if ($op_puc->ordenPago->saldo == 0){
                                                    $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                                    foreach ($pagosOP as $pay){
                                                        if ($op_puc->valor_debito > 0){
                                                            $debito = 0;
                                                            $credito = $pay->valor;
                                                            $total = $total - $credito;
                                                        } else{
                                                            $debito = $pay->valor;
                                                            $credito = 0;
                                                            $total = $total + $debito;
                                                        }
                                                        $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                                            'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                            'total' => '$'.number_format($total,0), 'from' => 2]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                            $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
                            if (count($pagoBanks) > 0){
                                foreach ($pagoBanks as $pagoBank){
                                    if ($pagoBank->pago->estado == 1){
                                        if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                            if ($mes == 0 ){
                                                $total = $total - $pagoBank->valor;
                                                $pago = Pagos::find($pagoBank->pagos_id);
                                                if (isset($pago->orden_pago->registros->persona)){
                                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                                } else{
                                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                                    $numIdent = 800197268;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pago->code,
                                                    'debito' => '$'.number_format(0,0),
                                                    'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 3]);
                                            } elseif ($mes == Carbon::parse($pagoBank->pago->created_at)->format('m')){
                                                $total = $total - $pagoBank->valor;
                                                $pago = Pagos::find($pagoBank->pagos_id);
                                                if (isset($pago->orden_pago->registros->persona)){
                                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                                } else{
                                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                                    $numIdent = 800197268;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pago->code,
                                                    'debito' => '$'.number_format(0,0),
                                                    'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 3]);
                                            }
                                        }
                                    }
                                }
                            }

                            //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                            $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array(Carbon::today()->format('Y').'-01-01',
                                Carbon::today()->format('Y').'-12-31'))->get();
                            if (count($compsCont) > 0){
                                foreach ($compsCont as $compCont){
                                    if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                                        if ($mes == 0 ){
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
                                                    'total' => '$'.number_format($total,0), 'from' => 4]);
                                            } else{
                                                $total = $total + $compCont->debito;
                                                $total = $total - $compCont->credito;
                                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 4]);
                                            }
                                        } elseif ($mes == Carbon::parse($compCont->comprobante->ff)->format('m')){
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
                                                    'total' => '$'.number_format($total,0), 'from' => 4]);
                                            } else{
                                                $total = $total + $compCont->debito;
                                                $total = $total - $compCont->credito;
                                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 4]);
                                            }
                                        }
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

    public function findHijo($account, $mes){

        $total = 0;
        if ($account->id == 765){
            //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
            $pagosFin = Pagos::where('estado','1')->get();
            foreach ($pagosFin as $pagoF){
                $añoPago = Carbon::parse($pagoF->created_at)->format('Y');
                $añoActual = Carbon::today()->format('Y');
                if ($añoPago == $añoActual){
                    if ($mes == 0 ){
                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                            if ($descuento->valor > 0){
                                if ($descuento->desc_municipal_id != null){
                                    //DESCUENTOS MUNICIPALES
                                    if ($account->code == $descuento->descuento_mun->codigo){
                                        $total = $total + $descuento->valor;
                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 5]);
                                        //return $descuento->descuento_mun;

                                    }
                                }
                            }
                        }
                    } elseif ($mes == Carbon::parse($pagoF->created_at)->format('m')){
                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                            if ($descuento->valor > 0){
                                if ($descuento->desc_municipal_id != null){
                                    //DESCUENTOS MUNICIPALES
                                    if ($account->code == $descuento->descuento_mun->codigo){
                                        $total = $total + $descuento->valor;
                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 5]);
                                        //return $descuento->descuento_mun;

                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else{
            //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
            $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $account->id)->get();
            if (count($ordenPagosPUC) > 0){
                foreach ($ordenPagosPUC as $op_puc){
                    if ($op_puc->ordenPago->estado == '1'){
                        if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                            if ($mes == 0 ){
                                $total = $total + $op_puc->valor_debito;
                                $total = $total - $op_puc->valor_credito;
                                if (isset($op_puc->ordenPago->registros->persona)){
                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                } else{
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                    'debito' => '$'.number_format($op_puc->valor_debito,0), 'credito' => '$'.number_format($op_puc->valor_credito,0),
                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                    'cuenta' => $account->code.' - '.$account->concepto, 'total' => '$'.number_format($total,0), 'from' => 6]);

                                //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                if ($op_puc->ordenPago->saldo == 0){
                                    $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                    foreach ($pagosOP as $pay){
                                        if ($op_puc->valor_debito > 0){
                                            $debito = 0;
                                            $credito = $pay->valor;
                                            $total = $total - $credito;
                                        } else{
                                            $debito = $pay->valor;
                                            $credito = 0;
                                            $total = $total + $debito;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                            'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 6]);
                                    }
                                }
                            } elseif ($mes == Carbon::parse($op_puc->ordenPago->created_at)->format('m')){
                                $total = $total + $op_puc->valor_debito;
                                $total = $total - $op_puc->valor_credito;
                                if (isset($op_puc->ordenPago->registros->persona)){
                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                } else{
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                    'debito' => '$'.number_format($op_puc->valor_debito,0), 'credito' => '$'.number_format($op_puc->valor_credito,0),
                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                    'cuenta' => $account->code.' - '.$account->concepto, 'total' => '$'.number_format($total,0), 'from' => 6]);

                                //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                if ($op_puc->ordenPago->saldo == 0){
                                    $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                    foreach ($pagosOP as $pay){
                                        if ($op_puc->valor_debito > 0){
                                            $debito = 0;
                                            $credito = $pay->valor;
                                            $total = $total - $credito;
                                        } else{
                                            $debito = $pay->valor;
                                            $credito = 0;
                                            $total = $total + $debito;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                            'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 6]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
            $pagoBanks = PagoBanks::where('rubros_puc_id', $account->id)->get();
            if (count($pagoBanks) > 0){
                foreach ($pagoBanks as $pagoBank){
                    if ($pagoBank->pago->estado == 1){
                        if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                            if ($mes == 0 ){
                                $total = $total - $pagoBank->valor;
                                $pago = Pagos::find($pagoBank->pagos_id);
                                if (isset($pago->orden_pago->registros->persona)){
                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                } else{
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($pagoBank->pago->created_at)->format('d-m-Y'),
                                    'modulo' => 'Pago #'.$pago->code, 'debito' => '$'.number_format(0,0),
                                    'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $pago->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 7]);
                            } elseif ($mes == Carbon::parse($pagoBank->pago->created_at)->format('m')){
                                $total = $total - $pagoBank->valor;
                                $pago = Pagos::find($pagoBank->pagos_id);
                                if (isset($pago->orden_pago->registros->persona)){
                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                } else{
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($pagoBank->pago->created_at)->format('d-m-Y'),
                                    'modulo' => 'Pago #'.$pago->code, 'debito' => '$'.number_format(0,0),
                                    'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $pago->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 7]);
                            }
                        }
                    }
                }
            }

            //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
            $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array(Carbon::today()->format('Y').'-01-01',
                Carbon::today()->format('Y').'-12-31'))->get();
            if (count($compsCont) > 0){
                foreach ($compsCont as $compCont){
                    if ($compCont->cuenta_banco == $account->id or $compCont->cuenta_puc_id == $account->id){
                        if ($mes == 0 ){
                            if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                                $user = User::find($compCont->comprobante->persona_id);
                                $tercero = $user->name;
                                $numIdent = $user->email;
                            } else{
                                $persona = Persona::find($compCont->comprobante->persona_id);
                                $tercero = $persona->nombre;
                                $numIdent = $persona->num_dc;
                            }
                            if ($compCont->cuenta_banco == $account->id){
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 8]);
                            } else{
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 8]);
                            }
                        } elseif ($mes == Carbon::parse($compCont->comprobante->ff)->format('m')){
                            if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                                $user = User::find($compCont->comprobante->persona_id);
                                $tercero = $user->name;
                                $numIdent = $user->email;
                            } else{
                                $persona = Persona::find($compCont->comprobante->persona_id);
                                $tercero = $persona->nombre;
                                $numIdent = $persona->num_dc;
                            }
                            if ($compCont->cuenta_banco == $account->id){
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 8]);
                            } else{
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                    'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                    'total' => '$'.number_format($total,0), 'from' => 8]);
                            }
                        }

                    }
                }
            }
        }

        return $result;
    }

    public function findlvl4($account, $mes){
        $rubrosPUC = PucAlcaldia::where('padre_id',$account->id)->get();
        $total = 0;

        if ($rubrosPUC->count() >= 1){
            foreach ($rubrosPUC as $rubroPUC){

                if ($account->id == 765){
                    //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                    $pagosFin = Pagos::where('estado','1')->get();
                    foreach ($pagosFin as $pagoF){
                        $añoPago = Carbon::parse($pagoF->created_at)->format('Y');
                        $añoActual = Carbon::today()->format('Y');
                        if ($añoPago == $añoActual){
                            foreach ($pagoF->orden_pago->descuentos as $descuento){
                                if ($descuento->valor > 0){
                                    if ($descuento->desc_municipal_id != null){
                                        //DESCUENTOS MUNICIPALES
                                        if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                            if ($mes == 0 ){
                                                $total = $total + $descuento->valor;
                                                $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                                $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                                $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                    'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 9]);
                                                //return $descuento->descuento_mun;
                                            } elseif ($mes == Carbon::parse($pagoF->created_at)->format('m')){
                                                $total = $total + $descuento->valor;
                                                $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                                $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                                $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                    'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 9]);
                                                //return $descuento->descuento_mun;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else{
                    //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                    $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                    if (count($ordenPagosPUC) > 0){
                        foreach ($ordenPagosPUC as $op_puc){
                            if ($op_puc->ordenPago->estado == '1'){
                                if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                    if ($mes == 0 ){
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        if (isset($op_puc->ordenPago->registros->persona)){
                                            $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                            $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                            'debito' => '$'.number_format($op_puc->valor_debito,0), 'credito' => '$'.number_format($op_puc->valor_credito,0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 10]);

                                        //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                        if ($op_puc->ordenPago->saldo == 0){
                                            $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                            foreach ($pagosOP as $pay){
                                                if ($op_puc->valor_debito > 0){
                                                    $debito = 0;
                                                    $credito = $pay->valor;
                                                    $total = $total - $credito;
                                                } else{
                                                    $debito = $pay->valor;
                                                    $credito = 0;
                                                    $total = $total + $debito;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                                    'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 10]);
                                            }
                                        }
                                    } elseif ($mes == Carbon::parse($op_puc->ordenPago->created_at)->format('m')){
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        if (isset($op_puc->ordenPago->registros->persona)){
                                            $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                            $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #'.$op_puc->ordenPago->code,
                                            'debito' => '$'.number_format($op_puc->valor_debito,0), 'credito' => '$'.number_format($op_puc->valor_credito,0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 10]);

                                        //SI LA ORDEN DE PAGO TIENE SU SALDO EN 0$ POR ENDE YA FUE PAGADA Y SE DEBE VOLTEAR EL VALOR
                                        if ($op_puc->ordenPago->saldo == 0){
                                            $pagosOP = Pagos::where('orden_pago_id', $op_puc->ordenPago->id)->get();
                                            foreach ($pagosOP as $pay){
                                                if ($op_puc->valor_debito > 0){
                                                    $debito = 0;
                                                    $credito = $pay->valor;
                                                    $total = $total - $credito;
                                                } else{
                                                    $debito = $pay->valor;
                                                    $credito = 0;
                                                    $total = $total + $debito;
                                                }
                                                $result[] = collect(['fecha' => Carbon::parse($pay->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pay->code,
                                                    'debito' => '$'.number_format($debito,0),'credito' => '$'.number_format($credito,0),
                                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pay->concepto, 'cuenta' => $account->code.' - '.$account->concepto,
                                                    'total' => '$'.number_format($total,0), 'from' => 10]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                    $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
                    if (count($pagoBanks) > 0){
                        foreach ($pagoBanks as $pagoBank){
                            if ($pagoBank->pago->estado == 1){
                                if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                    if ($mes == 0 ){
                                        $total = $total - $pagoBank->valor;
                                        $pago = Pagos::find($pagoBank->pagos_id);
                                        if (isset($pago->orden_pago->registros->persona)){
                                            $tercero = $pago->orden_pago->registros->persona->nombre;
                                            $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->pago->created_at)->format('d-m-Y'),
                                            'modulo' => 'Pago #'.$pago->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 11]);
                                    } elseif ($mes == Carbon::parse($pagoBank->pago->created_at)->format('m')){
                                        $total = $total - $pagoBank->valor;
                                        $pago = Pagos::find($pagoBank->pagos_id);
                                        if (isset($pago->orden_pago->registros->persona)){
                                            $tercero = $pago->orden_pago->registros->persona->nombre;
                                            $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->pago->created_at)->format('d-m-Y'),
                                            'modulo' => 'Pago #'.$pago->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 11]);
                                    }
                                }
                            }
                        }
                    }

                    //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                    $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array(Carbon::today()->format('Y').'-01-01',
                        Carbon::today()->format('Y').'-12-31'))->get();
                    if (count($compsCont) > 0){
                        foreach ($compsCont as $compCont){
                            if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                                if ($mes == 0 ){
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
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 12]);
                                    } else{
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 12]);
                                    }
                                } elseif ($mes == Carbon::parse($compCont->comprobante->ff)->format('m')){
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
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 12]);
                                    } else{
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->comprobante->ff)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 12]);
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
}
