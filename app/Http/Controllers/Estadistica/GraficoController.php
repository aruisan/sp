<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EstadisticaData;
class GraficoController extends Controller
{
    private $ages = [2020,2021,2022,2023,2024,2025];
    private $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    public function grafico($tipo){
        //$age = date('Y');
        $age = 2022;
        if($tipo == 'educacion'){
            $items = EstadisticaData::whereIn('coleccion', ['colegio', 'sena', 'ludoteca'])->whereYear('created_at', $age)->get();
        }elseif($tipo == 'salud'){
            $items = EstadisticaData::whereIn('coleccion', ['hospital'])->whereYear('created_at', $age)->get();
        }elseif($tipo == 'otros'){
            $items = EstadisticaData::whereIn('coleccion', ['empresa de energia', 'empresa AAA', 'notaria'])->whereYear('created_at', $age)->get();
        }elseif($tipo == 'gobierno'){
            $items = EstadisticaData::whereIn('coleccion', ['policia', 'notaria', 'bomberos'])->whereYear('created_at', $age)->get();
        }

        //dd([$tipo, $items]);


        return view('estadistica.graficos.educacion', compact('items'));
    }




    public function format_barras($titulo, $headers, $data){
        return $options = [
            'title' => $titulo,
            'chartArea' => ['width' => '50%'],
            'hAxis' => [
                'title' => 'Total Population',
                'minValue' => 0
            ],
            'vAxis' => [
                'title' => 'City'
            ],
            'bars' => 'horizontal', //required if using material chart
            'axes' => [
                'y' => [0 => ['side' => 'right']]
            ]
        ];

        $cols = $headers;
        $rows = data;
    }
}
