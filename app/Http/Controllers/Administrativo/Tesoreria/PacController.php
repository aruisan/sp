<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\PacInformeIngresoEgreso;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Administrativo\Tesoreria\PacMeses;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Administrativo\Tesoreria\Pac;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Level;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class PacController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function rubros(){
        $añoActual = Carbon::now()->year;
        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
        $vigencia_id = $vigens[0]->id;
        $V = $vigencia_id;
        $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
        $registers = Register::where('level_id', $ultimoLevel->id)->get();
        $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
        $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
        $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
        $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
        $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();

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
                                            $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                            $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                            $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                            $All[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
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

        //LLENAR CON RUBROS DEL PRESUPUESTO DE INGRESOS

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
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $All[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $vigens[0]->presupuesto_inicial, 'register_id' => $register->register_id, 'valor_disp' => 0]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;

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
                            }
                        }
                    }
                }
            }
        }

        return ($All);
    }


    public function index()
    {
        $items = Pac::all();
        if ($items->count() >= 1){
            $rubros = $this->rubros();
            foreach ($items as $item){
                foreach ($rubros as $rubro){
                    if ($item->rubro_id == $rubro['id_rubro']){
                        $data[] = collect(['id' => $item->id, 'rubro_id' => $rubro['id_rubro'], 'rubro' => $rubro['codigo'], 'name' => $rubro['name'], 'valorD' => $item->distribuir, 'totalD' => $item->total_distri]);
                    }
                }
            }
        } else {
            if (!isset($data)){
                $data[] = null;
                unset($data[0]);
            }
        }

        return view('administrativo.pac.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $añoActual = Carbon::now()->year;
        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
        $vigencia_id = $vigens[0]->id;
        $V = $vigencia_id;
        $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
        $registers = Register::where('level_id', $ultimoLevel->id)->get();
        $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
        $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
        $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
        $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
        $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();

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
                                            $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                            $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                            $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                            $validate = Rubro::findOrFail($rubro->id);
                                            if ($validate->pac == null){
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
                                            }
                                            $All[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
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

        //LLENAR CON RUBROS DEL PRESUPUESTO DE INGRESOS

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
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $validate = Rubro::findOrFail($rubro->id);
                                                if ($validate->pac == null) if ($newCod == "110101") $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $vigens[0]->presupuesto_inicial, 'register_id' => $register->register_id, 'valor_disp' => 0]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;

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
                            }
                        }
                    }
                }
            }
        }

        if (!isset($Rubros)){
            Session::flash('error','No hay rubros faltantes para crear su respectivo PAC ');
            return redirect('administrativo/pac');
        }

        return view('administrativo.pac.create',compact('Rubros'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pac = new Pac();
        $pac->situacion_fondos = $request->fondos;
        $pac->aprobado = $request->apro;
        $pac->rezago = $request->rez;
        $pac->distribuir = $request->distri2;
        $pac->total_distri = $request->tot;
        $pac->rubro_id = $request->IdRub;
        $pac->save();

        $mes = new PacMeses();
        $mes->mes = "Enero";
        $mes->valor = $request->enero;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Febrero";
        $mes->valor = $request->febrero;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Marzo";
        $mes->valor = $request->marzo;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Abril";
        $mes->valor = $request->abril;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Mayo";
        $mes->valor = $request->mayo;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Junio";
        $mes->valor = $request->junio;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Julio";
        $mes->valor = $request->julio;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Agosto";
        $mes->valor = $request->agosto;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Septiembre";
        $mes->valor = $request->septiembre;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Octubre";
        $mes->valor = $request->octubre;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Noviembre";
        $mes->valor = $request->noviembre;
        $mes->pac_id = $pac->id;
        $mes->save();

        $mes = new PacMeses();
        $mes->mes = "Diciembre";
        $mes->valor = $request->diciembre;
        $mes->pac_id = $pac->id;
        $mes->save();

        Session::flash('success','El PAC se ha creado exitosamente');
        return redirect('/administrativo/pac');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pac  $pac
     * @return \Illuminate\Http\Response
     */
    public function show(Pac $pac)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pac  $pac
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pac = Pac::findOrFail($id);
        $meses = $pac->meses;
        $rubros = $this->rubros();
        foreach ($rubros as $rubro){
            if ($pac->rubro_id == $rubro['id_rubro']){
                $data[] = collect(['pac' => $pac, 'rubro' => $rubro]);
            }
        }

        return view('administrativo.pac.edit',compact('data','meses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pac  $pac
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pac = Pac::findOrFail($id);
        $pac->situacion_fondos = $request->fondos;
        $pac->aprobado = $request->apro;
        $pac->rezago = $request->rez;
        $pac->distribuir = $request->distri2;
        $pac->total_distri = $request->tot;
        $pac->save();

        foreach ($pac->meses as $mes){
            if ($mes->mes == "Enero"){
                $mes->valor = $request->Enero;
                $mes->save();
            } elseif ($mes->mes == "Febrero"){
                $mes->valor = $request->Febrero;
                $mes->save();
            } elseif ($mes->mes == "Marzo"){
                $mes->valor = $request->Marzo;
                $mes->save();
            } elseif ($mes->mes == "Abril"){
                $mes->valor = $request->Abril;
                $mes->save();
            } elseif ($mes->mes == "Mayo"){
                $mes->valor = $request->Mayo;
                $mes->save();
            } elseif ($mes->mes == "Junio"){
                $mes->valor = $request->Junio;
                $mes->save();
            } elseif ($mes->mes == "Julio"){
                $mes->valor = $request->Julio;
                $mes->save();
            } elseif ($mes->mes == "Agosto"){
                $mes->valor = $request->Agosto;
                $mes->save();
            } elseif ($mes->mes == "Septiembre"){
                $mes->valor = $request->Septiembre;
                $mes->save();
            } elseif ($mes->mes == "Octubre"){
                $mes->valor = $request->Octubre;
                $mes->save();
            } elseif ($mes->mes == "Noviembre"){
                $mes->valor = $request->Noviembre;
                $mes->save();
            } elseif ($mes->mes == "Diciembre"){
                $mes->valor = $request->Diciembre;
                $mes->save();
            }
        }

        Session::flash('success','El PAC se ha actualizado exitosamente');
        return redirect('/administrativo/pac/'.$id.'/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pac  $pac
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pac = Pac::findOrFail($id);
        foreach ($pac->meses as $mes){
            $mes->delete();
        }
        $pac->delete();

        Session::flash('error','El PAC se ha eliminado exitosamente');
        return redirect('/administrativo/pac');
    }


    public function informe_temporal($tipo){
        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $pacs = PacInformeIngresoEgreso::where('tipo', $tipo)->get();
        return view('administrativo.pac.informe', compact('pacs', 'tipo', 'meses'));
   }
}
