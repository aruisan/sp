@extends('layouts.dashboard')
@section('titulo')
    Información de Pago
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>{{ $pago->concepto }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                @if($pago->estado == 1)
                    <li class="nav-item regresar"> <a class="nav-link" href="{{ url('/administrativo/pagos/'.$pago->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id) }}">Volver a Pagos</a></li>
                    <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Pago {{ $pago->code }}</a></li>
                    <li class="nav-item pillPri"> <a class="tituloTabs" target="_blank" href="{{ url('/administrativo/ordenPagos/pdf/'.$ordenPago->id) }}"><i class="fa fa-file-pdf-o"></i>&nbsp; Orden de Pago</a></li>
                    <li class="nav-item pillPri"> <a class="tituloTabs" target="_blank" href="{{ url('/administrativo/egresos/pdf/'.$pago->id) }}"><i class="fa fa-file-pdf-o"></i>&nbsp; Comprobante de Egresos</a></li>
                @else
                    <li class="nav-item regresar"> <a class="nav-link" href="{{ url('/administrativo/pagos/'.$pago->orden_pago->registros->cdpsRegistro[0]->cdp->vigencia_id) }}">Volver a Pagos</a></li>
                    <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Pago {{ $pago->code }}</a></li>
                    <li class="nav-item"> <a href="{{ url('/administrativo/pagos/asignacion/'.$pago->id) }}" class="tituloTabs">Asignación de Monto</a></li>
                    <li class="nav-item"> <a href="{{ url('/administrativo/pagos/banks/'.$pago->id) }}" class="tituloTabs">Bancos</a></li>
                @endif
            </ul>
        </div>
        <div class="col-lg-12">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row">
                        <div class="form-validation">
                            <form class="form" action="">
                                <br><br>
                                {{ csrf_field() }}
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="nombre">Nombre:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $pago->concepto }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="valor">Registro:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" style="text-align:center" name="valor" value="{{ $ordenPago->registros->objeto }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="nombre">Tercero:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $pago->persona->nombre }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="valor">Fecha de Creación:</label>
                                        <div class="col-lg-6">
                                            <input type="text" disabled class="form-control" style="text-align:center" name="valor" value="{{ $ordenPago->created_at }}">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="col-lg-4">
                                <br>
                                <h4><b>Valor Pago</b></h4>
                                <div class="text-center">
                                    @if($pago->valor > 0)
                                        $<?php echo number_format($pago->valor,0) ?>
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <br>
                                <h4><b>Valor Orden de Pago</b></h4>
                                <div class="text-center">
                                    @if($ordenPago->valor > 0)
                                        $<?php echo number_format($ordenPago->valor,0) ?>
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <br>
                                <h4><b>Valor Disponible Orden de Pago</b></h4>
                                <div class="text-center">
                                    @if($ordenPago->saldo > 0)
                                        $<?php echo number_format($ordenPago->saldo,0) ?>
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <hr>
                            <center>
                                <h3>Movimiento Bancario</h3>
                            </center>
                            <hr>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaP">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Codigo</th>
                                        <th class="text-center">Banco / Cuenta</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($y = 0; $y < count($banks); $y++)
                                        <tr class="text-center">
                                            <td>{{ $banks[$y]->data_puc->code }}</td>
                                            <td>{{ $banks[$y]->data_puc->concepto }}</td>
                                            @if($pago->type_pay == "ACCOUNT")
                                                @php( $date = strftime("%d of %B %Y", strtotime($banks[$y]->created_at)))
                                                <td> Núm Cuenta: {{$pago->num}} - Fecha: {{$date}}</td>
                                            @elseif($pago->type_pay == "CHEQUE")
                                                @php( $date = strftime("%d of %B %Y", strtotime($banks[$y]->created_at)))
                                                <td> Núm Cheque: {{$pago->num}} - Fecha: {{$date}}</td>
                                            @endif
                                            <td>$<?php echo number_format($banks[$y]->valor,0);?></td>
                                        </tr>
                                    @endfor
                                    <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                                        <td colspan="3"><b>Total</b></td>
                                        <td><b>$<?php echo number_format($banks->sum('valor'),0);?></b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stop
