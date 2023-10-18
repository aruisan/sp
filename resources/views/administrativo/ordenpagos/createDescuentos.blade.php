@extends('layouts.dashboard')
@section('titulo')
    Creación de Descuentos
@stop
@section('sidebar')
    <li>
        <a href="{{ url('/administrativo/ordenPagos/'.$vigencia) }}" class="btn btn-success">
            <span class="hide-menu">Ordenes de Pago</span></a>
    </li>
    <br>
    <div class="card">
        <br>
        <center>
            <h4><b>Orden de Pago</b></h4>
            <table class="table text-center table-responsive">
                <tr>
                    <td>Valor Orden de Pago: $<?php echo number_format($ordenPago->valor - $ordenPago->iva,0) ?></td>
                    <td>Valor IVA Orden de Pago: $<?php echo number_format($ordenPago->iva,0) ?></td>
                    <td>Valor Total(+IVA): $<?php echo number_format($ordenPago->valor,0) ?></td>
                </tr>
            </table>
        </center>
        <br>
        <center>
            <h4><b>Valor Total de Descuentos</b></h4>
            $<?php echo number_format($ordenPago->descuentos->sum('valor'),0) ?>
        </center>
        <br>
    </div>
@stop
@section('content')
    <div class="col-md-12 align-self-center" id="crud" style="background-color: white">
        <div class="row justify-content-center">
            <br>
            <center><h2>Descuentos de: {{ $ordenPago->nombre }}</h2></center>
            <br>
            <div class="row">
                <div class="col-md-6 text-center">
                    @if($ordenPago->rad_cuenta_id != 0)
                        Radicacion de Cuenta: #{{ $ordenPago->radCuenta->code }} - {{ $ordenPago->radCuenta->registro->objeto }}
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
            @if($ordenPago->rad_cuenta_id != 0)
                <br>
                <div class="table-responsive">
                    <hr>
                    <h2 class="text-center">Descuentos Sugeridos de la Radicación de Cuenta</h2>
                    <br>
                    <table class="table table-bordered">
                        <thead>
                        <th class="text-center">Descuento</th>
                        <th class="text-center">Tarifa</th>
                        <th class="text-center">Valor</th>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td>Retención en la fuente DIAN</td>
                            <td>{{$ordenPago->radCuenta->pago->reteDIAN}}%</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->reteDIANValue,0) ?></td>
                        </tr>
                        <tr class="text-center">
                            <td>Estampilla adulto mayor</td>
                            <td>{{$ordenPago->radCuenta->pago->adulto}}%</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->adultoValue,0) ?></td>
                        </tr>
                        <tr class="text-center">
                            <td>Sobretasa deportiva</td>
                            <td>{{$ordenPago->radCuenta->pago->sobretasa}}%</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->sobretasaValue,0) ?></td>
                        </tr>
                        <tr class="text-center">
                            <td>Estampilla Justicia</td>
                            <td>{{$ordenPago->radCuenta->pago->estampilla}}%</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->estampillaValue,0) ?></td>
                        </tr>
                        <tr class="text-center">
                            <td>Industria y comercio ICA excepto Educación</td>
                            <td>{{$ordenPago->radCuenta->pago->ica}}x1000</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->icaValue,0) ?></td>
                        </tr>
                        <tr class="text-center">
                            <td>Contribución contrato de Obra pública</td>
                            <td>{{$ordenPago->radCuenta->pago->obraPub}}%</td>
                            <td>$<?php echo number_format($ordenPago->radCuenta->pago->obraPubValue,0) ?></td>
                        </tr>
                        @foreach($ordenPago->radCuenta->pago->descuentos as $descuento)
                            <tr class="text-center">
                                <td colspan="2">{{$descuento->type}}</td>
                                <td>$<?php echo number_format($descuento->valor,0) ?></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/ordenPagos/descuento')}}" method="POST" enctype="multipart/form-data">
                    <hr>
                    <center><h2>Descuentos Retención en la Fuente</h2></center>
                    <hr>
                    {{ csrf_field() }}
                    <input type="hidden" id="ordenPago_id" name="ordenPago_id" value="{{ $ordenPago->id }}">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla">
                            <thead>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Base</th>
                            <th class="text-center">Valor</th>
                            </thead>
                            <tbody>
                            @foreach($ordenPago->descuentos as $desc)
                                @if($desc->retencion_fuente_id != null)
                                    <tr>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="col-md-2 text-center">
                                                    <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarDescRF({{$desc->id}})" ><i class="fa fa-trash-o"></i></button>
                                                </div>
                                                <div class="col-md-10 text-left">
                                                    {{ $desc->nombre }}
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td class="text-center">{{ $desc->porcent }}</td>
                                        <td class="text-center">$<?php echo number_format($desc->base,0) ?></td>
                                        <td class="text-center">$<?php echo number_format($desc->valor,0) ?></td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <input type="hidden" name="id[]">
                                <td>
                                    <select class="form-control" id="reten" onchange="llenar()" name="retencion_fuente" required>
                                        <option>Selecciona un Concepto de Descuento</option>
                                        @foreach($retenF as $reten)
                                            <option value="{{$reten->id}}">{{$reten->concepto}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input class="form-control" type="number" id="valOP" name="valOP" style="text-align:center" onchange="valueLlenar(this.value)"
                                    value="{{ $ordenPago->valor }}"></td>
                                <td>
                                    <input class="form-control" type="number" id="percent" name="porcent" style="text-align:center" disabled>
                                </td>
                                <td><input class="form-control" type="number" id="base" name="base" style="text-align:center" disabled></td>
                                <td>
                                    <input class="form-control" type="number" id="valor" style="text-align:center" disabled>
                                    <input type="hidden" id="valor2" name="valor" value="">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <br>
                    <center><h2>Descuentos Municipales</h2></center>
                    <hr><br>
                    @if(count($reintegros) > 0)
                        <br>
                        <div class="alert alert-danger">
                            <center>
                                Se detectaron los siguientes reintegros pendientes por ejecución.
                                <br>
                                @foreach($reintegros as $reintegro)
                                    <input type="hidden" name="idReintegros[]" value="{{ $reintegro->id }}">
                                    {{ $reintegro->concepto }} - $<?php echo number_format($reintegro->val_total,0) ?> - {{ $reintegro->ff }}
                                    <br>
                                @endforeach
                                Deben ser realizados estos descuentos.
                            </center>
                        </div>
                        <br>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_desc_muni">
                            <thead>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Tarifa</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center"><i class="fa fa-trash-o"></i></th>
                            </thead>
                            <tbody>
                            @if(count($reintegros) > 0)
                                @foreach($reintegros as $reintegro)
                                    <tr style="background-color: #6c0e03; color: white">
                                        <td class="text-center" style="vertical-align: middle">Descuento Pendiente</td>
                                        <td class="text-center">
                                            Seleccione la cuenta del PUC <br>
                                            <select class="form-control" name="cuentaDesc[]">
                                                @foreach($cuentas24 as $cuenta)
                                                    @if($cuenta->padre_id == 653)
                                                        <option value="{{$cuenta->id}}">{{$cuenta->code}} - {{$cuenta->concepto}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            Tercero
                                            <select class="form-control" name="tercero[]">
                                                @foreach($personas as $persona)
                                                    @if($ordenPago->registros->persona_id == $persona->id)
                                                        <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            Valor a descontar<br>
                                            <input type="number" class="form-control" name="valorDesc[]" max="{{$reintegro->val_total}}" min="{{$reintegro->val_total}}" value="{{$reintegro->val_total}}" required>
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                            @foreach($ordenPago->descuentos as $desc)
                                @if($desc->retencion_fuente_id == null)
                                    <tr class="text-center">
                                        <td colspan="2"><b>{{ $desc->nombre }}</b></td>
                                        <td><b>{{ $desc->porcent }}</b></td>
                                        <td><b>$<?php echo number_format($desc->valor,0) ?></b></td>
                                        <td>
                                            <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarDescM({{$desc->id}})" ><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @for($i=0;$i< count($desMun); $i++)
                                <tr>
                                    <input type="hidden" value="{{$desMun[$i]->id}}" name="idDes[]">
                                    <td class="text-center">{{ $desMun[$i]->id }}</td>
                                    <td class="text-center">{{ $desMun[$i]->concepto }}</td>
                                    <td class="text-center">{{ $desMun[$i]->tarifa }}%</td>
                                    @if($desMun[$i]->id == 5)
                                            <?php
                                            $valorMulti = $ordenPago->valor * $desMun[$i]->tarifa;
                                            $value = $valorMulti / 1000;
                                            ?>
                                    @else
                                            <?php
                                            $valorMulti = $ordenPago->valor * $desMun[$i]->tarifa;
                                            $value = $valorMulti / 100;
                                            ?>
                                    @endif

                                    <td class="text-center">
                                        $<?php echo number_format($value,0) ?>
                                        <input type="hidden" name="valorMuni[]" value="{{ $value }}">
                                    </td>
                                    <td class="text-center"><input type="button" class="borrar btn-sm btn-danger" value=" - " /></td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                        <div class="text-center" id="buttonAddActividad">
                            <button type="button" @click.prevent="nuevaFilaDescMuni" class="btn btn-sm btn-primary">AGREGAR DESCUENTO MUNICIPAL</button>
                        </div>
                    </div>
                    <center>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>

        var Data = {
            @foreach($retenF as $key => $data)
                @if($ordenPago->valor >= $data->base)
                    <?php
                        $valorM = $ordenPago->valor * $data->tarifa;
                        $val = $valorM / 100;
                    ?>
                    "{{$data->id}}":["{{$data->tarifa}}","{{$data->base}}","{{$val}}"],
                @else
                    "{{$data->id}}":["{{$data->tarifa}}","{{$data->base}}","0"],
                @endif
            @endforeach
        };

        function valueLlenar(valor){

            var percent = document.getElementById('percent').value;
            var valueMul = valor * percent;
            var tot = valueMul/100;

            document.getElementById('valor').value = tot;
            document.getElementById('valor2').value = tot;
        }

        function llenar(){
            var select = document.getElementById('reten');
            var opcion = select.value;

            document.getElementById('percent').value = Data[opcion][0];
            document.getElementById('base').value = Data[opcion][1];
            document.getElementById('valor').value = Data[opcion][2];
            document.getElementById('valor2').value = Data[opcion][2];
        }

        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": 3000,
                "extendedTimeOut": 0,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "tapToDismiss": true
            }

            $('#tabla').DataTable( {
                responsive: true,
                "searching": false,
                "oLanguage": {"sZeroRecords": "", "sEmptyTable": ""}
            } );

        });

        //funcion para borrar una celda
        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        new Vue({
            el: '#crud',
            created: function(){
                this.getDatos();
            },
            data:{
                datos: []
            },
            methods:{
                getDatos: function(){
                    var urlVigencia = '/administrativo/ordenPagos/descuento/'+{{ $ordenPago->id }};
                    axios.get(urlVigencia).then(response => {
                        this.datos = response.data;
                    });
                },

                eliminarDescRF: function(dato){
                    var urlVigencia = '/administrativo/ordenPagos/descuento/rf/'+dato;
                    axios.delete(urlVigencia).then(response => {
                        location.reload();
                    });
                },

                nuevaFilaDescMuni(){
                    $('#tabla_desc_muni tbody tr:last').after('<tr>\n' +
                        '<td></td>\n'+
                        '<td>Seleccione la cuenta del PUC <br>' +
                        '<select class="form-control" name="cuentaDesc[]">\n' +
                        '                                        @foreach($cuentas24 as $cuenta)\n' +
                        '                                            <option value="{{$cuenta->id}}">{{$cuenta->code}} - {{$cuenta->concepto}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select></td>\n'+
                        '<td>Seleccione el tercero' +
                        '<select class="form-control" name="tercero[]">\n' +
                        '                                        @foreach($personas as $persona)\n' +
                        '                                            <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select></td>\n'+
                        '<td>Valor a descontar<br><input type="number" class="form-control" name="valorDesc[]" min="1" value="1" required></td>\n'+
                        '<td style="vertical-align: middle" class="text-center" ><button type="button" class="borrar btn-sm btn-danger">&nbsp;-&nbsp; </button></td>\n'+
                        '</tr>\n');
                },

                eliminarDescM: function(dato){
                    toastr.warning('ELIMINANDO DESCUENTO MUNICIPAL.... ESPERE UN MOMENTO POR FAVOR.');
                    var urlVigencia = '/administrativo/ordenPagos/descuento/m/'+dato;
                    axios.delete(urlVigencia).then(response => {
                        location.reload();
                    });
                },

                nuevaFila: function(){

                    $('#tabla tr:last').after('<tr><input type="hidden" name="id[]"><td>\n' +
                        '                                    <select class="form-control" name="retencion_fuente">\n' +
                        '                                        @foreach($retenF as $reten)\n' +
                        '                                            <option value="{{$reten->id}}">{{$reten->concepto}} - {{$reten->tarifa}}%</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select>\n' +
                        '                                </td><td><input type="number" name="porcent[]"  required></td>\n' +
                        '                                <td><input type="number" name="base[]"  required></td>\n' +
                        '                                <td><input type="number" name="valor[]" style="text-align:center" disabled placeholder="Al guardar el descuento surge el valor"></td><td class="text-center"><input type="button" class="borrar btn-sm btn-danger" value=" - " /></td></tr>');
                }
            }
        });
    </script>
@stop
