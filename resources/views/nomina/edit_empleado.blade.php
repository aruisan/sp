
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
		<a class="nav-link" href="{{route('nomina.'.$nomina->tipo.'s.index')}}"> {{$nomina->tipo}}s</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina.index', $nomina->tipo)}}">Nominas</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Editar Nomina</a>
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
        <div class="col-md-12">
            <form id="formulario" class="form-horizontal" method="post" action="{{route('nomina.update', $nomina->id)}}">
                {{ csrf_field() }}
                <input name="mes" id="input_mes" type="hidden">
                <input name="accion" id="input_accion" type="hidden">
                <input name="tipo" value="{{$nomina->tipo}}" type="hidden">
            </form>
            <div class="btn-group row" id="btn_anterior_siguiente">
            </div>
        </div>
        <div class="col-md-3" style="display:none">
            <embed src="{{asset('file_public/AYUDAS DE NOMINA.pdf')}}" type="application/pdf" width="100%" height="600px" />
        </div>
	</div>
</div>



@stop

@section('js')
    <script>
        const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        const terceros = {!!$terceros!!};
		let empleados = {!!$empleados!!};
        let nomina = {!!$nomina!!}
        let contador = 0;
        let options_terceros =  terceros.map(t => `<option value="${t.id}">${t.nombre}</option>`)
        const extras = [1.25, 1.75, 2, 2.5, 1.35]
        let sumatorias = [];
        let descuentos = [];

        $(document).ready(function(){
           pintar_empleados();
           pintar_meses({{date('m')}});
        })


        const pintar_empleados = () =>{
            empleados.forEach((e,index) => {
                sumatorias.push([
                    e.movimiento.v_dias_laborados,
                    e.movimiento.v_horas_extras,
                    e.movimiento.v_horas_extras_festivos,
                    e.movimiento.v_horas_extras_nocturnas,
                    e.movimiento.v_recargos_nocturnos,
                    e.movimiento.v_bonificacion_direccion,
                    e.movimiento.v_bonificacion_servicios,
                    e.movimiento.v_bonificacion_recreacion,
                    e.movimiento.v_prima_antiguedad,
                    e.total_descuentos
                ]);

                console.log('ll', sumatorias[index]);

                let descuentos = e.descuentos.map(d => `
                    <tr>
                        <td>
                            <select name="descuento_tercero_${index}[]" class="form_control">
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
                <div class="empleados ${index > 0 ? 'ocultar' : ''} " id="empleado_${index}">
                        <div class="col-md-12">
                            <label>ID: ${parseInt(index)+1}</label>
                        </div>
                        <div class="col-md-12">
                            <label>CC: ${e.datos.num_dc}</label>
                            <input name="empleado_id[]" type="hidden" value="${e.datos.id}">
                        </div>
                        <div class="col-md-12">
                            <label>Nombre: ${e.datos.nombre}</label>
                        </div>
                        <div class="col-md-12">
                            <label>Cargo: ${e.datos.cargo}</label>
                        </div>
                        <div class="col-md-6">
                            <label>Sueldo: ${formatterPeso.format(e.salario)}</label>
                            <input name="sueldo_${e.id}" type="hidden" value="${e.salario}" id="sueldo_${index}">
                        </div>
                         <div class="col-md-6">
                            <label id="v_total_${index}"></label>
                        </div>
                        @if($nomina->tipo == 'empleado')
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Dias laborados:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">#</div>
                                    <input name="dias_laborados_${e.id}" class="form-control" type="integer" value="${e.movimiento.dias_laborados}" onchange="dias_change(this, ${index})">
                                    <div class="input-group-addon" id="v_dias_laborados_${index}">${formatterPeso.format(e.movimiento.v_dias_laborados)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Horas Extras:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">#</div>
                                    <input name="horas_extras_${e.id}" class="form-control" type="integer" value="${e.movimiento.horas_extras}" onchange="horas_extras_change(this, ${index})">
                                    <div class="input-group-addon" id="v_horas_extras_${index}">${formatterPeso.format(e.movimiento.v_horas_extras)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Horas Extras Festivos:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">#</div>
                                    <input name="horas_extras_festivos_${e.id}" class="form-control" type="integer"  value="${e.movimiento.horas_extras_festivos}" onchange="horas_extras_festivos_change(this, ${index})">
                                    <div class="input-group-addon" id="v_horas_extras_festivos_${index}">${formatterPeso.format(e.movimiento.v_horas_extras_festivos)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Horas Extras Nocturnas:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">#</div>
                                    <input name="horas_extras_nocturnas_${e.id}" class="form-control" type="integer"  value="${e.movimiento.horas_extras_nocturnas}" onchange="horas_extras_nocturnas_change(this, ${index})">
                                    <div class="input-group-addon" id="v_horas_extras_nocturnas_${index}">${formatterPeso.format(e.movimiento.v_horas_extras_nocturnas)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Recargos Nocturnos:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">#</div>
                                    <input name="recargos_nocturnos_${e.id}" class="form-control" type="integer" value="${e.movimiento.recargos_nocturnos}" onchange="recargos_nocturnos_change(this, ${index})">
                                    <div class="input-group-addon" id="v_recargos_nocturnos_${index}">${formatterPeso.format(e.movimiento.v_recargos_nocturnos)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Bonificación Dirección:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">$</div>
                                    <input name="bonificacion_direccion_${e.id}" class="form-control" type="integer" value="${e.movimiento.bonificacion_direccion}" onchange="bonificacion_direccion_change(this, ${index})">
                                    <div class="input-group-addon" id="v_bonificacion_direccion_${index}">${formatterPeso.format(e.movimiento.bonificacion_direccion)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Bonificación Servicios:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">%</div>
                                    <input name="bonificacion_servicios_${e.id}" class="form-control" type="integer" value="${e.movimiento.bonificacion_servicios}" onchange="bonificacion_servicios_change(this, ${index})">
                                    <div class="input-group-addon" id="v_bonificacion_servicios_${index}">${formatterPeso.format(e.movimiento.v_bonificacion_servicios)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Bonificación Recreación:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">%</div>
                                    <input name="bonificacion_recreacion_${e.id}" class="form-control" type="integer" value="${e.movimiento.bonificacion_recreacion}" onchange="bonificacion_recreacion_change(this, ${index})">
                                    <div class="input-group-addon" id="v_bonificacion_recreacion_${index}">${formatterPeso.format(e.movimiento.v_bonificacion_recreacion)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label class="col-md-6">Prima Antiguedad:</label>
                                <div class="input-group col-md-6">
                                    <div class="input-group-addon">%</div>
                                    <input name="prima_antiguedad_${e.id}" class="form-control" type="integer" value="${e.movimiento.prima_antiguedad}" onchange="prima_antiguedad_change(this, ${index})">
                                    <div class="input-group-addon" id="v_prima_antiguedad_${index}">${formatterPeso.format(e.movimiento.v_prima_antiguedad)}</div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                //$(`#descuentos_${index}`).append(descuentos);
                $(`#v_dias_laborados_${index}`).html(formatterPeso.format(e.salario));
                total(index);
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
                        <select name="descuento_tercero_${index}[]" class="form_control">
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
                let item = `<option value="${i}" ${nomina.mes == e ? 'selected' : ''}>${e}</option>`;
                $('#select_mes').append(item);
            });
            mes_seleccionado();
        }

        const formulario_submit = action => {
            let incompletos = validar_descuentos();
            if(!incompletos){
                $('#input_accion').val(action);
                $('#formulario').submit();
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

        const dias_change = (e,i) =>{
            let valor_dia = parseInt(empleados[i].salario)/30;
            let pago = valor_dia *  parseInt(e.value);
            sumatorias[i][0] = pago;
            $(`#v_dias_laborados_${i}`).html(formatterPeso.format(pago));
            total(i);
        }

        const horas_extras_change = (e,i) =>{
            let valor_hora = parseInt(empleados[i].salario)/240;
            let pago = valor_hora * extras[0] * parseInt(e.value);
            sumatorias[i][1] = pago;
            $(`#v_horas_extras_${i}`).html(formatterPeso.format(pago));
            total(i);
        }

        const horas_extras_festivos_change = (e,i) =>{
            let valor_hora = parseInt(empleados[i].salario)/240;
            let pago = valor_hora * extras[1] * parseInt(e.value);
            sumatorias[i][2] = pago;
            $(`#v_horas_extras_festivos_${i}`).html(formatterPeso.format(pago));
            total(i);
        }

        const horas_extras_nocturnas_change = (e,i) =>{
            let valor_hora = parseInt(empleados[i].salario)/240;
            let pago = valor_hora * extras[2] * parseInt(e.value);
            sumatorias[i][3] = pago;
            $(`#v_horas_extras_nocturnas_${i}`).html(formatterPeso.format(pago));
            total(i);
        }

        const recargos_nocturnos_change = (e,i) =>{
            let valor_hora = parseInt(empleados[i].salario)/240;
            let pago = valor_hora * extras[4] * parseInt(e.value);
            sumatorias[i][4] = pago;
            $(`#v_recargos_nocturnos_${i}`).html(formatterPeso.format(pago));
            total(i);
        }


        const bonificacion_direccion_change = (e,i) =>{
            let pago = parseInt(e.value);
            sumatorias[i][5] = pago;
            total(i);
        }

         const bonificacion_servicios_change = (e,i) =>{
            let pago = parseInt(empleados[i].salario) * (parseInt(e.value)/100);
            sumatorias[i][6] = pago;
            $(`#v_bonificacion_servicios_${i}`).html(formatterPeso.format(pago));
            total(i);
        }


        const bonificacion_recreacion_change = (e,i) =>{
            let pago = parseInt(empleados[i].salario) * (parseInt(e.value)/100);
            sumatorias[i][7] = pago;
            $(`#v_bonificacion_recreacion_${i}`).html(formatterPeso.format(pago));
            total(i);
        }

        const prima_antiguedad_change = (e,i) =>{
            let pago = parseInt(empleados[i].salario) * (parseInt(e.value)/100);
            sumatorias[i][8] = pago;
            $(`#v_prima_antiguedad_${i}`).html(formatterPeso.format(pago));
            total(i);
        }


        const total  = i => {
            let total = 0;
            sumatorias[i].forEach((e,k)=> {
                total = sumatorias[i].length-1 == k ? total - e : total + e;
            });
            console.log('rr', sumatorias[i])
            $(`#v_total_${i}`).html(`Nomina: ${formatterPeso.format(total)}`);
        }

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
