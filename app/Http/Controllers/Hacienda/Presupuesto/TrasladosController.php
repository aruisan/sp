<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\bpinVigencias;
use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Http\Request;
use App\Traits\ResourceTraits;
use App\Resource;
use Illuminate\Support\Facades\Storage;

use Session;

class TrasladosController extends Controller
{

    public function index($año){
        $traslados = RubrosMov::where('valor','>',0)->whereBetween('created_at',array($año.'-01-01', $año.'-12-31'))->get();
        $rol = auth()->user()->roles->first()->id;

        return view('hacienda.presupuesto.traslados.index', compact('traslados','año', 'rol'));
    }

    public function create($año){
        $presupuestos = Vigencia::where('vigencia', $año)->get();
        foreach ($presupuestos as $prep){
            if ($prep->tipo == 0){
                $rubI = Rubro::where('vigencia_id', $prep->id)->orderBy('cod','ASC')->get();
                foreach ($rubI as $rub){
                    foreach ($rub->fontsRubro as $fuente){
                        $dependencias = DependenciaRubroFont::where('rubro_font_id', $fuente->id)->get();
                        foreach ($dependencias as $dependencia){
                            $rubrosEgresos[] = collect(['id' => $fuente->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                                $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description, 'dep' => $dependencia->dependencias]);
                        }
                    }
                }
            }
        }


        return view('hacienda.presupuesto.traslados.create', compact('año','presupuestos',
            'rubrosEgresos'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RubrosMov  $rubrosMov
     * @return \Illuminate\Http\Response
     */
    public function update($id, $idF, $valor, $idFR, $idR)
    {
        //SE TIENE QUE ACTUALIZAR TODO EL MODULO DE REALIZAR EL TRASLADO AL RUBRO
        $movim = RubrosMov::findOrFail($id);
        $valorAnterior = $movim->valor;
        $idFAnterior = $movim->font_vigencia_id;

        $Frubro = FontsRubro::findOrFail($idFR);

        $Frubro->valor_disp = $Frubro->valor_disp + $valorAnterior;
        $Frubro->valor_disp = $Frubro->valor_disp - $valor;
        $Frubro->save();

        if ($idF != $idFAnterior){
            $FAdd = FontsRubro::where([['rubro_id', $idR],['font_vigencia_id', '=', $idFAnterior]])->get();
            $count2 = $FAdd->count();

            for($x = 0; $x < $count2; $x++){
                $FAdd[$x]->valor_disp = $FAdd[$x]->valor_disp - $valorAnterior;
                $FAdd[$x]->save();
            }

            $FAdd2 = FontsRubro::where([['rubro_id', $idR],['font_vigencia_id', '=', $idF]])->get();
            $count3 = $FAdd2->count();

            for($y = 0; $y < $count3; $y++){
                $FAdd2[$y]->valor_disp = $FAdd2[$y]->valor_disp + $valor;
                $FAdd2[$y]->save();
            }

        } else{

            $FAdd = FontsRubro::where([['rubro_id', $idR],['font_vigencia_id', '=', $idF]])->get();
            $count2 = $FAdd->count();

            for($x = 0; $x < $count2; $x++){
                $FAdd[$x]->valor_disp = $FAdd[$x]->valor_disp - $valorAnterior;
                $FAdd[$x]->valor_disp = $FAdd[$x]->valor_disp + $valor;
                $FAdd[$x]->save();
            }
        }

        $mov = RubrosMov::findOrFail($id);

        $fontRubroCred = FontsRubro::where('rubro_id',$idR)->first();
        $idFontDepCred = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
            ->where('rubro_font_id', $fontRubroCred->id)->first();
        $mov->dep_rubro_font_cred_id = $idFontDepCred->id;

        $idFontDepCred->saldo = $idFontDepCred->saldo + $valor;
        $idFontDepCred->save();

        $idFontDepCC = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
            ->where('rubro_font_id', $idF)->first();
        $mov->dep_rubro_font_cc_id = $idFontDepCC->id;

        $idFontDepCC->saldo = $idFontDepCC->saldo - $valor;
        $idFontDepCC->save();

        $mov->font_vigencia_id = $idF;
        $mov->valor = $valor;
        $mov->save();


    }

    public function updateMov($idM, $val, $request, $id, $m, $vigencia)
    {
        if ($m == 2){

            $mov = RubrosMov::findOrFail($idM);

            if ($mov->movimiento == 2){
                $idResourceD = $mov->resource_id;

                $file = new ResourceTraits;
                $resource = $file->resource($request->fileAdicion, 'public/AdicionyRed');

                $fuenteR_id = $request->fontID;

                for($i = 0; $i < count( $fuenteR_id); $i++){
                    $FontRubro = FontsRubro::findOrFail($fuenteR_id[$i]);
                    $FontRubro->valor_disp = $FontRubro->valor_disp - $mov->valor;
                    $FontRubro->valor_disp = $FontRubro->valor_disp + $val;
                    $FontRubro->save();

                    if ($vigencia->tipo == 0){
                        $mov->dep_rubro_font_id = $request->depID[$i];

                        //SI EL RUBRO ES DE EGRESOS SE HACE LA ADICIÓN AL DINERO DE LA DEPENDENCIA
                        $idFontDepCred = DependenciaRubroFont::find($request->depID[$i]);

                        $idFontDepCred->saldo = $idFontDepCred->saldo - $mov->valor;
                        $idFontDepCred->saldo = $idFontDepCred->saldo + $val;
                        $idFontDepCred->save();
                    }
                }

                $mov->valor = $val;
                $mov->resource_id = $resource;

                //SI SE ESTA ENVIANDO EN 0 EL VALOR DE LA ADICION SE ELIMINA EL MOVIMIENTO.
                if ($val == 0) $mov->delete();
                else $mov->save();

                $archivo = Resource::findOrFail($idResourceD);
                Storage::delete($archivo->ruta);
                $archivo->delete();

                Session::flash('success','La adición se actualizó correctamente');
                return redirect('/presupuesto/rubro/'.$id);
            } else return redirect('/presupuesto/rubro/'.$id);


        }elseif ($m == 3){

            $mov = RubrosMov::findOrFail($idM);
            if ($mov->movimiento == 2){
                $idResourceD = $mov->resource_id;

                $file = new ResourceTraits;
                $resource = $file->resource($request->fileReduccion, 'public/AdicionyRed');

                $fuenteB_id = $request->fuenteBase_id;
                $fuenteR_id = $request->fuenteR_id;

                for($i = 0; $i < count( $fuenteR_id); $i++){
                    $FontRubro = FontsRubro::findOrFail($fuenteR_id[$i]);
                    $FontRubro->valor_disp = $FontRubro->valor_disp + $mov->valor;
                    $FontRubro->valor_disp = $FontRubro->valor_disp - $val;
                    $FontRubro->save();
                }

                $fontRubroCred = FontsRubro::where('rubro_id',$mov->rubro_id)->first();
                $idFontDepCred = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                    ->where('rubro_font_id', $fontRubroCred->id)->first();
                $mov->dep_rubro_font_cred_id = $idFontDepCred->id;

                $idFontDepCred->saldo = $idFontDepCred->saldo + $val;
                $idFontDepCred->save();

                $idFontDepCC = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                    ->where('rubro_font_id', $mov->fonts_rubro_id)->first();
                $mov->dep_rubro_font_cc_id = $idFontDepCC->id;

                $idFontDepCC->saldo = $idFontDepCC->saldo - $val;
                $idFontDepCC->save();

                $mov->valor = $val;
                $mov->resource_id = $resource;
                $mov->save();

                $archivo = Resource::findOrFail($idResourceD);
                Storage::delete($archivo->ruta);
                $archivo->delete();

                Session::flash('success','La reducción se actualizó correctamente');
                return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);
            } else return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);


        }
    }

    public function movimiento(Request $request, $m, $id)
    {
        if ($m == 1){

            $fuenteR_id = $request->fontID;
            $valor = $request->valorRed;
            $mov_id = $request->mov_id;
            $vigencia = Vigencia::find($request->vigencia_id);

            if ($vigencia->tipo == 0) $count = count($request->depID);
            else $count = count($request->mov_id);

            for($i = 0; $i < $count; $i++){

                if (isset($mov_id[$i])){

                    $this->updateMov($mov_id[$i], $valor[$i], $request, $id, $m, $vigencia);

                }else{

                    $Frubro = FontsRubro::findOrFail($fuenteR_id[$i]);
                    $Frubro->valor_disp = $Frubro->valor_disp - $valor[$i];
                    $Frubro->save();

                    $file = new ResourceTraits;
                    $resource = $file->resource($request->fileCyC, 'public/CreditoyCC');

                    $rubrosMov = new RubrosMov();
                    $rubrosMov->valor = $valor[$i];
                    $rubrosMov->fonts_rubro_id = $fuenteR_id[$i];
                    $rubrosMov->font_vigencia_id = $vigencia->id;
                    $rubrosMov->rubro_id = $id;
                    $rubrosMov->movimiento = $m;
                    $rubrosMov->resource_id = $resource;

                    if ($vigencia->tipo == 0 and $valor[$i] > 0){
                        $rubrosMov->dep_rubro_font_id = $request->depID[$i];

                        //SI EL RUBRO ES DE EGRESOS SE HACE LA ADICIÓN AL DINERO DE LA DEPENDENCIA
                        $idFontDepCred = DependenciaRubroFont::find($request->depID[$i]);

                        $idFontDepCred->saldo = $idFontDepCred->saldo - $valor[$i];
                        $idFontDepCred->save();
                    }

                    $rubrosMov->save();

                }

            }

            Session::flash('success','El Credito se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);

        } elseif ($m == 2){

            if (intval($request->valorAdd) <= 0){
                Session::flash('warning','El valor de la adición no puede ser menor o igual a 0');
                return redirect()->back();
            }

            //SIGNIFICA QUE ESA FUENTE NO HA RECIBIDO UNA ADICION Y SE TIENE QUE CREAR EL MOVIMIENTO
            if ($request->tipoVigencia == "1"){
                $fontRubro = FontsRubro::find($request->fuenteDep);
                $fontRubro->valor_disp = $fontRubro->valor_disp + intval($request->valorAdd);
                $fontRubro->save();

                $rubroMov = new RubrosMov();
                $rubroMov->valor = intval($request->valorAdd);
                $rubroMov->fonts_rubro_id = $request->fuenteDep;
                $rubroMov->font_vigencia_id = $request->vigencia_id;
                $rubroMov->rubro_id = $fontRubro->rubro_id;
                $rubroMov->resource_id = 0;
                $rubroMov->movimiento = '2';
                $rubroMov->save();

            } else {

                $depRubroFont = DependenciaRubroFont::find($request->DepFontID);
                $depRubroFont->saldo = $depRubroFont->saldo + intval($request->valorAdd);
                $depRubroFont->save();

                $rubroMov = new RubrosMov();
                $rubroMov->valor = intval($request->valorAdd);
                $rubroMov->fonts_rubro_id = $depRubroFont->rubro_font_id;
                $rubroMov->font_vigencia_id = $request->vigencia_id;
                $rubroMov->rubro_id = $depRubroFont->fontRubro->rubro_id;
                $rubroMov->resource_id = 0;
                $rubroMov->movimiento = '2';
                $rubroMov->dep_rubro_font_id = $request->DepFontID;
                $rubroMov->save();
            }

            $file = new ResourceTraits;
            $file->resourceMov($request->fileAdicion, 'public/AdicionyRed', $rubroMov->id);

            Session::flash('success', 'La adición se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);

        } elseif ($m == 3){

            if (intval($request->valorRed) <= 0){
                Session::flash('warning','El valor de la reducción no puede ser menor o igual a 0');
                return redirect()->back();
            }

            //SIGNIFICA QUE ESA FUENTE NO HA RECIBIDO UNA REDUCCIÓN Y SE TIENE QUE CREAR EL MOVIMIENTO
            if ($request->tipoVigencia == "1"){
                $fontRubro = FontsRubro::find($request->fuenteDep);
                $fontRubro->valor_disp = $fontRubro->valor_disp - intval($request->valorRed);
                $fontRubro->save();

                $rubroMov = new RubrosMov();
                $rubroMov->valor = intval($request->valorRed);
                $rubroMov->fonts_rubro_id = $request->fuenteDep;
                $rubroMov->font_vigencia_id = $request->vigencia_id;
                $rubroMov->rubro_id = $fontRubro->rubro_id;
                $rubroMov->resource_id = 0;
                $rubroMov->movimiento = '3';
                $rubroMov->save();

            }else {

                $depRubroFont = DependenciaRubroFont::find($request->fuenteDep);
                $depRubroFont->saldo = $depRubroFont->saldo - intval($request->valorRed);
                $depRubroFont->save();

                //SE DESCUENTA SI ESA FUENTE DE DEPENDENCIA ESTA ASIGNADA A UN BPIN
                $bpinsVig = bpinVigencias::where('dep_rubro_id', $request->fuenteDep)->get();
                foreach ($bpinsVig as $bpinV){
                    $bpinV->saldo =  $bpinV->saldo - intval($request->valorRed);
                    $bpinV->save();
                }

                $rubroMov = new RubrosMov();
                $rubroMov->valor = intval($request->valorRed);
                $rubroMov->fonts_rubro_id = $depRubroFont->rubro_font_id;
                $rubroMov->font_vigencia_id = $request->vigencia_id;
                $rubroMov->rubro_id = $depRubroFont->fontRubro->rubro_id;
                $rubroMov->resource_id = 0;
                $rubroMov->movimiento = '3';
                $rubroMov->dep_rubro_font_id = $request->fuenteDep;
                $rubroMov->save();
            }

            $file = new ResourceTraits;
            $file->resourceMov($request->fileReduccion, 'public/AdicionyRed', $rubroMov->id);

            Session::flash('success', 'La reducción se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);
        }
    }
}
