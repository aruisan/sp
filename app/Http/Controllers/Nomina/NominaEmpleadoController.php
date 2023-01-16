<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NominaEmpleadoController extends Controller
{
    private $view = "nomina.nomina";

    public function create(Nomina $nomina){
        $empleados = NominaEmpleado::all();
        return view("{$view}.create", compact('nomina', 'empleados'));
    }

    public function store(Nomina $nomina, Request $request){
        foreach($request->empleado_id as $k => $item):
            $nomina_empleado = NominaEmpleadoNomina::create([
                'nomina_empleado_id' => $item,
                'dias_laborados','horas_extras','recargos_nocturnos','sueldo']);
        endforeach;
    }
}
