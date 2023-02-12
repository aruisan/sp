<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\bpinVigencias;
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
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
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
            $comprobanteIng = ComprobanteIngresos::where('vigencia_id',$vigencia_id)->where('estado','3')->get();
            $plantillaIng = PlantillaCuipoIngresos::all();
            foreach ($plantillaIng as $data){
                if ($data->id == 1){
                    $adiciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','2')->get();
                    $reducciones = RubrosMov::where('font_vigencia_id', $vigens[0]->id)->where('movimiento','3')->get();
                    $definitivo = $adiciones->sum('valor') - $reducciones->sum('valor') + $vigens[0]->presupuesto_inicial;
                    $prepIng[] = collect(['id' => $data->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $vigens[0]->presupuesto_inicial, 'adicion' => $adiciones->sum('valor'), 'reduccion' => $reducciones->sum('valor'),
                        'anulados' => 0, 'recaudado' => $comprobanteIng->sum('val_total') , 'porRecaudar' => $definitivo - $comprobanteIng->sum('val_total'), 'definitivo' => $definitivo,
                        'hijo' => 0, 'cod_fuente' => '', 'name_fuente' => '']);
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

                                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                            if (isset($adicionesH)) unset($adicionesH);
                                                            if (isset($reduccionesH)) unset($reduccionesH);

                                                            // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                            foreach ($rubro[0]->fontsRubro as $font) {
                                                                $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                                if ($add) $hijosAdicion[] = $add->valor;
                                                                else $hijosAdicion[] = 0;

                                                                $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                                if ($red) $hijosReduccion[] = $red->valor;
                                                                else $hijosReduccion[] = 0;
                                                            }

                                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                            $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                                            if (count($rubro[0]->compIng) > 0) $civ[] = $rubro[0]->compIng->sum('valor');
                                                        } else {
                                                            foreach ($rubro as $rb){

                                                                //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                                if (isset($adicionesH)) unset($adicionesH);
                                                                if (isset($reduccionesH)) unset($reduccionesH);

                                                                // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                                foreach ($rb->fontsRubro as $font) {
                                                                    $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                                    if ($add) $hijosAdicion[] = $add->valor;
                                                                    else $hijosAdicion[] = 0;

                                                                    $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                                    if ($red) $hijosReduccion[] = $red->valor;
                                                                    else $hijosReduccion[] = 0;
                                                                }

                                                                if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                                if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                                $sum[] = $rb->fontsRubro->sum('valor');
                                                                if (count($rb->compIng) > 0) $civ[] = $rb->compIng->sum('valor');
                                                            }
                                                        }
                                                    }

                                                }
                                            } else{
                                                $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h3->id)->get();
                                                if (count($rubro) > 0){
                                                    if (count($rubro) == 1){

                                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                        if (isset($adicionesH)) unset($adicionesH);
                                                        if (isset($reduccionesH)) unset($reduccionesH);

                                                        // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                        foreach ($rubro[0]->fontsRubro as $font) {
                                                            $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                            if ($add) $hijosAdicion[] = $add->valor;
                                                            else $hijosAdicion[] = 0;

                                                            $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                            if ($red) $hijosReduccion[] = $red->valor;
                                                            else $hijosReduccion[] = 0;
                                                        }

                                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                        $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                                        if (count($rubro[0]->compIng) > 0) $civ[] = $rubro[0]->compIng->sum('valor');
                                                    } else {
                                                        foreach ($rubro as $rb){

                                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                            if (isset($adicionesH)) unset($adicionesH);
                                                            if (isset($reduccionesH)) unset($reduccionesH);

                                                            // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                            foreach ($rb->fontsRubro as $font) {
                                                                $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                                if ($add) $hijosAdicion[] = $add->valor;
                                                                else $hijosAdicion[] = 0;

                                                                $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                                if ($red) $hijosReduccion[] = $red->valor;
                                                                else $hijosReduccion[] = 0;
                                                            }

                                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                            $sum[] = $rb->fontsRubro->sum('valor');
                                                            if (count($rb->compIng) > 0) $civ[] = $rb->compIng->sum('valor');
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h2->id)->get();
                                        if (count($rubro) > 0){
                                            if (count($rubro) == 1){

                                                //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                if (isset($adicionesH)) unset($adicionesH);
                                                if (isset($reduccionesH)) unset($reduccionesH);

                                                // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                foreach ($rubro[0]->fontsRubro as $font) {
                                                    $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                    if ($add) $hijosAdicion[] = $add->valor;
                                                    else $hijosAdicion[] = 0;

                                                    $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                    if ($red) $hijosReduccion[] = $red->valor;
                                                    else $hijosReduccion[] = 0;
                                                }

                                                if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                $sum[] = $rubro[0]->fontsRubro->sum('valor');
                                                if (count($rubro[0]->compIng) > 0) $civ[] = $rubro[0]->compIng->sum('valor');

                                            } else {
                                                foreach ($rubro as $rb){
                                                    //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                                    if (isset($adicionesH)) unset($adicionesH);
                                                    if (isset($reduccionesH)) unset($reduccionesH);

                                                    // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                                    foreach ($rb->fontsRubro as $font) {
                                                        $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                        if ($add) $hijosAdicion[] = $add->valor;
                                                        else $hijosAdicion[] = 0;

                                                        $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                        if ($red) $hijosReduccion[] = $red->valor;
                                                        else $hijosReduccion[] = 0;
                                                    }

                                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                                    $sum[] = $rb->fontsRubro->sum('valor');
                                                    if (count($rb->compIng) > 0) $civ[] = $rb->compIng->sum('valor');
                                                }
                                            }
                                        }
                                    }
                                }
                            } else{
                                $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $h1->id)->get();
                                if (count($rubro) > 0){
                                    if (count($rubro) == 1){
                                        //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                        if (isset($adicionesH)) unset($adicionesH);
                                        if (isset($reduccionesH)) unset($reduccionesH);

                                        // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                        foreach ($rubro[0]->fontsRubro as $font) {
                                            $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                            if ($add) $hijosAdicion[] = $add->valor;
                                            else $hijosAdicion[] = 0;

                                            $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                            if ($red) $hijosReduccion[] = $red->valor;
                                            else $hijosReduccion[] = 0;
                                        }


                                        if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                        if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                        // VALIDACION PARA EL VALOR INICIAL DE LOS RUBROS PADRES
                                        $sum[] = $rubro[0]->fontsRubro->sum('valor');

                                        //VALIDACION PARA LOS VALORES DE LOS COMPROBANTES DE INGRESOS DE LOS PADRES
                                        if (count($rubro[0]->compIng) > 0) $civ[] = $rubro[0]->compIng->sum('valor');
                                    } else {
                                        foreach ($rubro as $rb){
                                            //SE LIMPIAN LAS VARIABLES PARA SU CORRESPONDUIENTE LLENADO EN LIMPIO
                                            if (isset($adicionesH)) unset($adicionesH);
                                            if (isset($reduccionesH)) unset($reduccionesH);

                                            // VALIDACION PARA LAS ADICIONES Y REDUCCIONES EN TOTAL PARA LOS RUBROS PADRE
                                            foreach ($rb->fontsRubro as $font) {
                                                $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                                if ($add) $hijosAdicion[] = $add->valor;
                                                else $hijosAdicion[] = 0;

                                                $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                                if ($red) $hijosReduccion[] = $red->valor;
                                                else $hijosReduccion[] = 0;
                                            }

                                            if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                            if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);

                                            // VALIDACION PARA EL VALOR INICIAL DE LOS RUBROS PADRES
                                            $sum[] = $rb->fontsRubro->sum('valor');

                                            //VALIDACION PARA LOS VALORES DE LOS COMPROBANTES DE INGRESOS DE LOS PADRES
                                            if (count($rb->compIng) > 0) $civ[] = $rb->compIng->sum('valor');
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
                                $compIngValue = 0;
                                if (count($rubro[0]->compIng) > 0) $compIngValue = $rubro[0]->compIng->sum('valor');
                                if (count($rubro[0]->fontsRubro) > 1){
                                    foreach ($rubro[0]->fontsRubro as $font){
                                        $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                        if ($add) $adicion = $add->valor;
                                        else $adicion = 0;

                                        $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                        if ($red) $reduccion = $red->valor;
                                        else $reduccion = 0;

                                        $definitivo = $adicion - $reduccion + $font->valor;

                                        $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name,
                                            'inicial' => $font->valor, 'adicion' => $adicion, 'reduccion' => $reduccion, 'anulados' => 0,
                                            'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' => $definitivo,'hijo' => $data->hijo,
                                            'cod_fuente' => $font->sourceFunding->code, 'name_fuente' => $font->sourceFunding->description]);
                                    }
                                } else {
                                    if (count($rubro[0]->fontsRubro) > 0){
                                        $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                        if ($add) $adicion = $add->valor;
                                        else $adicion = 0;

                                        $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $rubro[0]->fontsRubro[0]->id)->first();
                                        if ($red) $reduccion = $red->valor;
                                        else $reduccion = 0;

                                        $definitivo = $adicion - $reduccion + $rubro[0]->fontsRubro->sum('valor');

                                        $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $rubro[0]->fontsRubro->sum('valor'), 'adicion' => $adicion, 'reduccion' => $reduccion,
                                            'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo - $compIngValue, 'definitivo' =>  $definitivo,
                                            'hijo' => $data->hijo, 'cod_fuente' => $rubro[0]->fontsRubro, 'name_fuente' => $rubro[0]->fontsRubro]);
                                    }

                                }
                            } else {
                                //MAS DE UN RUBRO ASIGNADO A LA MISMA PLANTILLA
                                dd($rubro);
                                foreach ($rubro as $rb){
                                    foreach ($rb->fontsRubro as $font) {
                                        $add = RubrosMov::where('movimiento', '2')->where('fonts_rubro_id', $font->id)->first();
                                        if ($add) $hijosAdicion[] = $add->valor;
                                        else $hijosAdicion[] = 0;

                                        $red = RubrosMov::where('movimiento', '3')->where('fonts_rubro_id', $font->id)->first();
                                        if ($red) $hijosReduccion[] = $red->valor;
                                        else $hijosReduccion[] = 0;
                                    }

                                    if (isset($hijosAdicion)) $adicionesH[] = array_sum($hijosAdicion);
                                    if (isset($hijosReduccion)) $reduccionesH[] = array_sum($hijosReduccion);
                                    if (isset($adicionesH)) $adicionesTot = array_sum($adicionesH);
                                    if (isset($reduccionesH)) $reduccionesTot = array_sum($reduccionesH);

                                    $compIngValue = 0;
                                    if (count($rb->compIng) > 0) $compIngValue = $rb->compIng->sum('valor');
                                    $sum[] = $rb->fontsRubro->sum('valor');
                                    $definitivo = $adicionesTot - $reduccionesTot + $rb->fontsRubro->sum('valor');
                                    $prepIng[] = collect(['id' => $rubro[0]->id, 'code' => $data->code, 'name' => $data->name, 'inicial' => $rb->fontsRubro->sum('valor'), 'adicion' => $adicionesTot, 'reduccion' => $reduccionesTot,
                                        'anulados' => 0, 'recaudado' => $compIngValue, 'porRecaudar' => $definitivo  - $compIngValue, 'definitivo' => $definitivo,
                                        'hijo' => $data->hijo, 'cod_fuente' => $rubro[0]->fontsRubro[0]->code, 'name_fuente' => $rubro[0]->fontsRubro[0]->description]);
                                }
                            }
                        }
                    }
                }
            }

            //CODE CONTRACTUALES
            $codeCon = CodeContractuales::all();

            //TOTAL RECAUDO
            $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
            foreach ($rubros as $rubro){
                $totalRecaud[] = collect(['id' => $rubro->id, 'valor' => $rubro->compIng->sum('valor')]);
            }

            //SALDO POR RECAUDAR
            foreach ($rubros as $rubro){
                $recaudado = $rubro->compIng->sum('valor');
                $valor = $rubro->fontsRubro->sum('valor');
                $saldoRecaudo[] = collect(['id' => $rubro->id, 'valor' => $valor - $recaudado]);
            }


            $items = Pac::all();
            if ($items->count() >= 1){
                foreach ($items as $item){
                    foreach ($rubros as $rubro){
                        if ($item->rubro_id == $rubro['id_rubro']){
                            $PACdata[] = collect(['id' => $item->id, 'rubro_id' => $rubro['id_rubro'], 'rubro' => $rubro['codigo'], 'name' => $rubro['name'], 'valorD' => $item->distribuir, 'totalD' => $item->total_distri]);
                        }
                    }
                }
            }

            if (!isset($PACdata)){
                $PACdata[] = null;
                unset($PACdata[0]);
            }

            return view('hacienda.presupuesto.indexIngresos', compact('prepIng','V', 'codeCon','añoActual',
                'mesActual','totalRecaud','saldoRecaudo','years', 'comprobanteIng', 'rubros', 'PACdata'));
        }
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

}
