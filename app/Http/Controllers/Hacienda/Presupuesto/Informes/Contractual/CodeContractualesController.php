<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes\Contractual;

use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Http\Controllers\Controller;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Http\Request;
use App\Exports\CodeContractExport;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class CodeContractualesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $vigencia = Vigencia::findOrFail($id);
        $V = $vigencia->id;
        $vigencia_id = $V;
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
                                            if ($rubro->code_contractuales_id != null){
                                                $codigoCon = $rubro->codeCo->code." - ".$rubro->codeCo->name;
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'codigo' => $codigoCon, 'rubro' => $newCod, 'name' => $rubro->name,  'valor' => $valFuent, 'valor_disp' => $valDisp]);
                                                $codigoCon = null;
                                            }
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

        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }

        if (!isset($Rubros)){
            $Rubros[] = null;
            unset($Rubros[0]);
        }
        if (!isset($rubros)){
            $rubros[] = null;
            unset($rubros[0]);
        }
        $codes = CodeContractuales::all();
        return view('hacienda.presupuesto.informes.Contractual.Homologar.index',compact('rubros','Rubros','rol','codes','vigencia'));

    }

    public function rubros()
    {
        $vigencia = Vigencia::where('vigencia', 2019)->where('tipo', 0)->get();
        $V = $vigencia[0]->id;
        $ultimoLevel = Level::where('vigencia_id', $vigencia[0]->id)->get()->last();
        $registers = Register::where('level_id', $ultimoLevel->id)->get();
        $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
        $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
        $fonts = Font::where('vigencia_id',$vigencia[0]->id)->get();
        $rubros = Rubro::where('vigencia_id', $vigencia[0]->id)->get();
        $fontsRubros = FontsRubro::orderBy('font_id')->get();

        global $lastLevel;
        $lastLevel = $ultimoLevel->id;
        $lastLevel2 = $ultimoLevel2->level_id;

        foreach ($fonts as $font){
            $fuentes[] = collect(['id' => $font->id, 'name' => $font->name, 'code' => $font->code]);
        }
        foreach ($fontsRubros as $fontsRubro){
            $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'fount_id' => $fontsRubro->font_id,'id_rubro' => '']);
        }
        $tamFountsRubros = count($fuentesRubros);

        foreach ($registers2 as $register2) {
            global $codigoLast;
            if ($register2->register_id == null) {
                $codigoEnd = $register2->code;
                $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '','id_rubro' => '', 'register_id' => $register2->register_id]);
            } elseif ($codigoLast > 0) {
                if ($lastLevel2 == $register2->level_id) {
                    $codigo = $register2->code;
                    $codigoEnd = "$codigoLast$codigo";
                    $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '','id_rubro' => '', 'register_id' => $register2->register_id]);
                    foreach ($registers as $register) {
                        if($register2->id == $register->register_id){
                            $register_id = $register->code_padre->registers->id;
                            $code = $register->code_padre->registers->code . $register->code;
                            $ultimo = $register->code_padre->registers->level->level;

                            while ($ultimo > 1) {
                                $registro = Register::findOrFail($register_id);
                                $register_id = $registro->code_padre->registers->id;
                                $code = $registro->code_padre->registers->code . $code;

                                $ultimo = $registro->code_padre->registers->level->level;
                            }
                            $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '','id_rubro' => '','register_id' => $register2->register_id]);
                            if ($register->level_id == $lastLevel){
                                foreach ($rubros as $rubro) {
                                    if ($register->id == $rubro->register_id) {
                                        $newCod = "$code$rubro->cod";
                                        $fR = $rubro->FontsRubro;
                                        //dd($newCod, $fR);
                                        for ($i=0;$i<$tamFountsRubros;$i++){
                                            $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_id')->get();
                                            $numR = count($rubrosF);
                                            $numF = count($fonts);
                                            if ($numR == $numF){
                                                if ($fuentesRubros[$i]['rubro_id'] == $rubro->id){
                                                    $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['fount_id']]);
                                                }
                                            }else{
                                                foreach ($fonts as $font){
                                                    if ($fuentesRubros[$i]['fount_id'] == $font->id){
                                                        $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $font->id]);
                                                    }else{
                                                        $findFont = FontsRubro::where('rubro_id',$fuentesRubros[$i]['rubro_id'])->where('font_id',$font->id)->get();
                                                        $numFinds = count($findFont);
                                                        if ($numFinds >= 1){

                                                            $saveRubroF = new FontsRubro();

                                                            $saveRubroF->valor = 0;
                                                            $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                            $saveRubroF->font_id = $font->id+1;

                                                            $saveRubroF->save();

                                                            break;
                                                        }else{

                                                            $saveRubroF = new FontsRubro();

                                                            $saveRubroF->valor = 0;
                                                            $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                            $saveRubroF->font_id = $font->id;

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
                                        if ($rubro->code_contractuales_id == null){
                                            $Rubros[] = collect(['id_rubro' => $rubro->id, 'rubro' => $newCod, 'name' => $rubro->name,  'valor' => $valFuent, 'valor_disp' => $valDisp]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $codigo = $register2->code;
                    $codigoEnd = "$codigoLast$codigo";
                    $codigoLast = $codigoEnd;
                    $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '','id_rubro' => '', 'register_id' => $register2->register_id]);
                }
            } else {
                $codigo = $register2->code;
                $newRegisters = Register::findOrFail($register2->register_id);
                $codigoNew = $newRegisters->code;
                $codigoEnd = "$codigoNew$codigo";
                $codigoLast = $codigoEnd;
                $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '','id_rubro' => '', 'register_id' => $register2->register_id]);
            }
        }

        $codigos = CodeContractuales::where('estado','0')->get();

        return view('hacienda.presupuesto.informes.Contractual.Homologar.asignar',compact('codigos','Rubros'));
    }

    public function rubroStore(Request $request){
        $tamRubros = count($request->code);
        $data = $request->code;
        $ids = $request->idRubro;
        $count = 0;
        for ($i=0;$i<$tamRubros;$i++){
            if ($data[$i] == "Selecciona un Código Contractual"){
                $count++;
            } else {
                $rubro = Rubro::findOrFail($ids[$i]);
                $rubro->code_contractuales_id = $data[$i];
                $rubro->save();
            }
        }
        if ($count == $tamRubros){
            Session::flash('error','Recuerde seleccionar el codigo contractual al rubro que desee asignar');
            return back();
        } else {
            Session::flash('success','Códigos contractuales añadido a los rubros exitosamente');
            return back();
        }
    }

    public function report(Request $request){
        $data = Rubro::where('code_contractuales_id', $request->code)->get();
        if ($data->count() != 0){
            return Excel::download(new CodeContractExport($request->code), 'data.xlsx');
        } else {
            Session::flash('error','Actualmente ningun rubro esta asignado a ese código contractual.');
            return redirect('/presupuesto/');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vigencia = Vigencia::findOrFail($id);
        $codes = CodeContractuales::all();
        return view('hacienda.presupuesto.informes.Contractual.Homologar.create',compact('codes','vigencia'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = CodeContractuales::where('code',$request->code)->get();
        if ($validate->count() == 0){
            $codes = new CodeContractuales();
            $codes->code = $request->code;
            $codes->name = $request->name;
            $codes->estado = "0";
            $codes->save();

            Session::flash('success','Código contractual añadido exitosamente');
            return redirect('/presupuesto/informes/contractual/homologar/'. $request->vigencia.'/create');
        } else {
            Session::flash('error','Ya se encuentra un código contractual registrado en el software con ese mismo codigo');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CodeContractuales  $codeContractuales
     * @return \Illuminate\Http\Response
     */
    public function show(CodeContractuales $codeContractuales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CodeContractuales  $codeContractuales
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $codigo = CodeContractuales::findOrFail($id);
        return view('hacienda.presupuesto.informes.Contractual.Homologar.edit',compact('codigo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CodeContractuales  $codeContractuales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->name){
            $codigo = CodeContractuales::findOrFail($id);
            $codigo->code = $request->code;
            $codigo->name = $request->name;
            $codigo->estado = $request->estado;
            $codigo->save();

            Session::flash('success','El código contractual '.$request->code.' - '.$request->name.' se edito exitosamente.');
            return redirect('/presupuesto/informes/contractual/homologar');
        } else {
            $codigo = CodeContractuales::findOrFail($id);
            $codigo->estado = $request->estado;
            $codigo->save();

            Session::flash('success','El código contractual '.$codigo->code.' - '.$codigo->name.' se edito exitosamente.');
            return redirect('/presupuesto/informes/contractual/homologar');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CodeContractuales  $codeContractuales
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $codigo = CodeContractuales::findOrFail($id);
        $codigo->delete();

        Session::flash('error','El código contractual se eliminó exitosamente.');
        return redirect('/presupuesto/informes/contractual/homologar');

    }
}
