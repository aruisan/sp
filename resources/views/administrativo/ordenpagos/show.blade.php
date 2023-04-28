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
                @if($OrdenPago->estado >= 1)
                    <li class="nav-item regresar"> <a class="nav-link" href="{{ url('/administrativo/ordenPagos/'.$vigencia_id) }}">Volver a Ordenes de Pago</a></li>
                    <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Orden de Pago {{ $OrdenPago->code }}</a></li>
                    @if($OrdenPago->estado == 1)
                        @if(isset($OrdenPago->pago))
                            <li class="nav-item"> <a class="tituloTabs" href="{{ url('administrativo/pagos/show/'.$OrdenPago->pago->id) }}"><i class="fa fa-credit-card"></i>&nbsp; Ver Pago</a></li>
                        @else
                            <li class="nav-item"> <a class="tituloTabs" href="{{ url('/administrativo/pagos/create/'.$vigencia_id) }}"><i class="fa fa-credit-card"></i>&nbsp; Pagar</a></li>
                        @endif
                        <li class="nav-item pillPri"> <a class="tituloTabs" target="_blank" href="{{ url('/administrativo/ordenPagos/pdf/'.$OrdenPago->id) }}"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</a></li>
                    @endif
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
                        @if($OrdenPago->estado == 2)
                            <div class="col-lg-12 text-center">
                                <div class="col-lg-12">
                                    <br><div class="alert alert-danger">
                                        <center>La orden de pago ha sido anulada</center>
                                        <br>
                                        <center>Motivo: {{$OrdenPago->observacion}}</center>
                                    </div><br>
                                </div>
                            </div>
                        @endif
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
                                        <th class="text-center">%</th>
                                        <th class="text-center">Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($OrdenPagoDescuentos as  $PagosDesc)
                                        <tr class="text-center">
                                            @if($PagosDesc->desc_municipal_id != null)
                                                <td>{{ $PagosDesc->descuento_mun['codigo'] }}</td>
                                                <td>{{ $PagosDesc->descuento_mun['concepto'] }}</td>
                                                <td>$ <?php echo number_format($OrdenPago->valor - $OrdenPago->iva,0);?></td>
                                                @if($PagosDesc->descuento_mun['id'] == 5)
                                                    <td>7 X 1000</td>
                                                @else
                                                    <td>{{ $PagosDesc->descuento_mun['tarifa'] }}</td>
                                                @endif
                                            @elseif($PagosDesc->retencion_fuente_id != null)
                                                <td>{{ $PagosDesc->descuento_retencion->codigo}}</td>
                                                <td>{{ $PagosDesc->descuento_retencion->concepto }}</td>
                                                <td>$ <?php echo number_format($OrdenPago->valor - $OrdenPago->iva,0);?></td>
                                                <td>{{ $PagosDesc->descuento_retencion->tarifa }}</td>
                                            @else
                                                <td>{{ $PagosDesc->puc->code}}</td>
                                                <td>{{ $PagosDesc->puc->concepto}}</td>
                                                <td></td>
                                                <td></td>

                                            @endif
                                            <td>$ <?php echo number_format($PagosDesc['valor'],0);?></td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                                        <td colspan="4"><b>Total Descuentos</b></td>
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
                                        @if($R->cdpRegistroValor[$i]->valor > 0)
                                            @if($R->cdpRegistroValor[$i]->cdps->tipo == "Funcionamiento")
                                                <tr class="text-center">
                                                    <td>
                                                        @for($x = 0; $x < count($infoRubro); $x++)
                                                            @if($infoRubro[$x]['id_rubro'] == $R->cdpRegistroValor[$i]->fontRubro->rubro->id)
                                                                {{ $infoRubro[$x]['codigo'] }}
                                                            @endif
                                                        @endfor
                                                    </td>
                                                    <td>{{ $R->cdpRegistroValor[$i]->fontRubro->rubro->name}}</td>
                                                    @if($R->cdpRegistroValor[$i]->fontRubro->sourceFunding)
                                                        <td>{{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->description }}</td>
                                                    @else
                                                        <td>{{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->name }}</td>
                                                    @endif
                                                    <td>{{ $OrdenPago->registros->objeto }}</td>
                                                    <td>$ <?php echo number_format($OrdenPago->registros->valor,0);?></td>
                                                </tr>
                                            @elseif($R->cdpRegistroValor[$i]->cdps->tipo == "Inversion")
                                                <tr class="text-center">
                                                    <td>
                                                        {{$R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->actividad->cod_actividad}}
                                                    </td>
                                                    <td>{{$R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->actividad->actividad}}</td>
                                                    @if($R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->dependencia_rubro_font_id != null)
                                                        <td>{{ $R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->depRubroFont->fontRubro->sourceFunding->code }} - {{ $R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->depRubroFont->fontRubro->sourceFunding->description }}</td>
                                                    @else
                                                        <td></td>
                                                    @endif
                                                    <td>{{ $OrdenPago->registros->objeto }}</td>
                                                    <td>$ <?php echo number_format($OrdenPago->registros->valor,0);?></td>
                                                </tr>
                                            @endif

                                        @endif
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
                                            <td>{{$OrdenPago->pucs[$z]->data_puc->code}}</td>
                                            <td>{{$OrdenPago->pucs[$z]->data_puc->concepto}}</td>
                                            <td>{{ $OrdenPago->registros->persona->num_dc }} {{ $OrdenPago->registros->persona->nombre }}</td>
                                            <td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_debito,0);?></td>
                                            <td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_credito,0);?></td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                            @include('modal.anularOP')
                            @if($OrdenPago->saldo > 0 and $OrdenPago->estado == 1 and $rol == 3 )
                                <center>
                                    <a data-toggle="modal" data-target="#anularOP" class="btn btn-success">
                                        Anular Orden de Pago
                                    </a>
                                </center>
                            @endif
                            @if(count($pagos) > 0)
                                <hr>
                                <center>
                                    <h3>Pagos Asignados a la Orden de Pago</h3>
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
                                            <th class="text-center">Comprobante Egreso</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pagos as $pago)
                                            <tr class="text-center">
                                                <td><a href="{{ url('administrativo/pagos/show/'.$pago->id) }}" title="Ver Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a></td>
                                                <td>{{$pago->concepto}}</td>
                                                <td>$<?php echo number_format($pago->valor,0) ?> </td>
                                                <td>
                                                <span class="badge badge-pill badge-danger">
                                                    @if($pago->estado == "0")
                                                        Pendiente
                                                    @elseif($pago->estado == "1")
                                                        Finalizado
                                                    @else
                                                        Anulado
                                                    @endif
                                                </span>
                                                </td>
                                                <td>{{$pago->ff_fin}}</td>
                                                <td><a target="_blank" href="{{ url('administrativo/egresos/pdf/'.$pago->id) }}" title="Ver Comprobante de Egreso" class="btn-sm btn-success"><i class="fa fa-file-pdf-o"></i></a></td>
                                            </tr>
                                        @endforeach
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
