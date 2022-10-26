<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EstadisticaData;

class EstadisticaController extends Controller
{
    private $dir_view = 'estadistica';

    public function index(){
        return view("{$this->dir_view}.index");
    }

    public function load_data_collection(Request $request){
        //return $request->data;
        $coleccion = EstadisticaData::where('coleccion', $request->coleccion)->first();
        if(is_null($coleccion)):
            $coleccion = new EstadisticaData;
            $coleccion->coleccion = $request->coleccion;
            $coleccion->data = $this->format_data_set($request->data);
            $coleccion->save();
        endif;

        return json_encode($coleccion->data);
    }

    public function store_colecciones(Request $request){
        $data = $this->format_data_set($request->data);
        $coleccion = EstadisticaData::where('coleccion', $request->coleccion)->first();
        $coleccion->data = $this->format_data_set($request->data);
        $coleccion->save();
        return response()->json($coleccion);
    }

    public function format_data_set($data){
        $resp = collect();
        foreach($data as $item):
            $item_array = collect([$item[0] => $item[1]]);
            $resp->put($item[0], $item[1]);
        endforeach;
        return $resp;
    }
}
