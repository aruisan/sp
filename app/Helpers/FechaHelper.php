<?php

namespace App\Helpers;
use Carbon\Carbon;

class FechaHelper {
    public static function meses($year){
        $meses_array = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $age = date('Y');
        $rango = $year == $age ?  date('n') : 12;
        return array_slice($meses_array, 0, $rango-1);
    }

    public static function trimestres_actuales($year){
        $age = date('Y');
        $trimestre = $year == $age ? ceil(date('n')/3) : 3; 
        return range(0, $trimestre);
    }

    public static function years(){
        $year = date('Y');
        return range(2023, $year);
    }
}
