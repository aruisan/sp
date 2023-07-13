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
    <div class="col-md-12 align-self-center">
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
                                <h3>Contabilización</h3>
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
                        <div class="col-lg-12 ml-auto text-center"><button type="submit" class="btn btn-primary">Guardar</button></div>
                    @endif
                </form>
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
    </script>
@stop
