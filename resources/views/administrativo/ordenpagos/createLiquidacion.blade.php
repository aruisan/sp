@extends('layouts.dashboard')
@section('titulo')
    Orden de Pago
@stop
@section('sidebar')
    <li>
        <a href="{{ url('/administrativo/ordenPagos/'.$vigencia) }}" class="btn btn-success">
            <span class="hide-menu">Orden de Pago</span></a>
    </li>
    <br>
    <div class="card">
        <br>
        @if($ordenPago->rad_cuenta_id != 0)
            <center>
                <h4><b>Estado Actual de la Radicación de Cuenta</b></h4>
                <br>
                Valor Radicación: $<?php echo number_format($ordenPago->radCuenta->valor_fin,0) ?>
                <br>
            </center>
        @else
            <center>
                <h4><b>Estado Actual del Registro</b></h4>
                <br>
                Valor Registro: $<?php echo number_format($ordenPago->registros->valor,0) ?>
                <br>
                Valor Total(+IVA): $<?php echo number_format($ordenPago->registros->val_total,0) ?>
                <br>
            </center>
        @endif
        <br>
        <center>
            <h4><b>Valor Total de Descuentos</b></h4>
            <br>
            $<?php echo number_format($ordenPago->descuentos->sum('valor'),0) ?>
        </center>
        <br>
        <center>
            <h4><b>Valor Orden de Pago - Descuentos</b></h4>
            <br>
            $<?php echo number_format($ordenPago->valor - $ordenPago->descuentos->sum('valor'),0) ?>
        </center>
        <br>
        <center>
            <h4><b>Valor Total Orden de Pago</b></h4>
            <br>
            $<?php echo number_format($ordenPago->valor,0) ?>
        </center>
        <br>
    </div>
@stop
@section('content')
    <div class="col-md-12 align-self-center" id="crud">
        <div class="row justify-content-center">
            <br>
            <center><h2>Descuentos Orden de Pago: {{ $ordenPago->nombre }}</h2></center>
            <br>
            <div class="row">
                <div class="col-md-6 text-center">
                    @if($ordenPago->rad_cuenta_id != 0)
                        Registro Seleccionado: {{ $ordenPago->radCuenta->registro->objeto }}
                    @else
                        Registro Seleccionado: {{ $ordenPago->registros->objeto }}
                    @endif
                </div>
                <div class="col-md-6 text-center">
                    @if($ordenPago->rad_cuenta_id != 0)
                        Tercero: {{ $ordenPago->radCuenta->persona->nombre }}
                    @else
                        Tercero: {{ $ordenPago->registros->persona->nombre }}
                    @endif
                </div>
            </div>
            <br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/ordenPagos/liquidacion/store')}}" method="POST" enctype="multipart/form-data">
                    <hr>
                    {!! method_field('PUT') !!}
                    {{ csrf_field() }}
                    <input type="hidden" id="ordenPago_id" name="ordenPago_id" value="{{ $ordenPago->id }}">
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla">
                            <thead>
                            <th class="text-center">Codigo Cuenta</th>
                            <th class="text-center">Nombre Cuenta</th>
                            <th class="text-center">Debito</th>
                            <th class="text-center">Credito</th>
                            </thead>
                            <tbody>
                            @for($i=0;$i< count($ordenPagoDesc); $i++)
                                <tr>
                                    @if($ordenPagoDesc[$i]->retencion_fuente_id == null)
                                        @if($ordenPagoDesc[$i]->desc_municipal_id == null)
                                            <td class="text-center">
                                                {{ $ordenPagoDesc[$i]->puc->code }}
                                            </td>
                                        @else
                                            <td class="text-center">
                                                {{ $ordenPagoDesc[$i]->descuento_mun['codigo'] }}
                                            </td>
                                        @endif

                                    @else
                                        <td class="text-center">
                                            {{ $ordenPagoDesc[$i]->descuento_retencion->codigo }}
                                        </td>
                                    @endif
                                    @if($ordenPagoDesc[$i]->retencion_fuente_id == null)
                                            @if($ordenPagoDesc[$i]->desc_municipal_id == null)
                                                <td class="text-center">
                                                    {{ $ordenPagoDesc[$i]->puc->concepto }}
                                                </td>
                                            @else
                                                <td class="text-center">
                                                    {{ $ordenPagoDesc[$i]->descuento_mun['cuenta'] }}
                                                </td>
                                            @endif

                                    @else
                                        <td class="text-center">
                                            {{ $ordenPagoDesc[$i]->descuento_retencion->cuenta }}
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        <input class="form-control" type="text" value="$ 0" style="text-align:center" disabled>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-control" type="text" value="$<?php echo number_format($ordenPagoDesc[$i]->valor,0) ?>" style="text-align:center" disabled>
                                    </td>
                                </tr>
                            @endfor
                            @if($ordenPago->pucs != null)
                                @foreach($ordenPago->pucs as $pucs)
                                    <tr>
                                        <td class="text-center">
                                            <div class="col-md-12">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarPUC({{$pucs->id}})" ><i class="fa fa-trash-o"></i></button>
                                                </div>
                                                <div class="col-md-6">
                                                    <b> {{ $pucs->data_puc->code }}</b>
                                                </div>
                                            </div>

                                        </td>
                                        <td class="text-center"><b>{{ $pucs->data_puc->concepto }}</b></td>
                                        <td class="text-center"><b>$<?php echo number_format($pucs->valor_debito,0) ?></b></td>
                                        <td class="text-center"><b>$<?php echo number_format($pucs->valor_credito,0) ?></b></td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td colspan="2">
                                    <select class="form-control" id="PUC[]" name="PUC[]" required>
                                        <option>Selecciona un PUC</option>
                                        @foreach($hijosPUC as $hijo)
                                            <option value="{{ $hijo->id }}">{{ $hijo->code }} - {{ $hijo->concepto }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" style="text-align:center" name="valorPucD[]" id="valorPucD[]" value="0" min="0" required>
                                </td>
                                <td>
                                    <input class="form-control" type="number" style="text-align:center" name="valorPucC[]" id="valorPucC[]" value="0" min="0" required>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="col-md-12 align-self-center text-center">
                        <br>
                        <button type="submit" class="btn btn-success">Finalizar Orden de Pago</button>
                        <button type="button" v-on:click.prevent="nuevaFila" class="btn btn-primary"><i class="fa fa-plus"></i> &nbsp; Agregar PUC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });


        new Vue({
            el: '#crud',

            methods:{

                eliminarPUC: function(dato){
                    var urlVigencia = '/administrativo/ordenPagos/puc/delete/'+dato;
                    axios.delete(urlVigencia).then(response => {
                        location.reload();
                    });
                },

                nuevaFila: function(){

                    $('#tabla tr:last').after('<tr>\n' +
                        '                                <td class="text-center"><input type="button" class="borrar btn-sm btn-danger" value=" - " /></td>\n' +
                        '                                <td>\n' +
                        '                                    <select class="form-control" id="PUC[]" name="PUC[]" required>\n' +
                        '                                        <option>Selecciona un PUC</option>\n' +
                        '                                        @foreach($hijosPUC as $hijo)\n' +
                        '                                            <option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select>\n' +
                        '                                </td>\n' +
                        '                                <td>\n' +
                        '                                    <input class="form-control" type="number" style="text-align:center" name="valorPucD[]" id="valorPucD[]" value="0" min="0" required>\n' +
                        '                                </td>\n' +
                        '                                <td>\n' +
                        '                                    <input class="form-control" type="number" style="text-align:center" name="valorPucC[]" id="valorPucC[]" value="0" min="0" required>\n' +
                        '                                </td>\n' +
                        '                            </tr>');
                }
            }
        });

    </script>
@stop
