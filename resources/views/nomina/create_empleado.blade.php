
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
		<a class="nav-link"  href="{{route('nomina.index', 'empleado')}}">Nominas</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Crear Nomina</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3>
                <b>Nueva Nomina</b>
                <select name="mes" id="select_mes" class="form_control">
                </select>
                <b>{{date('Y')}}</b>
            </h3>

		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
            <form id="formulario" class="form-inline" method="post" action="{{route('nomina.store')}}">
                {{ csrf_field() }}
                <input name="mes" id="input_mes" type="hidden">
                <input name="accion" id="input_accion" type="hidden">
                <input name="tipo" value="empleado" type="hidden">
            </form>
            <div class="btn-group" id="btn_anterior_siguiente">
            </div>
        </div>
	</div>
</div>



@stop

@section('js')
    <script>
        const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        const terceros = {!!$terceros!!};
		let empleados = {!!$empleados!!};
        let contador = 0;
        let options_terceros =  terceros.map(t => `<option value="${t.id}">${t.nombre}</option>`)


        $(document).ready(function(){
           pintar_empleados();
           pintar_meses({{date('m')}});
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
                                <input name="dias_laborados[]" class="form-control" type="integer" value="30">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Horas Extras:</label>
                                <input name="horas_extras[]" class="form-control" type="integer" value="0">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Recargos Nocturnos:</label>
                                <input name="recargos_nocturnos[]" class="form-control" type="integer" value="0">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Bonificación Dirección:</label>
                                <input name="bonificacion_direccion[]" class="form-control" type="integer" value="0">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Bonificación Servicios:</label>
                                <input name="bonificacion_servicios[]" class="form-control" type="integer" value="0">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Bonificación Recreación:</label>
                                <input name="bonificacion_recreacion[]" class="form-control" type="integer" value="0">
                            </div>
                        </div>
                    </div>
                    <div  class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-4">Prima de Antiguedad:</label>
                                <input name="prima_antiguedad[]" class="form-control" type="integer" value="0">
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
                                <th>Tercero</th>
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
            }

            $('#btn_anterior_siguiente').append(
                `<button class="btn btn-primary" onclick="formulario_submit('guardar')">Guardar</button>`
            );
            
            if(contador+1 == empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button class="btn btn-primary" onclick="formulario_submit('finalizar')">Finalizar</button>`
                );
            }
        }

        const agregar_descuento = index =>{
            let item = `
                <tr>
                    <td>
                        <select name="descuento_tercero_${index}[]" class="form_control">
                            ${options_terceros}
                        </select>
                    </td>
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

        $('#select_mes').change(function(){
            mes_seleccionado()
        });

        const mes_seleccionado = () => {
            let index =  $('#select_mes').val();
            $('#input_mes').val(meses[index]);
        }

        const pintar_meses = () => {
            $('#select_mes').empty();
            meses.forEach((e,i) => {
                let item = `<option value="${i}">${e}</option>`;
                $('#select_mes').append(item);
            });
            mes_seleccionado();
        }

        const formulario_submit = action => {
            $('#input_accion').val(action);
            $('#formulario').submit();
            
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
