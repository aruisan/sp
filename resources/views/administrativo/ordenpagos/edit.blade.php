@extends('layouts.dashboard')
@section('titulo')
    Editar Orden de Pago
@stop
@section('sidebar')
    <li>
        <a href="{{ url('/administrativo/ordenPagos/'.$vigenc) }}" class="btn btn-success">
            <span class="hide-menu">Ordenes de Pago</span></a>
    </li>
@stop
@section('content')
    <div class="col-md-12 align-self-center" id="crud">
        <div class="row justify-content-center">
            <br>
            <center><h2>{{ $ordenPago->nombre }}</h2></center>
            <br>
            <div class="row">
                <div class="col-md-4 text-center">
                    Registro Seleccionado: {{ $ordenPago->registros->objeto }}
                </div>
                <div class="col-md-4 text-center">
                    Saldo del Registro: $<?php echo number_format($ordenPago->registros->saldo,0) ?>
                </div>
                <div class="col-md-4 text-center">
                    Tercero: {{ $ordenPago->registros->persona->nombre }}
                </div>
            </div>
            <div class="form-validation">
                <form class="form" action="{{url('/administrativo/ordenPagos/'.$ordenPago->id)}}" method="POST">
                    <br>
                    <hr>
                    {!! method_field('PUT') !!}
                    {{ csrf_field() }}
                    <div class="col-md-12 align-self-center">
                        <label>Concepto: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="nombre" required value="{{ $ordenPago->nombre }}">
                        </div>
                        <small class="form-text text-muted">Nombre que se desee asignar a la orden de pago</small>
                    </div>
                    @if($ordenPago->estado == "0")
                        <div class="col-lg-6 ml-auto text-center"><button type="submit" class="btn btn-primary">Guardar</button></div>
                    @elseif($ordenPago->estado == "1")
                        <br><br>
                        <div class="col-md-12 align-self-center">
                            <hr>
                            <center>
                                <h3>Corregir Cuentas Contables de la Contabilización</h3>
                            </center>
                            <hr>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaP">
                                    <thead>
                                    <tr>
                                        @if($ordenPago->pucs->count() > 20)
                                            <th class="text-center"><i class="fa fa-trash"></i></th>
                                        @endif
                                        <th class="text-center">Cuenta PUC</th>
                                        <th class="text-center">Debito</th>
                                        <th class="text-center">Credito</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($z = 0; $z < $ordenPago->pucs->count(); $z++)
                                        <tr class="text-center">
                                            @if($ordenPago->pucs->count() > 20)
                                                <td>
                                                    @if($z > 1)
                                                        <button type="button" class="btn-sm btn-danger" onclick="deletePUC({{ $ordenPago->pucs[$z]->id }})"><i class="fa fa-trash-o"></i></button>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <select class="form-control" id="PUC[]" name="PUC[]" required>
                                                    <option>Selecciona un PUC</option>
                                                    @foreach($hijosPUC as $hijo)
                                                        <option value="{{ $hijo->id }}" @if($hijo->id == $ordenPago->pucs[$z]->rubros_puc_id ) selected @endif>{{ $hijo->code }} - {{ $hijo->concepto }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="vertical-align: middle">$<?php echo number_format($ordenPago->pucs[$z]->valor_debito,0);?></td>
                                            <td style="vertical-align: middle">$<?php echo number_format($ordenPago->pucs[$z]->valor_credito,0);?></td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 ml-auto text-center"><button type="submit" class="btn btn-primary">Guardar Contabilización</button></div>
                </form>
                @if(count($pagos) == 0)
                    <form class="form-valide" action="{{url('/administrativo/ordenPagos/descuento/finished/changeDesc')}}" method="POST" enctype="multipart/form-data">
                        <br><br>
                        <div class="col-md-12 align-self-center">
                            <hr>
                            <center>
                                <h3>Corregir Descuentos</h3>
                            </center>
                            <hr>
                            <br>
                            <center><h2>Descuentos Retención en la Fuente</h2></center>
                            <hr><br>
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
                                                <td class="text-center">$<?php echo number_format($ordenPago->valor,0) ?></td>
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
                                    @foreach($ordenPago->descuentos as $desc)
                                        @if($desc->retencion_fuente_id == null)
                                            <tr>
                                                <td class="text-center"><b>{{ $desc->id }}</b></td>
                                                <td class="text-center"><b>{{ $desc->nombre }}</b></td>
                                                <td class="text-center"><b>{{ $desc->porcent }}</b></td>
                                                <td class="text-center"><b>$<?php echo number_format($desc->valor,0) ?></b></td>
                                                <td class="text-center">
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
                        </div>
                        <div class="col-lg-12 ml-auto text-center"><button type="submit" class="btn btn-primary">Guardar Descuentos</button></div>
                        @endif
                    </form>
                @endif
                @if($ordenPago->estado == "0")
                    <div class="col-lg-6 ml-auto text-center">
                        <form action="{{ asset('/administrativo/ordenPagos/'.$ordenPago->id) }}" method="post">
                            {!! method_field('DELETE') !!}
                            {{ csrf_field() }}
                            <button class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @stop
@section('js')
    <script>
        function deletePUC(id){
            console.log(id)
        }

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
            $('#tabla').DataTable( {
                responsive: true,
                "searching": false,
                "oLanguage": {"sZeroRecords": "", "sEmptyTable": ""}
            } );
        } );

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
                    var urlVigencia = '/administrativo/ordenPagos/descuento/rf/'+dato+'/finished';
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
                    var urlVigencia = '/administrativo/ordenPagos/descuento/m/'+dato+'/finished';
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
