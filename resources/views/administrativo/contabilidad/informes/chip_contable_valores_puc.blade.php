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
                    $m_debito = $puc['m_debito'];
                    $m_credito = $puc['m_credito'];
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
                        <th class="text-center warning" colspan="3">Valores del mes</th>
                    </tr>
                    <tr>
                        <th class="text-center warning">Debito</th>
                        <th class="text-center warning">Credito</th>
                        <th class="text-center warning">Valor</th>
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
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->pagos_bank_mensual->filter(function($p){ return $p->pago->estado == 1;})->sum('valor'))}}</td>
                    </tr>
                    {{--
                    <tr>
                        <td class='text-center' style='width=200px;'>Pagos bancarios nuevos (deivith)</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->pagos_bank_new_mensual->sum('debito'))}}</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->pagos_bank_new_mensual->sum('credito'))}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    --}}
                    <tr>
                        <td class='text-center' style='width=200px;'>Comprobantes</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->comprobantes_mensual->count() > 0 ?$puc->comprobantes_mensual->sum('debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->comprobantes_mensual->count() > 0 ?$puc->comprobantes_mensual->sum('credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Ordenes de Pago</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos_mensual->count() > 0 ?$puc->orden_pagos_mensual->sum('valor_debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos_mensual->count() > 0 ?$puc->orden_pagos_mensual->sum('valor_credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    {{--
                    <tr>
                        <td class='text-center' style='width=200px;'>Ordenes de Pago pagos</td>
                        {{--
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('suma_pagos') : 0)}}</td>
                        -}}
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->orden_pagos->count() > 0 ? $puc->orden_pagos->sum('suma_pagos') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    --}}
                    <tr>
                        <td class='text-center' style='width=200px;'>Movimientos Retefuente</td>
                        <td class='text-center info' style='width=200px;'></td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->retefuente_mensual->count() > 0 ?$puc->retefuente_mensual->sum('valor') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Almacen entrada debito</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->almacen_items_mensual->sum('total'))}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                    </tr>
                    <tr>
                        <td class='text-center' style='width=200px;'>Almacen entrada Credito</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                        <td class='text-center warning' style='width=200px;'>${{number_format($puc->almacen_items_creditos->sum('total'))}}</td>
                        <td class='text-center warning' style='width=200px;'>0</td>
                    </tr>
                    {{--
                    <tr>
                        <td class='text-center' style='width=200px;'>Otros Pucs</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->otros_ordenes_pago_pucs_mensual->count() > 0 ?$puc->otros_ordenes_pago_pucs_mensual->sum('valor_debito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>${{number_format($puc->otros_ordenes_pago_pucs_mensual->count() > 0 ?$puc->otros_ordenes_pago_pucs_mensual->sum('valor_credito') : 0)}}</td>
                        <td class='text-center info' style='width=200px;'>0</td>
                    </tr>
                    --}}

                </tbody>
            </table>
        
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#p_bank">Pagos Bancos {{$puc->pagos_bank_mensual->count()}}</a></li>
            <li><a data-toggle="tab" href="#p_bank_n">Pagos Bancos new (deivith) {{$puc->pagos_bank_new_mensual->count()}}</a></li>
            <li><a data-toggle="tab" href="#comprobantes">Comprobantes {{$puc->comprobantes_mensual->count()}}</a></li>
            <li><a data-toggle="tab" href="#o_pago">Ordenes de Pago {{$puc->orden_pagos_mensual->count()}}</a></li>
            <li><a data-toggle="tab" href="#retefuente">Retefuente Movimientos {{$puc->retefuente_mensual->count()}}</a></li>
            {{--
            <li><a data-toggle="tab" href="#o_pago_pago">Pagos</a></li>
            --}}
            <li><a data-toggle="tab" href="#almacen_entrada_debitos">Almacen entrada Debitos</a></li>
            <li><a data-toggle="tab" href="#almacen_entrada_debitos">Almacen entrada creditos</a></li>
            {{--
            <li><a data-toggle="tab" href="#otros_pucs">Otros Pucs</a></li>
            --}}
        </ul>

        <div class="tab-content">
            <div id="p_bank" class="tab-pane fade in active">
                <h3>Pagos Bancarios</h3>
                {{--
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="info">total hasta el dia de hoy</td>
                            <td class="info">{{$puc->pagos_bank_mensual->count() > 0 ? $this->pagos_bank_mensual->filter(function($p){ return $p->pago->estado == 1;})->sum('valor') : 0}}</td>
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
                        @foreach($puc->pagos_bank_mensual as $pago)
                        <tr class="{{$pago->created_at < $inicio || $pago->created_at > $final ? 'info' : 'warning'}}">
                            <td>{{number_format($pago->valor)}}</td>
                            <td>{{$pago->pago->status}}</td>
                            <td>{{$pago->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                {{--
            <div id="p_bank_n" class="tab-pane fade in active">
                <h3>Pagos Bancarios</h3>

                <table class="table tabla">
                    <thead>
                        <th>debito</th>
                        <th>credito</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->pagos_bank_new_mensual as $pago)
                        <tr class="info">
                            <td>{{number_format($pago->debito)}}</td>
                            <td>{{number_format($pago->credito)}}</td>
                            <td>{{$pago->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                --}}
            <div id="comprobantes" class="tab-pane fade">
                <h3>Comprobantes</h3>

                <table class="table tabla">
                    <thead>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Concepto</th>
                        <th>Fecha de Compra</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->comprobantes_mensual as $comprobante)
                        <tr class="info">
                            <td>{{number_format($comprobante->debito)}}</td>
                            <td>{{number_format($comprobante->credito)}}</td>
                            <td>{{$comprobante->comprobante->concepto}}</td>
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
                        <th>Id orden_pago puc</th>
                        <th>Id orden_pago</th>
                        <th>Codigo</th>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Resta</th>
                        <th>pagos</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->orden_pagos_mensual as $orden_pago)
                        <tr class="info">
                        <td>{{$orden_pago->id}}</td>
                            <td>{{$orden_pago->ordenPago->id}}</td>
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
                        <th>Credito</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->retefuente_mensual as $retefuente)
                        <tr class="info">
                            <td>{{number_format($retefuente->valor)}}</td>
                            <td>{{$retefuente->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{--
            <div id="o_pago_pago" class="tab-pane fade">
                <h3>Pagos</h3>
                <table class="table tabla">
                    <thead>
                        <th>Id</th>
                        <th>concepto</th>
                        <th>code</th>
                        <th>estado</th>
                        <th>valor</th>
                        <th>fecha</th>
                    </thead>
                    <tbody>
                    @if($puc->orden_pagos_mensual->count() > 0 )
                            @foreach($puc->orden_pagos_mensual as $orden_pago)
                                @if(!is_null($orden_pago->ordenPago))
                                    @foreach($orden_pago->ordenPago->pagos as $pago)
                                        <tr class="{{$pago->created_at < $inicio || $pago->created_at > $final ? 'info' : 'warning'}}">
                                            <td>{{$pago->id}}</td>
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
            --}}
            <div id="almacen_entrada_debitos" class="tab-pane fade">
                <h3>Akmacen Debitos</h3>
                <table class="table tabla">
                    <thead>
                        <th></th>
                        <th>articulo</th>
                        <th>cantidad</th>
                        <th>valor</th>
                        <th>total debito</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->almacen_items_mensual as $item)
                        <tr class="info">
                            <td>{{$item->nombre_articulo}}</td>
                            <td>{{number_format($item->cantidad)}}</td>
                            <td>{{number_format($item->valor)}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{$item->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{--
            <div id="almacen_entrada_creditos" class="tab-pane fade">
                <h3>Almacen Debitos</h3>
                <table class="table tabla">
                    <thead>
                        <th></th>
                        <th>articulo</th>
                        <th>cantidad</th>
                        <th>valor</th>
                        <th>total debito</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($$puc->almacen_items_creditos->sum('total') as $item)
                        <tr class="info">
                            <td>{{$item->nombre_articulo}}</td>
                            <td>{{number_format($item->cantidad)}}</td>
                            <td>{{number_format($item->valor)}}</td>
                            <td>{{number_format($item->total)}}</td>
                            <td>{{$item->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            --}}
            {{--
            <div id="otros_pucs" class="tab-pane fade">
                <h3>Ordenes Pago</h3>
                <table class="table tabla">
                    <thead>
                        <th>Id orden_pago puc</th>
                        <th>Id orden_pago</th>
                        <th>Codigo</th>
                        <th>Debito</th>
                        <th>Credito</th>
                        <th>Resta</th>
                        <th>pagos</th>
                        <th>Fecha</th>
                    </thead>
                    <tbody>
                        @foreach($puc->otros_ordenes_pago_pucs_mensual as $orden_pago)
                        <tr class="info">
                            <td>{{$orden_pago->id}}</td>
                            <td>{{$orden_pago->ordenPago->id}}</td>
                            <td>{{$orden_pago->ordenPago->code}}</td>
                            <td>{{number_format($orden_pago->valor_debito)}}</td>
                            <td>{{number_format($orden_pago->valor_credito)}}</td>
                            <td>{{$orden_pago->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            --}}
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            alert(1?+2+3)
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