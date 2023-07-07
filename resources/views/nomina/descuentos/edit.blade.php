
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
		<a class="nav-link" href="{{route('nomina.'.$nomina->tipo.'s.index')}}"> {{ucfirst($nomina->tipo)}}s</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina-descuentos.index', $nomina->tipo)}}">Nominas</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Editar Nomina de Descuentos mes de {{ucfirst($nomina->mes)}} </a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div class="container-fluid">
        <div class="col-md-12">
            <div class="btn-group row" id="btn_anterior_siguiente">
            </div>
            <br><br>
            <form id="formulario" class="form-horizontal" method="post" action="{{route('nomina.update', $nomina->id)}}">
                {{ csrf_field() }}
                <input name="mes" id="input_mes" type="hidden">
                <input name="accion" id="input_accion" type="hidden">
                <input name="tipo" value="{{$nomina->tipo}}" type="hidden">
                <input name="count" value="{{$nomina->empleados_nominas->count()}}" type="hidden">
            </form>
        </div>
        <div class="col-md-3" style="display:none">
            <embed src="{{asset('file_public/AYUDAS DE NOMINA.pdf')}}" type="application/pdf" width="100%" height="600px" />
        </div>
	</div>
</div>



@stop

@section('js')
    <script>
        const terceros = {!!$terceros!!};
		let empleados = {!!$empleados!!};
        let nomina = {!!$nomina!!}
        let contador = 0;
        let options_terceros =  terceros.map(t => `<option value="${t.id}">${t.nombre}</option>`)
        let descuentos = [];

        $(document).ready(function(){
           pintar_empleados();
        })


        const pintar_empleados = () =>{
            console.log('empleados', empleados)
            empleados.forEach((e,index) => {

                let descuentos = e.descuentos.map(d => `
                    <tr>
                        <td>
                            <select name="descuento_tercero_${index}[]" class="form_control descuento_tercero_${index}">
                                ${terceros.map(t => `<option value="${t.id}" ${t.id == d.tercero_id ? 'selected' : ''}>${t.nombre}</option>`)}
                            </select>
                        </td>
                        <td>
                            <input name="descuento_${index}[]" value="${d.nombre}" class="form_control descuento descuento_${index}">
                        </td>
                        <td>
                            <input name="descuento_valor_${index}[]" value="${d.valor}" class="form_control descuento_valor_${index}" onchange="descuento_change(${index})">
                        </td>
                        <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                    </tr>
                `);

                

                console.log('descuentos', e.descuentos);
                let form = `
                {{--
                <div class="empleados" id="empleado_${index}">
                --}}
                <div class="empleados ${index > 0 ? 'ocultar' : ''} " id="empleado_${index}">
                        <div class="col-md-12">
                            <label>ID: ${parseInt(index)+1} -- ${e.id} -- ${e.datos.id}</label>
                        </div>
                        <div class="col-md-12">
                            <label>CC: ${e.datos.num_dc}</label>
                            <input name="empleado_${index}[id]" class="empleado_${index}" type="hidden" value="${e.datos.id}">
                        </div>
                        <div class="col-md-12">
                            <label>Nombre: ${e.datos.nombre}</label>
                        </div>
                        <div class="col-md-12">
                            <label>Cargo: ${e.datos.cargo}</label>
                        </div>
                        <div class="col-md-6">
                            <label>Sueldo: ${formatterPeso.format(e.salario)}</label>
                            <input name="empleado_${index}[sueldo]" class="empleado_${index}" type="hidden" value="${e.salario}" id="sueldo_${index}">
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
                                    ${descuentos}
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
            console.log('empleados.length', empleados.length);
            $('#btn_anterior_siguiente').empty();
            if(contador > 1){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary" onclick="paginar(${0})">Primero</button>`
                );
            }

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

            if(contador+2 < empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary" onclick="paginar(${empleados.length-1})">Ultimo</button>`
                );
            }

            $('#btn_anterior_siguiente').append(
                `<button type="button" class="btn btn-primary" onclick="formulario_submit('guardar')">Guardar</button>`
            );
            
            if(contador+1 == empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary" onclick="formulario_submit('finalizar')">Finalizar</button>`
                );
            }
        }

        const agregar_descuento = index =>{
            let item = `
                <tr>
                    <td>
                        <select name="descuento_tercero_${index}[]" class="form_control descuento_tercero_${index}">
                            ${options_terceros}
                        </select>
                    </td>
                    <td>
                        <input name="descuento_${index}[]" class="form_control descuento_${index}" required>
                    </td>
                    <td>
                        <input name="descuento_valor_${index}[]" class="form_control descuento_valor_${index}" onchange="descuento_change(${index})" required>
                    </td>
                    <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                </tr>
            `;

            $(`#descuentos_${index}`).append(item);
        }
        const formulario_submit = action => {
            let incompletos = validar_descuentos();
            if(!incompletos){
                let contador_final = 0;
                empleados.forEach((e,index) => {
                    let empleado = [];
                    let descuentos_terceros = [];
                    let descuentos = [];
                    let descuentos_valor = [];

                    $(`.empleado_${index}`).each(function(n){
                        empleado.push($(this).val());
                    });

                    $(`.descuento_tercero_${index}`).each(function(i){
                        console.log('terceros_'+index, $(this).val());
                        descuentos_terceros.push($(this).val());
                    });

                    $(`.descuento_${index}`).each(function(k){
                        descuentos.push($(this).val());
                    });

                    $(`.descuento_valor_${index}`).each(function(v){
                        descuentos_valor.push($(this).val());
                    });

                    empleado.push(descuentos)
                    empleado.push(descuentos_terceros)
                    empleado.push(descuentos_valor)

                    $.post("{{route('nomina-descuentos.update', $nomina->id)}}", {_token: "{{ csrf_token() }}", data:empleado}, function(result){
                       contador_final +=1;
                        console.log(`empleado_${index}`, result);
                       if(empleados.length == contador_final){
                           /*
                        */
                        $('#input_accion').val(action);
                        $('#formulario').submit();
                       }
                    });
                });

                
            }
        }

        const paginar = pagina => {
            let incompletos = validar_descuentos();
            if(!incompletos){
                contador = pagina;
                $('.empleados').addClass('ocultar');
                $(`#empleado_${pagina}`).removeClass('ocultar');
                botones_paginacion();
            }
        }

        const validar_descuentos = () => {
            let descuentos_incompletos = 0;
            $(`.descuento_${contador} , .descuento_valor_${contador}`).each(function( index ) {
                if($(this).val() === ""){
                    descuentos_incompletos = descuentos_incompletos + 1;
                }
            });
            if(descuentos_incompletos){
                alert('los descuentos no pueden guardarse incompletos'); 
            }
            return descuentos_incompletos;
        }


        const formatterPeso = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        })

        const descuento_change = i => {
            let descuentos = 0;
            $(`.descuento_valor_${i}`).each(function( index ) {
                console.log('ddd', $(this).val())
                descuentos = descuentos + parseInt($(this).val());
            });
            console.log('desc', descuentos)
            sumatorias[i][9] = descuentos;
            console.log('rr', sumatorias[i])
            total(i);
        }


         $(document).on('click', '.borrar', function(event) {
             event.preventDefault();
            $(this).closest('tr').remove();
            descuento_change(contador);
        });


   </script>
@stop
