@extends('layouts.dashboard')
@section('titulo') {{ $rubroPUC->concepto}} - PAGO DE DESCUENTOS @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Pago de Descuentos Cuenta Bancaria: {{ $rubroPUC->code}} - {{ $rubroPUC->concepto}} </b></h4>
            <h4><b>Periodo a Pagar: {{ $fecha}} </b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link regresar"  href="{{url('administrativo/tesoreria/descuentos/'.$vigencia) }}">Volver a Descuentos</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">{{ $rubroPUC->code}} - {{ $rubroPUC->concepto}}</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <div class="table-responsive">
                <form class="form-valide" action="{{url('/administrativo/tesoreria/descuentos/store')}}" method="POST" enctype="multipart/form-data" id="prog">
                    {{ csrf_field() }}
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <input type="hidden" id="vigencia_id" name="vigencia_id" value="{{$vigencia}}">
                    <input type="hidden" id="mes" name="mes" value="{{$mes}}">
                    <div class="col-md-12 align-self-center">
                        Seleccione el tercero.
                        <select class="select-tercero" name="persona_id" style="width: 100%">
                            @foreach($personas as $persona)
                                <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <br><br><br>
                    <table class="table table-bordered" id="tablaData">
                        <thead>
                        <tr>
                            <th class="text-center">CONCEPTO</th>
                            <th class="text-center">DEBITO</th>
                            <th class="text-center">CREDITO</th>
                        </tr>
                        </thead>
                        <tbody id="prog">
                        @foreach($form as $index => $pago)
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="conceptoCuenta[]" value="{{ $pago['concepto'] }}">
                                    {{ $pago['concepto'] }}
                                </td>
                                <td></td>
                                <td class="text-center">
                                    <input type="hidden" name="credCuenta[]" value="{{ $pago['valorDesc'] }}">
                                    $ <?php echo number_format($pago['valorDesc'],0);?>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="text-center">
                                <label class="col-form-label" for="nombre">Cuenta Bancaria Credito <span class="text-danger">*</span></label>
                                <select class="select-cuenta-cred" name="cuentaCred" id="cuentaCred">
                                    @foreach($hijosDebito as $hijo)
                                        <option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td></td>
                            <td>
                                <input type="hidden" name="totPago" id="totPago" value="{{ $totalPago }}">
                                $ <?php echo number_format($totalPago,0);?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary" v-on:click.prevent="nuevaFilaBanco"><i class="fa fa-plus-circle"></i></button>
                                <label class="col-form-label" for="nombre">Cuenta Bancaria <span class="text-danger">*</span></label>
                                <select class="select-cuenta-deb" name="cuentaDeb[]" id="cuentaDeb[]">
                                    @foreach($hijosDebito as $hijo)
                                        <option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center"><input class="form-control" type="number" name="debCuenta[]" id="debCuenta[]" min="1" value="1"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary" v-on:click.prevent="nuevaFilaRubro"><i class="fa fa-plus-circle"></i></button>
                                <label class="col-form-label" for="nombre">Seleccione Rubro Ingresos <span class="text-danger">*</span></label>
                                <select class="form-control" name="rubroIngresos[]" id="rubroIngresos[]">
                                    @foreach($rubrosIngresos as $rubro)
                                        <option value="{{$rubro['id']}}">{{$rubro['code']}} - {{$rubro['nombre']}} - {{$rubro['fCode']}}  - {{$rubro['fName']}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center"><input class="form-control" type="number" name="debRub[]" id="debRub[]" min="1" value="1"></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <center>
                        <button id="buttonMake" class="btn-sm btn-primary">Almacenar Pago de Descuentos</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')

    <script type="text/javascript" >

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("prog").addEventListener('submit', validarFormulario);
        });

        function validarFormulario(evento) {
            evento.preventDefault();
            const pagoDeb= document.querySelectorAll('input[name="debCuenta[]"]');
            var pagoTotal = document.getElementById('totPago').value;
            pagoTotal = parseInt(pagoTotal);

            let valores = [0]
            pagoDeb.forEach((elemento) => {
                valores.push(parseInt(elemento.value));
            });

            let total = valores.reduce((a, b) => a + b, 0);

            if(total < pagoTotal || total > pagoTotal) {
                alert('Debe tener un pago en debito igual a '+pagoTotal+' actualmente esta el total en '+total);
                return;
            }
            //this.submit();
        }

        $(document).ready(function(){
            $('.nav-tabs a[href="#tabTareas"]').tab('show');

            $('.select-tercero').select2();
            $('.select-cuenta-cred').select2();
            $('.select-cuenta-deb').select2();

        });


        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        new Vue({
            el: '#prog',

            methods:{

                nuevaFilaBanco: function(){
                    var nivel=parseInt($("#tablaData tr").length);
                    $('#tablaData tbody tr:last').before('<tr>'+
                        '<td class="text-center">\n' +
                        '<button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button>\n' +
                        '<label class="col-form-label" for="nombre">Cuenta Bancaria <span class="text-danger">*</span></label>\n' +
                        '<select class="select-cuenta-deb" name="cuentaDeb[]" id="cuentaDeb[]">\n' +
                        '@foreach($hijosDebito as $hijo)<option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>@endforeach\n' +
                        '</select>\n' +
                        '</td>\n' +
                        '<td class="text-center"><input class="form-control" type="number" name="debCuenta[]" id="debCuenta[]" min="1" value="1"></td>\n' +
                        '<td></td>\n' +
                        '</tr>');
                    $(document).ready(function(){ $('.select-cuenta-deb').select2(); });
                },

                nuevaFilaRubro: function(){
                    var nivel=parseInt($("#tablaData tr").length);
                    $('#tablaData tbody tr:last').before('<tr>'+
                        '<td class="text-center">\n' +
                        '<button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button>\n' +
                        '<label class="col-form-label" for="nombre">Seleccione Rubro Ingresos <span class="text-danger">*</span></label>\n' +
                        '<select class="form-control" name="rubroIngresos[]" id="rubroIngresos[]">\n' +
                        '@foreach($rubrosIngresos as $rubro)<option value="{{$rubro['id']}}">{{$rubro['code']}} - {{$rubro['nombre']}} - {{$rubro['fCode']}}  - {{$rubro['fName']}}</option>@endforeach\n' +
                        '</select>\n' +
                        '</td>\n' +
                        '<td class="text-center"><input class="form-control" type="number" name="debRub[]" id="debRub[]" min="1" value="1"></td>\n' +
                        '<td></td>\n' +
                        '</tr>');
                }
            }
        });

    </script>

@stop
