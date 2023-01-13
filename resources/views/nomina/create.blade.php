
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
		<a class="nav-link" data-toggle="pill" href="{{route('nomina.empleados.index')}}"> Empleados</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina.index')}}">Nominas</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Crear Nomina</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3><b>Nueva Nomina</b></h3>
		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
            <form id="formulario" class="form-inline" method="post" action="{{route('nomina.store')}}">
                {{ csrf_field() }}
            </form>
            <div class="btn-group" id="btn_anterior_siguiente">
            </div>
        </div>
	</div>
</div>



@stop

@section('js')
    <script>
		let empleados = {!!$empleados!!};
        let contador = 0;

        $(document).ready(function(){
           pintar_empleados();
        })


        const pintar_empleados = () =>{
            console.log(empleados)
            empleados.forEach((e,index) => {
                let form = `
                <div class="empleados ${index > 0 ? 'ocultar' : ''}" id="empleado_${index}">
                    <div  class="row"> 
                        <div class="col-md-12">
                            <label>ID: ${parseInt(index)+1}</label>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12">
                            <label>CC: ${e.num_dc}</label>
                            <input name="empleado_id[]" type="hidden" value="${e.id}">
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12">
                            <label>Nombre: ${e.nombre}</label>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12">
                            <label>Cargo: ${e.cargo}</label>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12">
                            <label>Sueldo: ${e.salario}</label>
                            <input name="sueldo[]" type="hidden" value="${e.salario}">
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Dias laborados:</label>
                                <input name="dias_laborados[]" class="form-control" type="integer">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Horas Extras:</label>
                                <input name="horas_extras[]" class="form-control" type="integer">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Recargos Nocturnos:</label>
                                <input name="recargos_nocturnos[]" class="form-control" type="integer">
                            </div>
                        </div>
                    </div>
                    <br><br><br>
                    <h3 class="text-center">
                        Descuentos
                        <button type="button" class="btn btn-primary" onclick="agregar_descuento(${index})">+</button>
                    </h3>
                    <div  class="row"> 
                        <table class="table">
                            <thead>
                                <th>Descuento</th>
                                <th>Valor</th>
                            </thead>
                            <tbody id="descuentos_${index}">
                            </tbody>
                        </table>
                    </div>
                </div>
                `;
                $('#formulario').append(form);
            });

            botones_paginacion();
        }

        const botones_paginacion = () => {
            $('#btn_anterior_siguiente').empty();
             if(contador > 0){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary" onclick="paginar(${parseInt(contador)-1})">Anterior</button>`
                );
            }

            if(contador+1 < empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary" onclick="paginar(${parseInt(contador)+1})">Siguiente</button>`
                );
            }else{
                $('#btn_anterior_siguiente').append(
                    `<button class="btn btn-primary" onclick="formulario.submit()">Finalizar</button>`
                );
            }
        }

        const agregar_descuento = index =>{
            let item = `
                <tr>
                    <td>
                        <input name="descuento_${index}[]" class="form_control">
                    </td>
                    <td>
                        <input name="descuento_valor_${index}[]" class="form_control">
                    </td>
                </tr>
            `;

            $(`#descuentos_${index}`).append(item);
        }

        const paginar = pagina => {
            console.log('paginar', pagina)
            contador = pagina;
            $('.empleados').addClass('ocultar');
            $(`#empleado_${pagina}`).removeClass('ocultar');
            botones_paginacion();
        }
   </script>
@stop
