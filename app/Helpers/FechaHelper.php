<?php

namespace App\Helpers;
use Carbon\Carbon;

class FechaHelper {
    public static function meses($year){
        $meses_array = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $age = date('Y');
        $rango = $year == $age ?  date('n') : 12;
        return array_slice($meses_array, 0, $rango );
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

    public static function bancos_terceros(){
        $entidad_nombre = ['Popular', 'Bogota', 'Agrario', 'Coosepark', 'Davivienda', 'Judicial', 'Coocasa', 'Sindicato'];
        $entidad_id = [1940, 1854, 1855, 1858, 1856, 1866, 1859, 2129];

        return [$entidad_nombre, $entidad_id];
    }

    
}
