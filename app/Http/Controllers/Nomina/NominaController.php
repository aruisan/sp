<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Nomina;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;
use App\Model\Persona;
use Session, PDF;

class NominaController extends Controller
{
    private $view = "nomina";

    public function __construct()
    {
         $this->middleware('permission:listar-nomina-empleados|listar-nomina-pensionados', ['only' => ['index']]);
         $this->middleware('permission:crear-nomina-empleados|crear-nomina-pensionados', ['only' => ['create', 'store']]);
         $this->middleware('permission:editar-nomina-empleados|editar-nomina-pensionados', ['only' => ['edit','update']]);
         $this->middleware('permission:ver-nomina-empleados|ver-nomina-pensionados', ['only' => ['show']]);
         $this->middleware('permission:pdf-nomina-empleados|pdf-nomina-pensionados', ['only' => ['pdf_nomina']]);
         $this->middleware('permission:pdf-desprendibles-nomina-empleados|pdf-desprendibles-nomina-pensionados', ['only' => ['pdf_desprendibles']]);
         $this->middleware('permission:pdf-contable-nomina-empleados|pdf-contable-nomina-pensionados', ['only' => ['pdf_contabilidad_presupuestal']]);
    }

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
            'mes' => $request->mes,
            'finalizado' => $request->accion == 'finalizar' ? 1 : 0
        ]);

        foreach($request->empleado_id as $k => $empleado):
            if($request->tipo == 'empleado'):
                $nomina_empleado = NominaEmpleadoNomina::create([
                    'nomina_id' => $nomina->id,
                    'nomina_empleado_id' => $empleado,
                    'dias_laborados' => $request->dias_laborados[$k],
                    'horas_extras' => $request->horas_extras[$k],
                    'horas_extras_festivos' => $request->horas_extras_festivos[$k],
                    'horas_extras_nocturnas' => $request->horas_extras_nocturnas[$k],
                    'recargos_nocturnos' => $request->recargos_nocturnos[$k],
                    'sueldo' => $request->sueldo[$k],
                    'bonificacion_direccion' => $request->bonificacion_direccion[$k],
                    'bonificacion_servicios' => $request->bonificacion_servicios[$k],
                    'bonificacion_recreacion' => $request->bonificacion_recreacion[$k],
                    'prima_antiguedad' => $request->prima_antiguedad[$k]
                ]);
            else:
                $nomina_empleado = NominaEmpleadoNomina::create([
                    'nomina_id' => $nomina->id,
                    'nomina_empleado_id' => $empleado,
                    'sueldo' => $request->sueldo[$k],
                    'tiene_eps' => $request->tiene_eps[$k],
                ]);
            endif;

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
        $terceros = Persona::where('tipo_tercero', 'especial')->get();
        //dd($nomina->empleados);
        $empleados =  $nomina->empleados_nominas->map(function($e){
            return [
                'salario' => !is_null($e->empleado->salario) ? $e->empleado->salario : 0,
                'datos' => $e->empleado,
                'movimiento' => [
                    'v_dias_laborados' => $e->v_dias_laborados, 
                    'v_horas_extras' => $e->v_horas_extras,
                    'v_horas_extras_festivos' => !is_null($e->v_horas_extras_festivos) ? $e->v_horas_extras_festivos : 0,
                    'v_horas_extras_nocturnas' => !is_null($e->v_horas_extras_nocturnas) ? $e->v_horas_extras_nocturnas : 0,
                    'v_recargos_nocturnos' =>  $e->v_recargos_nocturnos,
                    'v_bonificacion_direccion' => !is_null($e->v_bonificacion_direccion) ? $e->v_bonificacion_direccion : 0,
                    'v_bonificacion_servicios' => !is_null($e->v_bonificacion_servicios) ? $e->v_bonificacion_servicios : 0,
                    'v_bonificacion_recreacion' => !is_null($e->v_bonificacion_recreacion) ? $e->v_bonificacion_recreacion : 0,
                    'v_prima_antiguedad' => !is_null($e->v_prima_antiguedad) ? $e->v_prima_antiguedad : 0,
                    'dias_laborados' => !is_null($e->dias_laborados) ? $e->dias_laborados : 0, 
                    'horas_extras' => !is_null($e->horas_extras) ? $e->horas_extras : 0,
                    'horas_extras_festivos' => !is_null($e->horas_extras_festivos) ? $e->horas_extras_festivos : 0,
                    'horas_extras_nocturnas' => !is_null($e->horas_extras_nocturnas) ? $e->horas_extras_nocturnas : 0,
                    'recargos_nocturnos' => !is_null($e->recargos_nocturnos) ? $e->recargos_nocturnos : 0,
                    'bonificacion_direccion' => !is_null($e->bonificacion_direccion) ? $e->bonificacion_direccion : 0,
                    'bonificacion_servicios' => !is_null($e->bonificacion_servicios) ? $e->bonificacion_servicios : 0,
                    'bonificacion_recreacion' => !is_null($e->bonificacion_recreacion) ? $e->bonificacion_recreacion : 0,
                    'prima_antiguedad' => !is_null($e->prima_antiguedad) ? $e->prima_antiguedad : 0,
                    'tiene_eps' => $e->tiene_eps,
                ],
                'descuentos' => $e->descuentos,
                'total_descuentos' => $e->descuentos->count() > 0 ? $e->descuentos->sum('valor'): 0 
            ];
        });

        //dd($empleados[0]);
        return view("{$this->view}.edit_empleado", compact('nomina', 'empleados', 'terceros'));
    }

    public function show(Nomina $nomina){
        $movimientos =  $nomina->empleados_nominas->map(function($e){
            return [
                'nombre' => $e->empleado->nombre,
                'num_dc' => $e->empleado->num_dc,
                'cargo' => $e->empleado->cargo,
                'sueldo_basico' => $e->sueldo,
                'dias_trabajados' => $e->dias_laborados,
                'basico' => $e->v_dias_laborados,
                'v_horas_extras' => $e->v_horas_extras,
                'v_horas_extras_festivos' => $e->v_horas_extras_festivos,
                'v_horas_extras_nocturnas' => $e->v_horas_extras_nocturnas,
                'v_recargos_nocturnos' => $e->v_recargos_nocturnos,
                'v_bonificacion_direccion' => $e->bonificacion_direccion,
                'v_bonificacion_servicios' => $e->v_bonificacion_servicios,
                'v_bonificacion_recreacion' => $e->v_bonificacion_recreacion,
                'retroactivo' => 0,
                'v_prima_antiguedad' => $e->v_prima_antiguedad,
                'total_devengado' => $e->total_devengado,
                'salud' => $e->v_salud,
                'pension' => $e->v_pension,
                'fsp' => $e->fsp,
                'retefuente' => 0,
                'descuentos' => $e->descuentos->pluck('cop'),
                'total_descuentos' => $e->descuentos->sum('valor'),
                'embargos' => 0,
                'total_deduccion' => $e->total_deduccion,
                'neto' => $e->neto_pagar,
                'tipo_cuenta_bancaria' => $e->empleado->tipo_cuenta_bancaria,
                'banco_cuenta_bancaria' => $e->empleado->banco_cuenta_bancaria,
                'numero_cuenta_bancaria' => $e->empleado->numero_cuenta_bancaria              
            ];
        });

        //dd($movimientos[0]);
        return view("{$this->view}.show", compact('nomina', 'movimientos'));
    }

    public function update(Request $request, Nomina $nomina){
        //dd($request->all());
        $nomina->salud = '8.5';
        $nomina->pension = '12';
        $nomina->riesgos = '0.522';
        $nomina->sena = '2';
        $nomina->icbf = '3';
        $nomina->caja_compensacion = '4';
        $nomina->cesantias = '8.33';
        $nomina->interes_cesantias = 1;
        $nomina->prima_navidad = '8.33';
        $nomina->vacaciones = '4.17';
        $nomina->mes = $request->mes;
        $nomina->finalizado = $request->accion == 'finalizar' ? 1 : 0;
        $nomina->save();

        
        //dd($request->all());
        foreach($request->empleado_id as $k => $empleado):
            $nomina_empleado = NominaEmpleadoNomina::where('nomina_id', $nomina->id)->where('nomina_empleado_id', $empleado)->first();
            if(is_null($nomina_empleado)):
                $nomina_empleado = new  NominaEmpleadoNomina();
            endif;
            if($request->tipo == 'empleado'):
                $nomina_empleado->nomina_id = $nomina->id;
                $nomina_empleado->nomina_empleado_id = $empleado;
                $nomina_empleado->dias_laborados = $request->dias_laborados[$k];
                $nomina_empleado->horas_extras = $request->horas_extras[$k];
                $nomina_empleado->horas_extras_festivos = $request->horas_extras_festivos[$k];
                $nomina_empleado->horas_extras_nocturnas = $request->horas_extras_nocturnas[$k];
                $nomina_empleado->recargos_nocturnos = $request->recargos_nocturnos[$k];
                $nomina_empleado->sueldo = $request->sueldo[$k];
                $nomina_empleado->bonificacion_direccion = $request->bonificacion_direccion[$k];
                $nomina_empleado->bonificacion_servicios = $request->bonificacion_servicios[$k];
                $nomina_empleado->bonificacion_recreacion = $request->bonificacion_recreacion[$k];
                $nomina_empleado->prima_antiguedad = $request->prima_antiguedad[$k];
                $nomina_empleado->save();
            else:
                $nomina_empleado->nomina_id = $nomina->id;
                $nomina_empleado->nomina_empleado_id = $empleado;
                $nomina_empleado->sueldo = $request->sueldo[$k];
                $nomina_empleado->tiene_eps = $request["tiene_eps_{$k}"];
            endif;
            $nomina_empleado->save();

            if(isset($request["descuento_".$k])):
                $nomina_empleado->descuentos()->delete();
                foreach($request["descuento_".$k] as $y => $descuento):
                    $nomina_empleado->descuentos()->create([
                        'tercero_id' => $request["descuento_tercero_".$k][$y],
                        'nombre' => $descuento,
                        'valor' =>  $request["descuento_valor_".$k][$y]
                    ]);
                endforeach;
            endif;
        endforeach;

        if($request->accion == 'finalizar'):
           $nomina->data_desprendibles = $this->guardar_data_desprendible($nomina);
           $nomina->save();
            Session::flash('success', 'se ha finalizado la nomina.');
            return redirect()->route('nomina.show', $nomina->id);
        else:
            Session::flash('success', 'se ha guardado los cambios.');
            return redirect()->route('nomina.edit', $nomina->id);
        endif;
    }

    public function guardar_data_desprendible($nomina){
        $estructura = '';
        foreach($nomina->empleados_nominas->chunk(2) as  $chunk):
            foreach($chunk as  $movimiento):
            $estructura .= '    
                <div style="width:50%">
                    <table class="table">
                        <tr>
                            <td>
                                <b>FECHA INGRESO</b>
                            </td>
                            <td>
                                1-'.$nomina->mes.'-'.date('Y').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>SUELDO BASICO</b>
                            </td>
                            <td>
                                $'.number_format($movimiento->salario, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>DIAS LABORADOS</b>
                            </td>
                            <td>
                                '.$movimiento->dias_laborados.'
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>DEVENGADO</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Sueldo Básico
                            </td>
                            <td>
                                '.number_format($movimiento->v_dias_laborados, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Retroactivo
                            </td>
                            <td>
                                '.number_format($movimiento->retroactivo, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                H. Extras
                            </td>
                            <td>
                            '.number_format($movimiento->v_horas_extras, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                H. Extras Festivos
                            </td>
                            <td>
                                '.number_format($movimiento->v_horas_extras_festivos, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                H. Extras Nocturnas
                            </td>
                            <td>
                                '.number_format($movimiento->v_horas_extras_nocturnas, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Recargo Nocturno
                            </td>
                            <td>
                                '.number_format($movimiento->v_recargo_nocturno, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bonificación Dirección
                            </td>
                            <td>
                                '.number_format($movimiento->bonificacion_direccion, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bonificación Servicios
                            </td>
                            <td>
                                '.number_format($movimiento->v_bonificacion_servicios, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bonificación Recreación
                            </td>
                            <td>
                                '.number_format($movimiento->v_bonificacion_recreacion, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Prima Antiguedad
                            </td>
                            <td>
                                '.number_format($movimiento->v_prima_antiguedad, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Vacaciones
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Indemnizacion por Vacaciones
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bonificación especial por recreación
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Prima Servicios
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Prima Navidad
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Prima Vacaciones
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Prima Tecnica Salarial
                            </td>
                            <td>
                                $0
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>TOTAL DEVENGADO</b>
                            </td>
                            <td>
                                '.number_format($movimiento->total_devengado, 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>DESCUENTOS</b>
                            </td>
                        
                        </tr>';

                        foreach($movimiento->descuentos as $descuento):
                            $estructura .= '
                            <tr>
                                <td>
                                    '.$descuento->tercero->nombre.'
                                </td>
                                <td>
                                    '.number_format($descuento->valor, 0, ',', '.').'
                                </td>
                            </tr>';
                        endforeach;
                        $estructura .= '
                        <tr>
                            <td>
                                TOTAL DESCUENTOS
                            </td>
                            <td>
                                '.number_format($movimiento->descuentos->sum('valor'), 0, ',', '.').'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>NETO A PAGAR</b>
                            </td>
                            <td>
                                '.number_format($movimiento->neto_pagar, 0, ',', '.').'
                            </td>
                        </tr>
                    </table>

                    <table class="table">
                        <tr>
                            <td>CUENTA DE AHORROS</td>
                            <td>'.$movimiento->empleado->numero_cuenta_bancaria.'</td>
                        </tr>
                        <tr>
                            <td>ENTIDAD BANCARIA</td>
                            <td>'.$movimiento->empleado->banco_cuenta_bancaria.'</td>
                        </tr>
                    </table>
                </div>';
            endforeach;
        endforeach;
        return $estructura;
    }

    public function pdf_desprendibles(Nomina $nomina){
        set_time_limit(0);
        ini_set("memory_limit",-1);
        ini_set('max_execution_time', 0);
        $pdf = PDF::loadView('nomina.pdf-desprendibles', compact('nomina'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function pdf_nomina(Nomina $nomina){
        $pdf = PDF::loadView('nomina.pdf', compact('nomina'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function pdf_contabilidad_presupuestal(){
        $nomina_empleados = Nomina::find(19);
        $nomina_pensionados = Nomina::find(34);
        $pdf = PDF::loadView('nomina.pdf-contabilidad', compact('nomina_empleados', 'nomina_pensionados'))->setPaper('a4', 'landscape')->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function cuentas_bancarias_usuarios(Nomina $nomina){
        return view('nomina.cuentas_bancarias', compact('nomina'));
    }
}
