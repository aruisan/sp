<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Nomina;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;
use App\Model\Persona;

class NominaController extends Controller
{
    private $view = "nomina";

    public function index($tipo){
        $nominas = Nomina::where('tipo', $tipo)->get();
        return view("{$this->view}.index_{$tipo}", compact('nominas'));
    }

    public function create($tipo){
        //$empleados = NominaEmpleado::where('id', '<', 3)->get();
        $empleados = NominaEmpleado::where('activo', True)->where('tipo', $tipo)->get();
        $terceros = Persona::where('tipo_tercero', 'especial')->get();
        return view("{$this->view}.create_{$tipo}", compact('empleados', 'terceros'));
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
            'vacaciones' => '4.17',
            'tipo' => $request->tipo,
            'mes' => $request->mes
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

        if($request->accion == 'finalizar'):
            Session::flash('success', 'se ha finalizado la nomina.');
            return redirect()->route('nomina.show', $nomina->id);
        else:
            Session::flash('success', 'se ha guardado los cambios.');
            return redirect()->route('nomina.edit', $nomina->id);
        endif;
    }

    public function edit(Nomina $nomina){
        return view("{$this->view}.edit", compact('nomina'));
    }

    public function show(Nomina $nomina){
        return view("{$this->view}.show", compact('nomina'));
    }

    public function update(Request $request, Nomina $nomina){
        $nomina->update($request->all());
        return response()->json(200);
    }


}
