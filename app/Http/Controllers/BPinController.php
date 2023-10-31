<?php

namespace App\Http\Controllers;

use App\BPin;
use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
use App\Rubro;
use Illuminate\Http\Request;
use Session;

class BPinController extends Controller
{
    public function index(){
       $bpins = BPin::where('secretaria', auth()->user()->dependencia->nombre)->get()->unique('cod_proyecto');
       return view('bpin.index', compact('bpins'));
    }

    public function store(Request $request){

        $properties = ['confinanciado', 'entidad', 'secretaria', 'dependencia', 'cod_sector', 'nombre_sector','cod_proyecto'
        ,'nombre_proyecto' ,'metas' , 'fecha_radicado', 'inicial', 'final', 'cod_producto' ,'nombre_producto' , 'cod_indicador' 
        ,'nombre_indicador', 'vigencia_id'];
        $bpin_copia = BPin::where('cod_proyecto', $request->cod_proyecto)->first();
        $new_bpin = new BPin();

        foreach($properties as $item):
            $new_bpin[$item] = $bpin_copia[$item];
        endforeach;

        $new_bpin->cod_actividad = $request->cod_actividad;
        $new_bpin->actividad = $request->nombre_actividad;
        $new_bpin->propios = $request->propios;
        $new_bpin->sgp = $request->sgp;
        $new_bpin->save();

        $depRubroFont = new DependenciaRubroFont();
        $depRubroFont->dependencia_id = $request->dependencia;
        $depRubroFont->rubro_font_id = $request->fontRubEgr;
        $depRubroFont->vigencia_id = $request->vigencia_id;
        $depRubroFont->value = 0;
        $depRubroFont->saldo = 0;
        $depRubroFont->save();

        $bpinVig = new bpinVigencias();
        $bpinVig->bpin_id = $new_bpin->id;
        $bpinVig->vigencia_id = $request->vigencia_id;
        $bpinVig->dep_rubro_id = $depRubroFont->id;
        $bpinVig->propios = 0;
        $bpinVig->saldo = 0;
        $bpinVig->save();

        Session::flash('success','Se ha creado exitosamente la actividad.');
        return back();
    }

    public function create(){
        $rubros = Rubro::where('dependencia_id', auth()->user()->dependencia_id)->get();
        return view('bpin.create', compact('rubros'));
    }

    public function show(Bpin $bpin){
        $bpins = Bpin::where('cod_proyecto', $bpin->cod_proyecto)->get();
        return view('bpin.show', compact('bpins'));
    }

}
