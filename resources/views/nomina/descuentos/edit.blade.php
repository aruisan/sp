
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

        .m-2{
            margin:2px;
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
            
            <form id="formulario" class="form-horizontal" method="post" action="{{route('nomina-descuentos.update', $nomina->id)}}">
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
                let descuentos = e.descuentos.length == 0 ? [] 
                :e.descuentos.filter(d => d.is_padre || d.old).map(d => `
                    <tr>
                        <td width="280px">
                            <input value="${d.tercero.nombre}" class="form-control" readonly>
                        </td>
                        <td>
                            <input value="${d.nombre}" class="form-control" readonly>
                        </td>
                        <td>
                            <input value="${d.n_cuotas}" class="form-control" readonly>
                        </td>
                        <td>
                            <input value="${d.n_cuotas_faltantes}" class="form-control" readonly>
                        </td>
                        <td>
                            <input value="${d.valor}" class="form-control">
                        </td>
                    </tr>
                `);

                let descuentos_nuevos = e.descuentos.length == 0 ? [] 
                :e.descuentos.filter(d => !d.is_padre && !d.old).map(d => {
                    empleados[index].contador_descuentos +=1;
                    let contador = empleados[index].contador_descuentos;
                    return `
                    <tr>
                    <td width="280px">
                        <select name="descuento_tercero_${index}[]" class="form-control descuento_tercero_${index}">
                            ${terceros.map(t => `<option value="${t.id}" ${t.id == d.tercero_id ? 'selected' : ''}>${t.nombre}</option>`)}
                        </select>
                    </td>
                    <td>
                        <input value="${d.nombre}" name="descuento_${index}[]" class="form-control descuento_${index} descuento_${index}_${contador}" required>
                    </td>
                    <td>
                        <input value="${d.n_cuotas}" name="n_cuotas_${index}[]" class="form-control n_cuotas_${index} n_cuotas_${index}_${contador}" onchange="descuento_change(${index}, ${contador})" type="number" value="1" min="1" required>
                    </td>
                    <td>
                        <input  value="${d.n_cuotas_faltantes}" class="form-control n_cuotas_faltantes_${index}_${contador}" value="0" readonly>
                    </td>
                    <td>
                        <input  value="${d.valor}" class="form-control valor_pagar_${index} valor_pagar_${index}_${contador}" value="1" readonly>
                    </td>
                    <td><input type="button" class="borrar btn btn-danger" value="X" /></td></tr>
                `});
                 

                {{--<td><input type="button" class="borrar btn btn-danger" value="X" /></td>--}}

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
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>

                        <div class="panel panel-default">
                            <div class="panel-heading">Descuentos Anteriores</div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <th>Tercero</th>
                                        <th>Descuento</th>
                                        <th># cuotas</th>
                                        <th># cuotas Faltantes</th>
                                        <th>Valor a Pagar</th>
                                    </thead>
                                    <tbody>
                                        ${descuentos}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Descuentos Nuevos <button type="button" class="btn btn-primary" onclick="agregar_descuento(${index})">+</button></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <th>Tercero</th>
                                        <th>Descuento</th>
                                        <th># cuotas</th>
                                        <th># cuotas Faltantes</th>
                                        <th>Valor a Pagar</th>
                                    </thead>
                                    <tbody id="descuentos_${index}">
                                        ${descuentos_nuevos}
                                    </tbody>
                                </table>
                            </div>
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
                    `<button type="button" class="btn btn-primary m-2" onclick="paginar(${0})">Primero</button>`
                );
            }

            if(contador > 0){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary m-2" onclick="paginar(${parseInt(contador)-1})">Anterior</button>`
                );
            }

            if(contador+1 < empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary m-2" onclick="paginar(${parseInt(contador)+1})">Siguiente</button>`
                );
            }

            if(contador+2 < empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary m-2" onclick="paginar(${empleados.length-1})">Ultimo</button>`
                );
            }

            $('#btn_anterior_siguiente').append(
                `<button type="button" class="btn btn-primary m-2" onclick="formulario_submit('guardar')">Guardar</button>`
            );
            
            if(contador+1 == empleados.length){
                $('#btn_anterior_siguiente').append(
                    `<button type="button" class="btn btn-primary m-2" onclick="formulario_submit('finalizar')">Finalizar</button>`
                );
            }
        }

        const agregar_descuento = index =>{
            empleados[index].contador_descuentos +=1;
            let contador = empleados[index].contador_descuentos;
            let item = `
                <tr>
                    <td width="280px">
                        <select name="descuento_tercero_${index}[]" class="form-control descuento_tercero_${index} descuento_tercero_${index}_${contador}" style="width="100px">
                            ${options_terceros}
                        </select>
                    </td>
                    <td>
                        <input name="descuento_${index}[]" class="form-control descuento_${index} descuento_${index}_${contador}" required>
                    </td>
                    <td>
                        <input name="n_cuotas_${index}[]" class="form-control n_cuotas_${index} n_cuotas_${index}_${contador}" onchange="descuento_change(${index}, ${contador})" type="number" value="1" min="1" required>
                    </td>
                    <td>
                        <input  class="form-control n_cuotas_faltantes_${index}_${contador}" value="0" readonly>
                    </td>
                    <td>
                        <input  name="valor_pagar_${index}[]" class="form-control valor_pagar_${index} valor_pagar_${index}_${contador}" value="1">
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
                    let valor_pagar = [];
                    let cuotas = [];

                    $(`.empleado_${index}`).each(function(n){
                        empleado.push($(this).val());
                    });

                    $(`.descuento_tercero_${index}`).each(function(i){
                        //console.log('terceros_'+index, $(this).val());
                        descuentos_terceros.push($(this).val());
                    });

                    $(`.descuento_${index}`).each(function(k){
                        descuentos.push($(this).val());
                    });

                    $(`.valor_pagar_${index}`).each(function(v){
                        valor_pagar.push($(this).val());
                    });

                    $(`.n_cuotas_${index}`).each(function(v){
                        cuotas.push($(this).val());
                    });

                    empleado.push(descuentos)
                    empleado.push(descuentos_terceros)
                    empleado.push(valor_pagar)
                    empleado.push(cuotas)

                    $.post("{{route('nomina-descuentos.update.empleado', $nomina->id)}}", {_token: "{{ csrf_token() }}", data:empleado, input_accion:action}, function(result){
                       contador_final +=1;
                       console.log(`empleado_${index}`, result);
                       if(empleados.length == contador_final){
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

        const descuento_change = (i, c) => {
            let cuotas = parseInt($(`.n_cuotas_${i}_${c}`).val());
            let valor_total = parseInt($(`.descuento_valor_${i}_${c}`).val());

            let cuotas_faltantes = cuotas-1;
            let valor_cuota = Math.ceil(valor_total/cuotas);
            let saldo = valor_total - valor_cuota;

            console.log([cuotas, cuotas_faltantes, valor_total, valor_cuota, saldo]);

            $(`.n_cuotas_faltantes_${i}_${c}`).val(cuotas_faltantes);
            /*
            $(`.valor_pagar_${i}_${c}`).val(valor_cuota);
            $(`.saldo_${i}_${c}`).val(saldo);

            let descuentos = 0;
            $(`.descuento_valor_${i}`).each(function( index ) {
                console.log('ddd', $(this).val())
                descuentos = descuentos + parseInt($(this).val());
            });
            console.log('desc', descuentos)
            sumatorias[i][9] = descuentos;
            console.log('rr', sumatorias[i])
            total(i);
            */
        }

 


         $(document).on('click', '.borrar', function(event) {
             event.preventDefault();
            $(this).closest('tr').remove();
            descuento_change(contador);
        });


   </script>
@stop
