<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\BPin;
use App\bpinVigencias;
use App\Exports\InfMensualExport;
use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\InformePresupuestosExport;
use Illuminate\Http\Request;
use Session;

class InformeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function make(Request $request)
    {

        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
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

        //ORDEN DE PAGO
        $ordenP = OrdenPagos::all();
        foreach ($ordenP as $ord){
            if ($ord->registros->cdpsRegistro->first()->cdp->vigencia_id == $V) {
                $ordenPagos[] = collect(['id' => $ord->id, 'code' => $ord->code, 'nombre' => $ord->nombre, 'persona' => $ord->registros->persona->nombre, 'valor' => $ord->valor, 'estado' => $ord->estado]);
                foreach ($ord->rubros as $rubroOP){
                    //SE LLENAN LAS ORDENES DE PAGO CON LOS VALORES PARA EL LLENADO DE LA TABLA DEL PRESUPUESTO
                    if ($rubroOP->orden_pago->estado == "1") {
                        if ($ord->registros->cdpsRegistro->first()->cdp->tipo == "Funcionamiento"){
                            $valores[] = ['id' => $rubroOP->cdps_registro->fontRubro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->fontRubro->rubro->plantilla_cuipos_id];
                        } else {
                            $bpinCdpValue = $rubroOP->cdps_registro->cdps->bpinsCdpValor->first();
                            $bpinID = BPin::where('cod_actividad', $bpinCdpValue->cod_actividad )->first();
                            $idRub = bpinVigencias::where('bpin_id', $bpinID->id)->where('vigencia_id', $V)->first();

                            $depRubFont = DependenciaRubroFont::find($idRub->dep_rubro_id);

                            $valores[] = ['id' => $idRub->dep_rubro_id, 'val' => $rubroOP->valor, 'code' => $depRubFont->fontRubro->rubro->plantilla_cuipos_id];
                        }
                    }
                }
            }
        }
        if (!isset($valores)){
            $valores[] = null;
            unset($valores[0]);
        }
        if (!isset($ordenPagos)){
            $ordenPagos[] = null;
            unset($ordenPagos[0]);
        } else {
            //PAGOS
            foreach ($ordenPagos as $data){
                $pagoFind = Pagos::where('orden_pago_id',$data['id'])->get();
                if ($pagoFind->count() == 1){
                    $oPago = OrdenPagos::find($data['id']);
                    foreach ($oPago->rubros as $rubroOP){
                        //SE LLENAN LOS PAGOS CON LOS VALORES PARA EL LLENADO DE LA TABLA DEL PRESUPUESTO
                        if ($rubroOP->orden_pago->estado == "1") $valoresPagos[] = ['id' => $rubroOP->cdps_registro->fontRubro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->fontRubro->rubro->plantilla_cuipos_id];
                    }
                    $pagos[] = collect(['id' => $pagoFind[0]->id, 'code' =>$pagoFind[0]->code, 'nombre' => $data['nombre'], 'persona' => $pagoFind[0]->orden_pago->registros->persona->nombre, 'valor' => $pagoFind[0]->valor, 'estado' => $pagoFind[0]->estado]);
                } elseif($pagoFind->count() > 1){
                    foreach ($pagoFind as $info){
                        $oPago = OrdenPagos::find($info->id);
                        if (isset($oPago->rubros)){
                            foreach ($oPago->rubros as $rubroOP){
                                //SE LLENAN LOS PAGOS CON LOS VALORES PARA EL LLENADO DE LA TABLA DEL PRESUPUESTO
                                if ($rubroOP->orden_pago->estado == "1") $valoresPagos[] = ['id' => $rubroOP->cdps_registro->fontRubro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->fontRubro->rubro->plantilla_cuipos_id];
                            }
                        }
                        $pagos[] = collect(['id' => $info->id, 'code' => $info->code, 'nombre' => $data['nombre'], 'persona' => $info->orden_pago->registros->persona->nombre, 'valor' => $info->valor, 'estado' => $info->estado]);
                    }
                }
            }
        }
        if (!isset($pagos)){
            $pagos[] = null;
            unset($pagos[0]);
        }
        if (!isset($valoresPagos)){
            $valoresPagos[] = null;
            unset($valoresPagos[0]);
        }

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
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

                    //SOLO SE MUESTRA EL VALOR DEL PRESUPUESTO CUANDO NO SON USUARIOS DE TIPO SECRETARIA
                    if (auth()->user()->roles->first()->id != 2){
                        $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name,
                            'presupuesto_inicial' => $vigens[0]->presupuesto_inicial, 'adicion' => array_sum($valueRubrosAdd),
                            'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                            'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs),
                            'registros' => array_sum($valueRegistros), 'saldo_disp' => $PDef - array_sum($valueCDPs),
                            'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                            'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos),
                            'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago), 'rubros_disp' => 0, 'codBpin' => '',
                            'codActiv' => '', 'nameActiv' => '','codDep' => '', 'dep' => '', 'depRubID' => ''];
                    }

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
                                    } else{
                                        /**

                                        SE OCULTAN LOS DATOS DE LOS PADRES DE LOS RUBROS A LOS USUARIOS QUE SEAN DE TIPO
                                        SECRETARIA, DE ESTA FORMA VERAN SOLO LOS RUBROS QUE LES CORRESPONDE

                                        if (count($fuenteRubro->dependenciaFont) > 0){
                                        foreach ($fuenteRubro->dependenciaFont as $depFont){
                                        if ($depFont->dependencia_id == auth()->user()->dependencia->id){
                                        $valueRubros[] = $depFont->value;
                                        }
                                        }
                                        }

                                         **/
                                    }
                                }
                            } else $valueRubros[] = 0;

                            //2 ADD - 3 RED  - 1 CRED

                            if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                    if ($mov->valor > 0 ){
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
                                            $valueCDPs[] = $cdp->valor;
                                            if (count($cdp->cdps->cdpsRegistro) > 0) {
                                                //CONSULTA PARA LOS REGISTROS
                                                $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                foreach ($cdpsRegValue as $cdpRValue){
                                                    if ($cdpRValue->valor != 0) {
                                                        if ($cdpRValue->registro->jefe_e == 3) {
                                                            //VALOR REGISTROS
                                                            $valueRegistros[] = $cdpRValue->valor;
                                                            //VALOR ORDENES DE PAGO
                                                            $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $cdpRValue->id)->get();
                                                            if (count($ordenPagoRubros) > 0){
                                                                $ordenPagoRubro = $ordenPagoRubros->first();
                                                                if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $cdpRValue->registro_id){
                                                                    $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                    if ($ordenPagoRubro->orden_pago->pago){
                                                                        if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $valuePagos[] = $ordenPagoRubro->valor;
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
                            'dep' => '', 'depRubID' => ''];
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
                foreach ($rubro->first()->fontsRubro as $fuente){
                    $sourceFund = SourceFunding::findOrFail($fuente->source_fundings_id);
                    $fonts[] = ['id' => $rubro[0]->cod ,'idFont' => $sourceFund->id, 'code' => $sourceFund->code, 'description' => $sourceFund->description, 'value' => $fuente->valor ];
                }

                if (auth()->user()->roles->first()->id != 2){
                    $key = array_search($oldId, array_column($presupuesto, 'id'));
                    if ($key == false) {
                        $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$oldCode.".')");
                        if($otherRubs and $oldCode != null) {
                            foreach ($otherRubs as $other) {
                                $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();
                                if($rubroOtherFind->first()) {

                                    $exit = false;
                                    //VALIDACION DE ROL
                                    if (auth()->user()->roles->first()->id != 2){
                                        if($rubroOtherFind->first()->fontsRubro){
                                            foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) {
                                                $valueRubros[] = $fuenteRubro->valor;
                                                $valueRubrosDisp[] = $fuenteRubro->valor_disp;

                                                //VALIDACION PARA LAS ADICIONES Y REDUCCIONES
                                                foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                                    if ($mov->valor > 0 ){
                                                        if ($mov->movimiento == "2") {
                                                            if ($mov->fonts_rubro_id == $fuenteRubro->id) $valueRubrosAdd[] = $mov->valor;
                                                        }
                                                        elseif ($mov->movimiento == "3") {
                                                            if ($mov->fonts_rubro_id == $fuenteRubro->id) $valueRubrosRed[] = $mov->valor;
                                                        }
                                                    }
                                                }


                                            }
                                        } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;
                                    } else {
                                        /**

                                        SE OCULTAN LOS DATOS DE LOS PADRES DE LOS RUBROS A LOS USUARIOS QUE SEAN DE TIPO
                                        SECRETARIA, DE ESTA FORMA VERAN SOLO LOS RUBROS QUE LES CORRESPONDE

                                        if($rubroOtherFind->first()->fontsRubro){
                                        foreach ($rubroOtherFind->first()->fontsRubro as $itemFont) {
                                        if (count($itemFont->dependenciaFont) > 0){
                                        foreach ($itemFont->dependenciaFont as $depFont){
                                        if ($depFont->dependencia_id == auth()->user()->dependencia->id){
                                        $valueRubros[] = $depFont->value;
                                        $valueRubrosDisp[] = $depFont->saldo;
                                        }
                                        }
                                        }
                                        }
                                        } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;

                                         **/
                                    }

                                    if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                        foreach ($rubroOtherFind->first()->rubrosMov as $mov){

                                            if ($mov->valor > 0 ){
                                                if ($mov->movimiento == "1") {
                                                    $valueRubrosCred[] = $mov->valor;
                                                    $valueRubrosCCred[] = $mov->valor;
                                                    $rubAfectado = FontsRubro::find($mov->fonts_rubro_id);
                                                    $rubrosCC[] = ['id'=> $rubAfectado->rubro->plantilla_cuipos_id, 'value'=> $mov->valor];
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
                                                    $valueCDPs[] = $cdp->valor;
                                                    if (count($cdp->cdps->cdpsRegistro) > 0){
                                                        //CONSULTA PARA LOS REGISTROS
                                                        $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                        foreach ($cdpsRegValue as $data){
                                                            if ($data->valor != 0){
                                                                if ($data->registro->jefe_e == 3){
                                                                    //VALOR REGISTROS
                                                                    $valueRegistros[] = $data->valor;
                                                                    //ID REGISTROS
                                                                    $IDRegistros[] = $data->registro_id;
                                                                    //VALOR ORDENES DE PAGO
                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $data->id)->get();
                                                                    if (count($ordenPagoRubros) > 0){
                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $data->registro_id){
                                                                            $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $valuePagos[] = $ordenPagoRubro->valor;
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
                                    'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => '', 'codDep' => '', 'dep' => '', 'depRubID' => ''];
                            }

                            unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                            unset($valueOrdenPago);unset($valuePagos);unset($valueRubros);unset($valueRubrosDisp);unset($rubrosCC);
                        }
                    }
                }

                if($rubro->first()->fontsRubro){
                    //RUBROS HIJOS
                    //EN ESTA VALIDACION SE MUESTRAN LOS VALORES DE RUBROS USADOS DEPENDIENDO LA DEP DEL USUARIO
                    $exit = false;
                    foreach ($rubro->first()->fontsRubro as $itemFont){
                        //VALIDACION PARA LAS ADICIONES Y REDUCCIONES
                        if(count($itemFont->rubrosMov) > 0){
                            foreach ($itemFont->rubrosMov as $mov){
                                if ($mov->valor > 0 ){
                                    if ($mov->movimiento == "2") {
                                        if ($mov->fonts_rubro_id == $itemFont->id) $valueRubrosAdd[] = $mov->valor;
                                    }
                                    elseif ($mov->movimiento == "3") {
                                        if ($mov->fonts_rubro_id == $itemFont->id) $valueRubrosRed[] = $mov->valor;
                                    }
                                }
                            }
                        } else {
                            $valueRubrosAdd[] = 0;
                            $valueRubrosRed[] = 0;
                        }

                        if (count($itemFont->dependenciaFont) > 0){
                            foreach ($itemFont->dependenciaFont as $depFont){
                                //SE VALIDA SI ES USUARIO SUPERIOR A SECRETARIA
                                if (auth()->user()->roles->first()->id != 2){
                                    $value[] = $depFont->value;
                                    $valueRubrosDisp[] = $depFont->saldo;
                                } else {
                                    //SE VALIDA QUE LOS VALORES QUE VEA SEAN UNICAMENTE DE SU DEPENDENCIA
                                    if ($depFont->dependencia_id == auth()->user()->dependencia->id){
                                        $value[] = $depFont->value;
                                        $valueRubrosDisp[] = $depFont->saldo;
                                    }
                                }

                                //VALORES DE CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)->get();
                                if(count($rubrosCredMov) > 0) $valueRubrosCred[] = $rubrosCredMov->sum('valor');
                                else $valueRubrosCred[] = 0;

                                //VALORES DE CONTRA CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                                $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)->get();
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
                                                    if ($bpinCDP->dependencia_rubro_font_id == $depFont->id) $valueCDPs[] = $bpinCDP->valor;
                                                } else $valueCDPs[] = $bpinCDP->valor;
                                                $cdpsRegValue = CdpsRegistroValor::where('cdp_id', $bpinCDP->cdp->id)->get();
                                                if (count($cdpsRegValue) > 0){
                                                    //CONSULTA PARA LOS REGISTROS
                                                    foreach ($cdpsRegValue as $data){
                                                        if ($data->valor != 0){
                                                            if ($data->registro->jefe_e == 3){
                                                                if ($itemFont->id == $data->fontsRubro_id){
                                                                    //VALOR REGISTROS
                                                                    $valueRegistros[] = $data->valor;
                                                                    //ID REGISTROS
                                                                    $IDRegistros[] = $data->registro_id;
                                                                    //VALOR ORDENES DE PAGO
                                                                    $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $data->id)->get();
                                                                    if (count($ordenPagoRubros) > 0){
                                                                        $ordenPagoRubro = $ordenPagoRubros->first();
                                                                        if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $data->registro_id){
                                                                            $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                            if ($ordenPagoRubro->orden_pago->pago){
                                                                                if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $valuePagos[] = $ordenPagoRubro->valor;
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
                                                $valueCDPs[] = $cdp->valor;
                                                if (count($cdp->cdps->cdpsRegistro) > 0){
                                                    //CONSULTA PARA LOS REGISTROS
                                                    $cdpsRegValue = CdpsRegistroValor::where('fontsRubro_id', $cdp->fontsRubro_id)->where('cdp_id', $cdp->cdp_id)->get();
                                                    foreach ($cdpsRegValue as $data){
                                                        if ($data->valor != 0){
                                                            if ($data->registro->jefe_e == 3){
                                                                //VALOR REGISTROS
                                                                $valueRegistros[] = $data->valor;
                                                                //ID REGISTROS
                                                                $IDRegistros[] = $data->registro_id;
                                                                //VALOR ORDENES DE PAGO
                                                                $ordenPagoRubros = OrdenPagosRubros::where('cdps_registro_valor_id', $data->id)->get();
                                                                if (count($ordenPagoRubros) > 0){
                                                                    $ordenPagoRubro = $ordenPagoRubros->first();
                                                                    if ($ordenPagoRubro->orden_pago->estado == 1 and $ordenPagoRubro->orden_pago->registros_id == $data->registro_id){
                                                                        $valueOrdenPago[] = $ordenPagoRubro->valor;
                                                                        if ($ordenPagoRubro->orden_pago->pago){
                                                                            if ($ordenPagoRubro->orden_pago->pago->estado == 1 ) $valuePagos[] = $ordenPagoRubro->valor;
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

                                    $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                                        'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                        'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                        'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                        'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                        'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign),
                                        'codDep' => $code, 'dep' => $depFont->dependencias->name, 'depRubID' => $depFont->id ];

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
                        'codDep' => '', 'dep' => '', 'depRubID' => ''];
                }
            }
            if ($data->hijo == 0) {
                $oldId = $data->id;
                $oldCode = $data->code;
                $oldName = $data->name;
            }
        }

        //CDPS
        $cdps= Cdp::where('vigencia_id', $V)->get();

        //REGISTROS
        $allReg = Registro::all();
        foreach ($allReg as $reg) if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $V) $registros[] = ['id' => $reg->id, 'code' => $reg->code, 'objeto' => $reg->objeto, 'nombre' => $reg->persona->nombre, 'valor' => $reg->valor,
            'estadoSecretaria' => $reg->secretaria_e, 'estadoJefe' => $reg->jefe_e];

        //CODE CONTRACTUALES
        $codeCon = CodeContractuales::all();
        $lastDay = Carbon::now()->subDay()->toDateString();
        $actuallyDay = Carbon::now()->toDateString();

        //Rubros no asignados a alguna actividad
        foreach ($presupuesto as $item){
            if ($item['id_rubro'] != ""){
                if ($item['tipo'] == "Inversion") {
                    $validationUsed = bpinVigencias::where('vigencia_id', $V)->where('dep_rubro_id', $item['depRubID'])->get();
                    //SE VALIDA SI EL RUBRO DE LA DEPENDENCIA HA SIDO USADO
                    if (count($validationUsed) == 0) $rubBPIN[] = collect($item);
                }
            }
        }

        if (!isset($rubBPIN)){
            $rubBPIN[] = null;
            unset($rubBPIN[0]);
        }

        if (!isset($registros)){
            $registros[] = null;
            unset($registros[0]);
        }

        return Excel::download(new InformePresupuestosExport($añoActual, $presupuesto, $mesActual, $diaActual),
            'Presupuesto de Egresos '.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

}