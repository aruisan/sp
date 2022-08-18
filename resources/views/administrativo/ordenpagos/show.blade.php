@extends('layouts.dashboard')
@section('titulo')
    Información Orden de Pago
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>{{ $OrdenPago->nombre }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                @if($OrdenPago->estado == 1)
                    <li class="nav-item regresar"> <a class="nav-link" href="{{ url('/administrativo/ordenPagos/'.$vigencia_id) }}">Volver a Ordenes de Pago</a></li>
                    <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Orden de Pago {{ $OrdenPago->code }}</a></li>
                    @if(isset($OrdenPago->pago))
                        <li class="nav-item"> <a class="tituloTabs" href="{{ url('administrativo/pagos/show/'.$OrdenPago->pago->id) }}"><i class="fa fa-credit-card"></i>&nbsp; Ver Pago</a></li>
                    @else
                        <li class="nav-item"> <a class="tituloTabs" href="{{ url('/administrativo/pagos/create/'.$vigencia_id) }}"><i class="fa fa-credit-card"></i>&nbsp; Pagar</a></li>
                    @endif
                    <li class="nav-item pillPri"> <a class="tituloTabs" target="_blank" href="{{ url('/administrativo/ordenPagos/pdf/'.$OrdenPago->id) }}"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</a></li>
                @else
                    <li class="nav-item regresar"> <a class="nav-link" href="{{ url('/administrativo/ordenPagos/'.$vigencia_id) }}">Volver a Ordenes de Pago</a></li>
                    <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Orden de Pago {{ $OrdenPago->code }}</a></li>
                    <li class="nav-item"> <a href="{{ url('/administrativo/ordenPagos/monto/create/'.$OrdenPago->id) }}" class="tituloTabs">Asignación de Monto</a></li>
                    <li class="nav-item"> <a href="{{ url('/administrativo/ordenPagos/descuento/create/'.$OrdenPago->id) }}" class="tituloTabs">Descuentos</a></li>
                    <li class="nav-item"> <a href="{{ url('/administrativo/ordenPagos/liquidacion/create/'.$OrdenPago->id) }}" class="tituloTabs">Contabilizacion</a></li>
                @endif
            </ul>
        </div>
        <div class="col-lg-12">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row">
                        <div class="form-validation">
                            <form class="form">
                                <br><br>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="nombre">Nombre:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $OrdenPago->nombre }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="valor">Registro:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" style="text-align:center" name="valor" value="#{{$OrdenPago->registros->code}} - {{ $OrdenPago->registros->objeto }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="nombre">Tercero:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $OrdenPago->registros->persona->nombre }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="valor">Fecha de Creación:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" style="text-align:center" name="valor" value="{{ $OrdenPago->created_at }}">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="col-lg-6">
                                <br>
                                <h4><b>Valor Orden de Pago</b></h4>
                                <div class="text-center">
                                    @if($OrdenPago->valor > 0)
                                        $<?php echo number_format($OrdenPago->valor,0) ?>
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 text-center">
                                <br>
                                <h4><b>Valor Disponible Orden de Pago</b></h4>
                                <div class="text-center">
                                    @if($OrdenPago->saldo > 0)
                                        $<?php echo number_format($OrdenPago->saldo,0) ?>
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <hr>
                            <center>
                                <h3>Descuentos</h3>
                            </center>
                            <hr>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaDesc">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Codigo</th>
                                        <th class="text-center">Descripcion</th>
                                        <th class="text-center">Base</th>
                                        <th class="text-center">Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($OrdenPagoDescuentos as  $PagosDesc)
                                        <tr class="text-center">
                                            @if($PagosDesc->retencion_fuente_id == null)
                                                <td>{{ $PagosDesc->descuento_mun['codigo'] }}</td>
                                            @else
                                                <td>{{ $PagosDesc->descuento_retencion->codigo}}</td>
                                            @endif
                                            <td>{{ $PagosDesc->nombre }}</td>
                                            @if($PagosDesc->retencion_fuente_id == null)
                                                <td>$ <?php echo number_format($PagosDesc->descuento_mun['base'],0);?></td>
                                            @else
                                                <td>$ <?php echo number_format($PagosDesc->descuento_retencion->base,0);?></td>
                                            @endif
                                            <td>$ <?php echo number_format($PagosDesc['valor'],0);?></td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                                        <td colspan="3"><b>Total Descuentos</b></td>
                                        <td><b>$ <?php echo number_format($OrdenPagoDescuentos->sum('valor'),0);?></b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <center>
                                <h3>Presupuesto</h3>
                            </center>
                            <hr>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaP">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Codigo</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Fuente Financiación</th>
                                        <th class="text-center">Registro</th>
                                        <th class="text-center">Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($i = 0; $i < $R->cdpRegistroValor->count(); $i++)
                                        <tr class="text-center">
                                            <td>
                                                @for($x = 0; $x < count($infoRubro); $x++)
                                                    @if($infoRubro[$x]['id_rubro'] == $R->cdpRegistroValor[$i]->rubro_id)
                                                        {{ $infoRubro[$x]['codigo'] }}
                                                    @endif
                                                @endfor
                                            </td>
                                            <td>{{ $R->cdpRegistroValor[$i]->fontRubro->rubro->name}}</td>
                                            <td>{{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->font->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->font->name }}</td>
                                            <td>{{ $OrdenPago->registros->objeto }}</td>
                                            <td>$ <?php echo number_format($OrdenPago->registros->valor,0);?></td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
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
                                        <th class="text-center">Codigo</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Tercero</th>
                                        <th class="text-center">Debito</th>
                                        <th class="text-center">Credito</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($z = 0; $z < $OrdenPago->pucs->count(); $z++)
                                        <tr class="text-center">
                                            <td>{{$OrdenPago->pucs[$z]->data_puc->codigo}}</td>
                                            <td>{{$OrdenPago->pucs[$z]->data_puc->nombre_cuenta}}</td>
                                            <td>{{ $OrdenPago->registros->persona->num_dc }} {{ $OrdenPago->registros->persona->nombre }}</td>
                                            <td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_debito,0);?></td>
                                            <td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_credito,0);?></td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                            @if(isset($OrdenPago->pago))
                                <hr>
                                <center>
                                    <h3>Pago Asignado a la Orden de Pago</h3>
                                </center>
                                <hr>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaP">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Ver Pago</th>
                                            <th class="text-center">Concepto</th>
                                            <th class="text-center">Valor</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Fecha de Finalización</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="text-center">
                                            <td><a href="{{ url('administrativo/pagos/show/'.$OrdenPago->pago->id) }}" title="Ver Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a></td>
                                            <td>{{$OrdenPago->pago->concepto}}</td>
                                            <td>$<?php echo number_format($OrdenPago->pago->valor,0) ?> </td>
                                            <td>
                                                <span class="badge badge-pill badge-danger">
                                                    @if($OrdenPago->pago->estado == "0")
                                                        Pendiente
                                                    @elseif($OrdenPago->pago->estado == "1")
                                                        Finalizado
                                                    @else
                                                        Anulado
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{$OrdenPago->pago->ff_fin}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop
