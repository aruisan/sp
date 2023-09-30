<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\Nomina;
use App\NominaEmpleadoNomina;
use App\NominaEmpleadoDescuentos;
use App\Model\Persona;
use App\Helpers\FechaHelper;
use Session, PDF;

class DescuentosController extends Controller
{
    private $view = "nomina.descuentos";

    public function index($tipo){
        $nominas = Nomina::where('tipo', $tipo)->orderBy('id', 'desc')->get();
        //dd($nominas);
        return view("{$this->view}.index", compact('nominas', 'tipo'));
    }
/*
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

*/
    public function edit(Nomina $nomina){
        $movimientos = $nomina->empleados_nominas;
        $empleados =  $nomina->empleados_nominas->map(function($e){
            return [
                'id' => $e->id,
                'salario' => !is_null($e->empleado->salario) ? $e->empleado->salario : 0,
                'datos' => $e->empleado,
                'descuentos' => $e->descuentos->map(function($d){
                    return [
                        'id' => $d->id,
                        'nombre' =>$d->nombre,
                        'valor' =>$d->valor,
                        'tercero' => $d->tercero,
                        'n_cuotas' => $d->n_cuotas,
                        'n_cuotas_faltantes' => $d->n_cuotas_faltantes,
                        'valor_total' => is_null($d->padre) ? $d->valor_total : $d->padre->valor_total,
                        'saldo' => is_null($d->padre) ? $d->saldo : $d->padre->saldo,
                        'is_padre' => !is_null($d->padre),
                        'old' => $d->old                    ];
                }),
                'contador_descuentos' => 0,
                'total_descuentos' => $e->descuentos->count() > 0 ? $e->descuentos->sum('valor'): 0 
            ];
        });

        $terceros = Persona::where('tipo_tercero', 'especial')->get();
        //dd($movimientos);
        $salarios = $nomina->empleados_nominas->map(function($m){
            return [
                'id' => $m->id,
                'sueldo' => $m->sueldo,
                'descuentos' => $m->descuentos->map(function($d){
                    return [
                        'id' => $d->id,
                        'nombre' =>$d->nombre,
                        'valor' =>$d->valor,
                        'tercero' => $d->tercero,
                        'n_cuotas' => $d->n_cuotas,
                        'n_cuotas_faltantes' => $d->n_cuotas_faltantes,
                        'valor_total' => is_null($d->padre) ? $d->valor_total : $d->padre->valor_total,
                        'saldo' => is_null($d->padre) ? $d->saldo : $d->padre->saldo
                    ];
                }) 
            ];
        });
        //dd($salarios);

        //dd($salarios);

        return view("{$this->view}.edit", compact('nomina', 'movimientos', 'salarios', 'terceros', 'empleados'));
    }

    public function update_empleado_pensionado(Request $request, Nomina $nomina){
            //dd($request->all());
            $nomina_empleado = NominaEmpleadoNomina::where('nomina_id', $nomina->id)->where('nomina_empleado_id', $request->data[0])->first();

            foreach($nomina_empleado->descuentos->filter(function($d){  return is_null($d->padre) && !$d->old ; }) as $descuento):
                $descuento->delete();
            endforeach;

            if(isset($request->data[2])):
                foreach($request->data[3] as $y => $tercero):
                    $new_desc = new NominaEmpleadoDescuentos;
                    $new_desc->nomina_empleado_nomina_id = $nomina_empleado->id;
                    $new_desc->tercero_id = intval($tercero);
                    $new_desc->nombre = $request->data[2][$y];
                    $new_desc->valor_total = intval($request->data[4][$y]);
                    $new_desc->valor =  intval($request->data[4][$y]);
                    $new_desc->n_cuotas =  intval($request->data[5][$y]);
                    $new_desc->save();
                endforeach;
            endif;
        return response()->json(['entrada' => $request->all(), 'salida' => $nomina_empleado]);
    }

    public function update(Request $request, Nomina $nomina){
        $nomina->update([
            'descuentos' => $request->accion == 'finalizar' ? 1 : 0
        ]);

        if($request->accion == 'finalizar'):
            Session::flash('success', 'se ha finalizado la nomina de vacaciones.');
            return redirect()->route('nomina-descuentos.show', $nomina->id);
        else:
            Session::flash('success', 'se ha guardado los cambios.');
            return back();
        endif;
    }


    public function show(Nomina $nomina){
        $movimientos =  $nomina->empleados_nominas->filter(function($e){ return $e->descuentos->count() > 0;})->map(function($e){
            return [
                'nombre' => $e->empleado->nombre,
                'num_dc' => $e->empleado->num_dc,
                'cargo' => $e->empleado->cargo,
                'dias_trabajados' => $e->dias_laborados,
                'descuentos' => $e->descuento_x_entidad,
                'total_descuentos' => array_sum($e->descuento_x_entidad),
            ];
        })->values();
        //dd($movimientos);
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

    public function bancos_reportes(Nomina $nomina){
        return view("{$this->view}.reporte-bancos", compact('nomina'));
    }

    public function pasar(Nomina $nomina_old){
        if(1){
            $nomina = Nomina::where('tipo', $nomina_old->tipo)->where('id', '>', $nomina_old->id)->first();
            return view("{$this->view}.pasar-descuentos", compact('nomina_old', 'nomina'));
        }else{
            Session::flash('warning', 'Función desactivada, comunicarse con soporte.');
            return back();
        }
    }

    public function pasar_store(Nomina $nomina, Request $request){
        
        
        $movimientos = NominaEmpleadoNomina::whereIn('nomina_empleado_id', $request->empleado_id)->where('nomina_id', $nomina->id)->get();
        foreach($movimientos as $m):
            $m->descuentos()->delete();
        endforeach;
        foreach($request->empleado_id as $k => $empleado_id): 

            $movimiento = NominaEmpleadoNomina::where('nomina_empleado_id', $empleado_id)->where('nomina_id', $nomina->id)->first();
            if(!is_null($movimiento)):
                $desc = new NominaEmpleadoDescuentos;
                $desc->nombre = $request->nombre[$k];
                $desc->nomina_empleado_nomina_id = $movimiento->id;
                $desc->tercero_id = $request->tercero_id[$k];
                $desc->n_cuotas = $request->n_cuotas[$k];
                $desc->valor = $request->valor[$k];
                $desc->valor_total = $request->valor[$k];
                $desc->save();
            endif;
        endforeach;

        //Session::flash('success', 'se ha finalizado la nomina de vacaciones.');
        return redirect()->route('nomina-descuentos.show', $nomina->id);
    }

}
