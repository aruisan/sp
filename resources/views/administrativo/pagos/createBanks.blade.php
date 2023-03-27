@extends('layouts.dashboard')
@section('titulo')
    Bancos
@stop
@section('sidebar')
    <li>
        <a href="{{ url('/administrativo/pagos/'.$vigencia_id) }}" class="btn btn-success">
            <span class="hide-menu">Pagos</span></a>
    </li>
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="row justify-content-center">
            <br>
            <center><h2>Bancos</h2></center>
            <br>
            <div class="row">
                <div class="col-md-4 text-center">
                    Orden de Pago: {{ $pago->orden_pago->nombre }}
                </div>
                <div class="col-md-4 text-center">
                    <b>Monto a Pagar:
                        <input type="hidden" id="montoPago" value="{{$pago->valor}}">
                        <span id="montoPagoSpan">$<?php echo number_format($pago->valor,0) ?></span></b>
                </div>
                <div class="col-md-4 text-center">
                    @if(isset($pago->orden_pago->registros))
                        Tercero: {{ $pago->orden_pago->registros->persona->nombre }}
                    @else
                        Tercero: DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN
                    @endif
                </div>
            </div>
            <br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/pagos/banks/store')}}" method="POST" enctype="multipart/form-data">
                    <hr>
                    {!! method_field('PUT') !!}
                    {{ csrf_field() }}

                    @if(isset($pago->orden_pago->registros))
                        <center><h2>Descuentos Municipales</h2></center>
                        <hr><br>
                        <div class="table-responsive" id="crud">
                            <table class="table table-bordered" id="tabla_desc_muni">
                                <thead>
                                <th class="text-center">Codigo</th>
                                <th class="text-center">Descripcion</th>
                                <th class="text-center">Base</th>
                                <th class="text-center">%</th>
                                <th class="text-center">Valor</th>
                                </thead>
                                @foreach($pago->orden_pago->descuentos as $descuento)
                                    <tr>
                                        @if($descuento->desc_municipal_id != null)
                                            <td>{{ $descuento->descuento_mun['codigo'] }}</td>
                                            <td>{{ $descuento->descuento_mun['concepto'] }}</td>
                                            <td>$ <?php echo number_format($pago->orden_pago->valor - $pago->orden_pago->iva,0);?></td>
                                            @if($descuento->descuento_mun['id'] == 5)
                                                <td>7 X 1000</td>
                                            @else
                                                <td>{{ $descuento->descuento_mun['tarifa'] }}</td>
                                            @endif
                                        @elseif($descuento->retencion_fuente_id != null)
                                            <td>{{ $descuento->descuento_retencion->codigo}}</td>
                                            <td>{{ $descuento->descuento_retencion->concepto }}</td>
                                            <td>$ <?php echo number_format($pago->orden_pago->valor - $pago->orden_pago->iva,0);?></td>
                                            <td>{{ $descuento->descuento_retencion->tarifa }}</td>
                                        @else
                                            <td>{{ $descuento->puc->code}}</td>
                                            <td>{{ $descuento->puc->concepto}}</td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                        <td>$ <?php echo number_format($descuento['valor'],0);?></td>
                                    </tr>
                                @endforeach
                                <tbody>
                                </tbody>
                            </table>
                            <div class="text-center" id="buttonAddActividad">
                                <button type="button" @click.prevent="nuevaFilaDescMuni" class="btn btn-sm btn-primary">AGREGAR DESCUENTO MUNICIPAL</button>
                            </div>
                            <br>
                        </div>
                    @endif

                    <input type="hidden" name="ordenPago_id" value="{{ $pago->orden_pago->id }}">
                    <input type="hidden" name="pago_id" value="{{ $pago->id }}">

                    <div>
                        <label>Adulto Mayor: </label>
                        <div class="input-group text-center">
                            <select class="select-tercero" name="adultoMayor">
                                <option value="0">NO APLICA</option>
                                @foreach($personas as $persona)
                                    <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <select class="form-control" id="form_pay" name="type_pay" onchange="var date= document.getElementById('fecha'); var cheque = document.getElementById('cheque'); var tarjeta = document.getElementById('tarjeta'); var bank = document.getElementById('table_bank'); if(this.value=='1'){ fecha.style.display='inline'; cheque.style.display='inline'; bank.style.display='inline'; tarjeta.style.display='none';}else if(this.value=='2'){ fecha.style.display='inline'; cheque.style.display='none'; bank.style.display='inline'; tarjeta.style.display='inline';}else{fecha.style.display='none'; bank.style.display='none'; cheque.style.display='none'; tarjeta.style.display='none'; }">
                                <option>Selecciona Forma de Pago</option>
                                <option value="1">Cheque</option>
                                <option value="2">Transferencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center" id="fecha" style="display: none">
                        <div class="form-group">
                            <label class="control-label text-right col-md-4" for="formadepago">Fecha:</label>
                            <div class="col-lg-6">
                                <input type="date" disabled class="form-control" name="ff" style="text-align:center" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center" id="cheque" style="display: none">
                        <div class="form-group">
                            <label class="control-label text-right col-md-4" for="formadepago">Número de Cheque:</label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="num_cheque" style="text-align:center">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center" id="tarjeta" style="display: none">
                        <div class="form-group">
                            <label class="control-label text-right col-md-4" for="formadepago">Número de Cuenta:</label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="num_cuenta" style="text-align:center">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive" id="table_bank" style="display: none">
                        <br>
                        <table class="table table-bordered" id="banks">
                            <thead>
                            <tr>
                                <th class="text-center">Banco</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center"><i class="fa fa-trash-o"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tr>
                                <td>
                                    <select class="form-control" name="banco[]" required>
                                        @foreach($cuentasBanc as $hijo)
                                            <option @if($hijo->hijo == 0) disabled @endif value="{{ $hijo->id }}">{{ $hijo->code }} - {{ $hijo->concepto }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" required class="form-control" id="val[]" name="val[]" min="0" style="text-align:center"
                                    value="{{$pago->valor}}">
                                </td>
                                <td></td>
                            </tr>
                        </table>
                        <br>
                        <input type="text" class="form-control" name="referenciaPago" id="referenciaPago" placeholder="REFERENCIA DEL PAGO">
                        <br><br>
                        <center>
                            <button type="button" v-on:click.prevent="nuevoBanco" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp; Agregar Otro Banco</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-usd"></i><i class="fa fa-arrow-right"></i>&nbsp; &nbsp; Pagar</button>
                        </center>
                        <br>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">

        $('.select-tercero').select2();

        $('#tabla_Pago').DataTable( {
            responsive: true,
            "searching": false
        } );

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function valueDesc(value){
            var valuePay = document.getElementById('montoPago').value;
            document.getElementById('montoPagoSpan').innerHTML = formatter.format(valuePay - value);
            document.getElementById('val[]').value = valuePay - value;
        }

        $(document).ready(function() {
            $('#button').click( function () {
                table.row('.selected').remove().draw( false );
            } );

            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });
        } );

        new Vue({
            el: '#table_bank',

            methods:{

                nuevoBanco: function(){
                    var nivel=parseInt($("#banks tr").length);
                    $('#banks tbody tr:last').after('<tr><td>\n' +
                        '                                <select class="form-control" name="banco[]" required>\n' +
                        '                                    @foreach($cuentasBanc as $hijo)\n' +
                        '                                            <option @if($hijo->hijo == 0) disabled @endif value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                </select>\n' +
                        '                            </td>\n' +
                        '                            <td>\n' +
                        '                                <input type="number" required class="form-control" name="val[]" min="0" style="text-align:center">\n' +
                        '                            </td>\n' +
                        '                            <td class="text-center"><button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button></td></tr>');

                }
            }
        });

        new Vue({
            el: '#crud',

            methods:{

                nuevaFilaDescMuni(){
                    $('#tabla_desc_muni tbody tr:last').after('<tr>\n' +
                        '<td colspan ="2">Seleccione la cuenta del PUC <br>' +
                        '<select class="form-control" name="cuentaDesc[]">\n' +
                        '                                        @foreach($cuentas as $cuenta)\n' +
                        '                                            <option value="{{$cuenta->id}}">{{$cuenta->code}} - {{$cuenta->concepto}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select></td>\n'+
                        '<td>Seleccione el tercero' +
                        '<select class="form-control" name="tercero[]">\n' +
                        '                                        @foreach($personas as $persona)\n' +
                        '                                            <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select></td>\n'+
                        '<td>Valor a descontar<br><input type="number" class="form-control" name="valorDesc[]" min="1" value="1" required onchange="valueDesc(this.value)"></td>\n'+
                        '<td style="vertical-align: middle" class="text-center" ><button type="button" class="borrar btn-sm btn-danger">&nbsp;-&nbsp; </button></td>\n'+
                        '</tr>\n');
                },

            }
        });
    </script>
@stop
