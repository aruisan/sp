<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Font;
use App\Model\Planeacion\Pdd\SubProyecto;
use App\Model\Admin\Dependencia;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Register;
use Carbon\Carbon;

use Session;

class RubrosController extends Controller
{
    public function create($vigencia_id)
    {
        $vigencia = Vigencia::findOrFail($vigencia_id);

        // PLANTILLA PARA EGRESOS E INGRESOS
        // TIPO 1 -> PRESUPUESTO INGRESOS
        // TIPO 0 -> PRESUPUESTO EGRESOS

        if ($vigencia->tipo == 0) $plantilla = PlantillaCuipo::where('id','>=',318)->get();
        else $plantilla = PlantillaCuipoIngresos::all();

        $rubrosChecked = Rubro::where('plantilla_cuipos_id','!=',null)->where('vigencia_id', $vigencia_id)->select(['plantilla_cuipos_id'])->get();
        $validate = false;

        return view('hacienda.presupuesto.vigencia.createRubros', compact('vigencia','vigencia_id','plantilla','rubrosChecked','validate'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rubrosChecked = Rubro::where('plantilla_cuipos_id','!=',null)->where('vigencia_id', $request->vigencia_id)->get();
        $vigencia = Vigencia::find($request->vigencia_id);

        foreach ($request->checkedIDs as $checked){
            $validate = false;
            foreach ($rubrosChecked as $rubroChecked){
                if ($rubroChecked->plantilla_cuipos_id == $checked){
                    $validate = true;
                    break;
                }
            }
            if ($validate == false){

                if ($vigencia->tipo == 0) $dataPlantilla = PlantillaCuipo::findOrFail($checked);
                else $dataPlantilla = PlantillaCuipoIngresos::findOrFail($checked);

                $rubro = new Rubro();
                $rubro->name =  $dataPlantilla->name;
                $rubro->cod = $dataPlantilla->code;
                $rubro->subproyecto_id = 1;
                $rubro->vigencia_id = $request->vigencia_id;
                $rubro->plantilla_cuipos_id = $checked;
                $rubro->save();

            }
        }

        Session::flash('success','Los rubros han sido actualizados exitosamente');
        return redirect('/presupuesto/rubro/create/'.$request->vigencia_id);
        /**
        ANTERIOR LOGICA
            $id         = $request->rubro_id;
            $name       = $request->nombre;
            $subProy    = 1;
            $code       = $request->code;
            $register   = $request->register_id;
            $vigencia   = $request->vigencia_id;
            $count = count($register);

            for($i = 0; $i < $count; $i++){

                if($id[$i]){
                    $this->update($id[$i], $name[$i], $code[$i], $register[$i], $subProy[$i]);
                }else{
                    $rubro = new Rubro();
                    $rubro->name = $name[$i];
                    $rubro->cod = $code[$i];
                    $rubro->register_id = $register[$i];
                    $rubro->subproyecto_id = $subProy[$i];
                    $rubro->vigencia_id = $vigencia;
                    $rubro->save();
                }
            }
         */

        //return  back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $roles = auth()->user()->roles;
        foreach ($roles as $role) $rol= $role->id;
        $rubro = Rubro::findOrFail($id);
        $rubros = Rubro::where('id', '!=', $id)->where('vigencia_id', $rubro->vigencia_id)->get();
        $fuentesR = $rubro->fontsRubro;
        $valor = $fuentesR->sum('valor');
        $valorDisp = $fuentesR->sum('valor_disp');

        $add = rubrosMov::where([['rubro_id','=',$id],['movimiento','=','2']])->get();
        $red = rubrosMov::where([['rubro_id','=',$id],['movimiento','=','3']])->get();
        $vigens = Vigencia::findOrFail($rubro->vigencia_id);
        $fuentesAll = SourceFunding::all();
        $dependencias = Dependencia::all();

        if (isset($fuentesR)){
            foreach ($fuentesR as $fuente){
                $suma[] = null;$sumaC[] = null;$resta[] = null;$restaC[] = null;

                if (count($fuente->rubrosMov) > 0){
                    foreach($fuente->rubrosMov as $RM){
                        if ($RM->fonts_rubro_id == $fuente->id){
                            if ($RM->movimiento == 1) $suma[] = $RM->valor;
                            elseif($RM->movimiento == 2) $sumaC[] = $RM->valor;
                        } else{
                            if ($RM->movimiento == 1) $suma[] = 0;
                            elseif($RM->movimiento == 2) $sumaC[] = 0;
                        }
                    }
                } else{
                    $suma[] = 0;
                    $sumaC[] = 0;
                }

                //VALORES DE CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                $rubrosCredMov = RubrosMov::where('dep_rubro_font_cred_id', $depFont->id)->get();
                if(count($rubrosCredMov) > 0) $valueRubrosCred[] = $rubrosCredMov->sum('valor');
                else $valueRubrosCred[] = 0;

                //VALORES DE CONTRA CREDITO DE LAS FUENTES DE LAS DEPENDENCIAS
                $rubrosCCMov = RubrosMov::where('dep_rubro_font_cc_id', $depFont->id)->get();
                if(count($rubrosCCMov) > 0) $valueRubrosCCred[] = $rubrosCCMov->sum('valor');
                else $valueRubrosCCred[] = 0;

                $val = array_sum($suma);
                $Cred = array_sum($sumaC);
                if ($fuente->rubrosMov){
                    foreach ($fuente->rubrosMov as $item) {
                        if ($item->movimiento == 1) $resta[] = $item->valor;
                        elseif($item->movimiento == 3) $restaC[] = $item->valor;
                    }
                }else{
                    $resta[] = 0;
                    $restaC[] = 0;
                }
                $val2 = array_sum($resta);
                $CCred = array_sum($restaC);

                $valores[] = collect(['id' => $fuente->id, 'credito' => $val, 'ccredito' => $val2, 'adicion' => $Cred, 'reduccion' => $CCred]);
                unset($suma, $resta, $Cred, $CCred, $sumaC);
            }
        } else{
            $fuentesR[] = 0; $valores[] = 0;
        }
        $RubrosM = RubrosMov::where([['rubro_id','=',$rubro->id],['valor','>','0']])->get();
        foreach ($RubrosM as $data){
            $files[] = collect(['idResource' => $data->resource_id , 'ruta' => $data->Resource->ruta, 'mov' => $data->movimiento]);
        }
        if (!isset($files)){
            foreach ($rubro->fontsRubro as $fr){
                $RubrosMov = RubrosMov::where([['fonts_rubro_id','=',$fr->id],['valor','>','0']])->get();
                foreach ($RubrosMov as $data2){
                    $files[] = collect(['idResource' => $data2->resource_id , 'ruta' => $data2->Resource->ruta, 'mov' => $data2->movimiento]);
                }
            }
            if (!isset($files)) $files = 0;
        }

        $contadorRubDisp = 0;
        foreach ($rubros as $rub){
            if ($rub->fontsRubro->sum('valor_disp') > 0) $contadorRubDisp = $contadorRubDisp + 1;
        }

        return view('hacienda.presupuesto.rubro.show', compact('rubro','fuentesR','valor','valorDisp','rol','rubros','fuentesAll','valores','files','add','red','contadorRubDisp','vigens','dependencias'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $name, $code, $register, $subproyecto_id)
    {
        $rubro = Rubro::findOrFail($id);
        $rubro->name = $name;
        $rubro->cod = $code;
        $rubro->register_id = $register;
        $rubro->subproyecto_id = $subproyecto_id;
        $rubro->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteRubro($id, $vigencia)
    {
        $rubrosDelete = Rubro::where('plantilla_cuipos_id',$id)->where('vigencia_id', $vigencia)->get();
        $rubrosDelete[0]->delete();
    }

    public function index()
    {
        $dependencia = auth()->user()->dependencia_id;
        $usuario = auth()->id();
        $rubros = Rubro::all();

        foreach ($rubros as $rubro){
            if ($dependencia == $rubro->subProyecto->dependencia->id){
                $datas[]= collect(['idRubro'=>$rubro->id,'codRubro'=> $rubro->cod,'name' => $rubro->name, 'dep' => $rubro->subProyecto->dependencia->name, 'subP' => $rubro->subProyecto->name, 'valor' => $rubro->fontsRubro->sum('valor')]);
            }
        }

        return view('administrativo.contractual.rubrosAsignados', compact('datas'));
    }

    /**
     * Asignar dinero a dependencias
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function asignarDineroDep(Request $request, $id)
    {
        for ($i = 0; $i < sizeof($request->valorAsignar); $i++) {
            if ($request->depFontID != 0){
                $fontDep = DependenciaRubroFont::find($request->depFontID);
                if ($fontDep->value == $fontDep->saldo){
                    $fontRubro = FontsRubro::find($request->fuenteRid);
                    if ($request->valorAsignar[0] > $fontRubro->valor_disp_asign){
                        Session::flash('warning','El dinero solicitado es superior al disponible para asignar.');
                    } else {
                        //SE ADICIONA EL DINERO QUE HABIA SIDO RETIRADO ANTERIORMENTE
                        $add = $fontRubro->valor_disp_asign + $fontDep->value;
                        $fontRubro->valor_disp_asign = $add - $request->valorAsignar[0];
                        $fontRubro->save();

                        $fontDep->value = $request->valorAsignar[0];
                        $fontDep->saldo = $request->valorAsignar[0];
                        $fontDep->save();

                        Session::flash('success', 'Se ha actualizado el dinero correctamente de la dependencia');
                    }
                } else Session::flash('warning','Ya se han usado dineros de la dependencia, no se puede cambiar el valor.');
            } else {
                $fontRubro = FontsRubro::find($request->fuenteRid);
                if ($request->valorAsignar[0] > $fontRubro->valor_disp_asign){
                    Session::flash('warning','El dinero solicitado es superior al disponible para asignar.');
                } else {
                    $fontRubro->valor_disp_asign = $fontRubro->valor_disp_asign - $request->valorAsignar[0];
                    $fontRubro->save();

                    $depAsignar = new DependenciaRubroFont();
                    $depAsignar->dependencia_id = $request->idDep;
                    $depAsignar->rubro_font_id  = $request->fuenteRid;
                    $depAsignar->vigencia_id  = $request->vigenciaid;
                    $depAsignar->value = $request->valorAsignar[$i];
                    $depAsignar->saldo = $request->valorAsignar[$i];
                    $depAsignar->save();

                    Session::flash('success','Se ha asignado el dinero correctamente a la dependencia');
                }
            }
        }
        return redirect('/presupuesto/rubro/'.$id);
    }

}