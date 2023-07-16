<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\BPin;
use App\bpinVigencias;
use App\Exports\ChipEgrExcExport;
use App\Exports\ChipEgrProgExport;
use App\Exports\ChipIngExcExport;
use App\Exports\InfOrdenPagosExcExport;
use App\Exports\InfPagosExcExport;
use App\Exports\InfPrepIngExcExport;
use App\Exports\InfPrepEgrExcExport;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoEgresos;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\PrepEgresosTraits;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\InformePresupuestosExport;
use Illuminate\Http\Request;
use Session;
use PDF;

class InformesCHIPController extends Controller
{
    public function prepEgresos($inicio = null, $final = null){
        $añoActual = Carbon::now()->year;

        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
        $historico = Vigencia::where('vigencia', '!=', $añoActual)->get();
        foreach ($historico as $his) {
            if ($his->tipo == "0") {
                $years[] = ['info' => $his->vigencia . " - Egresos", 'id' => $his->id];
            } else {
                $years[] = ['info' => $his->vigencia . " - Ingresos", 'id' => $his->id];
            }
        }
        asort($years);

        $V = $vigens[0]->id;
        $vigencia_id = $V;

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipoEgresos::all();
        $oldId = "0";
        $oldCode = "0";
        $oldName = "0";

        //LLENADO DEL PRESUPUESTO
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            //if ($data->id == 750) dd($data, end($presupuesto), $rubro);
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
                    if ($inicio != null) $ordenesPago = OrdenPagos::where('id','>=',708)->where('estado','1')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $ordenesPago = OrdenPagos::where('id','>=',708)->where('estado','1')->get();

                    if (count($ordenesPago) > 0) $valueOrdenPago[] = $ordenesPago->sum('valor');
                    else $valueOrdenPago[] = 0;

                    //pagos
                    if ($inicio != null) $pagosDB = Pagos::where('id','>=',683)->where('estado','1')
                        ->whereBetween('created_at',array($inicio, $final))->get();
                    else $pagosDB = Pagos::where('id','>=',683)->where('estado','1')->get();

                    if (count($pagosDB) > 0) $valuePagos[] = $pagosDB->sum('valor');
                    else $valuePagos[] = 0;

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$data->code.".')");
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
                    if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= $vigens[0]->presupuesto_inicial + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                    else $PDef = $vigens[0]->presupuesto_inicial + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

                    //SOLO SE MUESTRA EL VALOR DEL PRESUPUESTO CUANDO NO SON USUARIOS DE TIPO SECRETARIA
                    $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name,
                        'presupuesto_inicial' => $vigens[0]->presupuesto_inicial, 'adicion' => array_sum($valueRubrosAdd),
                        'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs),
                        'registros' => array_sum($valueRegistros), 'saldo_disp' => $PDef - array_sum($valueCDPs),
                        'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos),
                        'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago), 'rubros_disp' => 0, 'codBpin' => '',
                        'codActiv' => '', 'nameActiv' => '','codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => ''];

                    unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                    unset($valueOrdenPago);unset($valuePagos);
                } else {
                    //LLENADO DE PADRES

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$data->code.".')");

                    foreach ($otherRubs as $other) {
                        $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();

                        if($rubroOtherFind->first()) {

                            if($rubroOtherFind->first()->fontsRubro){
                                foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) {
                                    if (auth()->user()->roles->first()->id != 2){
                                        $valueRubros[] = $fuenteRubro->valor;
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
                                                    $valueRubrosCCred[] = $mov->valor;
                                                    $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                    $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                                }
                                                elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                                elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;

                                            }
                                        } else{
                                            if ($mov->movimiento == "1") {
                                                $valueRubrosCred[] = $mov->valor;
                                                $valueRubrosCCred[] = $mov->valor;
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

                            //VALORES CONTRA CREDITO
                            if (isset($rubrosCC)) {
                                //dd($rubrosCC);

                                //foreach ($rubrosCC as $cc) if ($cc['id'] == $other->id) $valueRubrosCCred[] = $cc['value'];
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
                            'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                            'saldo_disp' => $PDef - array_sum($valueCDPs),
                            'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros),
                            'ordenes_pago' => array_sum($valueOrdenPago), 'pagos' => array_sum($valuePagos),
                            'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos),
                            'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                            'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '','codDep' => '',
                            'dep' => '', 'depRubID' => '', 'fuente' => ''];
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

                //LLENADO PARA LAS FUENTES DEL PRESUPUESTO
                //foreach ($rubro->first()->fontsRubro as $fuente){
                //$sourceFund = SourceFunding::findOrFail($fuente->source_fundings_id);
                //$fonts[] = ['id' => $rubro[0]->cod ,'idFont' => $sourceFund->id, 'code' => $sourceFund->code, 'description' => $sourceFund->description, 'value' => $fuente->valor ];
                //}

                $key = array_search($oldId, array_column($presupuesto, 'id'));
                //if ($data->id == 1074) dd($data, end($presupuesto), $rubro->first()->fontsRubro, $key, $oldId);

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

                    $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$oldCode.".')");
                    if($otherRubs and $oldCode != null) {
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
                                    }
                                } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;

                                if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                    foreach ($rubroOtherFind->first()->rubrosMov as $mov){

                                        if ($mov->valor > 0 ){
                                            if ($mov->movimiento == "1") {
                                                if ($inicio != null){
                                                    if (date('Y-m-d', strtotime($mov->created_at)) <= $final and date('Y-m-d', strtotime($mov->created_at)) >= $inicio){
                                                        $valueRubrosCred[] = $mov->valor;
                                                        $valueRubrosCCred[] = $mov->valor;
                                                        $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                        $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
                                                    }
                                                } else {
                                                    $valueRubrosCred[] = $mov->valor;
                                                    $valueRubrosCCred[] = $mov->valor;
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

                            $presupuesto[] = ['id_rubro' => 0 ,'id' => $oldId, 'cod' => $oldCode, 'name' => $oldName, 'presupuesto_inicial' => array_sum($valueRubros),
                                'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => ''];
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

                                if ($PDef > 0){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id];

                                } elseif(array_sum($valueRubrosCCred) == array_sum($value)){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id];
                                } elseif(array_sum($valueRubrosCCred) == array_sum($valueRubrosAdd)){
                                    $fuente = $depFont->fontRubro->sourceFunding->code.' - '.$depFont->fontRubro->sourceFunding->description;

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id, 'fuente' =>  $fuente, 'padreID' => $plantillaCuipoFind->padre_id];
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
                        'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => ''];
                }
            } elseif (count($rubro) == 0){
                if ($data->id == 465 or $data->id == 514 or $data->id == 527 or $data->id == 543 or $data->id == 551 or
                    $data->id == 584 or $data->id == 589 or $data->id == 624 or $data->id == 827 or
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
                    //if ($data->id == 749) dd($data, end($presupuesto), $presupuesto, $found_key, $prep);

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
                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => ''];
            }  elseif(array_sum($valueRubrosCCred) == array_sum($valueRubros)){
                $presupuesto = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                    'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                    'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                    'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                    'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => '', 'fuente' => ''];
            }

            //if ($data->id == 750) dd($data, $otherRubs, $PDef, array_sum($valueRubros), array_sum($valueRubrosCred), array_sum($valueRubrosCCred), $valueRubrosCred, $valueRubrosCCred);

            unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
            unset($valueOrdenPago);unset($valuePagos);unset($valueRubros);unset($valueRubrosDisp);unset($rubrosCC);

            return $presupuesto;
        }
    }

    public function prepIngresos($inicio = null, $final = null ){
        $añoActual = Carbon::now()->year;

        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 1)->where('estado', '0')->get();
        $historico = Vigencia::where('vigencia', '!=', $añoActual)->get();

        foreach ($historico as $his) {
            if ($his->tipo == "0"){
                $years[] = [ 'info' => $his->vigencia." - Egresos", 'id' => $his->id];
            }else{
                $years[] = [ 'info' => $his->vigencia." - Ingresos", 'id' => $his->id];
            }
        }
        asort($years);

        $V = $vigens[0]->id;
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
                if ($inicio != null) $adiciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','2')
                    ->whereBetween('created_at',array($inicio, $final))->get();
                else  $adiciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','2')->get();

                if ($inicio != null) $reducciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','3')
                    ->whereBetween('created_at',array($inicio, $final))->get();
                else  $reducciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','3')->get();

                $definitivo = $adiciones->sum('valor') - $reducciones->sum('valor') + $vigens[0]->presupuesto_inicial;
                $prepIng[] = collect(['id' => $data->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $vigens[0]->presupuesto_inicial, 'adicion' => $adiciones->sum('valor'), 'reduccion' => $reducciones->sum('valor'),
                    'anulados' => 0, 'recaudado' => array_sum($totComIng) , 'porRecaudar' => $definitivo - array_sum($totComIng), 'definitivo' => $definitivo,
                    'hijo' => 0, 'cod_fuente' => '', 'name_fuente' => '']);
                unset($totComIng);
            } else {
                $hijos1 = PlantillaCuipoIngresos::where('padre_id', $data->id)->get();
                if (count($hijos1) > 0){
                    foreach ($hijos1 as $h1){
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

                                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                        $sum[] = $rubro[0]->fontsRubro->sum('valor');

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

                                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                    $sum[] = $rubro[0]->fontsRubro->sum('valor');
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

                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                            $sum[] = $rubro[0]->fontsRubro->sum('valor');
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

                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                    // VALIDACION PARA EL VALOR INICIAL DE LOS RUBROS PADRES
                                    $sum[] = $rubro[0]->fontsRubro->sum('valor');
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

                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $rubro[0]->fontsRubro->sum('valor'), 'adicion' => $adicion, 'reduccion' => $reduccion,
                                        'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' =>  $definitivo,
                                        'hijo' => $data->hijo, 'cod_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->code, 'name_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->description]);
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

    public function makeEgresosExec(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepEgresos($inicio, $final);

        return Excel::download(new ChipEgrExcExport($presupuesto),
            'CHIP Egresos Ejecucion '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeIngresosExec(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepIngresos($inicio, $final);

        return Excel::download(new ChipIngExcExport($presupuesto),
            'Ejecucion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeEgresosProg(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepEgresos($inicio, $final);

        return Excel::download(new ChipEgrProgExport($presupuesto),
            'Programacion Presupuesto de Egresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeIngresosProg(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepIngresos($inicio, $final);

        return Excel::download(new ChipIngExcExport($presupuesto),
            'Programacion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function make(Request $request){
        $año = Carbon::today()->year;
        $inicio = $año.'-01-01';
        switch ($request->periodo){
            case 1:
                $final = $año.'-03-31';
                break;
            case 2:
                $final = $año.'-06-30';
                break;
            case 3:
                $final = $año.'-09-30';
                break;
            case 4:
                $final = $año.'-12-31';
                break;
        }

        if ($request->categoria == "ProgIng") {
            $presupuesto = $this->prepIngresos($inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11206, '4' => $año, '5' => 'A_PROGRAMACION_DE_INGRESOS']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Ppto Inicial', '4' => 'Ppto Final']);
            foreach ($presupuesto as $data) {
                $prep[] = collect(['1' => 'D', '2' => $data['code'], '3' => $data['inicial'], '4' => $data['definitivo']]);
            }

            return $prep;
        } elseif ($request->categoria == "EjecIng") {
            $presupuesto = $this->prepIngresos($inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'B_EJECUCION_DE_INGRESOS']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'CPC', '4' => 'Detalle Sectorial', '5' => 'Codigo Fuente',
                '6' => 'Tercero', '7' => 'Politica Publica', '8' => 'Numero y Fecha Norma', '9' => 'Tipo Norma',
                '10' => 'Recaudo vigencia actual sin situación de fondos', '11' => 'Recaudo vigencia actual con fondos',
                '12' => 'Recaudo vigencia anterior sin situación de fondos', '13' => 'Recaudo Anterior  con fondos']);
            foreach ($presupuesto as $data) {
                if ($data['cod_fuente']) $prep[] = collect(['1' => 'D', '2' => $data['code'], '3' => 0, '4' => 0, '5' => $data['cod_fuente'],
                    '6' => 1, '7' => 0, 8 => 'ley 99 de 1993', '9' => 5, '10' => 0, '11' => $data['recaudado'],
                    '12' => 0, '13' => 0]);
            }

            return $prep;

        }elseif ($request->categoria == "ProgGasAdm"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_PROGRAMACION_GASTOS_ADMINISTRACION_CENTRAL']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central', '5' => 'Programa MGA',
                '6' => 'BPIN', '7' => 'Apropiacion Inicial', '8' => 'Apropiacion Definitiva']);
            foreach ($result as $prep){
                if ( $prep['dep'] != "" and $prep['dep'] != "ADMINISTRACION CENTRAL"){
                    $prep[] = collect(['1' => 'D', '2' => $data['code'], '3' => 1, '4' => 16, '5' => 0,
                        '6' => 0, '7' => $data['presupuesto_inicial'], 8 => $data['presupuesto_def']]);

                }
            }

            return $prep;
        } else {
            dd("other");
        }
    }
}