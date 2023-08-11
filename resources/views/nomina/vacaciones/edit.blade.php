
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Personas
@stop

@section('sidebar')
	{{-- <li><a href="{{route('personas.create')}}" class="btn btn-success">Nuevo Tercero</a></li> --}}
@stop

@section('css')
	<style>
        .ocultar{
            display:none;
        }
    </style>
@stop

@section('content')


<ul class="nav nav-pills">
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina-vacaciones.index')}}">Nominas de vacaciones</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Nomina de Vacaciones</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3>
                <b>Nueva Nomina de Vacaciones</b>
                <b>{{$nomina->mes}} - {{date('Y')}}</b>
            </h3>
		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-12">
            <form action="{{route('nomina-vacaciones.update', $nomina->id)}}" method="post" id="formulario">
                {{ csrf_field() }}
                <input type="hidden" name="accion" id="input_action">
                <table class="table">
                    <thead>
                        <th>tipo</th>
                        <th>Dias</th>
                        <th>Empleado</th>
                        <th>Sueldo</th>
                        <th>Vacaciones</th>
                        <th>Prima de Vacaciones</th>
                        <th>Indemnización</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $k => $movimiento)
                            <tr>
                                <td>
                                    <input type="hidden" name="empleado_id[]" value="{{$movimiento->id}}">
                                   {{$movimiento->ind_vac}}
                                </td>
                                <td>
                                    @if($movimiento->ind_vac == 'indemnizacion')
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            onchange="change_dia({{$movimiento->id}},'{{$movimiento->ind_vac}}')" 
                                            name="dias_vacaciones_laborados_{{$movimiento->id}}" 
                                            value="{{$movimiento->dias_vacaciones_laborados}}"
                                            id="dias_vacaciones_laborados_{{$movimiento->id}}"
                                        >
                                    @else
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        onchange="change_dia({{$movimiento->id}},'{{$movimiento->ind_vac}}')" 
                                        name="dias_vacaciones_{{$movimiento->id}}" 
                                        value="{{$movimiento->dias_vacaciones}}"
                                        id="dias_vacaciones_{{$movimiento->id}}"
                                    >
                                    @endif
                                </td>
                                <td>
                                    {{$movimiento->empleado->nombre}}
                                </td>
                                <td>
                                    ${{number_format($movimiento->sueldo, 0, ',', '.')}}
                                </td>
                                <td id="td_vac_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_vacaciones, 0, ',', '.')}}
                                </td>
                                <td id="td_pv_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_prima_vacaciones, 0, ',', '.')}}
                                </td>
                                <td id="td_ind_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_ind, 0, ',', '.')}}
                                </td>
                                <td id="td_total_{{$movimiento->id}}">
                                    ${{number_format($movimiento->total_vacaciones, 0, ',', '.')}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <button class="btn btn-primary" type="button" onclick="guardar('guardar')">
                        Guardar
                    </button>  
                    <button class="btn btn-primary" type="button" onclick="guardar('finalizar')">
                        Finalizar
                    </button>  
                </div>
            </form>
        </div>
	</div>
</div>

@stop

@section('js')
    <script>
        const salarios = {!!$salarios!!};

        const formatterPeso = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })

        const change_dia = (i, ind_vac) =>{
            let dias = ind_vac == 'vacaciones' ? $(`#dias_vacaciones_${i}`).val() : 0;
            let dias_l = ind_vac == 'indemnizacion' ? $(`#dias_vacaciones_laborados_${i}`).val() : 0;
            let salario = salarios[i];
            let v_dia = salario/30;


            let bs = salario <= 1901879 ? parseInt(salario) * 0.5 :  parseInt(salario) * 0.35;
            let ps = (salario+(bs/12));
            //let va = ((bs+ps)/2)/30
            let pv = (salario+(bs/12)+(ps/12))/30*15;
            let vac =  ((bs/12)+(ps/12))/30*dias;
            let ind = (salario+(ps/12)+(bs/12))/30*dias_l;
            let total = ind_vac == 'vacaciones' ? pv+vac :pv+ind;

            $(`#td_vac_${i}`).text(formatterPeso.format(vac));
            $(`#td_pv_${i}`).text(formatterPeso.format(pv));
            $(`#td_ind_${i}`).text(formatterPeso.format(ind));
            $(`#td_total_${i}`).text(formatterPeso.format(total));
        }
        const guardar  = a => {
            $('#input_action').val(a);
            $('#formulario').submit();
        }


   </script>
@stop
