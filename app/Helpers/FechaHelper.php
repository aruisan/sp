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
        $trimestre = $year == $age ?  intval(floor(date('n')/3))-1: 3; 
        return range(0, $trimestre);
    }

    public static function semestres_actuales($year){
        $age = date('Y');
        $trimestre = $year == $age ? intval(floor(date('n')/6))-1 : 1; 
        return range(0, $trimestre);
    }


    public static function years(){
        $year = date('Y');
        return range(2023, $year);
    }

    public static function periodos(){
        return ['mensual', 'trimestre', 'semestre', 'anual'];
    }

    public static function estructura_periodos(){
        $mensual = [];
        $trimestral = [];
        $semestral = [];
        $anual = [];
        $years = self::years();

        foreach($years as $y):
            
            $mensual[$y] = self::meses($y);
            $trimestral[$y] = self::trimestres_actuales($y);
            $semestral[$y] = self::semestres_actuales($y);
        endforeach; 

        $periodos = [
            'mensual' => $mensual,
            'trimestre' => $trimestral,
            'semestre' => $semestral,
            'anual' => $years
        ];

        return $periodos;
    }

    public static function bancos_terceros(){
        $entidad_nombre = ['Popular', 'Bogota', 'Agrario', 'Coosepark', 'Davivienda', 'Judicial', 'Coocasa', 'Sindicato'];
        $entidad_id = [1940, 1854, 1855, 1858, 1856, 1866, 1859, 2129];

        return [$entidad_nombre, $entidad_id];
    }

    
}
