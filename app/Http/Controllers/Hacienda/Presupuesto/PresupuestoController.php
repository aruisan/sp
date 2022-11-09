<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\Http\Controllers\Administrativo\Tesoreria\PacController;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Administrativo\ComprobanteIngresos\CIRubros;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Administrativo\Tesoreria\Pac;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Cdp\Cdp;
use App\Http\Controllers\Controller;
use App\BPin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;
use Session;
use function Complex\add;


class PresupuestoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:presupuesto-list');
    }


    public function index(){
        $bpins = BPin::all();
        foreach ($bpins as $bpin){
            if ($bpin->rubro_id != 0) $bpin['rubro'] = $bpin->rubro->name;
            else $bpin['rubro'] = "No";
        }
        //dd($bpins);

        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
        $historico = Vigencia::where('vigencia', '!=', $añoActual)->get();
        foreach ($historico as $his) {
            if ($his->tipo == "0"){
                $years[] = [ 'info' => $his->vigencia." - Egresos", 'id' => $his->id];
            }else{
                $years[] = [ 'info' => $his->vigencia." - Ingresos", 'id' => $his->id];
            }
        }
        asort($years);

        if ($vigens->count() == 0){
            $V = "Vacio";
            return view('hacienda.presupuesto.index', compact('V', 'añoActual','mesActual', 'bpins'));
        } else {
            $V = $vigens[0]->id;
            $vigencia_id = $V;

            //ORDEN DE PAGO
            $ordenP = OrdenPagos::all();
            foreach ($ordenP as $ord){
                if ($ord->registros->cdpsRegistro->first()->cdp->vigencia_id == $V) {
                    $ordenPagos[] = collect(['id' => $ord->id, 'code' => $ord->code, 'nombre' => $ord->nombre, 'persona' => $ord->registros->persona->nombre, 'valor' => $ord->valor, 'estado' => $ord->estado]);
                    foreach ($ord->rubros as $rubroOP){
                        //SE LLENAN LAS ORDENES DE PAGO CON LOS VALORES PARA EL LLENADO DE LA TABLA DEL PRESUPUESTO
                        if ($rubroOP->orden_pago->estado == "1") $valores[] = ['id' => $rubroOP->cdps_registro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->rubro->plantilla_cuipos_id];
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
                            if ($rubroOP->orden_pago->estado == "1") $valoresPagos[] = ['id' => $rubroOP->cdps_registro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->rubro->plantilla_cuipos_id];
                        }
                        $pagos[] = collect(['id' => $pagoFind[0]->id, 'code' =>$pagoFind[0]->code, 'nombre' => $data['nombre'], 'persona' => $pagoFind[0]->orden_pago->registros->persona->nombre, 'valor' => $pagoFind[0]->valor, 'estado' => $pagoFind[0]->estado]);
                    } elseif($pagoFind->count() > 1){
                        foreach ($pagoFind as $info){
                            $oPago = OrdenPagos::find($info->id);
                            foreach ($oPago->rubros as $rubroOP){
                                //SE LLENAN LOS PAGOS CON LOS VALORES PARA EL LLENADO DE LA TABLA DEL PRESUPUESTO
                                if ($rubroOP->orden_pago->estado == "1") $valoresPagos[] = ['id' => $rubroOP->cdps_registro->rubro->id, 'val' => $rubroOP->valor, 'code' => $rubroOP->cdps_registro->rubro->plantilla_cuipos_id];
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

                                //CDPS
                                if(count($rubroOtherFind->first()->rubrosCdp) > 0){
                                    foreach ($rubroOtherFind->first()->rubrosCdp as $cdp) if ($cdp->cdps->jefe_e == "3") $valueCDPs[] = $cdp->cdps->valor;
                                } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;

                                //REGISTROS
                                if(count($rubroOtherFind->first()->cdpRegistroValor) > 0){
                                    foreach ($rubroOtherFind->first()->cdpRegistroValor as $reg) if ($reg->registro->secretaria_e == "3") $valueRegistros[] = $reg->registro->valor;
                                } else $valueRegistros[] = 0;

                                //ORDENES DE PAGO
                                if (isset($valores)){
                                    foreach ($valores as $dataOP){
                                        if ($dataOP['code'] == $other->id) {
                                            $valueOrdenPago[] = $dataOP['val'];
                                        }
                                    }
                                }

                                //PAGOS
                                if (isset($valoresPagos)){
                                    foreach ($valoresPagos as $dataP){
                                        if ($dataP['code'] == $other->id) {
                                            $valuePagos[] = $dataP['val'];
                                        }
                                    }
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

                        $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => $vigens[0]->presupuesto_inicial,
                            'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                            'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                            'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                            'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                            'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => ''];

                        unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                        unset($valueOrdenPago);unset($valuePagos);
                    } else {
                        if(auth()->user()->roles->first()->id != 2){
                            $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$data->code.".')");

                            foreach ($otherRubs as $other) {
                                $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();

                                if($rubroOtherFind->first()) {

                                    if($rubroOtherFind->first()->fontsRubro){
                                        foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) $valueRubros[] = $fuenteRubro->valor;
                                    } else $valueRubros[] = 0;
                                    //2 ADD - 3 RED  - 1 CRED

                                    if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                        foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                            if ($mov->valor > 0 ){
                                                if ($mov->movimiento == "1") {
                                                    $valueRubrosCred[] = $mov->valor;
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
                                    if (isset($rubrosCC)) foreach ($rubrosCC as $cc) if ($cc['id'] == $other->id) $valueRubrosCCred[] = $cc['value'];

                                    //CDPS
                                    if(count($rubroOtherFind->first()->rubrosCdp) > 0){
                                        foreach ($rubroOtherFind->first()->rubrosCdp as $cdp) if ($cdp->cdps->jefe_e == "3") $valueCDPs[] = $cdp->cdps->valor;
                                    } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;

                                    //REGISTROS
                                    if(count($rubroOtherFind->first()->cdpRegistroValor) > 0){
                                        foreach ($rubroOtherFind->first()->cdpRegistroValor as $reg) if ($reg->registro->secretaria_e == "3") $valueRegistros[] = $reg->registro->valor;
                                    } else $valueRegistros[] = 0;

                                    //ORDENES DE PAGO
                                    if (isset($valores)){
                                        foreach ($valores as $dataOP) {
                                            if ($dataOP['code'] == $other->id) {
                                                $valueOrdenPago[] = $dataOP['val'];
                                            }
                                        }
                                    }

                                    //PAGOS
                                    if (isset($valoresPagos)){
                                        foreach ($valoresPagos as $dataP) {
                                            if ($dataP['code'] == $other->id) {
                                                $valuePagos[] = $dataP['val'];
                                            }
                                        }
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

                            $presupuesto[] = ['id_rubro' => 0 ,'id' => $data->id, 'cod' => $data->code, 'name' => $data->name, 'presupuesto_inicial' => array_sum($valueRubros),
                                'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => ''];

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

                    }

                } elseif (count($rubro) > 0) {

                    //LLENADO PARA LAS FUENTES DEL PRESUPUESTO
                    foreach ($rubro->first()->fontsRubro as $fuente){
                        $sourceFund = SourceFunding::findOrFail($fuente->source_fundings_id);
                        $fonts[] = ['id' => $rubro[0]->cod ,'idFont' => $sourceFund->id, 'code' => $sourceFund->code, 'description' => $sourceFund->description, 'value' => $fuente->valor ];
                    }

                    $key = array_search($oldId, array_column($presupuesto, 'id'));
                    if ($key == false) {
                        $otherRubs = DB::select("SELECT * from plantilla_cuipos where code REGEXP CONCAT('^','".$oldCode.".')");
                        if($otherRubs) {
                            foreach ($otherRubs as $other) {
                                $rubroOtherFind = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $other->id)->get();
                                if($rubroOtherFind->first()) {

                                    $exit = false;
                                    if (auth()->user()->roles->first()->id != 2){
                                        if($rubroOtherFind->first()->fontsRubro){
                                            foreach ($rubroOtherFind->first()->fontsRubro as $fuenteRubro) $valueRubros[] = $fuenteRubro->valor; $valueRubrosDisp[] = $fuenteRubro->valor_disp;
                                        } else $valueRubros[] = 0; $valueRubrosDisp[] = 0;
                                    } else {
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
                                    }

                                    if(count($rubroOtherFind->first()->rubrosMov) > 0){
                                        foreach ($rubroOtherFind->first()->rubrosMov as $mov){
                                            if ($mov->valor > 0 ){
                                                if ($mov->movimiento == "1") {
                                                    $valueRubrosCred[] = $mov->valor;
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
                                    if (isset($rubrosCC)) foreach ($rubrosCC as $cc) if ($cc['id'] == $other->id) $valueRubrosCCred[] = $cc['value'];

                                    //CDPS
                                    if(count($rubroOtherFind->first()->rubrosCdp) > 0){
                                        foreach ($rubroOtherFind->first()->rubrosCdp as $cdp) if ($cdp->cdps->jefe_e == "3") $valueCDPs[] = $cdp->cdps->valor;
                                    } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;

                                    //REGISTROS
                                    if(count($rubroOtherFind->first()->cdpRegistroValor) > 0){
                                        foreach ($rubroOtherFind->first()->cdpRegistroValor as $reg) if ($reg->registro->secretaria_e == "3") $valueRegistros[] = $reg->registro->valor;
                                    } else $valueRegistros[] = 0;

                                    //ORDENES DE PAGO
                                    if (isset($valores)){
                                        foreach ($valores as $dataOP) {
                                            if ($dataOP['code'] == $other->id) {
                                                $valueOrdenPago[] = $dataOP['val'];
                                            }
                                        }
                                    }

                                    //PAGOS
                                    if (isset($valoresPagos)){
                                        foreach ($valoresPagos as $dataP) {
                                            if ($dataP['code'] == $other->id) {
                                                $valuePagos[] = $dataP['val'];
                                            }
                                        }
                                    }

                                } else $valueRubros[] = 0;$valueCDPs[] = 0;$valueRegistros[] = 0;$valueOrdenPago[] = 0; $valuePagos[] = 0; $valueRubrosDisp[] = 0;
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

                            $presupuesto[] = ['id_rubro' => 0 ,'id' => $oldId, 'cod' => $oldCode, 'name' => $oldName, 'presupuesto_inicial' => array_sum($valueRubros),
                                'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                                'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                                'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                                'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                                'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => '', 'codActiv' => '', 'nameActiv' => ''];

                            unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                            unset($valueOrdenPago);unset($valuePagos);unset($valueRubros);unset($valueRubrosDisp);unset($rubrosCC);
                        }
                    }
                    if($rubro->first()->fontsRubro){
                        //RUBROS HIJOS
                        //EN ESTA VALIDACION SE MUESTRAN LOS VALORES DE RUBROS USADOS DEPENDIENDO LA DEP DEL USUARIO
                        $exit = false;
                        if (auth()->user()->roles->first()->id != 2){
                            foreach ($rubro->first()->fontsRubro as $itemFont) $value[] = $itemFont->valor; $valueRubrosDisp[] = $itemFont->valor_disp; $valueRubrosAsign[] = $itemFont->valor_disp_asign;
                        } else {
                            foreach ($rubro->first()->fontsRubro as $itemFont) {
                                if (count($itemFont->dependenciaFont) > 0){
                                    foreach ($itemFont->dependenciaFont as $depFont){
                                        if ($depFont->dependencia_id == auth()->user()->dependencia->id){
                                            $value[] = $depFont->value;
                                            $valueRubrosDisp[] = $depFont->saldo;
                                        }
                                    }
                                } else $exit = true;
                            }
                        }
                        if ($exit) break;

                        //TRAS VALIDAR ESO SE PROSIGUE CON EL LLENADO DE LOS DATOS DEL RUBRO.
                        if(count($rubro->first()->rubrosMov) > 0){
                            foreach ($rubro->first()->rubrosMov as $mov){
                                if ($mov->valor > 0 ){
                                    if ($mov->movimiento == "1") {
                                        $valueRubrosCred[] = $mov->valor;
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

                        //BPIN
                        if ($rubro->first()->bpin_id != null){
                            $codBpin = $rubro->first()->bpin->cod_proyecto;
                            $codActiv = $rubro->first()->bpin->cod_actividad;
                            $nameActiv = $rubro->first()->bpin->actividad;
                        } else{
                            $codBpin = "";
                            $codActiv = "";
                            $nameActiv = "";
                        }

                        //VALORES CONTRA CREDITO
                        foreach ($rubro->first()->fontsRubro as $font){
                            $ccred = RubrosMov::where('fonts_rubro_id', $font->id )->get();
                            if (count($ccred) > 0) foreach ($ccred as $c) $rubrosCC[] = ['id'=> $rubro[0]->plantilla_cuipos_id, 'value'=> $c->valor];
                        }

                        if (isset($rubrosCC))foreach ($rubrosCC as $cc) if ($cc['id'] == $rubro[0]->plantilla_cuipos_id) $valueRubrosCCred[] = $cc['value'];

                        //CDPS
                        if(count($rubro->first()->rubrosCdp) > 0){
                            foreach ($rubro->first()->rubrosCdp as $cdp) if ($cdp->cdps->jefe_e == "3") $valueCDPs[] = $cdp->cdps->valor;
                        } else $valueCDPs[] = 0; $valueOrdenPago[] = 0; $valuePagos[] = 0;

                        //REGISTROS
                        if(count($rubro->first()->cdpRegistroValor) > 0){
                            foreach ($rubro->first()->cdpRegistroValor as $reg) if ($reg->registro->secretaria_e == "3") $valueRegistros[] = $reg->registro->valor;
                        } else $valueRegistros[] = 0;

                        //ORDENES DE PAGO
                        if (isset($valores)){
                            foreach ($valores as $dataOP) {
                                if ($dataOP['code'] == $rubro->first()->plantilla_cuipos_id) {
                                    $valueOrdenPago[] = $dataOP['val'];
                                }
                            }
                        }

                        //PAGOS
                        if (isset($valoresPagos)){
                            foreach ($valoresPagos as $dataP) {
                                if ($dataP['code'] == $rubro->first()->plantilla_cuipos_id) {
                                    $valuePagos[] = $dataP['val'];
                                }
                            }
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

                        //PRESUPUESTO DEFINITIVO
                        if (isset($valueRubrosAdd) and isset($valueRubrosRed)) $PDef= array_sum($value) + array_sum($valueRubrosAdd) - array_sum($valueRubrosRed) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);
                        else $PDef = array_sum($value) + array_sum($valueRubrosCred) - array_sum($valueRubrosCCred);

                        $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro[0]->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => array_sum($value),
                            'adicion' => array_sum($valueRubrosAdd), 'reduccion' => array_sum($valueRubrosRed), 'credito' => array_sum($valueRubrosCred),
                            'ccredito' => array_sum($valueRubrosCCred), 'presupuesto_def' => $PDef, 'cdps' => array_sum($valueCDPs), 'registros' => array_sum($valueRegistros),
                            'saldo_disp' => $PDef - array_sum($valueCDPs), 'saldo_cdp' => array_sum($valueCDPs) - array_sum($valueRegistros), 'ordenes_pago' => array_sum($valueOrdenPago),
                            'pagos' => array_sum($valuePagos), 'cuentas_pagar' => array_sum($valueOrdenPago) - array_sum($valuePagos), 'reservas' => array_sum($valueRegistros) - array_sum($valueOrdenPago),
                            'rubros_disp' => array_sum($valueRubrosDisp), 'codBpin' => $codBpin, 'codActiv' => $codActiv, 'nameActiv' => $nameActiv, 'tipo' => $rubro->first()->tipo, 'rubros_asign' => array_sum($valueRubrosAsign)];

                        unset($value);unset($valueRubrosAdd);unset($valueRubrosRed);unset($valueRubrosCred);unset($valueRubrosCCred);unset($valueCDPs);unset($valueRegistros);
                        unset($valueOrdenPago);unset($valuePagos);unset($valueRubrosDisp);unset($rubrosCC);unset($valueRubrosAsign);

                    } else {
                        $presupuesto[] = ['id_rubro' => $rubro->first()->id ,'id' => $rubro->first()->plantilla_cuipos_id, 'cod' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'presupuesto_inicial' => 0,
                            'adicion' => 0, 'reduccion' => 0, 'credito' => 0, 'ccredito' => 0, 'presupuesto_def' => 0, 'cdps' => 0, 'registros' => 0,
                            'saldo_disp' => 0, 'ordenes_pago' => 0, 'pagos' => 0, 'cuentas_pagar' => 0, 'reservas' => 0, 'rubros_disp' => 0, 'codBpin' => '', 'codActiv' => '', 'nameActiv' => ''];
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
            foreach ($allReg as $reg) if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $V) $registros[] = ['id' => $reg->id, 'code' => $reg->code, 'objeto' => $reg->objeto, 'nombre' => $reg->persona->nombre, 'valor' => $reg->valor, 'estado' => $reg->secretaria_e];
            if (!isset($registros)){
                $registros[] = null;
                unset($registros[0]);
            }

            //dd($presupuesto);

            return view('hacienda.presupuesto.indexCuipo', compact('V', 'presupuesto', 'añoActual', 'mesActual', 'years', 'fonts', 'cdps', 'registros','ordenPagos', 'pagos', 'bpins'));

            /**
             *
             *
             * $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
             * $primerLevel = Level::where('vigencia_id', $vigencia_id)->get()->first();
             * $registers = Register::where('level_id', $ultimoLevel['id'])->get();
             * dd($ultimoLevel);
             *
             * $registers2 = Register::where('level_id', '<', $ultimoLevel['id'])->get();
             * $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel['id'])->get()->last();
             * $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
             * $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
             * $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
             * $allRegisters = Register::orderByDesc('level_id')->get();
             * $ordenP = OrdenPagos::all();
             * foreach ($ordenP as $ord){
             * if ($ord->registros->cdpsRegistro[0]->cdp->vigencia_id == $V){
             * $ordenPagos[] = collect(['id' => $ord->id, 'code' => $ord->code, 'nombre' => $ord->nombre, 'persona' => $ord->registros->persona->nombre, 'valor' => $ord->valor, 'estado' => $ord->estado]);
             * }
             * }
             * if (!isset($ordenPagos)){
             * $ordenPagos[] = null;
             * unset($ordenPagos[0]);
             * } else {
             * foreach ($ordenPagos as $data){
             * $pagoFind = Pagos::where('orden_pago_id',$data['id'])->get();
             * if ($pagoFind->count() == 1){
             * $pagos[] = collect(['id' => $pagoFind[0]->id, 'code' =>$pagoFind[0]->code, 'nombre' => $data['nombre'], 'persona' => $pagoFind[0]->orden_pago->registros->persona->nombre, 'valor' => $pagoFind[0]->valor, 'estado' => $pagoFind[0]->estado]);
             * } elseif($pagoFind->count() > 1){
             * foreach ($pagoFind as $info){
             * $pagos[] = collect(['id' => $info->id, 'code' => $info->code, 'nombre' => $data['nombre'], 'persona' => $info->orden_pago->registros->persona->nombre, 'valor' => $info->valor, 'estado' => $info->estado]);
             * }
             * }
             * }
             * }
             * if (!isset($pagos)){
             * $pagos[] = null;
             * unset($pagos[0]);
             * }
             *
             * global $lastLevel;
             * $lastLevel = $ultimoLevel->id;
             * $lastLevel2 = $ultimoLevel2->level_id;
             *
             * foreach ($fonts as $font){
             * $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
             * }
             *
             * foreach ($fontsRubros as $fontsRubro){
             * if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
             * $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
             * }
             * }
             * $tamFountsRubros = count($fuentesRubros);
             *
             * foreach ($registers2 as $register2) {
             * if ($register2->level->vigencia_id == $vigencia_id) {
             * global $codigoLast;
             * if ($register2->register_id == null) {
             * $codigoEnd = $register2->code;
             * $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
             * } elseif ($codigoLast > 0) {
             * if ($lastLevel2 == $register2->level_id) {
             * $codigo = $register2->code;
             * $codigoEnd = "$codigoLast$codigo";
             * $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
             * foreach ($registers as $register) {
             * if ($register2->id == $register->register_id) {
             * $register_id = $register->code_padre->registers->id;
             * $code = $register->code_padre->registers->code . $register->code;
             * $ultimo = $register->code_padre->registers->level->level;
             *
             * while ($ultimo > 1) {
             * $registro = Register::findOrFail($register_id);
             * $register_id = $registro->code_padre->registers->id;
             * $code = $registro->code_padre->registers->code . $code;
             *
             * $ultimo = $registro->code_padre->registers->level->level;
             * }
             * $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
             * if ($register->level_id == $lastLevel) {
             * foreach ($rubros as $rubro) {
             * if ($register->id == $rubro->register_id) {
             * $newCod = "$code$rubro->cod";
             * $fR = $rubro->FontsRubro;
             * //dd($newCod, $fR);
             * for ($i = 0; $i < $tamFountsRubros; $i++) {
             * $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_vigencia_id')->get();
             * $numR = count($rubrosF);
             * $numF = count($fonts);
             * if ($numR == $numF) {
             * if ($fuentesRubros[$i]['rubro_id'] == $rubro->id) {
             * $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['font_vigencia_id']]);
             * }
             * } else {
             * foreach ($fonts as $font) {
             * if ($fuentesRubros[$i]['font_vigencia_id'] == $font->id) {
             * $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'font_vigencia_id' => $font->id]);
             * } else {
             * $findFont = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->where('font_vigencia_id', $font->id)->get();
             * $numFinds = count($findFont);
             * if ($numFinds >= 1) {
             *
             * $saveRubroF = new FontsRubro();
             *
             * $saveRubroF->valor = 0;
             * $saveRubroF->valor_disp = 0;
             * $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
             * $saveRubroF->font_vigencia_id = $font->id + 1;
             *
             * $saveRubroF->save();
             *
             * break;
             * } else {
             *
             * $saveRubroF = new FontsRubro();
             *
             * $saveRubroF->valor = 0;
             * $saveRubroF->valor_disp = 0;
             * $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
             * $saveRubroF->font_vigencia_id = $font->id;
             *
             * $saveRubroF->save();
             *
             * break;
             * }
             * }
             * }
             * }
             * }
             * $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
             * $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
             * $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
             * $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
             * }
             * }
             * }
             * }
             * }
             * } else {
             * $codigo = $register2->code;
             * $codigoEnd = "$codigoLast$codigo";
             * $codigoLast = $codigoEnd;
             * $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
             * }
             * } else {
             * $codigo = $register2->code;
             * $newRegisters = Register::findOrFail($register2->register_id);
             * $codigoNew = $newRegisters->code;
             * $codigoEnd = "$codigoNew$codigo";
             * $codigoLast = $codigoEnd;
             * $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
             * }
             * }
             * }
             *
             * //VALOR DEL PRESUPUESTO INICIAL
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
             * $ArraytotalFR[] = $valFuent;
             * }
             * if (isset($ArraytotalFR)) {
             * $totalFR = array_sum($ArraytotalFR);
             * $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalFR);
             * } else {
             * $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
             * if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresIniciales[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //VALOR TOTAL DE LAS ADICIONES
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valAdd = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"2")->sum('valor');
             * $ArraytotalAdd[] = $valAdd;
             * }
             * if (isset($ArraytotalAdd)) {
             * $totalAdd = array_sum($ArraytotalAdd);
             * $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $totalAdd, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalAdd);
             * } else {
             * $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinAdd); $i++) {
             * if ($valoresFinAdd[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinAdd[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //VALOR TOTAL DE LAS REDUCCIONES
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valRed = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"3")->sum('valor');
             * $ArraytotalRed[] = $valRed;
             * }
             * if (isset($ArraytotalRed)) {
             * $totalRed = array_sum($ArraytotalRed);
             * $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $totalRed, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalRed);
             * } else {
             * $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinRed); $i++) {
             * if ($valoresFinRed[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinRed[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * // VALOR TOTAL DE LOS CREDITOS
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valCred = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"1")->sum('valor');
             * $ArraytotalCred[] = $valCred;
             * }
             * if (isset($ArraytotalCred)) {
             * $totalCred = array_sum($ArraytotalCred);
             * $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalCred);
             * } else {
             * $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinCred); $i++) {
             * if ($valoresFinCred[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinCred[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * // VALOR TOTAL DE LOS CONTRACREDITOS
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * foreach ($rubrosReg->fontsRubro as $FR){
             * foreach ($FR->rubrosMov as $movR){
             * if ($movR->movimiento == 1){
             * $ArraytotalCCred[] = $movR->valor;
             * }
             * }
             * }
             * }
             * if (isset($ArraytotalCCred)) {
             * $totalCCred = array_sum($ArraytotalCCred);
             * $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalCCred);
             * } else {
             * $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinCCred); $i++) {
             * if ($valoresFinCCred[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinCCred[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //VALOR TOTAL DE CDPS
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * foreach ($rubrosReg->rubrosCdp as $Rcdp){
             * $cdpInfo = Cdp::where('id', $Rcdp->cdps->id)->where('jefe_e', '!=', 3)->get();
             * if ($cdpInfo->count() > 0 ){
             * $ArraytotalCdp[] = $Rcdp->cdps->valor;
             * }
             * }
             * }
             * if (isset($ArraytotalCdp)) {
             * $totalCdp = array_sum($ArraytotalCdp);
             * $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $totalCdp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalCdp);
             * } else {
             * $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinCdp); $i++) {
             * if ($valoresFinCdp[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinCdp[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //VALOR TOTAL DE LOS REGISTROS
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosFReg = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosFReg as $rubro) {
             * if ($rubro->cdpRegistroValor->count() == 0){
             * $ArraytotalReg[] =  0 ;
             * }elseif ($rubro->cdpRegistroValor->count() > 1){
             * foreach ($rubro->cdpRegistroValor as $cdpRV){
             * if ($cdpRV->registro->secretaria_e == "3"){
             * $sumaValores[] = $cdpRV->registro->val_total;
             * }
             * }
             * $ArraytotalReg[] = array_sum($sumaValores);
             * unset($sumaValores);
             * }else{
             * $reg = $rubro->cdpRegistroValor->first();
             * $ArraytotalReg[] = $reg['valor'];
             * }
             * }
             * if (isset($ArraytotalReg)) {
             * $totalReg = array_sum($ArraytotalReg);
             * $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $totalReg, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalReg);
             * } else {
             * $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinReg); $i++) {
             * if ($valoresFinReg[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinReg[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //SUMA DE VALOR DISPONIBLE DEL RUBRO - CDP
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor_disp');
             * $ArraytotalFR[] = $valFuent;
             * }
             * if (isset($ArraytotalFR)) {
             * $totalFR = array_sum($ArraytotalFR);
             * $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalFR);
             * } else {
             * $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valorDisp); $i++) {
             * if ($valorDisp[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valorDisp[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             * }
             *
             * //VALOR DE LOS CDPS DEL RUBRO
             * foreach ($rubros as $R){
             * if ($R->rubrosCdp->count() == 0){
             * $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0 ]) ;
             * }
             * if ($R->rubrosCdp->count() > 1){
             * foreach ($R->rubrosCdp as $R3){
             * if ($R3->cdps->jefe_e == "2"){
             * $suma2[] = 0;
             * } else{
             * $suma2[] = $R3->rubrosCdpValor->sum('valor');
             * }
             * }
             * $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => array_sum($suma2)]) ;
             * unset($suma2);
             * }else{
             * foreach ($R->rubrosCdp as $R2){
             * if ($R2->cdps->jefe_e == "2"){
             * $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0]) ;
             * }else {
             * $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => $R2->rubrosCdpValor->sum('valor')]) ;
             * }
             * }
             * }
             * }
             *
             * //VALOR DE LOS REGISTROS DEL RUBRO
             * foreach ($rubros as $rub){
             * if ($rub->cdpRegistroValor->count() == 0){
             * $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => 0 ]) ;
             * }elseif ($rub->cdpRegistroValor->count() > 1){
             * foreach ($rub->cdpRegistroValor as $cdpRV){
             * if ($cdpRV->registro->secretaria_e == "3"){
             * $sumaValores[] = $cdpRV->registro->val_total;
             * }
             * }
             * $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => array_sum($sumaValores)]);
             * unset($sumaValores);
             * }else{
             * $reg = $rub->cdpRegistroValor->first();
             * $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => $reg['valor']]) ;
             * }
             * }
             *
             * //VALORES TOTALES SALDO CDP
             *
             * for ($i=0;$i<count($valoresFinCdp);$i++){
             * $valorFcdp[] = collect(['id' => $valoresFinCdp[$i]['id'], 'valor' => $valoresFinCdp[$i]['valor'] - $valoresFinReg[$i]['valor']]);
             * }
             *
             *
             * //VALOR DISPONIBLE CDP - REGISTROS
             * for ($i=0;$i<count($valoresCdp);$i++){
             * $valorDcdp[] = collect(['id' => $valoresCdp[$i]['id'], 'valor' => $valoresCdp[$i]['valor'] - $valoresRubro[$i]['valor']]);
             * }
             *
             *
             * //ORDEN DE PAGO
             *
             * $OP = OrdenPagosRubros::all();
             * if ($OP->count() != 0){
             * foreach ($OP as $val){
             * //Se valida que la orden de pago este finalizada
             * if ($val->orden_pago->estado == "1") $valores[] = ['id' => $val->cdps_registro->rubro->id, 'val' => $val->valor];
             * }
             * foreach ($valores as $id){
             * $ids[] = $id['id'];
             * }
             * $valores2 = array_unique($ids);
             * foreach ($valores2 as $valUni){
             * $keys = array_keys(array_column($valores, 'id'), $valUni);
             * foreach ($keys as $key){
             * $values[] = $valores[$key]["val"];
             * }
             * $valoresF[] = ['id' => $valUni, 'valor' => array_sum($values)];
             * unset($values);
             * }
             * foreach ($rubros as $rub){
             * $validate = in_array($rub->id, $valores2);
             * if ($validate == true ){
             * $data = array_keys(array_column($valoresF, 'id'), $rub->id);
             * $x[] = $valoresF[$data[0]];
             * $valOP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
             * unset($x);
             * } else {
             * $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
             * }
             * }
             * } else {
             * foreach ($rubros as $rub){
             * $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
             * }
             * }
             *
             * //TOTALES ORDEN DE PAGO
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubs as $rubro) {
             * foreach ($valOP as $data){
             * if ($data['id'] == $rubro->id and $data['valor'] != 0){
             * $ArrayTotalOp[] = $data['valor'];
             * }
             * }
             * }
             * if (isset($ArrayTotalOp)) {
             * $totalOp = array_sum($ArrayTotalOp);
             * $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $totalOp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArrayTotalOp);
             * } else {
             * $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinOp); $i++) {
             * if ($valoresFinOp[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinOp[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //PAGOS
             *
             * $pagos2 = PagoRubros::all();
             * if ($pagos2->count() != 0) {
             *
             * foreach ($pagos2 as $val) {
             * //Se valida que el pago este finalizado
             * if ($val->pago->estado == "1") $valores1[] = ['id' => $val->rubro->id, 'val' => $val->valor];
             * }
             * foreach ($valores1 as $id1) {
             * $ides[] = $id1['id'];
             * }
             * $valores3 = array_unique($ides);
             * foreach ($valores3 as $valUni) {
             * $keys = array_keys(array_column($valores1, 'id'), $valUni);
             * foreach ($keys as $key) {
             * $values1[] = $valores1[$key]["val"];
             * }
             * $valoresZ[] = ['id' => $valUni, 'valor' => array_sum($values1)];
             * unset($values1);
             * }
             *
             * foreach ($rubros as $rub) {
             * $validate = in_array($rub->id, $valores3);
             * if ($validate == true) {
             * $data = array_keys(array_column($valoresZ, 'id'), $rub->id);
             * $x[] = $valoresZ[$data[0]];
             * $valP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
             * unset($x);
             * } else {
             * $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
             * }
             * }
             * }else {
             * foreach ($rubros as $rub) {
             * $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
             * }
             * }
             *
             * //TOTALES PAGOS
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubs as $rubro) {
             * foreach ($valP as $data){
             * if ($data['id'] == $rubro->id and $data['valor'] != 0){
             * $ArrayTotalP[] = $data['valor'];
             * }
             * }
             * }
             * if (isset($ArrayTotalP)) {
             * $totalP = array_sum($ArrayTotalP);
             * $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $totalP, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArrayTotalP);
             * } else {
             * $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresFinP); $i++) {
             * if ($valoresFinP[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresFinP[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             * //ADICION
             * foreach ($rubros as $R2){
             * $ad = RubrosMov::where([['rubro_id', $R2->id],['movimiento', '=', '2']])->get();
             * if ($ad->count() > 0){
             * $valoresAdd[] = collect(['id' => $R2->id, 'valor' => $ad->sum('valor')]) ;
             * } else{
             * $valoresAdd[] = collect(['id' => $R2->id, 'valor' => 0]) ;
             * }
             * }
             *
             *
             * //REDUCCIÓN
             * foreach ($rubros as $R3){
             * $red = RubrosMov::where([['rubro_id', $R3->id],['movimiento', '=', '3']])->get();
             * if ($red->count() > 0){
             * $valoresRed[] = collect(['id' => $R3->id, 'valor' => $red->sum('valor')]) ;
             * } else{
             * $valoresRed[] = collect(['id' => $R3->id, 'valor' => 0]) ;
             * }
             * }
             *
             *
             * //CREDITO
             * foreach ($rubros as $R4){
             * $cred = RubrosMov::where([['rubro_id', $R4->id],['movimiento', '=', '1']])->get();
             * if ($cred->count() > 0){
             * $valoresCred[] = collect(['id' => $R4->id, 'valor' => $cred->sum('valor')]) ;
             * }else{
             * $valoresCred[] = collect(['id' => $R4->id, 'valor' => 0]) ;
             * }
             * }
             *
             * //CONTRACREDITO
             * foreach ($rubros as $R5){
             * foreach ($R5->fontsRubro as $FR){
             * foreach ($FR->rubrosMov as $movR) {
             * if ($movR->movimiento == 1){
             * $suma[] = $movR->valor;
             * }
             * }
             * }
             * if (isset($suma)){
             * $valoresCcred[] = collect(['id' => $R5->id, 'valor' => array_sum($suma)]);
             * unset($suma);
             * } else{
             * $valoresCcred[] = collect(['id' => $R5->id, 'valor' => 0]);
             * }
             * }
             *
             * //CREDITO Y CONTRACREDITO
             *
             * for ($i=0;$i<sizeof($valoresCcred);$i++){
             * if ($valoresCcred[$i]['id'] == $valoresCred[$i]['id']){
             * $valoresCyC[] = collect(['id' => $valoresCcred[$i]['id'], 'valorC' => $valoresCred[$i]['valor'], 'valorCC' => $valoresCcred[$i]['valor']]) ;
             * }
             * }
             *
             * //PRESUPUESTO DEFINITIVO
             *
             * foreach ($allRegisters as $allRegister){
             * if ($allRegister->level->vigencia_id == $vigencia_id) {
             * if ($allRegister->level_id == $lastLevel) {
             * $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
             * foreach ($rubrosRegs as $rubrosReg) {
             * $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
             * foreach ($valoresAdd as $valAdd) {
             * if ($rubrosReg->id == $valAdd["id"]) {
             * $valAdicion = $valAdd["valor"];
             * }
             * }
             * foreach ($valoresRed as $valRed) {
             * if ($rubrosReg->id == $valRed["id"]) {
             * $valReduccion = $valRed["valor"];
             * }
             * }
             * foreach ($valoresCred as $valCred) {
             * if ($rubrosReg->id == $valCred["id"]) {
             * $valCredito = $valCred["valor"];
             * }
             * }
             * foreach ($valoresCcred as $valCcred) {
             * if ($rubrosReg->id == $valCcred["id"]) {
             * $valCcredito = $valCcred["valor"];
             * }
             * }
             * if (isset($valAdicion) and isset($valReduccion)) {
             * $ArraytotalFR[] = $valFuent + $valAdicion - $valReduccion + $valCredito - $valCcredito;
             * } else {
             * $ArraytotalFR[] = $valFuent + $valCredito - $valCcredito;
             * }
             *
             * }
             * if (isset($ArraytotalFR)) {
             * $totalFR = array_sum($ArraytotalFR);
             * $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($ArraytotalFR);
             * } else {
             * $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * } else {
             * for ($i = 0; $i < sizeof($valoresDisp); $i++) {
             * if ($valoresDisp[$i]['register_id'] == $allRegister->id) {
             * $suma[] = $valoresDisp[$i]['valor'];
             * }
             * }
             * if (isset($suma)) {
             * $valSum = array_sum($suma);
             * $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * unset($suma);
             * } else {
             * $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
             * }
             * }
             * }
             * }
             *
             *
             * foreach ($codigos as $cod){
             * if ($cod['valor']){
             * foreach ($valoresAdd as $valores1){
             * if ($cod['id_rubro'] == $valores1['id']){
             * $valAd1 = $valores1['valor'];
             * }
             * }
             * foreach ($valoresRed as $valores2){
             * if ($cod['id_rubro'] == $valores2['id']){
             * $valRed1 = $valores2['valor'];
             * }
             * }
             * foreach ($valoresCred as $valores3){
             * if ($cod['id_rubro'] == $valores3['id']){
             * $valCred1 = $valores3['valor'];
             * }
             * }
             * foreach ($valoresCcred as $valores4){
             * if ($cod['id_rubro'] == $valores4['id']){
             * $valCcred1 = $valores4['valor'];
             * }
             * }
             * if (isset($valAd1) and isset($valRed1)){
             * $AD = $cod['valor'] + $valAd1 - $valRed1 + $valCred1 - $valCcred1;
             * } else{
             * $AD = $cod['valor'] + $valCred1 - $valCcred1;
             * }
             * $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
             * } elseif($cod['valor'] == 0 and $cod['id_rubro'] != ""){
             * foreach ($valoresAdd as $valores1){
             * if ($cod['id_rubro'] == $valores1['id']){
             * $valAd1 = $valores1['valor'];
             * }
             * }
             * foreach ($valoresRed as $valores2){
             * if ($cod['id_rubro'] == $valores2['id']){
             * $valRed1 = $valores2['valor'];
             * }
             * }
             * foreach ($valoresCred as $valores3){
             * if ($cod['id_rubro'] == $valores3['id']){
             * $valCred1 = $valores3['valor'];
             * }
             * }
             * foreach ($valoresCcred as $valores4){
             * if ($cod['id_rubro'] == $valores4['id']){
             * $valCcred1 = $valores4['valor'];
             * }
             * }
             * if (isset($valoresCred) and !isset($valoresCcred1)){
             * $AD = $cod['valor'] + $valCred1;
             * } else if (!isset($valoresCred) and isset($valoresCcred1)){
             * $AD = $cod['valor'] - $valCcred1;
             * }
             * $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
             * }
             * }
             *
             * //SALDO DISPONIBLE
             *
             * foreach ($ArrayDispon as $valDisp){
             * foreach ($valoresCdp as $valCdp){
             * if ($valCdp['id'] == $valDisp['id']){
             * $valrest = $valCdp['valor'];
             * }
             * }
             * $saldoDisp[] = collect(['id' => $valDisp['id'], 'valor' => $valDisp['valor'] - $valrest]);
             * }
             *
             * //ROL
             *
             * $roles = auth()->user()->roles;
             * foreach ($roles as $role){
             * $rol= $role->id;
             * }
             *
             * //CUENTAS POR PAGAR
             *
             * for ($i=0;$i<sizeof($valOP);$i++){
             * $valueTot = $valOP[$i]['valor'] - $valP[$i]['valor'];
             * $valCP[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valueTot]);
             * unset($valueTot);
             * }
             *
             * //TOTAL CUENTAS POR PAGAR
             *
             * for ($i=0;$i<sizeof($valoresFinOp);$i++){
             * $valTot = $valoresFinOp[$i]['valor'] - $valoresFinP[$i]['valor'];
             * $valoresFinC[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $valTot]);
             * unset($valTot);
             * }
             *
             * //RESERVAS
             *
             * for ($i=0;$i<sizeof($valoresRubro);$i++){
             * $valTot = $valoresRubro[$i]['valor'] - $valOP[$i]['valor'];
             * $valR[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valTot]);
             * unset($valTot);
             * }
             *
             * //TOTAL RESERVAS
             *
             * for ($i=0;$i<sizeof($valoresFinReg);$i++){
             * $totally = $valoresFinReg[$i]['valor'] - $valoresFinOp[$i]['valor'];
             * $valoresFinRes[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $totally]);
             * unset($totally);
             * }
             *
             * //CODE CONTRACTUALES
             *
             * $codeCon = CodeContractuales::all();
             *
             * //CDP's
             *
             * $cdps= Cdp::where('vigencia_id', $V)->get();
             *
             * //REGISTROS
             * $allReg = Registro::all();
             * foreach ($allReg as $reg){
             * if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $V){
             * $registros[] = collect(['id' => $reg->id, 'code' => $reg->code, 'objeto' => $reg->objeto, 'nombre' => $reg->persona->nombre, 'valor' => $reg->valor, 'estado' => $reg->secretaria_e]);
             * }
             * }
             * if (!isset($registros)){
             * $registros[] = null;
             * unset($registros[0]);
             * }
             * if (!isset($cdps)){
             * $cdps[] = null;
             * unset($cdps[0]);
             * }
             *
             * //PAC
             *
             * foreach ($Rubros as $data){
             * $rub = Rubro::findOrFail($data['id_rubro']);
             * if ($rub->pac != null){
             * $pacs[] = collect(['rubro' => $data, 'pac' => $rub->pac]);
             * }
             * }
             *
             * if (!isset($pacs)){
             * $pacs[] = null;
             * unset($pacs[0]);
             * }
             *
             * if (!isset($registros)){
             * $registros[] = null;
             * unset($registros[0]);
             * }
             *
             * $day = Carbon::now();
             * $lastDay = $day->subDay()->toDateString();
             * $actuallyDay = Carbon::now()->toDateString();
             *
             * //Rubros no asignados a alguna actividad
             * foreach ($Rubros as $item){
             * $bpin = BPin::where('rubro_id', $item['id_rubro'])->first();
             * if (!$bpin) $rubBPIN[] = collect($item);
             * }
             *
             * if (!isset($rubBPIN)){
             * $rubBPIN[] = null;
             * unset($rubBPIN[0]);
             * }
             *
             * //SE DEBE MOSTRAR CODIGO BPIN / CODIGO ACTIVIDAD / NOMBRE ACTIVIDAD
             *
             * foreach ($rubros as $rubro) {
             * $bpin2 = BPin::where('rubro_id', $rubro->id)->first();
             * if ($bpin2) {
             * if ( isset($bpinRubro)){
             * $found_key = array_search($rubro->id, array_column($bpinRubro, 'rubro_id'));
             * if ($found_key == false) $bpinRubro[] = collect(['rubro_id' => $rubro->id, 'code_bpin' => $bpin2->cod_proyecto, 'code_actividad' => $bpin2->cod_actividad, 'name_actividad' => $bpin2->actividad]);
             * } else $bpinRubro[] = collect(['rubro_id' => $rubro->id, 'code_bpin' => $bpin2->cod_proyecto, 'code_actividad' => $bpin2->cod_actividad, 'name_actividad' => $bpin2->actividad]);
             * } else $bpinRubro[] = collect(['rubro_id' => $rubro->id, 'code_bpin' => '', 'code_actividad' => '', 'name_actividad' => '']);
             * }
             *
             * if (!isset($bpinRubro)){
             * $bpinRubro[] = null;
             * unset($bpinRubro[0]);
             * }
             *
             * return view('hacienda.presupuesto.index', compact('codigos','V','fuentes','FRubros','fuentesRubros','valoresIniciales','cdps', 'Rubros','valoresCdp',
             * 'registros','valorDisp','valoresAdd','valoresRed','valoresDisp','ArrayDispon', 'saldoDisp','rol','valoresCred', 'valoresCcred','valoresCyC','ordenPagos','valoresRubro'
             * ,'valorDcdp','valOP','pagos','valP','valCP','valR','codeCon','añoActual','valoresFinAdd','valoresFinRed','valoresFinCred','valoresFinCCred','valoresFinCdp','valoresFinReg'
             * ,'valorFcdp','valoresFinOp','valoresFinP','valoresFinC','valoresFinRes','mesActual','primerLevel','years','pacs','lastDay','actuallyDay', 'bpins','rubBPIN', 'bpinRubro'));
             **/
        }
    }


    public function ingresos(){
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
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

        if ($vigens->count() == 0){
            $V = "Vacio";
            return view('hacienda.presupuesto.indexIngresos', compact('V', 'añoActual', 'mesActual'));
        } else {
            $V = $vigens[0]->id;
            $vigencia_id = $V;
            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            $registers = Register::where('level_id', $ultimoLevel->id)->get();
            $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
            $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
            $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
            $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
            $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
            $allRegisters = Register::orderByDesc('level_id')->get();
            $pagos = Pagos::all();
            $ordenPagos = OrdenPagos::all();
            $comprobanteIng = ComprobanteIngresos::where('vigencia_id',$vigencia_id)->where('estado','3')->get();


            global $lastLevel;
            $lastLevel = $ultimoLevel->id;
            $lastLevel2 = $ultimoLevel2->level_id;

            foreach ($fonts as $font){
                $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
            }

            foreach ($fontsRubros as $fontsRubro){
                if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
                    $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
                }
            }
            $tamFountsRubros = count($fuentesRubros);

            foreach ($registers2 as $register2) {
                if ($register2->level->vigencia_id == $vigencia_id) {
                    global $codigoLast;
                    if ($register2->register_id == null) {
                        $codigoEnd = $register2->code;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    } elseif ($codigoLast > 0) {
                        if ($lastLevel2 == $register2->level_id) {
                            if ($lastLevel == $register2->level_id){
                                $codigo = $register2->code;
                                $codigoEnd = "$codigoLast$codigo";
                            } else {
                                $codigo = $register2->code;
                                $newRegisters = Register::findOrFail($register2->register_id);
                                $codigoNew = $newRegisters->code;
                                $codigoEnd = "$codigoNew$codigo";
                                $codigoLast = $codigoEnd;
                            }

                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                            //dd($codigos, $lastLevel2, $register2->level_id, $ultimoLevel);

                            foreach ($registers as $register) {
                                if ($register2->id == $register->register_id) {
                                    $register_id = $register->code_padre->registers->id;
                                    $code = $register->code_padre->registers->code . $register->code;
                                    $ultimo = $register->code_padre->registers->level->level;

                                    while ($ultimo > 1) {
                                        $registro = Register::findOrFail($register_id);
                                        $register_id = $registro->code_padre->registers->id;
                                        $code = $registro->code_padre->registers->code . $code;

                                        $ultimo = $registro->code_padre->registers->level->level;
                                    }
                                    $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'register_id' => $register->register_id]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);

                        foreach ($registers as $register) {
                            if ($register2->id == $register->register_id) {
                                $register_id = $register->code_padre->registers->id;
                                $code = $register->code_padre->registers->code . $register->code;
                                $ultimo = $register->code_padre->registers->level->level;

                                while ($ultimo > 1) {
                                    $registro = Register::findOrFail($register_id);
                                    $register_id = $registro->code_padre->registers->id;
                                    $code = $registro->code_padre->registers->code . $code;

                                    $ultimo = $registro->code_padre->registers->level->level;
                                }
                                $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);

                            }
                        }
                    }
                }
            }

            //Sumas de los Valores
            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            //$totalFR = array_sum($ArraytotalFR);
                            $totalFR = 0;
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
                            if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresIniciales[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            if ($allRegister->register_id == null) $valSum = $vigens[0]->presupuesto_inicial;
                            else $valSum = 0;
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }
        }

        //CODE CONTRACTUALES

        $codeCon = CodeContractuales::all();

        //TOTAL RECAUDO
        foreach ($rubros as $rubro){
            $infoR = Rubro::findOrFail($rubro['id']);
            $totalRecaud[] = collect(['id' => $infoR->id, 'valor' => $infoR->compIng->sum('valor')]);
        }

        //TOTAL GENERAL VALOR RECAUDO

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        foreach ($rubrosReg->compIng as $Rub){
                            $compInfo = CIRubros::where('id', $Rub->id)->get();
                            if ($compInfo->count() > 0 ){
                                $ArraytotalRub[] = $Rub->valor;
                            }
                        }
                    }
                    if (isset($ArraytotalRub)) {
                        $totalRub = array_sum($ArraytotalRub);
                        $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => $totalRub, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalRub);
                    } else {
                        $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinRec); $i++) {
                        if ($valoresFinRec[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinRec[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //SALDO POR RECAUDAR
        foreach ($rubros as $rubro){
            $infoR2 = Rubro::findOrFail($rubro['id']);
            $recaudado = $infoR->compIng->sum('valor');
            $valor = $infoR->fontsRubro->sum('valor');
            $saldoRecaudo[] = collect(['id' => $infoR2->id, 'valor' => $valor - $recaudado]);
        }

        //TOTAL GENERAL SALDO POR RECAUDAR

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg2) {
                        $recaudado2 = $rubrosReg2->compIng->sum('valor');
                        $valor2 = $rubrosReg2->fontsRubro->sum('valor');
                        $ArraytotalSald[] = $valor2 - $recaudado2;
                    }
                    if (isset($ArraytotalSald)) {
                        $totalSald = array_sum($ArraytotalSald);
                        $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalSald);
                    } else {
                        $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {

                    foreach ($comprobanteIng as $compIng) $totCompIng[] = $compIng->val_total;
                    if (isset($totCompIng)){
                        $totalComp = array_sum($totCompIng);
                        $val = $vigens[0]->presupuesto_inicial - $totalComp;
                    }
                    else $val = $vigens[0]->presupuesto_inicial;

                    if ($allRegister->register_id != null) $val = 0;

                    $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => $val, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);

                }
            }
        }

        $items = Pac::all();
        if ($items->count() >= 1){
            foreach ($items as $item){
                foreach ($Rubros as $rubro){
                    if ($item->rubro_id == $rubro['id_rubro']){
                        $PACdata[] = collect(['id' => $item->id, 'rubro_id' => $rubro['id_rubro'], 'rubro' => $rubro['codigo'], 'name' => $rubro['name'], 'valorD' => $item->distribuir, 'totalD' => $item->total_distri]);
                    }
                }
            }
        } else {
            if (!isset($PACdata)){
                $PACdata[] = null;
                unset($PACdata[0]);
            }
        }

        return view('hacienda.presupuesto.indexIngresos', compact('codigos','V','fuentes','fuentesRubros','valoresIniciales','pagos', 'codeCon','añoActual',
            'mesActual','totalRecaud','saldoRecaudo','valoresFinRec','valoresFinSald','years', 'comprobanteIng', 'Rubros', 'PACdata'));
    }

    public function newPreIng($id, $year){

        $vigencia = Vigencia::where('tipo',$id)->where('vigencia', $year)->get();
        if ($vigencia->count() == 0){
            return view('hacienda.presupuesto.vigencia.createVigAdelantada',compact('id','year'));
        } else {
            $vigens= Vigencia::findOrFail($vigencia[0]->id);
            $añoActual = $vigens->vigencia;
            $mesActual = Carbon::now()->month;

            $V = $vigens->id;
            $vigencia_id = $V;
            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            $registers = Register::where('level_id', $ultimoLevel->id)->get();
            $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
            $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
            $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
            $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
            $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
            $allRegisters = Register::orderByDesc('level_id')->get();
            $pagos = Pagos::all();
            $ordenPagos = OrdenPagos::all();

            global $lastLevel;
            $lastLevel = $ultimoLevel->id;
            $lastLevel2 = $ultimoLevel2->level_id;

            foreach ($fonts as $font){
                $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
            }

            foreach ($fontsRubros as $fontsRubro){
                if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
                    $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
                }
            }
            $tamFountsRubros = count($fuentesRubros);

            foreach ($registers2 as $register2) {
                if ($register2->level->vigencia_id == $vigencia_id) {
                    global $codigoLast;
                    if ($register2->register_id == null) {
                        $codigoEnd = $register2->code;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    } elseif ($codigoLast > 0) {
                        if ($lastLevel2 == $register2->level_id) {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                            foreach ($registers as $register) {
                                if ($register2->id == $register->register_id) {
                                    $register_id = $register->code_padre->registers->id;
                                    $code = $register->code_padre->registers->code . $register->code;
                                    $ultimo = $register->code_padre->registers->level->level;

                                    while ($ultimo > 1) {
                                        $registro = Register::findOrFail($register_id);
                                        $register_id = $registro->code_padre->registers->id;
                                        $code = $registro->code_padre->registers->code . $code;

                                        $ultimo = $registro->code_padre->registers->level->level;
                                    }
                                    $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $fR = $rubro->FontsRubro;
                                                //dd($newCod, $fR);
                                                for ($i = 0; $i < $tamFountsRubros; $i++) {
                                                    $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_vigencia_id')->get();
                                                    $numR = count($rubrosF);
                                                    $numF = count($fonts);
                                                    if ($numR == $numF) {
                                                        if ($fuentesRubros[$i]['rubro_id'] == $rubro->id) {
                                                            $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['font_vigencia_id']]);
                                                        }
                                                    } else {
                                                        foreach ($fonts as $font) {
                                                            if ($fuentesRubros[$i]['font_vigencia_id'] == $font->id) {
                                                                $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'font_vigencia_id' => $font->id]);
                                                            } else {
                                                                $findFont = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->where('font_vigencia_id', $font->id)->get();
                                                                $numFinds = count($findFont);
                                                                if ($numFinds >= 1) {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id + 1;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                } else {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                                $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                                $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    }
                }
            }
            //Sumas de los Valores
            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
                            if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresIniciales[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //SUMA DE VALOR DISPONIBLE DEL RUBRO - CDP

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor_disp');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valorDisp); $i++) {
                            if ($valorDisp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valorDisp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }
            //VALOR DE LOS CDPS DEL RUBRO
            foreach ($rubros as $R){
                if ($R->rubrosCdp->count() == 0){
                    $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0 ]) ;
                }
                if ($R->rubrosCdp->count() > 1){
                    foreach ($R->rubrosCdp as $R3){
                        if ($R3->cdps->jefe_e == "2"){
                            $suma2[] = 0;
                        } else{
                            $suma2[] = $R3->rubrosCdpValor->sum('valor');
                        }
                    }
                    $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => array_sum($suma2)]) ;
                    unset($suma2);
                }else{
                    foreach ($R->rubrosCdp as $R2){
                        if ($R2->cdps->jefe_e == "2"){
                            $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0]) ;
                        }else {
                            $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => $R2->rubrosCdpValor->sum('valor')]) ;
                        }
                    }
                }

            }

            //VALOR DE LOS REGISTROS DEL RUBRO
            foreach ($rubros as $rub){
                if ($rub->cdpRegistroValor->count() == 0){
                    $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => 0 ]) ;
                }elseif ($rub->cdpRegistroValor->count() > 1){
                    $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => $rub->cdpRegistroValor->sum('valor')]);
                }else{
                    $reg = $rub->cdpRegistroValor->first();
                    $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => $reg['valor']]) ;
                }
            }

            //VALOR DISPONIBLE CDP - REGISTROS
            for ($i=0;$i<count($valoresCdp);$i++){
                $valorDcdp[] = collect(['id' => $valoresCdp[$i]['id'], 'valor' => $valoresCdp[$i]['valor'] - $valoresRubro[$i]['valor']]);
            }


            //ORDEN DE PAGO

            $OP = OrdenPagosRubros::all();
            if ($OP->count() != 0){
                foreach ($OP as $val){
                    $valores[] = ['id' => $val->cdps_registro->rubro->id, 'val' => $val->valor];
                }
                foreach ($valores as $id){
                    $ids[] = $id['id'];
                }
                $valores2 = array_unique($ids);
                foreach ($valores2 as $valUni){
                    $keys = array_keys(array_column($valores, 'id'), $valUni);
                    foreach ($keys as $key){
                        $values[] = $valores[$key]["val"];
                    }
                    $valoresF[] = ['id' => $valUni, 'valor' => array_sum($values)];
                    unset($values);
                }
                foreach ($rubros as $rub){
                    $validate = in_array($rub->id, $valores2);
                    if ($validate == true ){
                        $data = array_keys(array_column($valoresF, 'id'), $rub->id);
                        $x[] = $valoresF[$data[0]];
                        $valOP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                        unset($x);
                    } else {
                        $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
                    }
                }
            } else {
                foreach ($rubros as $rub){
                    $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }

            //PAGOS

            $pagos2 = PagoRubros::all();
            if ($pagos2->count() != 0) {

                foreach ($pagos2 as $val) {
                    $valores1[] = ['id' => $val->rubro->id, 'val' => $val->valor];
                }
                foreach ($valores1 as $id1) {
                    $ides[] = $id1['id'];
                }
                $valores3 = array_unique($ides);
                foreach ($valores3 as $valUni) {
                    $keys = array_keys(array_column($valores1, 'id'), $valUni);
                    foreach ($keys as $key) {
                        $values1[] = $valores1[$key]["val"];
                    }
                    $valoresZ[] = ['id' => $valUni, 'valor' => array_sum($values1)];
                    unset($values1);
                }

                foreach ($rubros as $rub) {
                    $validate = in_array($rub->id, $valores3);
                    if ($validate == true) {
                        $data = array_keys(array_column($valoresZ, 'id'), $rub->id);
                        $x[] = $valoresZ[$data[0]];
                        $valP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                        unset($x);
                    } else {
                        $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
                    }
                }
            }else {
                foreach ($rubros as $rub) {
                    $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }

            //ADICION
            foreach ($rubros as $R2){
                $ad = RubrosMov::where([['rubro_id', $R2->id],['movimiento', '=', '2']])->get();
                if ($ad->count() > 0){
                    $valoresAdd[] = collect(['id' => $R2->id, 'valor' => $ad->sum('valor')]) ;
                } else{
                    $valoresAdd[] = collect(['id' => $R2->id, 'valor' => 0]) ;
                }
            }


            //REDUCCIÓN
            foreach ($rubros as $R3){
                $red = RubrosMov::where([['rubro_id', $R3->id],['movimiento', '=', '3']])->get();
                if ($red->count() > 0){
                    $valoresRed[] = collect(['id' => $R3->id, 'valor' => $red->sum('valor')]) ;
                } else{
                    $valoresRed[] = collect(['id' => $R3->id, 'valor' => 0]) ;
                }
            }


            //CREDITO
            foreach ($rubros as $R4){
                $cred = RubrosMov::where([['rubro_id', $R4->id],['movimiento', '=', '1']])->get();
                if ($cred->count() > 0){
                    $valoresCred[] = collect(['id' => $R4->id, 'valor' => $cred->sum('valor')]) ;
                }else{
                    $valoresCred[] = collect(['id' => $R4->id, 'valor' => 0]) ;
                }
            }

            //CONTRACREDITO
            foreach ($rubros as $R5){
                foreach ($R5->fontsRubro as $FR){
                    foreach ($FR->rubrosMov as $movR) {
                        if ($movR->movimiento == 1){
                            $suma[] = $movR->valor;
                        }
                    }
                }
                if (isset($suma)){
                    $valoresCcred[] = collect(['id' => $R5->id, 'valor' => array_sum($suma)]);
                    unset($suma);
                } else{
                    $valoresCcred[] = collect(['id' => $R5->id, 'valor' => 0]);
                }
            }

            //CREDITO Y CONTRACREDITO

            for ($i=0;$i<sizeof($valoresCcred);$i++){
                if ($valoresCcred[$i]['id'] == $valoresCred[$i]['id']){
                    $valoresCyC[] = collect(['id' => $valoresCcred[$i]['id'], 'valorC' => $valoresCred[$i]['valor'], 'valorCC' => $valoresCcred[$i]['valor']]) ;
                }
            }

            //PRESUPUESTO DEFINITIVO

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            foreach ($valoresAdd as $valAdd) {
                                if ($rubrosReg->id == $valAdd["id"]) {
                                    $valAdicion = $valAdd["valor"];
                                }
                            }
                            foreach ($valoresRed as $valRed) {
                                if ($rubrosReg->id == $valRed["id"]) {
                                    $valReduccion = $valRed["valor"];
                                }
                            }
                            foreach ($valoresCred as $valCred) {
                                if ($rubrosReg->id == $valCred["id"]) {
                                    $valCredito = $valCred["valor"];
                                }
                            }
                            foreach ($valoresCcred as $valCcred) {
                                if ($rubrosReg->id == $valCcred["id"]) {
                                    $valCcredito = $valCcred["valor"];
                                }
                            }
                            if (isset($valAdicion) and isset($valReduccion)) {
                                $ArraytotalFR[] = $valFuent + $valAdicion - $valReduccion + $valCredito - $valCcredito;
                            } else {
                                $ArraytotalFR[] = $valFuent + $valCredito - $valCcredito;
                            }

                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresDisp); $i++) {
                            if ($valoresDisp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresDisp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            foreach ($codigos as $cod){
                if ($cod['valor']){
                    foreach ($valoresAdd as $valores1){
                        if ($cod['id_rubro'] == $valores1['id']){
                            $valAd1 = $valores1['valor'];
                        }
                    }
                    foreach ($valoresRed as $valores2){
                        if ($cod['id_rubro'] == $valores2['id']){
                            $valRed1 = $valores2['valor'];
                        }
                    }
                    foreach ($valoresCred as $valores3){
                        if ($cod['id_rubro'] == $valores3['id']){
                            $valCred1 = $valores3['valor'];
                        }
                    }
                    foreach ($valoresCcred as $valores4){
                        if ($cod['id_rubro'] == $valores4['id']){
                            $valCcred1 = $valores4['valor'];
                        }
                    }
                    if (isset($valAd1) and isset($valRed1)){
                        $AD = $cod['valor'] + $valAd1 - $valRed1 + $valCred1 - $valCcred1;
                    } else{
                        $AD = $cod['valor'] + $valCred1 - $valCcred1;
                    }
                    $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
                }
            }

            //SALDO DISPONIBLE

            foreach ($ArrayDispon as $valDisp){
                foreach ($valoresCdp as $valCdp){
                    if ($valCdp['id'] == $valDisp['id']){
                        $valrest = $valCdp['valor'];
                    }
                }
                $saldoDisp[] = collect(['id' => $valDisp['id'], 'valor' => $valDisp['valor'] - $valrest]);
            }

            //ROL

            $roles = auth()->user()->roles;
            foreach ($roles as $role){
                $rol= $role->id;
            }

            //CUENTAS POR PAGAR

            for ($i=0;$i<sizeof($valOP);$i++){
                $valueTot = $valOP[$i]['valor'] - $valP[$i]['valor'];
                $valCP[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valueTot]);
                unset($valueTot);
            }

            //RESERVAS

            for ($i=0;$i<sizeof($valoresRubro);$i++){
                $valTot = $valoresRubro[$i]['valor'] - $valOP[$i]['valor'];
                $valR[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valTot]);
                unset($valTot);
            }

            //CODE CONTRACTUALES

            $codeCon = CodeContractuales::all();

            return view('hacienda.presupuesto.newIndexIngresos', compact('codigos','V','fuentes','FRubros','fuentesRubros','valoresIniciales', 'Rubros','valoresCdp','valorDisp','valoresAdd','valoresRed','valoresDisp','ArrayDispon', 'saldoDisp','rol','valoresCred', 'valoresCcred','valoresCyC','ordenPagos','valoresRubro','valorDcdp','valOP','pagos','valP','valCP','valR','codeCon','añoActual','mesActual'));
        }
    }

    public function newPre($id, $year){
        $vigencia = Vigencia::where('tipo',$id)->where('vigencia', $year)->get();
        if ($vigencia->count() == 0){
            return view('hacienda.presupuesto.vigencia.createVigAdelantada',compact('id','year'));
        } else {
            $vigens = Vigencia::findOrFail($vigencia[0]->id);
            $añoActual = Carbon::now()->year;
            $mesActual = Carbon::now()->month;

            $V = $vigens->id;
            $vigencia_id = $V;
            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            $primerLevel = Level::where('vigencia_id', $vigencia_id)->get()->first();
            $registers = Register::where('level_id', $ultimoLevel->id)->get();
            $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
            $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
            $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
            $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
            $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
            $allRegisters = Register::orderByDesc('level_id')->get();
            $ordenP = OrdenPagos::all();
            foreach ($ordenP as $ord){
                if ($ord->registros->cdpsRegistro[0]->cdp->vigencia_id == $V){
                    $ordenPagos[] = collect(['id' => $ord->id, 'nombre' => $ord->nombre, 'persona' => $ord->registros->persona->nombre, 'valor' => $ord->valor, 'estado' => $ord->estado]);
                }
            }
            if (!isset($ordenPagos)){
                $ordenPagos[] = null;
                unset($ordenPagos[0]);
            } else {
                foreach ($ordenPagos as $data){
                    $pagoFind = Pagos::where('orden_pago_id',$data['id'])->get();
                    if ($pagoFind->count() == 1){
                        $pagos[] = collect(['id' => $pagoFind[0]->id, 'nombre' => $data['nombre'], 'persona' => $pagoFind[0]->orden_pago->registros->persona->nombre, 'valor' => $pagoFind[0]->valor, 'estado' => $pagoFind[0]->estado]);
                    } elseif($pagoFind->count() > 1){
                        foreach ($pagoFind as $info){
                            $pagos[] = collect(['id' => $info->id, 'nombre' => $data['nombre'], 'persona' => $info->orden_pago->registros->persona->nombre, 'valor' => $info->valor, 'estado' => $info->estado]);
                        }
                    }
                }
            }
            if (!isset($pagos)){
                $pagos[] = null;
                unset($pagos[0]);
            }

            global $lastLevel;
            $lastLevel = $ultimoLevel->id;
            $lastLevel2 = $ultimoLevel2->level_id;

            foreach ($fonts as $font){
                $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
            }

            foreach ($fontsRubros as $fontsRubro){
                if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
                    $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
                }
            }
            $tamFountsRubros = count($fuentesRubros);

            foreach ($registers2 as $register2) {
                if ($register2->level->vigencia_id == $vigencia_id) {
                    global $codigoLast;
                    if ($register2->register_id == null) {
                        $codigoEnd = $register2->code;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    } elseif ($codigoLast > 0) {
                        if ($lastLevel2 == $register2->level_id) {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                            foreach ($registers as $register) {
                                if ($register2->id == $register->register_id) {
                                    $register_id = $register->code_padre->registers->id;
                                    $code = $register->code_padre->registers->code . $register->code;
                                    $ultimo = $register->code_padre->registers->level->level;

                                    while ($ultimo > 1) {
                                        $registro = Register::findOrFail($register_id);
                                        $register_id = $registro->code_padre->registers->id;
                                        $code = $registro->code_padre->registers->code . $code;

                                        $ultimo = $registro->code_padre->registers->level->level;
                                    }
                                    $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $fR = $rubro->FontsRubro;
                                                //dd($newCod, $fR);
                                                for ($i = 0; $i < $tamFountsRubros; $i++) {
                                                    $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_vigencia_id')->get();
                                                    $numR = count($rubrosF);
                                                    $numF = count($fonts);
                                                    if ($numR == $numF) {
                                                        if ($fuentesRubros[$i]['rubro_id'] == $rubro->id) {
                                                            $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['font_vigencia_id']]);
                                                        }
                                                    } else {
                                                        foreach ($fonts as $font) {
                                                            if ($fuentesRubros[$i]['font_vigencia_id'] == $font->id) {
                                                                $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'font_vigencia_id' => $font->id]);
                                                            } else {
                                                                $findFont = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->where('font_vigencia_id', $font->id)->get();
                                                                $numFinds = count($findFont);
                                                                if ($numFinds >= 1) {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id + 1;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                } else {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                                $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                                $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    }
                }
            }

            //VALOR DEL PRESUPUESTO INICIAL

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
                            if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresIniciales[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //VALOR TOTAL DE LAS ADICIONES

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valAdd = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"2")->sum('valor');
                            $ArraytotalAdd[] = $valAdd;
                        }
                        if (isset($ArraytotalAdd)) {
                            $totalAdd = array_sum($ArraytotalAdd);
                            $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $totalAdd, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalAdd);
                        } else {
                            $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinAdd); $i++) {
                            if ($valoresFinAdd[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinAdd[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //VALOR TOTAL DE LAS REDUCCIONES

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valRed = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"3")->sum('valor');
                            $ArraytotalRed[] = $valRed;
                        }
                        if (isset($ArraytotalRed)) {
                            $totalRed = array_sum($ArraytotalRed);
                            $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $totalRed, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalRed);
                        } else {
                            $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinRed); $i++) {
                            if ($valoresFinRed[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinRed[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            // VALOR TOTAL DE LOS CREDITOS

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valCred = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"1")->sum('valor');
                            $ArraytotalCred[] = $valCred;
                        }
                        if (isset($ArraytotalCred)) {
                            $totalCred = array_sum($ArraytotalCred);
                            $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalCred);
                        } else {
                            $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinCred); $i++) {
                            if ($valoresFinCred[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinCred[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            // VALOR TOTAL DE LOS CONTRACREDITOS

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            foreach ($rubrosReg->fontsRubro as $FR){
                                foreach ($FR->rubrosMov as $movR){
                                    if ($movR->movimiento == 1){
                                        $ArraytotalCCred[] = $movR->valor;
                                    }
                                }
                            }
                        }
                        if (isset($ArraytotalCCred)) {
                            $totalCCred = array_sum($ArraytotalCCred);
                            $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalCCred);
                        } else {
                            $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinCCred); $i++) {
                            if ($valoresFinCCred[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinCCred[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //VALOR TOTAL DE CDPS

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            foreach ($rubrosReg->rubrosCdp as $Rcdp){
                                $cdpInfo = Cdp::where('id', $Rcdp->cdps->id)->where('jefe_e', '!=', 3)->get();
                                if ($cdpInfo->count() > 0 ){
                                    $ArraytotalCdp[] = $Rcdp->cdps->valor;
                                }
                            }
                        }
                        if (isset($ArraytotalCdp)) {
                            $totalCdp = array_sum($ArraytotalCdp);
                            $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $totalCdp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalCdp);
                        } else {
                            $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinCdp); $i++) {
                            if ($valoresFinCdp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinCdp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //VALOR TOTAL DE LOS REGISTROS

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosFReg = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosFReg as $rubro) {
                            if ($rubro->cdpRegistroValor->count() == 0){
                                $ArraytotalReg[] =  0 ;
                            }elseif ($rubro->cdpRegistroValor->count() > 1){
                                foreach ($rubro->cdpRegistroValor as $cdpRV){
                                    if ($cdpRV->registro->secretaria_e == "3"){
                                        $sumaValores[] = $cdpRV->registro->val_total;
                                    }
                                }
                                $ArraytotalReg[] = array_sum($sumaValores);
                                unset($sumaValores);
                            }else{
                                $reg = $rubro->cdpRegistroValor->first();
                                $ArraytotalReg[] = $reg['valor'];
                            }
                        }
                        if (isset($ArraytotalReg)) {
                            $totalReg = array_sum($ArraytotalReg);
                            $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $totalReg, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalReg);
                        } else {
                            $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinReg); $i++) {
                            if ($valoresFinReg[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinReg[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //SUMA DE VALOR DISPONIBLE DEL RUBRO - CDP

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor_disp');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valorDisp); $i++) {
                            if ($valorDisp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valorDisp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }
        }

        //VALOR DE LOS CDPS DEL RUBRO
        foreach ($rubros as $R){
            if ($R->rubrosCdp->count() == 0){
                $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0 ]) ;
            }
            if ($R->rubrosCdp->count() > 1){
                foreach ($R->rubrosCdp as $R3){
                    if ($R3->cdps->jefe_e == "2"){
                        $suma2[] = 0;
                    } else{
                        $suma2[] = $R3->rubrosCdpValor->sum('valor');
                    }
                }
                $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => array_sum($suma2)]) ;
                unset($suma2);
            }else{
                foreach ($R->rubrosCdp as $R2){
                    if ($R2->cdps->jefe_e == "2"){
                        $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0]) ;
                    }else {
                        $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => $R2->rubrosCdpValor->sum('valor')]) ;
                    }
                }
            }

        }

        //VALOR DE LOS REGISTROS DEL RUBRO
        foreach ($rubros as $rub){
            if ($rub->cdpRegistroValor->count() == 0){
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => 0 ]) ;
            }elseif ($rub->cdpRegistroValor->count() > 1){
                foreach ($rub->cdpRegistroValor as $cdpRV){
                    if ($cdpRV->registro->secretaria_e == "3"){
                        $sumaValores[] = $cdpRV->registro->val_total;
                    }
                }
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => array_sum($sumaValores)]);
                unset($sumaValores);
            }else{
                $reg = $rub->cdpRegistroValor->first();
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => $reg['valor']]) ;
            }
        }

        //VALORES TOTALES SALDO CDP

        for ($i=0;$i<count($valoresFinCdp);$i++){
            $valorFcdp[] = collect(['id' => $valoresFinCdp[$i]['id'], 'valor' => $valoresFinCdp[$i]['valor'] - $valoresFinReg[$i]['valor']]);
        }


        //VALOR DISPONIBLE CDP - REGISTROS
        for ($i=0;$i<count($valoresCdp);$i++){
            $valorDcdp[] = collect(['id' => $valoresCdp[$i]['id'], 'valor' => $valoresCdp[$i]['valor'] - $valoresRubro[$i]['valor']]);
        }


        //ORDEN DE PAGO

        $OP = OrdenPagosRubros::all();
        if ($OP->count() != 0){
            foreach ($OP as $val){
                $valores[] = ['id' => $val->cdps_registro->rubro->id, 'val' => $val->valor];
            }
            foreach ($valores as $id){
                $ids[] = $id['id'];
            }
            $valores2 = array_unique($ids);
            foreach ($valores2 as $valUni){
                $keys = array_keys(array_column($valores, 'id'), $valUni);
                foreach ($keys as $key){
                    $values[] = $valores[$key]["val"];
                }
                $valoresF[] = ['id' => $valUni, 'valor' => array_sum($values)];
                unset($values);
            }
            foreach ($rubros as $rub){
                $validate = in_array($rub->id, $valores2);
                if ($validate == true ){
                    $data = array_keys(array_column($valoresF, 'id'), $rub->id);
                    $x[] = $valoresF[$data[0]];
                    $valOP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                    unset($x);
                } else {
                    $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }
        } else {
            foreach ($rubros as $rub){
                $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
            }
        }

        //TOTALES ORDEN DE PAGO

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubs as $rubro) {
                        foreach ($valOP as $data){
                            if ($data['id'] == $rubro->id and $data['valor'] != 0){
                                $ArrayTotalOp[] = $data['valor'];
                            }
                        }
                    }
                    if (isset($ArrayTotalOp)) {
                        $totalOp = array_sum($ArrayTotalOp);
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $totalOp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArrayTotalOp);
                    } else {
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinOp); $i++) {
                        if ($valoresFinOp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinOp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //PAGOS

        $pagos2 = PagoRubros::all();
        if ($pagos2->count() != 0) {

            foreach ($pagos2 as $val) {
                $valores1[] = ['id' => $val->rubro->id, 'val' => $val->valor];
            }
            foreach ($valores1 as $id1) {
                $ides[] = $id1['id'];
            }
            $valores3 = array_unique($ides);
            foreach ($valores3 as $valUni) {
                $keys = array_keys(array_column($valores1, 'id'), $valUni);
                foreach ($keys as $key) {
                    $values1[] = $valores1[$key]["val"];
                }
                $valoresZ[] = ['id' => $valUni, 'valor' => array_sum($values1)];
                unset($values1);
            }

            foreach ($rubros as $rub) {
                $validate = in_array($rub->id, $valores3);
                if ($validate == true) {
                    $data = array_keys(array_column($valoresZ, 'id'), $rub->id);
                    $x[] = $valoresZ[$data[0]];
                    $valP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                    unset($x);
                } else {
                    $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }
        }else {
            foreach ($rubros as $rub) {
                $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
            }
        }

        //TOTALES PAGOS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubs as $rubro) {
                        foreach ($valP as $data){
                            if ($data['id'] == $rubro->id and $data['valor'] != 0){
                                $ArrayTotalP[] = $data['valor'];
                            }
                        }
                    }
                    if (isset($ArrayTotalP)) {
                        $totalP = array_sum($ArrayTotalP);
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $totalP, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArrayTotalP);
                    } else {
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinP); $i++) {
                        if ($valoresFinP[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinP[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //ADICION
        foreach ($rubros as $R2){
            $ad = RubrosMov::where([['rubro_id', $R2->id],['movimiento', '=', '2']])->get();
            if ($ad->count() > 0){
                $valoresAdd[] = collect(['id' => $R2->id, 'valor' => $ad->sum('valor')]) ;
            } else{
                $valoresAdd[] = collect(['id' => $R2->id, 'valor' => 0]) ;
            }
        }


        //REDUCCIÓN
        foreach ($rubros as $R3){
            $red = RubrosMov::where([['rubro_id', $R3->id],['movimiento', '=', '3']])->get();
            if ($red->count() > 0){
                $valoresRed[] = collect(['id' => $R3->id, 'valor' => $red->sum('valor')]) ;
            } else{
                $valoresRed[] = collect(['id' => $R3->id, 'valor' => 0]) ;
            }
        }


        //CREDITO
        foreach ($rubros as $R4){
            $cred = RubrosMov::where([['rubro_id', $R4->id],['movimiento', '=', '1']])->get();
            if ($cred->count() > 0){
                $valoresCred[] = collect(['id' => $R4->id, 'valor' => $cred->sum('valor')]) ;
            }else{
                $valoresCred[] = collect(['id' => $R4->id, 'valor' => 0]) ;
            }
        }

        //CONTRACREDITO
        foreach ($rubros as $R5){
            foreach ($R5->fontsRubro as $FR){
                foreach ($FR->rubrosMov as $movR) {
                    if ($movR->movimiento == 1){
                        $suma[] = $movR->valor;
                    }
                }
            }
            if (isset($suma)){
                $valoresCcred[] = collect(['id' => $R5->id, 'valor' => array_sum($suma)]);
                unset($suma);
            } else{
                $valoresCcred[] = collect(['id' => $R5->id, 'valor' => 0]);
            }
        }

        //CREDITO Y CONTRACREDITO

        for ($i=0;$i<sizeof($valoresCcred);$i++){
            if ($valoresCcred[$i]['id'] == $valoresCred[$i]['id']){
                $valoresCyC[] = collect(['id' => $valoresCcred[$i]['id'], 'valorC' => $valoresCred[$i]['valor'], 'valorCC' => $valoresCcred[$i]['valor']]) ;
            }
        }

        //PRESUPUESTO DEFINITIVO

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                        foreach ($valoresAdd as $valAdd) {
                            if ($rubrosReg->id == $valAdd["id"]) {
                                $valAdicion = $valAdd["valor"];
                            }
                        }
                        foreach ($valoresRed as $valRed) {
                            if ($rubrosReg->id == $valRed["id"]) {
                                $valReduccion = $valRed["valor"];
                            }
                        }
                        foreach ($valoresCred as $valCred) {
                            if ($rubrosReg->id == $valCred["id"]) {
                                $valCredito = $valCred["valor"];
                            }
                        }
                        foreach ($valoresCcred as $valCcred) {
                            if ($rubrosReg->id == $valCcred["id"]) {
                                $valCcredito = $valCcred["valor"];
                            }
                        }
                        if (isset($valAdicion) and isset($valReduccion)) {
                            $ArraytotalFR[] = $valFuent + $valAdicion - $valReduccion + $valCredito - $valCcredito;
                        } else {
                            $ArraytotalFR[] = $valFuent + $valCredito - $valCcredito;
                        }

                    }
                    if (isset($ArraytotalFR)) {
                        $totalFR = array_sum($ArraytotalFR);
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalFR);
                    } else {
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresDisp); $i++) {
                        if ($valoresDisp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresDisp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }


        foreach ($codigos as $cod){
            if ($cod['valor']){
                foreach ($valoresAdd as $valores1){
                    if ($cod['id_rubro'] == $valores1['id']){
                        $valAd1 = $valores1['valor'];
                    }
                }
                foreach ($valoresRed as $valores2){
                    if ($cod['id_rubro'] == $valores2['id']){
                        $valRed1 = $valores2['valor'];
                    }
                }
                foreach ($valoresCred as $valores3){
                    if ($cod['id_rubro'] == $valores3['id']){
                        $valCred1 = $valores3['valor'];
                    }
                }
                foreach ($valoresCcred as $valores4){
                    if ($cod['id_rubro'] == $valores4['id']){
                        $valCcred1 = $valores4['valor'];
                    }
                }
                if (isset($valAd1) and isset($valRed1)){
                    $AD = $cod['valor'] + $valAd1 - $valRed1 + $valCred1 - $valCcred1;
                } else{
                    $AD = $cod['valor'] + $valCred1 - $valCcred1;
                }
                $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
            }elseif($cod['valor'] == 0 and $cod['id_rubro'] != ""){
                $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => 0]);
            }
        }

        //SALDO DISPONIBLE

        foreach ($ArrayDispon as $valDisp){
            foreach ($valoresCdp as $valCdp){
                if ($valCdp['id'] == $valDisp['id']){
                    $valrest = $valCdp['valor'];
                }
            }
            $saldoDisp[] = collect(['id' => $valDisp['id'], 'valor' => $valDisp['valor'] - $valrest]);
        }

        //ROL

        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }

        //CUENTAS POR PAGAR

        for ($i=0;$i<sizeof($valOP);$i++){
            $valueTot = $valOP[$i]['valor'] - $valP[$i]['valor'];
            $valCP[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valueTot]);
            unset($valueTot);
        }

        //TOTAL CUENTAS POR PAGAR

        for ($i=0;$i<sizeof($valoresFinOp);$i++){
            $valTot = $valoresFinOp[$i]['valor'] - $valoresFinP[$i]['valor'];
            $valoresFinC[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $valTot]);
            unset($valTot);
        }

        //RESERVAS

        for ($i=0;$i<sizeof($valoresRubro);$i++){
            $valTot = $valoresRubro[$i]['valor'] - $valOP[$i]['valor'];
            $valR[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valTot]);
            unset($valTot);
        }

        //TOTAL RESERVAS

        for ($i=0;$i<sizeof($valoresFinReg);$i++){
            $totally = $valoresFinReg[$i]['valor'] - $valoresFinOp[$i]['valor'];
            $valoresFinRes[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $totally]);
            unset($totally);
        }

        //CODE CONTRACTUALES

        $codeCon = CodeContractuales::all();

        //CDP's

        $cdps= Cdp::where('vigencia_id', $V)->get();

        //REGISTROS
        $allReg = Registro::all();
        foreach ($allReg as $reg){
            if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $V){
                $registros[] = collect(['id' => $reg->id, 'code' => $reg->code, 'objeto' => $reg->objeto, 'nombre' => $reg->persona->nombre, 'valor' => $reg->valor, 'estado' => $reg->secretaria_e]);
            }
        }
        if (!isset($registros)){
            $registros[] = null;
            unset($registros[0]);
        }
        if (!isset($cdps)){
            $cdps[] = null;
            unset($cdps[0]);
        }

        return view('hacienda.presupuesto.newIndex', compact('codigos','V','fuentes','FRubros','fuentesRubros','valoresIniciales','cdps', 'Rubros','valoresCdp','registros','valorDisp','valoresAdd','valoresRed','valoresDisp','ArrayDispon', 'saldoDisp','rol','valoresCred', 'valoresCcred','valoresCyC','ordenPagos','valoresRubro','valorDcdp','valOP','pagos','valP','valCP','valR','codeCon','añoActual','valoresFinAdd','valoresFinRed','valoresFinCred','valoresFinCCred','valoresFinCdp','valoresFinReg','valorFcdp','valoresFinOp','valoresFinP','valoresFinC','valoresFinRes','mesActual','primerLevel','vigencia'));
    }

    /**
     * Assign Rubro to Actividad.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function asignaRubroProyecto(Request $request)
    {
        $bpinFind = BPin::where('cod_actividad', $request->actividadCode)->where('vigencia_id', $request->vigencia_id)->first();
        $bpinFind->rubro_id = $request->rubro_id;
        $bpinFind->save();

        Session::flash('success','Se ha asignado exitosamente la actividad al rubro.');
        return redirect('presupuesto/');
    }
}