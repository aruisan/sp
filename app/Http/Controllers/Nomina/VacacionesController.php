<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\Nomina;
use App\NominaEmpleadoNomina;
use Session, PDF;

class VacacionesController extends Controller
{
    private $view = "nomina.vacaciones";

    public function index(){
        $nominas = Nomina::where('tipo', 'empleado')->where('vacaciones', '<>', 0)->get();
        //dd($nominas);
        return view("{$this->view}.index", compact('nominas'));
    }

    public function create(){
        $age_actual = date('Y');
        $nominas = Nomina::whereYear('created_at', $age_actual)->where('tipo', 'empleado')->where('vacaciones', 0)->get()->map(function($n){
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
            Session::flash('warning', 'No existe nomina de Salario para Relacionar vacaciones.');
            return back();
        endif;

    }

    public function store(Request $request){
       // dd($request->all());
        $nomina = Nomina::find($request->nomina_id);
        $nomina->vacaciones = 1;
        $nomina->save();

        foreach($request->ind_vac as $empleado):
            $data  = explode(',', $empleado);
            $nomina_empleado = NominaEmpleadoNomina::find($data[1]);
            //dd($nomina_empleado);
            $nomina_empleado->ind_vac = $data[0];
            $nomina_empleado->save();
        endforeach; 

        return redirect()->route('nomina-vacaciones.edit', $nomina->id);
    }


    public function edit(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return !is_null($e->ind_vac);});
        //dd($movimientos);
        $salarios = $movimientos->pluck('sueldo', 'id');
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

            $nomina_empleado->dias_vacaciones_laborados = $nomina_empleado->ind_vac == 'indemnizacion' ? $request["dias_vacaciones_laborados_{$empleado}"]: 0;
            $nomina_empleado->dias_vacaciones = $nomina_empleado->ind_vac == 'vacaciones' ? $request['dias_vacaciones_'.$empleado]: 0;
            $nomina_empleado->save();
        endforeach;

        if($request->accion == 'finalizar'):
            $nomina->vacaciones = 2;
            $nomina->update();
            Session::flash('success', 'se ha finalizado la nomina de vacaciones.');
            return redirect()->route('nomina-vacaciones.show', $nomina->id);
        else:
            $nomina->vacaciones = 1;
            $nomina->update();
            Session::flash('success', 'se ha guardado los cambios.');
            return back();
        endif;
    }


    public function show(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return !is_null($e->ind_vac);});
        
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
        $movimientos = $nomina->empleados_nominas->filter(function($e){ return !is_null($e->ind_vac);});
        //dd($movimientos[0]->v_vacaciones);
        $pdf = PDF::loadView("{$this->view}.pdf", compact('nomina', 'movimientos'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

}
