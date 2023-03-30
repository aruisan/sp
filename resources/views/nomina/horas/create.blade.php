
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
	<li class="nav-item">
		<a class="nav-link" href="{{route('nomina.empleados.index')}}"> Empleados</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina-horas.index')}}">Nominas horas Extras</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Crear Nomina de >Horas Extras</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3>
                <b>Nueva Nomina de Horas Extras</b>
                <select name="mes" id="select_mes" class="form_control">
                    @foreach($nominas as $nomina)
                        <option value="{{$nomina['id']}}">{{$nomina['mes']}}</option>
                    @endforeach
                </select>
                <b>{{date('Y')}}</b>
            </h3>

		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-12">
            <form id="formulario" class="form-horizontal" method="post" action="{{route('nomina-horas.store')}}">
                {{ csrf_field() }}
                <input name="nomina_id" id="input_mes" type="hidden">
                <input name="accion" id="input_accion" type="hidden">
                    <table class="table">
                        <thead>
                            <th>Tiene Horas Extras</th>
                            <th>Empleado</th>  
                        </thead>
                        <tbody id="div_empleados">
                        
                        </tbody>
                    </table>
                
                <br><br>
                <div class="row">
                    <button class="btn btn-sm btn-primary" type="button" onclick="guardar()">
                        guardar
                    </button>
                </div>
            </form>
        </div>
	</div>
</div>



@stop

@section('js')
    <script>
		let nominas = {!!$nominas!!};
        let contador_checks = 0;

        $(document).ready(function(){
            mes_seleccionado();
        })

        const pintar_empleados = id =>{
            let nomina = nominas.find(e => e.id == id);
            $('#div_empleados').empty();
            nomina.empleados.forEach((e,index) => {
                let form = `
                    <tr>  
                        <td>
                            <input type="checkbox" name="empleado_id[]" value="${e.id}" class="tiene_horas">
                        </td>
                        <td>
                            ${e.nombre}
                        </td>
                    </tr>
                `;
                $('#div_empleados').append(form);
            });
        }


        $('#select_mes').change(function(){
            mes_seleccionado()
        });

        const mes_seleccionado = () => {
            let id =  $('#select_mes').val();
            $('#input_mes').val(id);
            pintar_empleados(id);
        }


        $(document).on('click', '.tiene_horas', function() {
            if($(this).is(':checked'))
            {
                contador_checks++;
            }else{
                contador_checks--;
            }
        });

        const guardar = () => {
            
            if(contador_checks > 0){
                $('#formulario').submit();
            }else{
                alert('debe seleccionar un empleado para la nomina de vacaciones');
            }
            
        }

   </script>
@stop
