<?php
namespace App\Traits;

use App\Model\Hacienda\Presupuesto\PlantillaCuipoEgresos;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Cdp\Cdp;
use Illuminate\Support\Facades\DB;
use App\bpinVigencias;
use App\BPin;

Class PrepEgresosTraits
{
    public function prepEgresos($vigencia, $inicio = null, $final = null){
        $V = $vigencia->id;
        $vigencia_id = $V;

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipoEgresos::all();
        $oldId = "0";
        $oldCode = "0";
        $oldName = "0";

        //LLENADO DEL PRESUPUESTO
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            //PRIMER RUBRO
            if ($data->id < '324') {
                //RUBROS INICIALES
                if ($data->id == '318') {

                    //CDPS
                    if ($inicio != null) $cdpsFind = Cdp::where('vigencia_id', $vigencia_id)->where('jefe_e', '3')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $cdpsFind = Cdp::where('vigencia_id', $vigencia_id)->where('jefe_e', '3')->get();

                    if (count($cdpsFind) > 0) $valueCDPs[] = $cdpsFind->sum('valor');
                    else $valueCDPs[] = 0;

                    //REGISTROS
                    if ($inicio != null) $registrosFind = Registro::where('id','>=', 778)->where('jefe_e','3')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $registrosFind = Registro::where('id','>=', 778)->where('jefe_e','3')->get();

                    if (count($registrosFind) > 0) $valueRegistros[] = $registrosFind->sum('valor');
                    else $valueRegistros[] = 0;

                    //orden pagos
                    if ($inicio != null) $ordenesPago = OrdenPagos::where('estado','1')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $ordenesPago = OrdenPagos::where('estado','1')->get();

                    if (count($ordenesPago) > 0) $valueOrdenPago[] = $ordenesPago->sum('valor');
                    else $valueOrdenPago[] = 0;

                    //pagos
                    if ($inicio != null) $pagosDB = Pagos::where('id','>=',683)->where('estado','1')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $pagosDB = Pagos::where('id','>=',683)->where('estado','1')->get();

                    if (count($pagosDB) > 0) $valuePagos[] = $pagosDB->sum('valor');
                    else $valuePagos[] = 0;

                    dd($valuePagos, $valueOrdenPago );

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos_egresos where code REGEXP CONCAT('^','".$data->code.".')");
                    foreach ($otherRubs as $other) {
                        $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();
                        if($rubroOtherFind->first()) {
                            //2 ADD - 3 RED  - 1 CRED
                            if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                    if ($mov->valor > 0 ){
                                        if ($inicio != null) {
                                            if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio) {
                                                if ($mov->movimiento == "1") {
                                                    $valueRubrosCred[] = $mov->valor;
                                                    $valueRubrosCCred[] = $mov->valor;
                                                } elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                                elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;
                                            }
                                        }else{
                                            if ($mov->movimiento == "1") {
                                                $valueRubrosCred[] = $mov->valor;
                                                $valueRubrosCCred[] = $mov->valor;
                                            } elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                            elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;
                                        }
                                    }
                                }
                            } else {
                                $valueRubrosAdd[] = 0;
                                $valueRubrosRed[] = 0;
                                $valueRubrosCred[] = 0;
                                $valueRubrosCCred[] = 0;
                            }

                        } else {
                            $valueRubros[] = 0;
                            $valueRubrosAdd[] = 0;
                            $valueRubrosRed[] = 0;
                            $valueRubrosCred[] = 0;
                            $valueRubrosCCred[] = 0;
                            $valueCDPs[] = 0;
                            $valueRegistros[] = 0;
                            $valueOrdenPago[] = 0;
                            $valuePagos[] = 0;
                        }
                    }

                    if (!isset($valueRubrosAdd)) {
                        $valueRubrosAdd[] = null;
                        unset($valueRubrosAdd[0]);
                    }

                    if (!isset($valueRubrosRed)) {
                        $valueRubrosRed[] = null;
                        unset($valueRubrosRed[0]);
                    }

                    if (!isset($valueRubrosCred)) {
                        $valueRubrosCred[] = null;
                        unset($valueRubrosCred[0]);
                    }

                    if (!isset($valueRubrosCCred)) {
                        $valueRubrosCCred[] = null;
                        unset($valueRubrosCCred[0]);
                    }

                    if (!isset($valueCDPs)) {
                        $valueCDPs[] = null;
                        unset($valueCDPs[0]);
                    }

                    if (!isset($valueRegistros)) {
                        $valueRegistros[] = null;
                        unset($valueRegistros[0]);
                    }

                    if (!isset($valueOrdenPago)) {
                        $valueOrdenPago[] = null;
                        unset($valueOrdenPago[0]);
                    }

                    if (!isset($valuePagos)) {
                        $valuePagos[] = null;
                        unset($valuePagos[0]);
                    }

                    //PRESUPUESTO DEFINITIVO
                    if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= $vigencia->presupuesto_inicial + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                    else $PDef = $vigencia->presupuesto_inicial + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

                    //SOLO SE MUESTRA EL VALOR DEL PRESUPUESTO CUANDO NO SON USUARIOS DE TIPO SECRETARIA
                    $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name,
                        'presupuesto_inicial' => $vigencia->presupuesto_inicial, 'adicion' => array_sum($valueRubrosAdd),
                        'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs),
                        'registros' => array_sum($valueRegistros), 'saldo_disp' => $PDef - array_sum($valueCDPs),
                        'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos),
                        'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago), 'rubros_disp' => 0, 'codBpin' => '',
                        'codActiv' => '', 'nameActiv' => '','codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '',
                        'codProd' => '', 'codIndProd' => '', 'codProgMGA' => ''];

                    unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                    unset($valueOrdenPago);unset($valuePagos);
                } else {
                    //LLENADO DE PADRES

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos_egresos where code REGEXP CONCAT('^','".$data->code.".')");

                    foreach ($otherRubs as $other) {
                        $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();

                        if($rubroOtherFind->first()) {

                            if($rubroOtherFind->first()->fontsRubro){
                                foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro){
                                    $valueRubros[] = $fuenteRubro->valor;

                                    //RECORRIDO PARA LA OBTENCION DEL VALOR DE CONTRACREDITO
                                    $depRubFont = DependenciaRubroFont::where('rubro_font_id', $fuenteRubro->id)->get();
                                    foreach ($depRubFont as $depRF){
                                        $movRubs = RubrosMov::where('dep_rubro_font_cc_id', $depRF->id)->get();
                                        foreach ($movRubs as $movRub){
                                            if ($movRub->valor > 0){
                                                //SE ALMACENA EL VALOR DE CC
                                                $valueRubrosCCred[] = $movRub->valor;
                                            }
                                        }
                                    }
                                }
                            } else $valueRubros[] = 0;

                            //2 ADD - 3 RED  - 1 CRED

                            if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                    if ($mov->valor > 0 ){
                                        if ($inicio != null){
                                            if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                if ($mov->movimiento == "1") {
                                                    $valueRubrosCred[] = $mov->valor;
                                                    //$valueRubrosCCred[] = $mov->valor;
                                                    $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                    $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                                }
                                                elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                                elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;

                                            }
                                        } else{
                                            if ($mov->movimiento == "1") {
                                                $valueRubrosCred[] = $mov->valor;
                                                //$valueRubrosCCred[] = $mov->valor;
                                                $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                            }
                                            elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                            elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;
                                        }
                                    }
                                }
                            } else {
                                $valueRubrosAdd[] = 0;
                                $valueRubrosRed[] = 0;
                                $valueRubrosCred[] = 0;
                                $valueRubrosCCred[] = 0;
                            }

                            //CDPS
                            foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro){
                                $rubCdpValue = RubrosCdpValor::where('fontsRubro_id', $fuenteRubro->id)->get();
                                if(count($rubCdpValue) > 0){
                                    foreach ($rubCdpValue as $cdp) {
                                        if ($cdp->cdps->jefe_e == "3") {
                                            if ($inicio != null){
                                                if (date('Y-m-d', strtotime($cdp->cdps->created_at)) <= $final and date('Y-m-d', strtotime($cdp->cdps->created_at)) >= $inicio){
                                                    $valueCDPs[] = $cdp->valor;
                                                }
                                            } else $valueCDPs[] = $cdp->valor;

                                            if (count($cdp->cdps->cdpsRegistro) > 0) {
                                                //CONSULTA PARA LOS REGISTROS
                                                $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                foreach ($cdpsRegValue as $cdpRValue){
                                                    if ($cdpRValue->valor != 0) {
                                                        if ($cdpRValue->registro->jefe_e == 3) {
                                                            //VALOR REGISTROS
                                                            if ($inicio != null){
                                                                if (date('Y-m-d', strtotime($cdpRValue->registro->created_at)) <= $final and date('Y-m-d', strtotime($cdpRValue->registro->created_at)) >= $inicio){
                                                                    $valueRegistros[] = $cdpRValue->valor;
                                                                }
                                                            } else $valueRegistros[] = $cdpRValue->valor;
                                                            //VALOR ORDENES DE PAGO
                                                            $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRValue->id)->get();
                                                            if (count($ordenPagoRubros) > 0){
                                                                $ordenPagoRubro = $ordenPagoRubros->first();
                                                                if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRValue->registro_id){
                                                                    if ($inicio != null){
                                                                        if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                            $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                        }
                                                                    } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                    if ($ordenPagoRubro->orden_pago->pago){
                                                                        if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                            if ($inicio != null){
                                                                                if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                    $valuePagos[] = $ordenPagoRubro->valor;
                                                                                }
                                                                            } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else $valueRegistros[] = 0;
                                        }
                                    }
                                }else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;
                            }
                        } else {
                            $valueRubros[] = 0;
                            $valueRubrosAdd[] = 0;
                            $valueRubrosRed[] = 0;
                            $valueRubrosCred[] = 0;
                            $valueRubrosCCred[] = 0;
                            $valueCDPs[] = 0;
                            $valueRegistros[] = 0;
                            $valueOrdenPago[] = 0;
                            $valuePagos[] = 0;
                        }
                    }

                    //PRESUPUESTO DEFINITIVO
                    if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= array_sum($valueRubros) + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                    else $PDef = array_sum($valueRubros) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

                    //LLENADO DE PADRES
                    if (array_sum($valueRubros) > 0){
                        $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code,
                            'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                            'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed),
                            'credito' => array_sum($valueRubrosCred), 'ccredito' => array_sum($valueRubrosCCred),
                            'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs),
                            'registros' => array_sum($valueRegistros),
                            'saldo_disp' => $PDef - array_sum($valueCDPs),
                            'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros),
                            'ordenes_pago' => array_sum($valueOrdenPago), 'pagos' => array_sum($valuePagos),
                            'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos),
                            'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                            'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '','codDep' => '',
                            'dep' => '', 'depRubID' => '', 'fuente' => '', 'codProd' => '', 'codIndProd' => '',
                            'codProgMGA' => ''];
                    }


                    if (!isset($valueRubros)) {
                        $valueRubros[] = null;
                        unset($valueRubros[0]);
                    } else unset($valueRubros);

                    if (!isset($valueRubrosAdd)) {
                        $valueRubrosAdd[] = null;
                        unset($valueRubrosAdd[0]);
                    } else unset($valueRubrosAdd);

                    if (!isset($valueRubrosRed)) {
                        $valueRubrosRed[] = null;
                        unset($valueRubrosRed[0]);
                    } else unset($valueRubrosRed);

                    if (!isset($valueRubrosCred)) {
                        $valueRubrosCred[] = null;
                        unset($valueRubrosCred[0]);
                    } else unset($valueRubrosCred);

                    if (!isset($valueRubrosCCred)) {
                        $valueRubrosCCred[] = null;
                        unset($valueRubrosCCred[0]);
                    } else unset($valueRubrosCCred);

                    if (!isset($rubrosCC)) {
                        $rubrosCC[] = null;
                        unset($rubrosCC[0]);
                    } else unset($rubrosCC);

                    if (!isset($valueCDPs)) {
                        $valueCDPs[] = null;
                        unset($valueCDPs[0]);
                    } else unset($valueCDPs);

                    if (!isset($valueRegistros)) {
                        $valueRegistros[] = null;
                        unset($valueRegistros[0]);
                    } else unset($valueRegistros);

                    if (!isset($valueOrdenPago)) {
                        $valueOrdenPago[] = null;
                        unset($valueOrdenPago[0]);
                    } else unset($valueOrdenPago);

                    if (!isset($valuePagos)) {
                        $valuePagos[] = null;
                        unset($valuePagos[0]);
                    } else unset($valuePagos);

                }

            } elseif (count($rubro) > 0) {

                $key = array_search($oldId, array_column($presupuesto, 'id'));

                if ($key == false) {
                    //VALIDACION DE LOS PADRES DE LOS PADRES

                    $plantillaCuipoFind = PlantillaCuipoEgresos::find($oldId);
                    if ($plantillaCuipoFind){
                        $found_key = array_search($plantillaCuipoFind->padre_id, array_column($presupuesto, 'id'));
                        if ($found_key === false){
                            $plantillaCuipoFaltante = PlantillaCuipoEgresos::find($plantillaCuipoFind->padre_id);
                            $prep = $this->llenarPresupuesto($plantillaCuipoFaltante, $vigencia_id);
                            $presupuesto[] = $prep;
                        }
                    }

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos_egresos where code REGEXP CONCAT('^','".$oldCode.".')");
                    if($otherRubs and $oldCode != null) {
                        foreach ($otherRubs as $other) {
                            $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();
                            if($rubroOtherFind->first()) {

                                $exit = false;
                                if($rubroOtherFind->first()->fontsRubro){
                                    foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) {
                                        $valueRubros[] = $fuenteRubro->valor;
                                        $valueRubrosDisp[] = $fuenteRubro->valor_disp;

                                        //RECORRIDO PARA LA OBTENCION DEL VALOR DE CONTRACREDITO
                                        $depRubFont = DependenciaRubroFont::where('rubro_font_id', $fuenteRubro->id)->get();
                                        foreach ($depRubFont as $depRF){
                                            $movRubs = RubrosMov::where('dep_rubro_font_cc_id', $depRF->id)->get();
                                            foreach ($movRubs as $movRub){
                                                if ($movRub->valor > 0){
                                                    //SE ALMACENA EL VALOR DE CC
                                                    $valueRubrosCCred[] = $movRub->valor;
                                                }
                                            }
                                        }

                                        //VALIDACION PARA LAS ADICIONES Y REDUCCIONES
                                        foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                            if ($mov->valor > 0 ){
                                                if ($mov->movimiento == "2") {
                                                    if ($mov->fonts_rubro_id == $fuenteRubro->id) {
                                                        if ($inicio != null){
                                                            if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                                $valueRubrosAdd[] = $mov->valor;
                                                            }
                                                        } else $valueRubrosAdd[] = $mov->valor;
                                                    }
                                                }
                                                elseif ($mov->movimiento == "3") {
                                                    if ($mov->fonts_rubro_id == $fuenteRubro->id) {
                                                        if ($inicio != null){
                                                            if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                                $valueRubrosRed[] = $mov->valor;
                                                            }
                                                        } else $valueRubrosRed[] = $mov->valor;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;


                                if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                    foreach ($rubroOtherFind->first()->rubrosMov as $mov){

                                        if ($mov->valor > 0 ){
                                            if ($mov->movimiento == "1") {
                                                if ($inicio != null){
                                                    if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                        $valueRubrosCred[] = $mov->valor;
                                                        //$valueRubrosCCred[] = $mov->valor;
                                                        $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                        $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                                    }
                                                } else {
                                                    $valueRubrosCred[] = $mov->valor;
                                                    //$valueRubrosCCred[] = $mov->valor;
                                                    $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                    $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $valueRubrosAdd[] = 0;
                                    $valueRubrosRed[] = 0;
                                    $valueRubrosCred[] = 0;
                                    $valueRubrosCCred[] = 0;
                                }

                                //CDPS
                                foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro){
                                    $rubCdpValue = RubrosCdpValor::where('fontsRubro_id', $fuenteRubro->id)->get();
                                    $depRubroFontValue = DependenciaRubroFont::where('rubro_font_id', $fuenteRubro->id)->get();

                                    if(count($rubCdpValue) > 0){
                                        foreach ($rubCdpValue as $cdp) {
                                            if ($cdp->cdps->jefe_e == "3") {
                                                if ($inicio != null){
                                                    if (date('Y-m-d', strtotime($cdp->cdps->created_at)) <= $final and date('Y-m-d', strtotime($cdp->cdps->created_at)) >= $inicio){
                                                        $valueCDPs[] = $cdp->valor;
                                                    }
                                                } else $valueCDPs[] = $cdp->valor;
                                                if (count($cdp->cdps->cdpsRegistro) > 0){
                                                    //CONSULTA PARA LOS REGISTROS
                                                    $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                    foreach ($cdpsRegValue as $valueCdpReg){
                                                        if ($valueCdpReg->valor != 0){
                                                            if ($valueCdpReg->registro->jefe_e == 3){
                                                                //VALOR REGISTROS
                                                                if ($inicio != null){
                                                                    if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                                        $valueRegistros[] = $valueCdpReg->valor;
                                                                    }
                                                                } else $valueRegistros[] = $valueCdpReg->valor;

                                                                //ID REGISTROS
                                                                $IDRegistros[] = $valueCdpReg->registro_id;
                                                                //VALOR ORDENES DE PAGO
                                                                $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                                if (count($ordenPagoRubros) > 0){
                                                                    $ordenPagoRubro = $ordenPagoRubros->first();
                                                                    if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                                        if ($inicio != null){
                                                                            if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                                $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                        if ($ordenPagoRubro->orden_pago->pago){
                                                                            if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                                if ($inicio != null){
                                                                                    if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                        $valuePagos[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                            }
                                        }
                                    } elseif (count($depRubroFontValue) > 0){
                                        //VALIDATE CDPs INVERSION
                                        foreach ($depRubroFontValue as $depRubValue){
                                            $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $depRubValue->id)->get();
                                            foreach ($bpinCdpValues as $bpinCdpValue){


                                                if ($bpinCdpValue->cdp->jefe_e == "3") {
                                                    if ($inicio != null){
                                                        if (date('Y-m-d', strtotime($bpinCdpValue->cdp->created_at)) <= $final and date('Y-m-d', strtotime($bpinCdpValue->cdp->created_at)) >= $inicio){
                                                            $valueCDPs[] = $bpinCdpValue->valor;
                                                        }
                                                    } else $valueCDPs[] = $bpinCdpValue->valor;

                                                    if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                                        //CONSULTA PARA LOS REGISTROS
                                                        $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $fuenteRubro->id)->where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                                        foreach ($cdpsRegValue as $valueCdpReg){
                                                            if ($valueCdpReg->valor != 0){
                                                                if ($valueCdpReg->registro->jefe_e == 3){
                                                                    //VALOR REGISTROS
                                                                    if ($inicio != null){
                                                                        if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                                            $valueRegistros[] = $valueCdpReg->valor;
                                                                        }
                                                                    } else $valueRegistros[] = $valueCdpReg->valor;


                                                                    //ID REGISTROS
                                                                    $IDRegistros[] = $valueCdpReg->registro_id;
                                                                    //VALOR ORDENES DE PAGO
                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                                    if (count($ordenPagoRubros) > 0){
                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                                            if ($inicio != null){
                                                                                if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                                    $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                                }
                                                                            } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                                    if ($inicio != null){
                                                                                        if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                            $valuePagos[] = $ordenPagoRubro->valor;
                                                                                        }
                                                                                    } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                                }
                                            }
                                        }

                                    }
                                    else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;
                                    $valueRegistros[] = 0; $IDRegistros[] = 0;
                                }

                            } else $valueRubros[] = 0;$valueCDPs[] = 0;$valueRegistros[] = 0;$valueOrdenPago[] = 0; $valuePagos[] = 0; $valueRubrosDisp[] = 0;
                        }

                        //if ($oldCode == '2.3.2.02.02' ) dd($valueRubros, $otherRubs);


                        if (!isset($valueRubrosAdd)) {
                            $valueRubrosAdd[] = null;
                            unset($valueRubrosAdd[0]);
                        }

                        if (!isset($valueRubrosRed)) {
                            $valueRubrosRed[] = null;
                            unset($valueRubrosRed[0]);
                        }

                        if (!isset($valueRubrosCred)) {
                            $valueRubrosCred[] = null;
                            unset($valueRubrosCred[0]);
                        }

                        if (!isset($valueRubrosCCred)) {
                            $valueRubrosCCred[] = null;
                            unset($valueRubrosCCred[0]);
                        }

                        if (!isset($rubrosCC)) {
                            $rubrosCC[] = null;
                            unset($rubrosCC[0]);
                        }

                        if (!isset($valueCDPs)) {
                            $valueCDPs[] = null;
                            unset($valueCDPs[0]);
                        }

                        if (!isset($valueRegistros)) {
                            $valueRegistros[] = null;
                            unset($valueRegistros[0]);
                        }

                        if (!isset($valueOrdenPago)) {
                            $valueOrdenPago[] = null;
                            unset($valueOrdenPago[0]);
                        }

                        if (!isset($valuePagos)) {
                            $valuePagos[] = null;
                            unset($valuePagos[0]);
                        }

                        if (!isset($valueRubros)) {
                            $valueRubros[] = null;
                            unset($valueRubros[0]);
                        }

                        if (!isset($valueRubrosDisp)) {
                            $valueRubrosDisp[] = null;
                            unset($valueRubrosDisp[0]);
                        }

                        //PRESUPUESTO DEFINITIVO
                        if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= array_sum($valueRubros) + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                        else $PDef = array_sum($valueRubros) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

                        if ($PDef > 0){

                            $presupuesto[] = ['id_rubro' => 0 ,'id' => $oldId, 'cod' => $oldCode, 'name' => $oldName, 'presupuesto_inicial' => array_sum($valueRubros),
                                'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '',
                                'codProd' => '', 'codIndProd' => '', 'codProgMGA' => ''];
                        }

                        unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                        unset($valueOrdenPago);unset($valuePagos);unset($valueRubros);unset($valueRubrosDisp);unset($rubrosCC);
                    }
                }


                if($rubro->first()->fontsRubro){
                    $plantillaCuipoFind = PlantillaCuipoEgresos::find($rubro->first()->plantilla_cuipos_id );
                    if(isset(end($presupuesto)['padreID'])){
                        if (end($presupuesto)['id'] != $plantillaCuipoFind->padre_id){
                            if (end($presupuesto)['padreID'] != $plantillaCuipoFind->padre_id){
                                $found_key = array_search($plantillaCuipoFind->padre_id, array_column($presupuesto, 'id'));
                                if ($found_key === false){
                                    //dd(end($presupuesto), $rubro->first(), $plantillaCuipoFind, $found_key);

                                }
                            }
                        }
                    } else {
                        $found_key = array_search($plantillaCuipoFind->padre_id, array_column($presupuesto, 'id'));
                        if ($found_key === false){
                            //dd(end($presupuesto), $rubro->first(), $plantillaCuipoFind);
                        }
                    }

                    //RUBROS HIJOS
                    //EN ESTA VALIDACION SE MUESTRAN LOS VALORES DE RUBROS USADOS DEPENDIENDO LA DEP DEL USUARIO
                    $exit = false;
                    foreach ($rubro->first()->fontsRubro as $itemFont){
                        //VALIDACION PARA LAS ADICIONES Y REDUCCIONES
                        if(count($itemFont->rubrosMov) > 0){
                            foreach ($itemFont->rubrosMov as $mov){
                                if ($mov->valor > 0 ){
                                    if ($mov->movimiento == "2") {
                                        if ($mov->fonts_rubro_id == $itemFont->id) {
                                            if ($inicio != null){
                                                if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                    $valueRubrosAdd[] = $mov->valor;
                                                }
                                            } else $valueRubrosAdd[] = $mov->valor;
                                        }
                                    }
                                    elseif ($mov->movimiento == "3") {
                                        if ($mov->fonts_rubro_id == $itemFont->id) {
                                            if ($inicio != null){
                                                if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                    $valueRubrosRed[] = $mov->valor;
                                                }
                                            } else $valueRubrosRed[] = $mov->valor;
                                        }
                                    }
                                }
                            }
                        } else {
                            $valueRubrosAdd[] = 0;
                            $valueRubrosRed[] = 0;
                        }

                        if (count($itemFont->dependenciaFont) > 0){
                            foreach ($itemFont->dependenciaFont as $depFont){

                                $value[] = $depFont->value;
                                $valueRubrosDisp[] = $depFont->saldo;

                                //VALORES DE CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                if ($inicio != null) $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)
                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                else $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)->get();

                                if(count($rubrosCredMov) > 0) $valueRubrosCred[] = $rubrosCredMov->sum('valor');
                                else $valueRubrosCred[] = 0;

                                //VALORES DE CONTRA CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                if ($inicio != null) $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)
                                    ->whereBetween('created_at',array($inicio, $final))->get();
                                else $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)->get();

                                if(count($rubrosCCMov) > 0) $valueRubrosCCred[] = $rubrosCCMov->sum('valor');
                                else $valueRubrosCCred[] = 0;

                                //BPIN
                                $bpinVigen = bpinVigencias::where('dep_rubro_id', $depFont->id)->where('vigencia_id',$vigencia_id)->get();

                                if (count($bpinVigen) > 0){

                                    //AL SER UN RUBRO DE INVERSION SE REALIZA EL LLENADO DE MANERA DISTINTA LOS
                                    //VALORES DE LOS CDPs
                                    $codBpin = $bpinVigen->first()->bpin->cod_proyecto;
                                    $codActiv = $bpinVigen->first()->bpin->cod_actividad;
                                    $nameActiv = $bpinVigen->first()->bpin->actividad;
                                    $codProd = $bpinVigen->first()->bpin->cod_producto;
                                    $codIndProd = $bpinVigen->first()->bpin->cod_indicador;
                                    $codProgMGA = $bpinVigen->first()->bpin->cod_sector;

                                    if (isset($rubrosCC)){
                                        foreach ($rubrosCC as $cc) if ($cc['id'] == $depFont->id) $valueRubrosCCred[] = $cc['value'];
                                    }

                                    $bpinCdpValor = BpinCdpValor::where('cod_actividad', $bpinVigen->first()->bpin->cod_actividad)->get();
                                    if (count($bpinCdpValor) > 0){
                                        foreach ($bpinCdpValor as $bpinCDP){
                                            $bpinArray = BPin::where('cod_actividad', $bpinCDP->cod_actividad)->first();
                                            if ($bpinCDP->cdp->jefe_e == "3" and  $bpinCDP->cdp->vigencia_id == $vigencia_id){
                                                //VALIDACION DE SI LA ACTIVIDAD CORRESPONDE A LA FUENTE DEL RUBRO DE LA DEP
                                                if ($bpinCDP->dependencia_rubro_font_id != null){
                                                    if ($bpinCDP->dependencia_rubro_font_id == $depFont->id) {
                                                        if ($inicio != null){
                                                            if (date('Y-m-d', strtotime($bpinCDP->cdp->created_at)) <= $final and date('Y-m-d', strtotime($bpinCDP->cdp->created_at)) >= $inicio){
                                                                $valueCDPs[] = $bpinCDP->valor;
                                                            }
                                                        } else $valueCDPs[] = $bpinCDP->valor;
                                                    }
                                                } else {
                                                    if ($inicio != null){
                                                        if (date('Y-m-d', strtotime($bpinCDP->cdp->created_at)) <= $final and date('Y-m-d', strtotime($bpinCDP->cdp->created_at)) >= $inicio){
                                                            $valueCDPs[] = $bpinCDP->valor;
                                                        }
                                                    } else $valueCDPs[] = $bpinCDP->valor;
                                                }
                                                $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCDP->cdp->id)
                                                    ->where('bpin_cdp_valor_id', $bpinCDP->id)->get();
                                                if (count($cdpsRegValue) > 0){
                                                    //CONSULTA PARA LOS REGISTROS
                                                    foreach ($cdpsRegValue as $valueCdpReg){
                                                        if ($valueCdpReg->valor != 0){
                                                            if ($valueCdpReg->registro->jefe_e == 3){
                                                                if ($itemFont->id == $valueCdpReg->fontsRubro_id){
                                                                    $validateValuedepFont = BpinCdpValor::find($valueCdpReg->bpin_cdp_valor_id);
                                                                    if (isset($validateValuedepFont->dependencia_rubro_font_id)){
                                                                        if ($validateValuedepFont->dependencia_rubro_font_id == $depFont->id){
                                                                            //VALOR REGISTROS
                                                                            if ($inicio != null){
                                                                                if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                                                    $valueRegistros[] = $valueCdpReg->valor;
                                                                                }
                                                                            } else $valueRegistros[] = $valueCdpReg->valor;
                                                                            //ID REGISTROS
                                                                            $IDRegistros[] = $valueCdpReg->registro_id;
                                                                            //VALOR ORDENES DE PAGO
                                                                            $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                                            if (count($ordenPagoRubros) > 0){
                                                                                $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                                                    if ($inicio != null){
                                                                                        if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                                            $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                                        }
                                                                                    } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                                    if ($ordenPagoRubro->orden_pago->pago){
                                                                                        if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                                            if ($inicio != null){
                                                                                                if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                                    $valuePagos[] = $ordenPagoRubro->valor;
                                                                                                }
                                                                                            } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    } else{
                                                                        //VALOR REGISTROS
                                                                        if ($inicio != null){
                                                                            if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                                                $valueRegistros[] = $valueCdpReg->valor;
                                                                            }
                                                                        } else $valueRegistros[] = $valueCdpReg->valor;
                                                                        //ID REGISTROS
                                                                        $IDRegistros[] = $valueCdpReg->registro_id;
                                                                        //VALOR ORDENES DE PAGO
                                                                        $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                                        if (count($ordenPagoRubros) > 0){
                                                                            $ordenPagoRubro = $ordenPagoRubros->first();
                                                                            if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                                                if ($inicio != null){
                                                                                    if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                                        $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                                if ($ordenPagoRubro->orden_pago->pago){
                                                                                    if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                                        if ($inicio != null){
                                                                                            if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                                $valuePagos[] = $ordenPagoRubro->valor;
                                                                                            }
                                                                                        } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                            }
                                        }

                                    } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;
                                    $valueRegistros[] = 0; $IDRegistros[] = 0;

                                } else{
                                    $codBpin = "";
                                    $codActiv = "";
                                    $nameActiv = "";
                                    $codProd = "";
                                    $codIndProd = "";
                                    $codProgMGA = "";

                                    if (isset($rubrosCC)){
                                        foreach ($rubrosCC as $cc) if ($cc['id'] == $depFont->id) $valueRubrosCCred[] = $cc['value'];
                                    }

                                    //CDPS
                                    $rubCdpValue = RubrosCdpValor::where('fontsDep_id', $depFont->id)->get();
                                    if(count($rubCdpValue) > 0){
                                        foreach ($rubCdpValue as $cdp) {
                                            if ($cdp->cdps->jefe_e == "3") {
                                                if ($inicio != null){
                                                    if (date('Y-m-d', strtotime($cdp->cdps->created_at)) <= $final and date('Y-m-d', strtotime($cdp->cdps->created_at)) >= $inicio){
                                                        $valueCDPs[] = $cdp->valor;
                                                    }
                                                } else $valueCDPs[] = $cdp->valor;
                                                if (count($cdp->cdps->cdpsRegistro) > 0){
                                                    //CONSULTA PARA LOS REGISTROS
                                                    $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                    foreach ($cdpsRegValue as $data){
                                                        if ($data->valor != 0){
                                                            if ($data->registro->jefe_e == 3){
                                                                //VALOR REGISTROS
                                                                if ($inicio != null){
                                                                    if (date('Y-m-d', strtotime($data->registro->created_at)) <= $final and date('Y-m-d', strtotime($data->registro->created_at)) >= $inicio){
                                                                        $valueRegistros[] = $data->valor;
                                                                    }
                                                                } else $valueRegistros[] = $data->valor;
                                                                //ID REGISTROS
                                                                $IDRegistros[] = $data->registro_id;
                                                                //VALOR ORDENES DE PAGO
                                                                $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $data->id)->get();
                                                                if (count($ordenPagoRubros) > 0){
                                                                    $ordenPagoRubro = $ordenPagoRubros->first();
                                                                    if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $data->registro_id){
                                                                        if ($inicio != null){
                                                                            if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                                $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                        if ($ordenPagoRubro->orden_pago->pago){
                                                                            if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                                if ($inicio != null){
                                                                                    if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                        $valuePagos[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                            }
                                        }
                                    } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;
                                    $valueRegistros[] = 0; $IDRegistros[] = 0;
                                }

                                if (!isset($value)){
                                    $value[] = null;
                                    unset($value[0]);
                                }

                                if (!isset($valueRubrosDisp)){
                                    $valueRubrosDisp[] = null;
                                    unset($valueRubrosDisp[0]);
                                }

                                if (!isset($valueRubrosAsign)){
                                    $valueRubrosAsign[] = null;
                                    unset($valueRubrosAsign[0]);
                                }

                                if (!isset($valueRubrosAdd)) {
                                    $valueRubrosAdd[] = null;
                                    unset($valueRubrosAdd[0]);
                                }

                                if (!isset($valueRubrosRed)) {
                                    $valueRubrosRed[] = null;
                                    unset($valueRubrosRed[0]);
                                }

                                if (!isset($valueRubrosCred)) {
                                    $valueRubrosCred[] = null;
                                    unset($valueRubrosCred[0]);
                                }

                                if (!isset($valueRubrosCCred)) {
                                    $valueRubrosCCred[] = null;
                                    unset($valueRubrosCCred[0]);
                                }

                                if (!isset($rubrosCC)) {
                                    $rubrosCC[] = null;
                                    unset($rubrosCC[0]);
                                }

                                if (!isset($valueCDPs)) {
                                    $valueCDPs[] = null;
                                    unset($valueCDPs[0]);
                                }

                                if (!isset($valueRegistros)) {
                                    $valueRegistros[] = null;
                                    unset($valueRegistros[0]);
                                }

                                if (!isset($valueOrdenPago)) {
                                    $valueOrdenPago[] = null;
                                    unset($valueOrdenPago[0]);
                                }

                                if (!isset($valuePagos)) {
                                    $valuePagos[] = null;
                                    unset($valuePagos[0]);
                                }

                                if (!isset($IDRegistros)) {
                                    $IDRegistros[] = null;
                                    unset($IDRegistros[0]);
                                }

                                //PRESUPUESTO DEFINITIVO
                                if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= array_sum($value) + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                                else $PDef = array_sum($value) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                                $code = $depFont->dependencias->num.'.'.$depFont->dependencias->sec;


                                if ($PDef > 0 or array_sum($value) > 0){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id,
                                        'codProd' => $codProd, 'codIndProd' => $codIndProd, 'codProgMGA' => $codProgMGA];

                                } elseif(array_sum($valueRubrosCCred) == array_sum($value)){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id,
                                        'codProd' => $codProd, 'codIndProd' => $codIndProd, 'codProgMGA' => $codProgMGA];

                                } elseif(array_sum($valueRubrosCCred) == array_sum($valueRubrosAdd)){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id,
                                        'codProd' => $codProd, 'codIndProd' => $codIndProd, 'codProgMGA' => $codProgMGA];
                                    
                                } elseif(array_sum($valueRubrosCred) > 0){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id,
                                        'codProd' => $codProd, 'codIndProd' => $codIndProd, 'codProgMGA' => $codProgMGA];
                                }

                                unset($value);unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                                unset($valueOrdenPago);unset($valuePagos);unset($valueRubrosDisp);unset($rubrosCC);unset($valueRubrosAsign);unset($IDRegistros);
                            }
                        } else $value[] = $itemFont->valor; $valueRubrosDisp[] = $itemFont->valor_disp; $valueRubrosAsign[] = $itemFont->valor_disp_asign;
                    }

                } else {
                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro->first()->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => 0,
                        'adicion' => 0, 'reduccion' => 0, 'credito' => 0, 'ccredito' => 0, 'presupuesto_def' => 0, 'cdps' => 0, 'registros' => 0,
                        'saldo_disp' => 0, 'ordenes_pago' => 0, 'pagos' => 0, 'cuentas_pagar' => 0, 'reservas' => 0, 'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '',
                        'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '', 'codProd' => '', 'codIndProd' => '',
                        'codProgMGA' => ''];
                }
            } elseif (count($rubro) == 0){
                if ($data->id == 465 or $data->id == 514 or $data->id == 527 or $data->id == 543 or $data->id == 551 or
                    $data->id == 584 or $data->id == 589 or $data->id == 624 or $data->id == 783 or $data->id == 827 or
                    $data->id == 923 or $data->id == 924 or $data->id == 925 or $data->id == 1014 or $data->id == 1026 or
                    $data->id == 1044 or $data->id == 1046 or $data->id == 1070) {
                    $found_key = array_search($data->padre_id, array_column($presupuesto, 'id'));
                    if ($found_key === false){
                        $plantillaCuipoFaltante = PlantillaCuipoEgresos::find($data->padre_id);
                        $found_key2 = array_search($plantillaCuipoFaltante->padre_id, array_column($presupuesto, 'id'));
                        if ($found_key2 === false){
                            $plantillaCuipoFaltante2 = PlantillaCuipoEgresos::find($plantillaCuipoFaltante->padre_id);
                            $found_key3 = array_search($plantillaCuipoFaltante2->padre_id, array_column($presupuesto, 'id'));
                            if ($found_key3 === false){
                                $plantillaCuipoFaltante3 = PlantillaCuipoEgresos::find($plantillaCuipoFaltante2->padre_id);
                                //LLENADO DEL PRESUPUESTO CON VALORES DE FECHA EN CASO DE REPORTE DE EJECUCION
                                if ($inicio != null) $prep = $this->llenarPresupuesto($plantillaCuipoFaltante3, $vigencia_id, $inicio, $final);
                                else $prep = $this->llenarPresupuesto($plantillaCuipoFaltante3, $vigencia_id);

                                $presupuesto[] = $prep;
                            }

                            if ($inicio != null) $prep = $this->llenarPresupuesto($plantillaCuipoFaltante2, $vigencia_id, $inicio, $final );
                            else $prep = $this->llenarPresupuesto($plantillaCuipoFaltante2, $vigencia_id);

                            $presupuesto[] = $prep;
                        }
                        if ($inicio != null) $prep = $this->llenarPresupuesto($plantillaCuipoFaltante, $vigencia_id, $inicio, $final );
                        else $prep = $this->llenarPresupuesto($plantillaCuipoFaltante, $vigencia_id);

                        $presupuesto[] = $prep;
                    }
                    if ($inicio != null) $prep = $this->llenarPresupuesto($data, $vigencia_id, $inicio, $final );
                    else $prep = $this->llenarPresupuesto($data, $vigencia_id);

                    $presupuesto[] = $prep;

                }
            }
            if ($data->hijo == 0) {
                $oldId = $data->id;
                $oldCode = $data->code;
                $oldName = $data->name;
            }
        }

        return $presupuesto;
    }

    public function llenarPresupuesto($data, $vigencia_id, $inicio = null, $final = null){
        $otherRubs = DB::select("SELECT * from plantilla_cuipos_egresos where code REGEXP CONCAT('^','".$data->code.".')");
        if($otherRubs) {
            foreach ($otherRubs as $other) {
                $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();
                if($rubroOtherFind->first()) {

                    $exit = false;
                    if($rubroOtherFind->first()->fontsRubro){
                        foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) {
                            $valueRubros[] = $fuenteRubro->valor;
                            $valueRubrosDisp[] = $fuenteRubro->valor_disp;

                            //VALIDACION PARA LAS ADICIONES Y REDUCCIONES
                            foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                if ($mov->valor > 0 ){
                                    if ($mov->movimiento == "2") {
                                        if ($mov->fonts_rubro_id == $fuenteRubro->id) {
                                            if ($inicio != null){
                                                if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                    $valueRubrosAdd[] = $mov->valor;
                                                }
                                            } else $valueRubrosAdd[] = $mov->valor;
                                        }
                                    }
                                    elseif ($mov->movimiento == "3") {
                                        if ($mov->fonts_rubro_id == $fuenteRubro->id) {
                                            if ($inicio != null){
                                                if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                    $valueRubrosRed[] = $mov->valor;
                                                }
                                            } else $valueRubrosRed[] = $mov->valor;
                                        }
                                    }
                                }
                            }

                            if (count($fuenteRubro->dependenciaFont) > 0) {
                                foreach ($fuenteRubro->dependenciaFont as $depFont) {

                                    $value[] = $depFont->value;
                                    $valueRubrosDisp[] = $depFont->saldo;

                                    //VALORES DE CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                    if ($inicio != null) $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)
                                        ->whereBetween('created_at',array($inicio, $final))->get();
                                    else $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)->get();

                                    if (count($rubrosCredMov) > 0) $valueRubrosCred[] = $rubrosCredMov->sum('valor');
                                    else $valueRubrosCred[] = 0;

                                    //VALORES DE CONTRA CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                    if ($inicio != null) $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)
                                        ->whereBetween('created_at',array($inicio, $final))->get();
                                    else $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)->get();

                                    if (count($rubrosCCMov) > 0) $valueRubrosCCred[] = $rubrosCCMov->sum('valor');
                                    else $valueRubrosCCred[] = 0;
                                }
                            }

                        }
                    } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;

                    //CDPS
                    foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro){

                        $rubCdpValue = RubrosCdpValor::where('fontsRubro_id', $fuenteRubro->id)->get();
                        $depRubroFontValue = DependenciaRubroFont::where('rubro_font_id', $fuenteRubro->id)->get();

                        if(count($rubCdpValue) > 0){
                            foreach ($rubCdpValue as $cdp) {
                                if ($cdp->cdps->jefe_e == "3") {
                                    if ($inicio != null){
                                        if (date('Y-m-d', strtotime($cdp->cdps->created_at)) <= $final and date('Y-m-d', strtotime($cdp->cdps->created_at)) >= $inicio){
                                            $valueCDPs[] = $cdp->valor;
                                        }
                                    } else $valueCDPs[] = $cdp->valor;

                                    if (count($cdp->cdps->cdpsRegistro) > 0){
                                        //CONSULTA PARA LOS REGISTROS
                                        $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                        foreach ($cdpsRegValue as $valueCdpReg){
                                            if ($valueCdpReg->valor != 0){
                                                if ($valueCdpReg->registro->jefe_e == 3){
                                                    //VALOR REGISTROS
                                                    if ($inicio != null){
                                                        if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                            $valueRegistros[] = $valueCdpReg->valor;
                                                        }
                                                    } else $valueRegistros[] = $valueCdpReg->valor;
                                                    //ID REGISTROS
                                                    $IDRegistros[] = $valueCdpReg->registro_id;
                                                    //VALOR ORDENES DE PAGO
                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                    if (count($ordenPagoRubros) > 0){
                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                            if ($inicio != null){
                                                                if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                    $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                }
                                                            } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                    if ($inicio != null){
                                                                        if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                            $valuePagos[] = $ordenPagoRubro->valor;
                                                                        }
                                                                    } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                }
                            }
                        } elseif (count($depRubroFontValue) > 0){
                            //VALIDATE CDPs INVERSION
                            foreach ($depRubroFontValue as $depRubValue){
                                $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $depRubValue->id)->get();
                                foreach ($bpinCdpValues as $bpinCdpValue){


                                    if ($bpinCdpValue->cdp->jefe_e == "3") {
                                        if ($inicio != null){
                                            if (date('Y-m-d', strtotime($bpinCdpValue->cdp->created_at)) <= $final and date('Y-m-d', strtotime($bpinCdpValue->cdp->created_at)) >= $inicio){
                                                $valueCDPs[] = $bpinCdpValue->valor;
                                            }
                                        } else $valueCDPs[] = $bpinCdpValue->valor;

                                        if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                            //CONSULTA PARA LOS REGISTROS
                                            $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $fuenteRubro->id)->where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                            foreach ($cdpsRegValue as $valueCdpReg){
                                                if ($valueCdpReg->valor != 0){
                                                    if ($valueCdpReg->registro->jefe_e == 3){
                                                        //VALOR REGISTROS
                                                        if ($inicio != null){
                                                            if (date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) <= $final and date('Y-m-d', strtotime($valueCdpReg->registro->created_at)) >= $inicio){
                                                                $valueRegistros[] = $valueCdpReg->valor;
                                                            }
                                                        } else $valueRegistros[] = $valueCdpReg->valor;


                                                        //ID REGISTROS
                                                        $IDRegistros[] = $valueCdpReg->registro_id;
                                                        //VALOR ORDENES DE PAGO
                                                        $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $valueCdpReg->id)->get();
                                                        if (count($ordenPagoRubros) > 0){
                                                            $ordenPagoRubro = $ordenPagoRubros->first();
                                                            if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $valueCdpReg->registro_id){
                                                                if ($inicio != null){
                                                                    if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->created_at)) >= $inicio){
                                                                        $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                    }
                                                                } else $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                if ($ordenPagoRubro->orden_pago->pago){
                                                                    if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) {
                                                                        if ($inicio != null){
                                                                            if (date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) <= $final and date('Y-m-d', strtotime($ordenPagoRubro->orden_pago->pago->created_at)) >= $inicio){
                                                                                $valuePagos[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        } else $valuePagos[] = $ordenPagoRubro->valor;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else $valueRegistros[] = 0; $IDRegistros[] = 0;
                                    }
                                }
                            }

                        } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;
                        $valueRegistros[] = 0; $IDRegistros[] = 0;
                    }

                } else $valueRubros[] = 0;$valueCDPs[] = 0;$valueRegistros[] = 0;$valueOrdenPago[] = 0; $valuePagos[] = 0; $valueRubrosDisp[] = 0;
            }

            //VALORES CONTRA CREDITO
            if (isset($rubrosCC)){
                //dd($rubrosCC);
                //foreach ($rubrosCC as $cc) if ($cc['id'] == $other->id) $valueRubrosCCred[] = $cc['value'];
            }

            if (!isset($valueRubrosAdd)) {
                $valueRubrosAdd[] = null;
                unset($valueRubrosAdd[0]);
            }

            if (!isset($valueRubrosRed)) {
                $valueRubrosRed[] = null;
                unset($valueRubrosRed[0]);
            }

            if (!isset($valueRubrosCred)) {
                $valueRubrosCred[] = null;
                unset($valueRubrosCred[0]);
            }

            if (!isset($valueRubrosCCred)) {
                $valueRubrosCCred[] = null;
                unset($valueRubrosCCred[0]);
            }

            if (!isset($rubrosCC)) {
                $rubrosCC[] = null;
                unset($rubrosCC[0]);
            }

            if (!isset($valueCDPs)) {
                $valueCDPs[] = null;
                unset($valueCDPs[0]);
            }

            if (!isset($valueRegistros)) {
                $valueRegistros[] = null;
                unset($valueRegistros[0]);
            }

            if (!isset($valueOrdenPago)) {
                $valueOrdenPago[] = null;
                unset($valueOrdenPago[0]);
            }

            if (!isset($valuePagos)) {
                $valuePagos[] = null;
                unset($valuePagos[0]);
            }

            if (!isset($valueRubros)) {
                $valueRubros[] = null;
                unset($valueRubros[0]);
            }

            if (!isset($valueRubrosDisp)) {
                $valueRubrosDisp[] = null;
                unset($valueRubrosDisp[0]);
            }

            //PRESUPUESTO DEFINITIVO
            if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= array_sum($valueRubros) + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
            else $PDef = array_sum($valueRubros) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

            if ($PDef > 0){
                $presupuesto = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                    'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                    'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                    'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                    'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '',
                    'codProd' => '', 'codIndProd' => '', 'codProgMGA' => ''];
            }  elseif(array_sum($valueRubrosCCred) == array_sum($valueRubros)){
                $presupuesto = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                    'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                    'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                    'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                    'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '',
                    'codProd' => '', 'codIndProd' => '', 'codProgMGA' => ''];
            } elseif(array_sum($valueRubrosCCred) > 0) {
                $presupuesto = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                    'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                    'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                    'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                    'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => '',
                    'codProd' => '', 'codIndProd' => '', 'codProgMGA' => ''];
            }

            unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
            unset($valueOrdenPago);unset($valuePagos);unset($valueRubros);unset($valueRubrosDisp);unset($rubrosCC);

            return $presupuesto;
        }
    }

}