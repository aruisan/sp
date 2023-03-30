<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\Nomina;
use App\NominaEmpleadoNomina;
use Session, PDF;

class HorasController extends Controller
{
    private $view = "nomina.descuentos";

    public function index(){
        $nominas = Nomina::where('tipo', 'empleado')->where('descuentos', '<>', 0)->get();
        //dd($nominas);
        return view("{$this->view}.index", compact('nominas'));
    }

    public function create(){
        $age_actual = date('Y');
        $nominas = Nomina::whereYear('created_at', $age_actual)->where('tipo', 'empleado')->where('descuentos', 0)->get()->map(function($n){
            return [
                'id' => $n->id,
                'mes' => $n->mes,
                'empleados' => $n->empleados_nominas->map(function($e){ 
                    return [
                        'nombre' => $e->empleado->nombre,
                        'id' => $e->id
                    ];
                })
            ];
        });

        //dd($nominas);

        if($nominas->count() > 0):
            return view("{$this->view}.create", compact('nominas'));
        else:
            Session::flash('warning', 'No existe nomina de Salario para Relacionar Descuentos.');
            return back();
        endif;

    }

    public function store(Request $request){
        //dd($request->all());
        $nomina = Nomina::find($request->nomina_id);
        $nomina->descuentos = 1;
        $nomina->save();

        foreach($request->empleado_id as $empleado):
            $nomina_empleado = NominaEmpleadoNomina::find($empleado);
            $nomina_empleado->descuentos = 1;
            $nomina_empleado->save();
        endforeach; 

        return redirect()->route('nomina-descuentos.edit', $nomina->id);
    }


    public function edit(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return $e->descuentos;});
        //dd($movimientos);
        $salarios = $nomina->empleados_nominas->map(function($m){
            return [
                'id' => $m->id,
                'sueldo' => $m->sueldo,
                'descuentos' => $m->descuentos->map(function($d){
                    return [
                        'id' => $d->id,
                        'valor' =>$d->valor,
                        'tercero' => $d->tercero
                    ];
                }) 
            ];
        });
        //dd($salarios);

        //dd($salarios);

        return view("{$this->view}.edit", compact('nomina', 'movimientos', 'salarios'));
    }

    public function update(Request $request, Nomina $nomina){
        //dd($request->all());
        $nomina->update([
            'finalizado' => $request->accion == 'finalizar' ? 1 : 0
        ]);
            
        foreach($request->empleado_id as $k => $empleado):
            //dd($empleado);
            $nomina_empleado = NominaEmpleadoNomina::find($empleado);
            $nomina_empleado->horas_extras = $request['horas_extras_'.$empleado];
            $nomina_empleado->horas_extras_festivos = $request['horas_extras__festivos'.$empleado];
            $nomina_empleado->horas_extras_nocturnas = $request['horas_extras_nocturnas_'.$empleado];
            $nomina_empleado->recargos_nocturnos = $request['recargos_nocturnos_'.$empleado];
            $nomina_empleado->save();
        endforeach;

        if($request->accion == 'finalizar'):
            $nomina->horas = 2;
            $nomina->save();
            Session::flash('success', 'se ha finalizado la nomina de vacaciones.');
            return redirect()->route('nomina-horas.show', $nomina->id);
        else:
            $nomina->horas = 1;
            $nomina->save();
            Session::flash('success', 'se ha guardado los cambios.');
            return back();
        endif;
    }
+

    public function show(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return $e->horas;});
        
        return view("{$this->view}.show", compact('nomina', 'movimientos'));
    }

    public function pdf_desprendibles(Nomina $nomina){
        set_time_limit(0);
        ini_set("memory_limit",-1);
        ini_set('max_execution_time', 0);
        $pdf = PDF::loadView("{$this->view}.pdf-desprendibles", compact('nomina'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function pdf_nomina(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return !is_null($e->horas);});
        //dd($movimientos[0]->v_vacaciones);
        $pdf = PDF::loadView("{$this->view}.pdf", compact('nomina', 'movimientos'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

}
