@extends('layouts.dashboard')
@section('titulo')
    Balance de Prueba
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>puc {{$puc->codigo_punto}} --- {{$puc->concepto}}</b></h4>
            </strong>
        </div>

        <div class="row">
            @php 
                $debito = $puc->naturaleza == "DEBITO" ? $puc->v_inicial : 0;
                $credito= $puc->naturaleza != "DEBITO" ? $puc->v_inicial : 0;
                $m_debito = $puc->m_debito;
                $m_credito = $puc->m_credito;
                $s_debito = $puc-> naturaleza == "DEBITO" ? $debito + $m_debito - $m_credito:0;
                $s_credito = $puc-> naturaleza == "CREDITO" ? $credito + $m_credito - $m_debito: 0;
            @endphp
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">Valor Inicial</th>
                        <th class="text-center">Movimiento Debito</th>
                        <th class="text-center">Movimiento Credito</th>
                        <th class="text-center">Saldo Final</th>
                        <th class="text-center">Corriente</th>
                        <th class="text-center">No Corriente</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $m_debito = $puc['m_debito_trimestre'];
                    $m_credito = $puc['m_credito_trimestre'];
                    $s_final = $puc['naturaleza'] == "DEBITO" ? $puc['v_inicial'] + $m_debito - $m_credito : $puc['v_inicial'] + $m_credito - $m_debito;
                    $corriente = $puc['estado_corriente'] ? $s_final : 0;
                    $no_corriente = !$puc['estado_corriente'] ? $s_final : 0;
                    @endphp
                    <tr>
                        <td class='text-right' style='width=200px;'>${{number_format($puc['v_inicial'])}}</td>
                        <td class='text-right' style='width=200px;'>${{number_format($m_debito)}}</td>
                        <td class='text-right' style='width=200px;'>${{number_format($m_credito)}}</td>
                        <td class='text-right' style='width=200px;'>${{number_format($s_final)}}</td>
                        <td class='text-right' style='width=200px;'>${{number_format($corriente)}}</td>
                        <td class='text-right' style='width=200px;'>${{number_format($no_corriente)}}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2">Movimiento</th>
                        <th class="text-center warning" colspan="3">Valores del primer trimestre</th>
                        <th class="text-center info" colspan="3">Hasta el dia de hoy</th>
                    </tr>
                    <tr>
                        <th class="text-center warning">Debito</th>
                        <th class="text-center warning">Credito</th>
                        <th class="text-center warning">Valor</th>
                        <th class="text-center info">Debito</th>
                        <th class="text-center info">Credito</th>
                        <th class="text-center info">Valor</th>
                    </tr>
                </thead>
                @php 
                    $inicio = "2023-01-01";
                    $final = "2023-03-31";
                @endphp
                <tbody>
                    <tr>
                        <td class='text-center' style='width=200px;'>Pagos bancarios</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->pagos_bank->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->filter(function($p){ return $p->pago->estado == 1;})->sum('valor'))}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->pagos_bank->filter(function($p){ return $p->pago->estado == 1;})->sum('valor'))}}</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Comprobantes</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->comprobantes->count() > 0 ?$puc->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito'): 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->comprobantes->count() > 0 ?$puc->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito'): 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->comprobantes->count() > 0 ?$puc->comprobantes->sum('debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->comprobantes->count() > 0 ?$puc->comprobantes->sum('credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Ordenes de Pago</td>
                        
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_debito') : 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_credito') : 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ?$puc->orden_pagos->sum('valor_debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ?$puc->orden_pagos->sum('valor_credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Ordenes de Pago pagos</td>
                        {{--
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('suma_pagos') : 0)}}</td>
                        --}}
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_credito') : 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->sum('suma_pagos') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Movimientos Retefuente</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->retefuente_movimientos->count() > 0 ? $puc->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito') : 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->retefuente_movimientos->count() > 0 ? $puc->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito') : 0)}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->retefuente_movimientos->count() > 0 ?$puc->retefuente_movimientos->sum('debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->retefuente_movimientos->count() > 0 ?$puc->retefuente_movimientos->sum('credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Almacen debito</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->almacen_items->sum('total'))}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Almacen Credito</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->almacen_items_creditos->sum('total'))}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>

                </tbody>
            </table>
        
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#p_bank">Pagos Bancos {{$puc->pagos_bank->count()}}</a></li>
            <li><a data-toggle="tab" href="#comprobantes">Comprobantes {{$puc->comprobantes->count()}}</a></li>
            <li><a data-toggle="tab" href="#o_pago">Ordenes de Pago {{$puc->orden_pagos->count()}}</a></li>
            <li><a data-toggle="tab" href="#retefuente">Retefuente Movimientos {{$puc->retefuente_movimientos->count()}}</a></li>
            <li><a data-toggle="tab" href="#o_pago_pago">Pagos</a></li>
        </ul>

        <div class="tab-content">
            <div id="p_bank" class="tab-pane fade in active">
                <h3>Pagos Bancarios</h3>
                {{--
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="info">total hasta el dia de hoy</td>
                            <td class="info">{{$puc->pagos_bank->count() > 0 ? $this->pagos_bank->filter(function($p){ return $p->pago->estado == 1;})->sum('valor') : 0}}</td>
                            <td class="warning">total hasta el 31 de marzo</td>
                            <td class="warning">{{$puc->pagos_bank->count() > 0 ? $this->pagos_bank->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->filter(function($p){ return $p->pago->estado == 1;})->sum('valor') : 0}}</td>
                        </tr>
                    </tbody>
                </table>
                --}}

                <table class="table tabla">
                    <thead>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->pagos_bank as $pago)
                        <tr class="{{$pago->created_at < $inicio || $pago->created_at > $final ? 'info' : 'warning'}}">
                            <td>{{number_format($pago->valor)}}</td>
                            <td>{{$pago->pago->status}}</td>
                            <td>{{$pago->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="comprobantes" class="tab-pane fade">
                <h3>Comprobantes</h3>

                <table class="table tabla">
                    <thead>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Fecha de Compra</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->comprobantes as $comprobante)
                        <tr class="{{$comprobante->created_at < $inicio || $comprobante->created_at > $final ? 'info' : 'warning'}}">
                            <td>{{number_format($comprobante->debito)}}</td>
                            <td>{{number_format($comprobante->credito)}}</td>
                            <td>{{$comprobante->fechaComp}}</td>
                            <td>{{$comprobante->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="o_pago" class="tab-pane fade">
                <h3>Ordenes Pago</h3>
                <table class="table tabla">
                    <thead>
                        <th>Codigo</th>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Resta</th>
                        <th>Pagos</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->orden_pagos as $orden_pago)
                        <tr class="{{$orden_pago->created_at < $inicio || $orden_pago->created_at > $final ? 'info' : 'warning'}}">
                            <td>{{$orden_pago->ordenPago->code}}</td>
                            <td>{{number_format($orden_pago->valor_debito)}}</td>
                            <td>{{number_format($orden_pago->valor_credito)}}</td>
                            <td>{{number_format($orden_pago->valor_credito - $orden_pago->ordenPago->suma_pagos_aceptados)}}</td>
                            <td>{{number_format($orden_pago->ordenPago->suma_pagos_aceptados)}}</td>
                            <td>{{$orden_pago->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="retefuente" class="tab-pane fade">
                <h3>retefuente Movimientos</h3>
                <table class="table tabla">
                    <thead>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->retefuente_movimientos as $retefuente)
                        <tr class="{{$retefuente->created_at < $inicio || $retefuente->created_at > $final ? 'info' : 'warning'}}">
                            <td>{{number_format($retefuente->debito)}}</td>
                            <td>{{number_format($retefuente->credito)}}</td>
                            <td>{{$retefuente->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="o_pago_pago" class="tab-pane fade">
                <h3>Pagos</h3>
                <table class="table tabla">
                    <thead>
                        <th>concepto</th>
                        <th>code</th>
                        <th>estado</th>
                        <th>valor</th>
                        <th>fecha</th>
                    </thead>
                    <tbody>
                    @if($puc->orden_pagos->count() > 0 )
                            @foreach($puc->orden_pagos as $orden_pago)
                                @if(!is_null($orden_pago->ordenPago))
                                    @foreach($orden_pago->ordenPago->pagos as $pago)
                                        <tr class="{{$pago->created_at < $inicio || $pago->created_at > $final ? 'info' : 'warning'}}">
                                            <td>{{$pago->concepto}}</td>
                                            <td>{{$pago->code}}</td>
                                            <td>{{$pago->estado}}</td>
                                            <td>{{number_format($pago->valor)}}</td>
                                            <td>{{$pago->created_at}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                <tr class="danger">
                                    <td colspan="5">No tiene</td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                        <tr class="danger">
                            <td colspan="5">No tiene Ordenes de pagos</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
          let tbl =  $('.tabla').DataTable({
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                pageLength: 2000,
                responsive: true,
                "searching": true,
                ordering: false
            })
        })


    </script>
@stop