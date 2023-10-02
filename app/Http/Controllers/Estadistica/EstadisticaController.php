<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EstadisticaData;
use App\BPin;

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

    public function proyectos(){
        $porcentaje =  [
            0 => 99.999333333333,
            1 => 70.493047506851,
            2 => 68.131701026919,
            3 => 77.047850770479,
            4 => 100,
            5 => 94.39357475,
            6 => 100,
            7 => 61.333333333333,
            8 => 94.218422540607,
            9 => 99.951402352333,
            10 => 96.791568421053,
            11 => 94.438445510218,
            12 => 54.077956309029,
            13 => 99.985539314963,
            14 => 82.17260028374,
            15 => 100,
            16 => 99.957894225935,
            17 => 100,
            18 => 98.941796144701,
            19 => 99.078967920784,
            20 => 100,
            21 => 79.722022490235,
            22 => 75.528700906344,
            23 => 84.808765343171,
            24 => 100,
            25 => 97.659166668509,
            26 => 100,
            27 => 100,
            28 => 97.875696470082,
            29 => 94.463246915944,
            30 => 99.71317282397,
            31 => 98.677905454545,
            32 => 91.512069203973,
            33 => 100,
            34 => 71.173280134741,
            35 => 88.371424649518,
            36 => 97.717432053772,
            37 => 67.555555555556,
            38 => 98.751875,
            39 => 100,
            40 => 84.161484095404,
            41 => 20.652002832866,
            42 => 100
        ];
          $proyecto_code = [
            0 => "2021885640055",
            1 => "2021885640039",
            2 => "2021885640029",
            3 => "2021885640044",
            4 => "2021885640020",
            5 => "2021885640062",
            6 => "2022885640013",
            7 => "2021885640043",
            8 => "2022885640009",
            9 => "2022885640004",
            10 => "2022885640014",
            11 => "2021885640067",
            12 => "2022885640006",
            13 => "2022885640001",
            14 => "2021885640041",
            15 => "2021885640031",
            16 => "2021885640022",
            17 => "2022885640002",
            18 => "2021885640069",
            19 => "2021885640016",
            20 => "2021885640036",
            21 => "2021885640063",
            22 => "2020885640041",
            23 => "2021885640045",
            24 => "2021885640026",
            25 => "2021885640025",
            26 => "2021885640059",
            27 => "2021885640014",
            28 => "2022885640008",
            29 => "2021885640049",
            30 => "2022885640003",
            31 => "2021885640035",
            32 => "2021885640037",
            33 => "2021885640061",
            34 => "2021885640019",
            35 => "2021885640032",
            36 => "2021885640033",
            37 => "2022885640010",
            38 => "2022885640012",
            39 => "2021885640028",
            40 => "2021885640051",
            41 => "2021885640040",
            42 => "2021885640052"
          ];

          $proyectos = collect();

          foreach($proyecto_code as $i => $p_code): 
            $proyecto = BPin::where('cod_proyecto', $p_code)->first();
            $proyecto->porcentaje_ejecucion = $porcentaje[$i];
            $proyectos->push($proyecto);
          endforeach;

          return view('estadistica.proyectos.public', compact('proyectos'));
    }
}
