<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Nomina;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;
use App\NominaEmpleadoDescuentos;
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
        $nominas = Nomina::where('tipo', $tipo)->orderBy('id', 'desc')->get();
        return view("{$this->view}.index_{$tipo}", compact('nominas'));
    }

    public function create($tipo){
        $clon = Nomina::where('tipo', $tipo)->orderBy('id', 'desc')->first();

        if(is_null($clon)):
            $age_actual = date('Y');
            $empleados = NominaEmpleado::where('activo', True)->where('tipo', $tipo)->get();
            $terceros = Persona::where('tipo_tercero', 'especial')->get();
            $meses = Nomina::whereYear('created_at', $age_actual)->where('tipo', $tipo)->pluck('mes');
            
            return view("{$this->view}.create_{$tipo}", compact('empleados', 'terceros', 'meses'));
        else:
            $meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
            $mes_index = array_search($clon->mes, $meses);
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
                'tipo' => $clon->tipo,
                'mes' => $meses[$mes_index+1],
                'finalizado' =>  0
            ]);

            foreach($clon->empleados_nominas as $movimiento):
                //dd($clon->empleados_nominas->pluck('empleado.activo'));
                if($movimiento->empleado->activo > 0):
                    if($clon->tipo == 'empleado'):
                            $nomina_empleado = NominaEmpleadoNomina::create([
                                'nomina_id' => $nomina->id,
                                'nomina_empleado_id' => $movimiento->empleado->id,
                                'dias_laborados' => $movimiento->dias_laborados,
                                'horas_extras' => $movimiento->horas_extras,
                                'horas_extras_festivos' => $movimiento->horas_extras_festivos,
                                'horas_extras_nocturnas' => $movimiento->horas_extras_nocturnas,
                                'recargos_nocturnos' => $movimiento->recargos_nocturnos,
                                'sueldo' => $movimiento->empleado->salario,
                                'bonificacion_direccion' => $movimiento->bonificacion_direccion,
                                'bonificacion_servicios' => $movimiento->bonificacion_servicios,
                                'bonificacion_recreacion' => $movimiento->bonificacion_recreacion,
                                'prima_antiguedad' => $movimiento->prima_antiguedad
                            ]);
                    else:
                        
                            $nomina_empleado = NominaEmpleadoNomina::create([
                                'nomina_id' => $nomina->id,
                                'nomina_empleado_id' => $movimiento->empleado->id,
                                'sueldo' => $movimiento->empleado->salario,
                                'tiene_eps' => $movimiento->tiene_eps,
                            ]);
                    endif;

                    if($movimiento->empleado->descuentos->count() > 0):
                        foreach($movimiento->empleado->descuentos as $descuento):
                            $nomina_empleado->descuentos()->create([
                                'nombre' => $descuento->nombre,
                                'valor' =>  $descuento->valor,
                                'tercero_id' => $descuento->tercero_id,
                                'padre_id' => $descuento->id,
                                'n_cuotas' => 1,
                                'valor_total' => $descuento->valor,
                                ]);
                        endforeach;
                    endif;
                endif;
            endforeach;
            //dd($nomina->empleados_nominas->pluck('empleado.activo'));

                $ids_movimientos = $clon->empleados_nominas->pluck('nomina_empleado_id')->toArray();
                $empleados_faltantes = NominaEmpleado::where('tipo', $clon->tipo)->where('activo', 1)->get()->filter(function($n)use($ids_movimientos){ return !in_array($n->id, $ids_movimientos);  });

                foreach($empleados_faltantes as $faltante):
                    if($clon->tipo == 'empleado'):
                        $nomina_empleado = NominaEmpleadoNomina::create([
                            'nomina_id' => $nomina->id,
                            'nomina_empleado_id' => $faltante->id,
                            'dias_laborados' => 0,
                            'horas_extras' => 0,
                            'horas_extras_festivos' => 0,
                            'horas_extras_nocturnas' => 0,
                            'recargos_nocturnos' => 0,
                            'sueldo' => $faltante->salario,
                            'bonificacion_direccion' => 0,
                            'bonificacion_servicios' => 0,
                            'bonificacion_recreacion' => 0,
                            'prima_antiguedad' => 0
                        ]);
                    else:
                        $nomina_empleado = NominaEmpleadoNomina::create([
                            'nomina_id' => $nomina->id,
                            'nomina_empleado_id' => $faltante->id,
                            'sueldo' => $faltante->salario
                        ]);
                    endif;
                endforeach;

            return redirect()->route('nomina.edit', $nomina->id);
        endif;
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
            'vacaciones' => 0,
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
                        'valor' =>  $request["descuento_valor_".$k][$y],
                        'tercero_id' => $request["descuento_tercero_".$k][$y]
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
                'id' => $e->id,
                'salario' => !is_null($e->empleado->salario) ? $e->empleado->salario : 0,
                'datos' => $e->empleado,
                'movimiento' => [
                    'v_dias_laborados' => $e->v_dias_laborados, 
                    'v_horas_extras' => $e->v_horas_extras,
                    'v_horas_extras_festivos' => !is_null($e->v_horas_extras_festivos) ? $e->v_horas_extras_festivos : 0,
                    'v_horas_extras_nocturnas' => !is_null($e->v_horas_extras_nocturnas) ? $e->v_horas_extras_nocturnas : 0,
                    'v_recargos_nocturnos' =>  $e->v_recargos_nocturnos,
                    'retroactivo' =>  $e->retroactivo,
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
                'v_bonificacion_servicios' => $e->v_bonificacion_servicios,
                'prima' => $e->prima,
                'v_prima_antiguedad' => $e->v_prima_antiguedad,
                'v_vacaciones' => $e->v_vacaciones,
                'v_prima_vacaciones' => $e->v_prima_vacaciones,
                'v_prima_navidad' => $e->prima_navidad,
                'v_ind' => $e->v_ind,
                'total_devengado' => $e->total_devengado,
                'retroactivo' => $e->retroactivo,
                'ibc' => $e->v_ibc,
                'eps' => $e->empleado->eps,
                'salud' => $e->v_salud,
                'porc_salud' => $e->porc_salud,
                'fondo_pensiones' => $e->empleado->fondo_pensiones,
                'pension' => $e->v_pension,
                'fsp' => $e->fsp,
                'tarifa_retefuente' => $e->retencion_fuente > 0 ? '2.5%' : "0%",
                'retefuente' => $e->retencion_fuente,
                'descuentos' => $e->descuento_x_entidad,
                'reintegro' => $e->descuento_reintegro,
                'total_descuentos' => array_sum($e->descuento_x_entidad) + $e->descuento_reintegro,
                'total_deduccion' => $e->total_deduccion,
                'neto' => $e->neto_pagar,
                'tipo_cuenta_bancaria' => $e->empleado->tipo_cuenta_bancaria,
                'banco_cuenta_bancaria' => $e->empleado->banco_cuenta_bancaria,
                'numero_cuenta_bancaria' => $e->empleado->numero_cuenta_bancaria,
                'v_caja' => $e->v_caja_compensacion,     
                'tarifa_riesgos' => $e->empleado->porc_riesgos,    
                'v_riesgos' => $e->v_riesgos, 
                'v_sena' => $e->v_sena,   
                'v_icbf' => $e->v_icbf,  
                'v_esap' => $e->v_esap,  
                'v_men' => $e->v_men,  
            ];
        });

        //dd($movimientos);
        return view("{$this->view}.show", compact('nomina', 'movimientos'));
    }

    public function update_empleado(Request $request, Nomina $nomina){
            //return response()->json($request->all());
            $nomina_empleado = NominaEmpleadoNomina::where('nomina_id', $nomina->id)->where('nomina_empleado_id', $request->data[0])->first();
            $nomina_empleado->sueldo = $request->data[1];
            $nomina_empleado->dias_laborados = $request->data[2];
            $nomina_empleado->horas_extras = $request->data[3];
            $nomina_empleado->horas_extras_festivos = $request->data[4];
            $nomina_empleado->horas_extras_nocturnas = $request->data[5];
            $nomina_empleado->retroactivo = $request->data[6];
            $nomina_empleado->recargos_nocturnos = $request->data[7];
            $nomina_empleado->bonificacion_direccion = $request->data[8];
            $nomina_empleado->bonificacion_servicios = $request->data[9];
            $nomina_empleado->bonificacion_recreacion = $request->data[10];
            $nomina_empleado->prima_antiguedad = $request->data[11];
            $nomina_empleado->save();

            $nomina_empleado->descuentos()->delete();
            if(isset($request->data[12])):
                foreach($request->data[13] as $y => $tercero):
                    $new_desc = new NominaEmpleadoDescuentos;
                    $new_desc->nomina_empleado_nomina_id = $nomina_empleado->id;
                    $new_desc->tercero_id = intval($tercero);
                    $new_desc->nombre = $request->data[12][$y];
                    $new_desc->valor =  intval($request->data[14][$y]);
                    $new_desc->save();
                endforeach;
            endif;
        return response()->json(['entrada' => $request->all(), 'salida' => $nomina_empleado]);
    }

    public function update_pensionado(Request $request, Nomina $nomina){
        //return response()->json($request->data);
            //return response()->json([isset($request->data[2]), isset($request->data[2]) ? $request->data[3][0] : 'no tiene']);
            $nomina_empleado = NominaEmpleadoNomina::where('nomina_id', $nomina->id)->where('nomina_empleado_id', $request->data[0])->first();
        
            $nomina_empleado->sueldo = $request->data[1];
            $nomina_empleado->save();

            $nomina_empleado->descuentos()->delete();
            if(isset($request->data[2])):
                foreach($request->data[3] as $y => $tercero):
                    $new_desc = new NominaEmpleadoDescuentos;
                    $new_desc->nomina_empleado_nomina_id = $nomina_empleado->id;
                    $new_desc->tercero_id = intval($tercero);
                    $new_desc->nombre = $request->data[2][$y];
                    $new_desc->valor =  intval($request->data[4][$y]);
                    $new_desc->save();
                endforeach;
            endif;

            return response()->json(['entrada' => $request->all(), 'salida' => $nomina_empleado]);
    }

    public function update(Request $request, Nomina $nomina){
        $nomina->salud = '8.5';
        $nomina->pension = '12';
        $nomina->riesgos = '0.522';
        $nomina->sena = '2';
        $nomina->icbf = '3';
        $nomina->caja_compensacion = '4';
        $nomina->cesantias = '8.33';
        $nomina->interes_cesantias = 1;
        $nomina->prima_navidad = '8.33';
        $nomina->vacaciones = $nomina->vacaciones ? 1 : 0;
        $nomina->mes = $request->mes;
        $nomina->finalizado = $request->accion == 'finalizar' ? 1 : 0;
        $nomina->save();

        if($request->accion == 'finalizar'):
           $nomina->data_desprendibles = "";//$this->guardar_data_desprendible($nomina);
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
