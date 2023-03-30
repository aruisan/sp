
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
		<a class="nav-link"  href="{{route('nomina-horas.index')}}">Nominas de Horas Extras</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Nomina de Horas Extras</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3>
                <b>Nueva Nomina de Horas Extras</b>
                <b>{{$nomina->mes}} - {{date('Y')}}</b>
            </h3>
		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-12">
            <form action="{{route('nomina-horas.update', $nomina->id)}}" method="post" id="formulario">
                {{ csrf_field() }}
                <input type="hidden" name="accion" id="input_action">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">Empleado</th>
                            <th rowspan="2">Sueldo</th>
                            <th colspan="2">Horas Extras</th>
                            <th colspan="2">Horas Extras Festivos</th>
                            <th colspan="2">Horas Extras Nocturnas</th>
                            <th colspan="2">Recargos Nocturnos</th>
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <th>Horas</th>
                            <th>Valor</th>
                            <th>Horas</th>
                            <th>Valor</th>
                            <th>Horas</th>
                            <th>Valor</th>
                            <th>Horas</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $k => $movimiento)
                            <tr>
                                <td>
                                    <input type="hidden" name="empleado_id[]" value="{{$movimiento->id}}">
                                    {{$movimiento->empleado->nombre}}
                                </td>
                                <td>
                                    ${{number_format($movimiento->sueldo, 0, ',', '.')}}
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        onchange="change_he(this,{{$movimiento->id}})" 
                                        name="horas_extras_{{$movimiento->id}}" 
                                        value="{{$movimiento->horas_extras}}"
                                    >
                                </td>
                                <td id="td_vhe_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_horas_extras, 0, ',', '.')}}
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        onchange="change_hf(this,{{$movimiento->id}})" 
                                        name="horas_extras_festivos_{{$movimiento->id}}" 
                                        value="{{$movimiento->horas_extras_festivos}}"
                                    >
                                </td>
                                <td id="td_vhf_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_horas_extras_festivos, 0, ',', '.')}}
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        onchange="change_hn(this,{{$movimiento->id}})" 
                                        name="horas_extras_nocturnas_{{$movimiento->id}}" 
                                        value="{{$movimiento->horas_extras_nocturnas}}"
                                    >
                                </td>
                                <td id="td_vhn_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_horas_extras_nocturnas, 0, ',', '.')}}
                                </td>
                                <td>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        onchange="change_rn(this,{{$movimiento->id}})" 
                                        name="recargos_nocturnos_{{$movimiento->id}}" 
                                        value="{{$movimiento->recargos_nocturnos}}"
                                    >
                                </td>
                                <td id="td_vrn_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_recargos_nocturnos, 0, ',', '.')}}
                                </td>
                                <td id="td_total_{{$movimiento->id}}">
                                    ${{number_format($movimiento->v_horas_extras_festivos + $movimiento->v_horas_extras +$movimiento->v_recargos_nocturnos +$movimiento->v_horas_extras_nocturnas, 0, ',', '.')}}
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
        const propiedades = ['vhe', 'vhf', 'vhn', 'vrn'];
        console.log(salarios);

        const formatterPeso = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })

        const change_he = (t,id) => {
            let index = salarios.findIndex(s => s.id ==id);
            let he = t.value;
            salarios[index].he = parseInt(he);
            salarios[index].vhe = Math.round(salarios[index].v_hora * 1.25 * salarios[index].he);
            total(index);
        }

        const change_hf = (t,id) => {
            let index = salarios.findIndex(s => s.id ==id);
            let hf = t.value;
            salarios[index].hf = parseInt(hf);
            salarios[index].vhf = Math.round(salarios[index].v_hora * 1.75 * salarios[index].hf);
            total(index);
        }

        const change_hn = (t,id) => {
            let index = salarios.findIndex(s => s.id ==id);
            let hn = t.value;
            salarios[index].hn = parseInt(hn);
            salarios[index].vhn = Math.round(salarios[index].v_hora * 1.35 * salarios[index].hn);
            total(index);
        }

        const change_rn = (t,id) => {
            let index = salarios.findIndex(s => s.id ==id);
            let rn = t.value;
            salarios[index].rn = parseInt(rn);
            salarios[index].vrn = Math.round(salarios[index].v_hora * 2 * salarios[index].rn);
            total(index);
        }

        const total = index => {
            let s = salarios[index];
            let total = 0;
            propiedades.forEach(e => {
                let valor = s[e];
                total = total + valor;
                $(`#td_${e}_${s.id}`).text(formatterPeso.format(valor));
            });

            $(`#td_total_${s.id}`).text(formatterPeso.format(total));
        }

        const guardar  = a => {
            $('#input_action').val(a);
            $('#formulario').submit();
        }


   </script>
@stop
