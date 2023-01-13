<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Nomina;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;

class NominaController extends Controller
{
    private $view = "nomina";

    public function index(){
        $nominas = Nomina::all();
        return view("{$this->view}.index", compact('nominas'));
    }

    public function create(){
        $empleados = NominaEmpleado::all();
        return view("{$this->view}.create", compact('empleados'));
    }

    public function store(Request $request){
        //dd($request->all());
        $nomina = Nomina::create([
            'salud' => '8.5',
            'pension' => '12',
            'riesgos' => '0.522',
            'sena' => '2',
            'icbf' => '3',
            'caja_compensacion' => '4',
            'cesantias' => '8.33',
            'interes_cesantias' => 1,
            'prima_navidad' => '8.33',
            'vacaciones' => '4.17'
        ]);

        foreach($request->empleado_id as $k => $empleado):
            $nomina_empleado = NominaEmpleadoNomina::create([
                'nomina_empleado_id' => $empleado,
                'dias_laborados' => $request->dias_laborados[$k],
                'horas_extras' => $request->horas_extras[$k],
                'recargos_nocturnos' => $request->recargos_nocturnos[$k],
                'sueldo' => $request->sueldo[$k]
            ]);

            if(isset($request["descuento_".$k])):
                foreach($request["descuento_".$k] as $y => $descuento):
                    $nomina_empleado->descuentos()->create([
                        'nombre' => $descuento,
                        'valor' =>  $request["descuento_valor_".$k][$y]
                    ]);
                endforeach;
            endif;
        endforeach;

        return redirect()->route('nomina.show', $nomina->id);
    }
}
