<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Http\Request;
use App\Traits\ResourceTraits;
use App\Resource;
use Illuminate\Support\Facades\Storage;

use Session;

class RubrosMovController extends Controller
{
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

        }elseif ($m == 3){

            $mov = RubrosMov::findOrFail($idM);
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

        }
    }

    public function movimiento(Request $request, $m, $id)
    {
        if ($m == 1){

            $fuenteR_id = $request->fuenteR_id;
            $valor_Red  = $request->valorRed;
            $count = count($fuenteR_id);
            $fuente_id_Add = $request->fuente_id;
            $rubro_mov_id = $request->rubro_Mov_id;

            for($i = 0; $i < $count; $i++){

                if ($rubro_mov_id[$i]){

                    $this->update($rubro_mov_id[$i], $fuente_id_Add[$i], $valor_Red[$i], $fuenteR_id[$i], $id);

                }else{

                    $Frubro = FontsRubro::findOrFail($fuenteR_id[$i]);
                    $Frubro->valor_disp = $Frubro->valor_disp - $valor_Red[$i];
                    $Frubro->save();

                    $file = new ResourceTraits;
                    $resource = $file->resource($request->fileCyC, 'public/CreditoyCC');

                    $rubrosMov = new RubrosMov();
                    $rubrosMov->valor = $valor_Red[$i];
                    $rubrosMov->fonts_rubro_id = $fuenteR_id[$i];
                    $rubrosMov->font_vigencia_id = $fuente_id_Add[$i];
                    $rubrosMov->rubro_id = $id;
                    $rubrosMov->movimiento = $m;
                    $rubrosMov->resource_id = $resource;

                    $fontRubroCred = FontsRubro::where('rubro_id',$id)->first();
                    $idFontDepCred = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                        ->where('rubro_font_id', $fontRubroCred->id)->first();
                    $rubrosMov->dep_rubro_font_cred_id = $idFontDepCred->id;

                    $idFontDepCred->saldo = $idFontDepCred->saldo + $valor_Red[$i];
                    $idFontDepCred->save();

                    $idFontDepCC = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                            ->where('rubro_font_id', $fuenteR_id[$i])->first();
                    $rubrosMov->dep_rubro_font_cc_id = $idFontDepCC->id;

                    $idFontDepCC->saldo = $idFontDepCC->saldo - $valor_Red[$i];
                    $idFontDepCC->save();

                    $rubrosMov->save();

                    $FAdd = FontsRubro::where([['rubro_id', $id],['font_vigencia_id', '=', $fuente_id_Add[$i]]])->get();
                    $count2 = $FAdd->count();
                    for($x = 0; $x < $count2; $x++){
                        $FAdd[$x]->valor_disp = $FAdd[$x]->valor_disp + $valor_Red[$i];
                        $FAdd[$x]->save();
                    }

                }

            }

            Session::flash('success','El Credito se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);

        } elseif ($m == 2){

            dd($request->depID);

            $fuenteR_id = $request->fontID;
            $valor = $request->valorAdd;
            $mov_id = $request->mov_id;
            $vigencia = Vigencia::find($request->vigencia_id);

            for($i = 0; $i < count($fuenteR_id); $i++){

                if (isset($mov_id[$i])){
                    $this->updateMov($mov_id[$i], $valor[$i], $request, $id, $m, $vigencia);
                }else{

                    $FontRubro = FontsRubro::findOrFail($fuenteR_id[$i]);
                    $FontRubro->valor_disp = $FontRubro->valor_disp + $valor[$i];
                    $FontRubro->save();

                    $file = new ResourceTraits;
                    $resource = $file->resource($request->fileAdicion, 'public/AdicionyRed');

                    $rubrosMov2 = new RubrosMov();

                    $rubrosMov2->valor = $valor[$i];
                    $rubrosMov2->fonts_rubro_id = $fuenteR_id[$i];
                    $rubrosMov2->font_vigencia_id = $vigencia->id;
                    $rubrosMov2->rubro_id = $id;
                    $rubrosMov2->movimiento = $m;
                    $rubrosMov2->resource_id = $resource;

                    if ($vigencia->tipo == 0 and $valor[$i] > 0){
                        $rubrosMov2->dep_rubro_font_id = $request->depID[$i];

                        //SI EL RUBRO ES DE EGRESOS SE HACE LA ADICIÓN AL DINERO DE LA DEPENDENCIA
                        $idFontDepCred = DependenciaRubroFont::find($request->depID[$i]);

                        $idFontDepCred->saldo = $idFontDepCred->saldo + $valor[$i];
                        $idFontDepCred->save();
                    }

                    $rubrosMov2->save();
                }
            }

            Session::flash('success','La adición se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);


        } elseif ($m == 3){

            $fuenteR2_id = $request->fuenteR_id;
            $valor2 = $request->valorCred;
            $mov_id2 = $request->mov_id;
            $count2 = count($fuenteR2_id);

            for($i = 0; $i < $count2; $i++){

                if ($mov_id2[$i]){
                    $this->updateMov($mov_id2[$i], $valor2[$i], $request, $id, $m);
                }else{
                    $FontRubro2 = FontsRubro::findOrFail($fuenteR2_id[$i]);
                    $FontRubro2->valor_disp = $FontRubro2->valor_disp - $valor2[$i];
                    $FontRubro2->save();

                    $file2 = new ResourceTraits;
                    $resource2 = $file2->resource($request->fileReduccion, 'public/AdicionyRed');

                    $rubrosMov3 = new RubrosMov();

                    $fontRubroCred = FontsRubro::where('rubro_id',$id)->first();
                    $idFontDepCred = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                        ->where('rubro_font_id', $fontRubroCred->id)->first();
                    $rubrosMov3->dep_rubro_font_cred_id = $idFontDepCred->id;

                    $idFontDepCred->saldo = $idFontDepCred->saldo + $valor2[$i];
                    $idFontDepCred->save();

                    $idFontDepCC = DependenciaRubroFont::where('dependencia_id', auth()->user()->dependencia->id)
                        ->where('rubro_font_id', $fuenteR2_id[$i])->first();
                    $rubrosMov3->dep_rubro_font_cc_id = $idFontDepCC->id;

                    $idFontDepCC->saldo = $idFontDepCC->saldo - $valor2[$i];
                    $idFontDepCC->save();

                    $rubrosMov3->valor = $valor2[$i];
                    $rubrosMov3->fonts_rubro_id = $fuenteR2_id[$i];
                    $rubrosMov3->font_vigencia_id = 1;
                    $rubrosMov3->rubro_id = $id;
                    $rubrosMov3->movimiento = $m;
                    $rubrosMov3->resource_id = $resource2;
                    $rubrosMov3->save();
                }
            }

            Session::flash('success','La reducción se realizo correctamente');
            return redirect()->action('Hacienda\Presupuesto\RubrosController@show', [$id]);
        }
    }
}
