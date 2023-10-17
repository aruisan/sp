<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\BPin;
use App\bpinVigencias;
use App\Exports\InfMensualExport;
use App\Exports\InfPrepIngExcExport;
use App\Exports\InfPrepEgrExcExport;
use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Tesoreria\Pac;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoEgresos;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\PrepEgresosTraits;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\InformePresupuestosExport;
use Illuminate\Http\Request;
use Session;
use PDF;

class InformeController extends Controller
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

    public function newEgresos(){
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

        $bpins = BPin::all();
        foreach ($bpins as $bpin){
            $bpin['rubro'] = "No";
            if (count($bpin->rubroFind) > 0) {
                foreach ($bpin->rubroFind as $rub){
                    if ($rub->vigencia_id == $V){
                        $bpin['rubro'] = $rub->dep_rubro_id;
                    }
                }
            }
        }

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipoEgresos::all();
        $oldId = "0";
        $oldCode = "0";
        $oldName = "0";

        foreach ($plantilla as $data){
            //RUBROS INICIALES
            if ($data->id == 318) {
                //CDPS
                $cdpsFind = Cdp::where('vigencia_id', $vigencia_id)->where('jefe_e', '3')->get();
                if (count($cdpsFind) > 0) $valueCDPs[] = $cdpsFind->sum('valor');
                else $valueCDPs[] = 0;

                //registros
                $registrosFind = Registro::where('id','>=', 778)->where('jefe_e','3')->get();
                if (count($registrosFind) > 0) $valueRegistros[] = $registrosFind->sum('valor');
                else $valueRegistros[] = 0;

                //orden pagos
                $ordenesPago = OrdenPagos::where('id','>=',708)->where('estado','1')->get();
                if (count($ordenesPago) > 0) $valueOrdenPago[] = $ordenesPago->sum('valor');
                else $valueOrdenPago[] = 0;

                //pagos
                $pagosDB = Pagos::where('id','>=',683)->where('estado','1')->get();
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
                                    if ($mov->movimiento == "1") {
                                        $valueRubrosCred[] = $mov->valor;
                                        $valueRubrosCCred[] = $mov->valor;
                                    }
                                    elseif ($mov->movimiento == "2") $valueRubrosAdd[] = $mov->valor;
                                    elseif ($mov->movimiento == "3") $valueRubrosRed[] = $mov->valor;
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

                $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'code' => $data->code, 'name' => $data->name,
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
                $hijos1 = PlantillaCuipoEgresos::where('padre_id', $data->id)->get();
                if (count($hijos1) > 0) {
                    foreach ($hijos1 as $h1) {
                        $hijos2 = PlantillaCuipoEgresos::where('padre_id', $h1->id)->get();
                        if (count($hijos2) > 0) {
                            foreach ($hijos2 as $h2) {
                                $hijos3 = PlantillaCuipoEgresos::where('padre_id', $h2->id)->get();
                                if (count($hijos3) > 0) {
                                    foreach ($hijos3 as $h3) {
                                        $hijos4 = PlantillaCuipoEgresos::where('padre_id', $h3->id)->get();
                                        if (count($hijos4) > 0) {
                                            foreach ($hijos4 as $h4) {
                                                $rubros = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h4->id)->get();
                                                if (count($rubros) > 0) {
                                                    foreach ($rubros as $rubro) {
                                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                        if (isset($adicionesH)) unset($adicionesH);
                                                        if (isset($reduccionesH)) unset($reduccionesH);
                                                        if (isset($creditosH)) unset($creditosH);
                                                        if (isset($contraCredH)) unset($contraCredH);
                                                        if (isset($cdpH)) unset($cdpH);
                                                        if (isset($rpH)) unset($rpH);
                                                        if (isset($opH)) unset($opH);
                                                        if (isset($pagosH)) unset($pagosH);

                                                        //2 ADD - 3 RED  - 1 CRED
                                                        if(count($rubro->rubrosMov) > 0){
                                                            foreach ($rubro->rubrosMov as $mov){
                                                                if ($mov->valor > 0 ){
                                                                    if ($mov->movimiento == "1") {
                                                                        $hijosCred[] = $mov->valor;
                                                                        $hijosCC[] = $mov->valor;
                                                                    }
                                                                    elseif ($mov->movimiento == "2") $hijosAdicion[] = $mov->valor;
                                                                    elseif ($mov->movimiento == "3") $hijosReduccion[] = $mov->valor;
                                                                }
                                                            }
                                                        } else {
                                                            $hijosAdicion[] = 0;
                                                            $hijosReduccion[] = 0;
                                                            $hijosCred[] = 0;
                                                            $hijosCC[] = 0;
                                                        }

                                                        foreach ($rubro->fontsRubro as $font) {
                                                            foreach ($font->dependenciaFont as $dependencia){
                                                                //VALIDACION PARA LOS CDPS FUNCIONAMIENTO
                                                                $rubCdpValues = RubrosCdpValor::where('fontsDep_id', $dependencia->id)->get();
                                                                foreach ($rubCdpValues as $rubCdpValue) {
                                                                    if ($rubCdpValue->cdps->jefe_e == "3") {
                                                                        if (!isset($CDPID)) {
                                                                            $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                            $CDPID[] = $rubCdpValue->cdps->id;
                                                                        }
                                                                        else if (array_search($rubCdpValue->cdps->id, $CDPID) === false){
                                                                            $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                            $CDPID[] = $rubCdpValue->cdps->id;
                                                                        }
                                                                        if (count($rubCdpValue->cdps->cdpsRegistro) > 0){
                                                                            //CONSULTA PARA LOS REGISTROS
                                                                            $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $rubCdpValue->cdp_id)->get();
                                                                            foreach ($cdpsRegValue as $cdpRegV){
                                                                                if ($cdpRegV->valor != 0){
                                                                                    if ($cdpRegV->registro->jefe_e == 3){
                                                                                        //VALOR REGISTROS
                                                                                        $hijosRP[] = $cdpRegV->valor;
                                                                                        //ID REGISTROS
                                                                                        $IDRegistros[] = $cdpRegV->registro_id;
                                                                                        //VALOR ORDENES DE PAGO
                                                                                        $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                                        if (count($ordenPagoRubros) > 0){
                                                                                            $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                            if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                                $hijosOP[] = $ordenPagoRubro->valor;
                                                                                                if ($ordenPagoRubro->orden_pago->pago){
                                                                                                    if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                                    }
                                                                }

                                                                //VALIDACION PARA LOS CDPS INVERSION
                                                                $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $dependencia->id)->get();
                                                                foreach ($bpinCdpValues as $bpinCdpValue) {
                                                                    if ($bpinCdpValue->cdp->jefe_e == "3") {
                                                                        if (!isset($CDPID)) {
                                                                            $hijosCDP[] = $bpinCdpValue->cdp->valor;
                                                                            $CDPID[] = $rubCdpValue->cdps->id;
                                                                        }
                                                                        else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                                            $hijosCDP[] = $bpinCdpValue->cdp->valor;
                                                                            $CDPID[] = $rubCdpValue->cdps->id;
                                                                        }
                                                                        if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                                                            //CONSULTA PARA LOS REGISTROS
                                                                            $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                                                            foreach ($cdpsRegValue as $cdpRegV){
                                                                                if ($cdpRegV->valor != 0){
                                                                                    if ($cdpRegV->registro->jefe_e == 3){
                                                                                        //VALOR REGISTROS
                                                                                        $hijosRP[] = $cdpRegV->valor;
                                                                                        //ID REGISTROS
                                                                                        $IDRegistros[] = $cdpRegV->registro_id;
                                                                                        //VALOR ORDENES DE PAGO
                                                                                        $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                                        if (count($ordenPagoRubros) > 0){
                                                                                            $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                            if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                                $hijosOP[] = $ordenPagoRubro->valor;
                                                                                                if ($ordenPagoRubro->orden_pago->pago){
                                                                                                    if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                                        if (isset($hijosCred)) $creditosH[] = array_sum($hijosCred);
                                                        if (isset($hijosCC)) $contraCredH[] = array_sum($hijosCC);
                                                        if (isset($hijosCDP)) $cdpH[] = array_sum($hijosCDP);
                                                        if (isset($hijosRP)) $rpH[] = array_sum($hijosRP);
                                                        if (isset($hijosOP)) $opH[] = array_sum($hijosOP);
                                                        if (isset($hijosPagos)) $pagosH[] = array_sum($hijosPagos);

                                                        $sum[] = $rubro->fontsRubro->sum('valor');
                                                    }
                                                }
                                            }
                                        } else {
                                            $rubros = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h3->id)->get();
                                            if (count($rubros) > 0) {
                                                foreach ($rubros as $rubro) {
                                                    //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                                    if (isset($adicionesH)) unset($adicionesH);
                                                    if (isset($reduccionesH)) unset($reduccionesH);
                                                    if (isset($creditosH)) unset($creditosH);
                                                    if (isset($contraCredH)) unset($contraCredH);
                                                    if (isset($cdpH)) unset($cdpH);
                                                    if (isset($rpH)) unset($rpH);
                                                    if (isset($opH)) unset($opH);
                                                    if (isset($pagosH)) unset($pagosH);

                                                    //2 ADD - 3 RED  - 1 CRED
                                                    if(count($rubro->rubrosMov) > 0){
                                                        foreach ($rubro->rubrosMov as $mov){
                                                            if ($mov->valor > 0 ){
                                                                if ($mov->movimiento == "1") {
                                                                    $hijosCred[] = $mov->valor;
                                                                    $hijosCC[] = $mov->valor;
                                                                }
                                                                elseif ($mov->movimiento == "2") $hijosAdicion[] = $mov->valor;
                                                                elseif ($mov->movimiento == "3") $hijosReduccion[] = $mov->valor;
                                                            }
                                                        }
                                                    } else {
                                                        $hijosAdicion[] = 0;
                                                        $hijosReduccion[] = 0;
                                                        $hijosCred[] = 0;
                                                        $hijosCC[] = 0;
                                                    }

                                                    foreach ($rubro->fontsRubro as $font) {
                                                        foreach ($font->dependenciaFont as $dependencia){
                                                            //VALIDACION PARA LOS CDPS FUNCIONAMIENTO
                                                            $rubCdpValues = RubrosCdpValor::where('fontsDep_id', $dependencia->id)->get();
                                                            foreach ($rubCdpValues as $rubCdpValue) {
                                                                if ($rubCdpValue->cdps->jefe_e == "3") {
                                                                    if (!isset($CDPID)) {
                                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                                    }
                                                                    else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                                    }
                                                                    if (count($rubCdpValue->cdps->cdpsRegistro) > 0){
                                                                        //CONSULTA PARA LOS REGISTROS
                                                                        $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $rubCdpValue->cdp_id)->get();
                                                                        foreach ($cdpsRegValue as $cdpRegV){
                                                                            if ($cdpRegV->valor != 0){
                                                                                if ($cdpRegV->registro->jefe_e == 3){
                                                                                    //VALOR REGISTROS
                                                                                    $hijosRP[] = $cdpRegV->valor;
                                                                                    //ID REGISTROS
                                                                                    $IDRegistros[] = $cdpRegV->registro_id;
                                                                                    //VALOR ORDENES DE PAGO
                                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                                    if (count($ordenPagoRubros) > 0){
                                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                            $hijosOP[] = $ordenPagoRubro->valor;
                                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                                }
                                                            }

                                                            //VALIDACION PARA LOS CDPS INVERSION
                                                            $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $dependencia->id)->get();
                                                            foreach ($bpinCdpValues as $bpinCdpValue) {
                                                                if ($bpinCdpValue->cdp->jefe_e == "3") {
                                                                    if (!isset($CDPID)) {
                                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                                    }
                                                                    else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                                    }
                                                                    if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                                                        //CONSULTA PARA LOS REGISTROS
                                                                        $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                                                        foreach ($cdpsRegValue as $cdpRegV){
                                                                            if ($cdpRegV->valor != 0){
                                                                                if ($cdpRegV->registro->jefe_e == 3){
                                                                                    //VALOR REGISTROS
                                                                                    $hijosRP[] = $cdpRegV->valor;
                                                                                    //ID REGISTROS
                                                                                    $IDRegistros[] = $cdpRegV->registro_id;
                                                                                    //VALOR ORDENES DE PAGO
                                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                                    if (count($ordenPagoRubros) > 0){
                                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                            $hijosOP[] = $ordenPagoRubro->valor;
                                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                                    if (isset($hijosCred)) $creditosH[] = array_sum($hijosCred);
                                                    if (isset($hijosCC)) $contraCredH[] = array_sum($hijosCC);
                                                    if (isset($hijosCDP)) $cdpH[] = array_sum($hijosCDP);
                                                    if (isset($hijosRP)) $rpH[] = array_sum($hijosRP);
                                                    if (isset($hijosOP)) $opH[] = array_sum($hijosOP);
                                                    if (isset($hijosPagos)) $pagosH[] = array_sum($hijosPagos);

                                                    $sum[] = $rubro->fontsRubro->sum('valor');

                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $rubros = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h2->id)->get();
                                    if (count($rubros) > 0) {
                                        foreach ($rubros as $rubro) {
                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                            if (isset($adicionesH)) unset($adicionesH);
                                            if (isset($reduccionesH)) unset($reduccionesH);
                                            if (isset($creditosH)) unset($creditosH);
                                            if (isset($contraCredH)) unset($contraCredH);
                                            if (isset($cdpH)) unset($cdpH);
                                            if (isset($rpH)) unset($rpH);
                                            if (isset($opH)) unset($opH);
                                            if (isset($pagosH)) unset($pagosH);

                                            //2 ADD - 3 RED  - 1 CRED
                                            if(count($rubro->rubrosMov) > 0){
                                                foreach ($rubro->rubrosMov as $mov){
                                                    if ($mov->valor > 0 ){
                                                        if ($mov->movimiento == "1") {
                                                            $hijosCred[] = $mov->valor;
                                                            $hijosCC[] = $mov->valor;
                                                        }
                                                        elseif ($mov->movimiento == "2") $hijosAdicion[] = $mov->valor;
                                                        elseif ($mov->movimiento == "3") $hijosReduccion[] = $mov->valor;
                                                    }
                                                }
                                            } else {
                                                $hijosAdicion[] = 0;
                                                $hijosReduccion[] = 0;
                                                $hijosCred[] = 0;
                                                $hijosCC[] = 0;
                                            }

                                            foreach ($rubro->fontsRubro as $font) {
                                                foreach ($font->dependenciaFont as $dependencia){
                                                    //VALIDACION PARA LOS CDPS FUNCIONAMIENTO
                                                    $rubCdpValues = RubrosCdpValor::where('fontsDep_id', $dependencia->id)->get();
                                                    foreach ($rubCdpValues as $rubCdpValue) {
                                                        if ($rubCdpValue->cdps->jefe_e == "3") {
                                                            if (!isset($CDPID)) {
                                                                $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                $CDPID[] = $rubCdpValue->cdps->id;
                                                            }
                                                            else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                                $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                $CDPID[] = $rubCdpValue->cdps->id;
                                                            }
                                                            if (count($rubCdpValue->cdps->cdpsRegistro) > 0){
                                                                //CONSULTA PARA LOS REGISTROS
                                                                $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $rubCdpValue->cdp_id)->get();
                                                                foreach ($cdpsRegValue as $cdpRegV){
                                                                    if ($cdpRegV->valor != 0){
                                                                        if ($cdpRegV->registro->jefe_e == 3){
                                                                            //VALOR REGISTROS
                                                                            $hijosRP[] = $cdpRegV->valor;
                                                                            //ID REGISTROS
                                                                            $IDRegistros[] = $cdpRegV->registro_id;
                                                                            //VALOR ORDENES DE PAGO
                                                                            $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                            if (count($ordenPagoRubros) > 0){
                                                                                $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                    $hijosOP[] = $ordenPagoRubro->valor;
                                                                                    if ($ordenPagoRubro->orden_pago->pago){
                                                                                        if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                        }
                                                    }

                                                    //VALIDACION PARA LOS CDPS INVERSION
                                                    $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $dependencia->id)->get();
                                                    foreach ($bpinCdpValues as $bpinCdpValue) {
                                                        if ($bpinCdpValue->cdp->jefe_e == "3") {
                                                            if (!isset($CDPID)) {
                                                                $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                $CDPID[] = $rubCdpValue->cdps->id;
                                                            }
                                                            else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                                $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                                $CDPID[] = $rubCdpValue->cdps->id;
                                                            }
                                                            if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                                                //CONSULTA PARA LOS REGISTROS
                                                                $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                                                foreach ($cdpsRegValue as $cdpRegV){
                                                                    if ($cdpRegV->valor != 0){
                                                                        if ($cdpRegV->registro->jefe_e == 3){
                                                                            //VALOR REGISTROS
                                                                            $hijosRP[] = $cdpRegV->valor;
                                                                            //ID REGISTROS
                                                                            $IDRegistros[] = $cdpRegV->registro_id;
                                                                            //VALOR ORDENES DE PAGO
                                                                            $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                            if (count($ordenPagoRubros) > 0){
                                                                                $ordenPagoRubro = $ordenPagoRubros->first();
                                                                                if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                                    $hijosOP[] = $ordenPagoRubro->valor;
                                                                                    if ($ordenPagoRubro->orden_pago->pago){
                                                                                        if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                        }
                                                    }
                                                }
                                            }

                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                            if (isset($hijosCred)) $creditosH[] = array_sum($hijosCred);
                                            if (isset($hijosCC)) $contraCredH[] = array_sum($hijosCC);
                                            if (isset($hijosCDP)) $cdpH[] = array_sum($hijosCDP);
                                            if (isset($hijosRP)) $rpH[] = array_sum($hijosRP);
                                            if (isset($hijosOP)) $opH[] = array_sum($hijosOP);
                                            if (isset($hijosPagos)) $pagosH[] = array_sum($hijosPagos);

                                            $sum[] = $rubro->fontsRubro->sum('valor');

                                        }
                                    }
                                }
                            }
                        } else {
                            $rubros = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h1->id)->get();
                            if (count($rubros) > 0) {
                                foreach ($rubros as $rubro) {
                                    //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                    if (isset($adicionesH)) unset($adicionesH);
                                    if (isset($reduccionesH)) unset($reduccionesH);
                                    if (isset($creditosH)) unset($creditosH);
                                    if (isset($contraCredH)) unset($contraCredH);
                                    if (isset($cdpH)) unset($cdpH);
                                    if (isset($rpH)) unset($rpH);
                                    if (isset($opH)) unset($opH);
                                    if (isset($pagosH)) unset($pagosH);

                                    //2 ADD - 3 RED  - 1 CRED
                                    if(count($rubro->rubrosMov) > 0){
                                        foreach ($rubro->rubrosMov as $mov){
                                            if ($mov->valor > 0 ){
                                                if ($mov->movimiento == "1") {
                                                    $hijosCred[] = $mov->valor;
                                                    $hijosCC[] = $mov->valor;
                                                }
                                                elseif ($mov->movimiento == "2") $hijosAdicion[] = $mov->valor;
                                                elseif ($mov->movimiento == "3") $hijosReduccion[] = $mov->valor;
                                            }
                                        }
                                    } else {
                                        $hijosAdicion[] = 0;
                                        $hijosReduccion[] = 0;
                                        $hijosCred[] = 0;
                                        $hijosCC[] = 0;
                                    }

                                    foreach ($rubro->fontsRubro as $font) {
                                        foreach ($font->dependenciaFont as $dependencia){
                                            //VALIDACION PARA LOS CDPS FUNCIONAMIENTO
                                            $rubCdpValues = RubrosCdpValor::where('fontsDep_id', $dependencia->id)->get();
                                            foreach ($rubCdpValues as $rubCdpValue) {
                                                if ($rubCdpValue->cdps->jefe_e == "3") {
                                                    if (!isset($CDPID)) {
                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                    }
                                                    else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                    }
                                                    if (count($rubCdpValue->cdps->cdpsRegistro) > 0){
                                                        //CONSULTA PARA LOS REGISTROS
                                                        $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $rubCdpValue->cdp_id)->get();
                                                        foreach ($cdpsRegValue as $cdpRegV){
                                                            if ($cdpRegV->valor != 0){
                                                                if ($cdpRegV->registro->jefe_e == 3){
                                                                    //VALOR REGISTROS
                                                                    $hijosRP[] = $cdpRegV->valor;
                                                                    //ID REGISTROS
                                                                    $IDRegistros[] = $cdpRegV->registro_id;
                                                                    //VALOR ORDENES DE PAGO
                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                    if (count($ordenPagoRubros) > 0){
                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                            $hijosOP[] = $ordenPagoRubro->valor;
                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                }
                                            }

                                            //VALIDACION PARA LOS CDPS INVERSION
                                            $bpinCdpValues = BpinCdpValor::where('dependencia_rubro_font_id', $dependencia->id)->get();
                                            foreach ($bpinCdpValues as $bpinCdpValue) {
                                                if ($bpinCdpValue->cdp->jefe_e == "3") {
                                                    if (!isset($CDPID)) {
                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                    }
                                                    else if (array_search($rubCdpValue->cdps->id, $CDPID) === false) {
                                                        $hijosCDP[] = $rubCdpValue->cdps->valor;
                                                        $CDPID[] = $rubCdpValue->cdps->id;
                                                    }
                                                    if (count($bpinCdpValue->cdp->cdpsRegistro) > 0){
                                                        //CONSULTA PARA LOS REGISTROS
                                                        $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCdpValue->cdp_id)->get();
                                                        foreach ($cdpsRegValue as $cdpRegV){
                                                            if ($cdpRegV->valor != 0){
                                                                if ($cdpRegV->registro->jefe_e == 3){
                                                                    //VALOR REGISTROS
                                                                    $hijosRP[] = $cdpRegV->valor;
                                                                    //ID REGISTROS
                                                                    $IDRegistros[] = $cdpRegV->registro_id;
                                                                    //VALOR ORDENES DE PAGO
                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRegV->id)->get();
                                                                    if (count($ordenPagoRubros) > 0){
                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRegV->registro_id){
                                                                            $hijosOP[] = $ordenPagoRubro->valor;
                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $hijosPagos[] = $ordenPagoRubro->valor;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else $hijosRP[] = 0; $IDRegistros[] = 0;
                                                }
                                            }
                                        }
                                    }

                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                    if (isset($hijosCred)) $creditosH[] = array_sum($hijosCred);
                                    if (isset($hijosCC)) $contraCredH[] = array_sum($hijosCC);
                                    if (isset($hijosCDP)) $cdpH[] = array_sum($hijosCDP);
                                    if (isset($hijosRP)) $rpH[] = array_sum($hijosRP);
                                    if (isset($hijosOP)) $opH[] = array_sum($hijosOP);
                                    if (isset($hijosPagos)) $pagosH[] = array_sum($hijosPagos);

                                    $sum[] = $rubro->fontsRubro->sum('valor');

                                }
                            }
                        }
                    }
                    if (isset($sum)) {
                        $adicionesTot = 0;
                        $reduccionesTot = 0;
                        $credTot = 0;
                        $ccredTot = 0;
                        $cdpTot = 0;
                        $rpTot = 0;
                        $opTot = 0;
                        $pagoTot = 0;
                        if (isset($adicionesH)) $adicionesTot = array_sum($adicionesH);
                        if (isset($reduccionesH)) $reduccionesTot = array_sum($reduccionesH);
                        if (isset($creditosH)) $credTot = array_sum($creditosH);
                        if (isset($contraCredH)) $ccredTot = array_sum($contraCredH);
                        if (isset($cdpH)) $cdpTot = array_sum($cdpH);
                        if (isset($rpH)) $rpTot = array_sum($rpH);
                        if (isset($opH)) $opTot = array_sum($opH);
                        if (isset($pagosH)) $pagoTot = array_sum($pagosH);

                        $opeAddyRed = $adicionesTot - $reduccionesTot + array_sum($sum);
                        $definitivo = $opeAddyRed + $credTot - $ccredTot;
                        $disp = $definitivo - $cdpTot;
                        $saldoCDP = $cdpTot - $rpTot;
                        $cuentasPagar = $opTot - $pagoTot;
                        $reservas = $rpTot - $opTot;


                        $presupuesto[] = collect(['id' => $data->id, 'code' => $data->code, 'name' => $data->name,
                            'presupuesto_inicial' => array_sum($sum), 'adicion' => $adicionesTot, 'reduccion' => $reduccionesTot,
                            'credito' => $credTot, 'ccredito' => $ccredTot, 'presupuesto_def' => $definitivo,
                            'hijo' => $data->hijo, 'cdps' => $cdpTot, 'registros' => $rpTot, 'saldo_disp' => $disp,
                            'saldo_cdp' => $saldoCDP, 'ordenes_pago' => $opTot, 'pagos' => $pagoTot,
                            'cuentas_pagar' => $cuentasPagar, 'reservas' => $reservas, 'rubros_disp' => 0,
                            'codBpin' => '','codActiv' => '', 'nameActiv' => '','codDep' => '', 'dep' => '',
                            'depRubID' => '', 'fuente' => '']);

                        unset($sum);
                        if (isset($adicionesH)) unset($adicionesH);
                        if (isset($reduccionesH)) unset($reduccionesH);
                        if (isset($creditosH)) unset($creditosH);
                        if (isset($contraCredH)) unset($contraCredH);
                        if (isset($hijosAdicion)) unset($hijosAdicion);
                        if (isset($hijosReduccion)) unset($hijosReduccion);
                        if (isset($hijosCred)) unset($hijosCred);
                        if (isset($hijosCC)) unset($hijosCC);
                        if (isset($cdpH)) unset($cdpH);
                        if (isset($rpH)) unset($rpH);
                        if (isset($opH)) unset($opH);
                        if (isset($pagosH)) unset($pagosH);
                        if (isset($hijosCDP)) unset($hijosCDP);
                        if (isset($CDPID)) unset($CDPID);
                        if (isset($hijosRP)) unset($hijosRP);
                        if (isset($hijosOP)) unset($hijosOP);
                        if (isset($hijosPagos)) unset($hijosPagos);
                    }
                } else {
                    dd($presupuesto);
                    //AL NO TENER HIJOS SE TOMA COMO SI FUERA YA EL RUBRO HIJO CON LOS VALORES
                    $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
                    if (count($rubro) > 0) {
                        if (count($rubro) == 1) {
                            $compIngValue = 0;
                            if (count($rubro[0]->compIng) > 0) $compIngValue = $rubro[0]->compIng->sum('valor');
                            if (count($rubro[0]->fontsRubro) > 1) {
                                foreach ($rubro[0]->fontsRubro as $font) {
                                    $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                    if ($add) $adicion = $add->valor;
                                    else $adicion = 0;

                                    $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                    if ($red) $reduccion = $red->valor;
                                    else $reduccion = 0;

                                    $definitivo = $adicion - $reduccion + $font->valor;

                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name,
                                        'inicial' => $font->valor, 'adicion' => $adicion, 'reduccion' => $reduccion, 'anulados' => 0,
                                        'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo, 'hijo' => $data->hijo,
                                        'cod_fuente' => $font->sourceFunding->code, 'name_fuente' => $font->sourceFunding->description]);
                                }
                            } else {
                                if (count($rubro[0]->fontsRubro) > 0) {
                                    $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                    if ($add) $adicion = $add->valor;
                                    else $adicion = 0;

                                    $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                    if ($red) $reduccion = $red->valor;
                                    else $reduccion = 0;

                                    $definitivo = $adicion - $reduccion + $rubro[0]->fontsRubro->sum('valor');

                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $rubro[0]->fontsRubro->sum('valor'), 'adicion' => $adicion, 'reduccion' => $reduccion,
                                        'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo,
                                        'hijo' => $data->hijo, 'cod_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->code, 'name_fuente' => $rubro[0]->fontsRubro[0]->sourceFunding->description]);
                                }

                            }
                        } else {
                            //MAS DE UN RUBRO ASIGNADO A LA MISMA PLANTILLA
                            foreach ($rubro as $rb) {
                                //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDIENTE LLENADO EN LIMPIO
                                if (isset($adicionesH)) unset($adicionesH);
                                if (isset($reduccionesH)) unset($reduccionesH);

                                foreach ($rb->fontsRubro as $font) {
                                    $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                    if ($add) {
                                        if ($add->fonts_rubro_id == $font->id) $hijosAdicion[] = $add->valor;
                                        else $hijosAdicion[] = 0;
                                    } else $hijosAdicion[] = 0;

                                    $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                    if ($red) {
                                        if ($red->fonts_rubro_id == $font->id) $hijosReduccion[] = $red->valor;
                                        else $hijosReduccion[] = 0;
                                    } else $hijosReduccion[] = 0;
                                }

                                if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                if (isset($adicionesH)) $adicionesTot = array_sum($adicionesH);
                                if (isset($reduccionesH)) $reduccionesTot = array_sum($reduccionesH);

                                $compIngValue = 0;
                                if (count($rb->compIng) > 0) $compIngValue = $rb->compIng->sum('valor');
                                $sum[] = $rb->fontsRubro->sum('valor');
                                $definitivo = $adicionesTot - $reduccionesTot + $rb->fontsRubro->sum('valor');
                                $prepIng[] = collect(['id' => $rb->id, 'code' => $data->code, 'name' => $rb->name, 'inicial' => $rb->fontsRubro->sum('valor'), 'adicion' => $adicionesTot, 'reduccion' => $reduccionesTot,
                                    'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo,
                                    'hijo' => $data->hijo, 'cod_fuente' => $rb->fontsRubro[0]->sourceFunding->code, 'name_fuente' => $rb->fontsRubro[0]->sourceFunding->description]);

                                unset($sum);
                                if (isset($adicionesH)) unset($adicionesH);
                                if (isset($reduccionesH)) unset($reduccionesH);
                                if (isset($hijosAdicion)) unset($hijosAdicion);
                                if (isset($hijosReduccion)) unset($hijosReduccion);
                            }
                        }
                    }
                }
            }

        }
        return $presupuesto;
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

    public function makeEgresosEXCEL(Request $request)
    {
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        $vigencia = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->first();
        $findSnap = PresupuestoSnap::where('vigencia_id', $vigencia->id)->where('mes', $mesActual)
            ->where('año', $añoActual)->where('tipo','EGRESOS')->first();
        if ($findSnap){
            $presupuesto = PresupuestoSnapData::where('pre_snap_id', $findSnap->id)->get();
        } else dd("no se detecta presupuesto almacenado");


        return Excel::download(new InfPrepEgrExcExport($presupuesto),
            'Informe Presupuesto de Egresos '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');
    }

    public function makeEgresosEjecucion(Request $request, $inicio, $final)
    {
        $prepTrait = new PrepEgresosTraits();
        $vigens = Vigencia::where('vigencia', Carbon::parse($inicio)->year)->where('tipo', 0)->where('estado', '0')->first();
        $presupuesto = $prepTrait->prepEgresos($vigens, $inicio, $final);

        $final = Carbon::parse($final)->subDays(1);
        dd($presupuesto, $presupuesto[0]);

        return Excel::download(new InfPrepEgrExcExport($presupuesto),
            'Ejecucion Presupuesto de Egresos '.$inicio.'-'.$final->format('Y-m-d').'.xlsx');
    }

    public function makeIngresosEXCEL()
    {
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
        $prepIng = $this->prepIngresos();

        return Excel::download(new InfPrepIngExcExport($prepIng),
            'Presupuesto de Ingresos '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');

    }

    public function makeIngresosEjecucion(Request $request, $inicio, $final)
    {
        $presupuesto = $this->prepIngresos($inicio, $final);

        return Excel::download(new InfPrepIngExcExport($presupuesto),
            'Ejecucion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeEgresosPDF(){
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $dia = Carbon::now()->day;
        $presupuesto = $this->prepEgresos();

        $pdf = PDF::loadView('hacienda.presupuesto.informes.pdfEgresos', compact('añoActual','mesActual','dia','presupuesto'))
            ->setPaper('a3', 'landscape')
            ->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function makeIngresosPDF(){
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $dia = Carbon::now()->day;
        $presupuesto = $this->prepIngresos();

        $pdf = PDF::loadView('hacienda.presupuesto.informes.pdfIngresos', compact('añoActual','mesActual','dia','presupuesto'))
            ->setPaper('a3', 'landscape')
            ->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

}