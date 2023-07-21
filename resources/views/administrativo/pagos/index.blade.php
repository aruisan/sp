@extends('layouts.dashboard')
@section('titulo') Pagos @stop
@section('content')
    @include('modal.chequePago')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Pagos</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuesto') }}" >PRESUPUESTO</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">TAREAS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">HISTORICO</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/pagos/create/'.$id) }}">NUEVO PAGO</a>
        </li>
        @if(auth()->user()->roles->first()->id != 8)
            <li class="nav-item pillPri">
                <a class="tituloTabs" href="{{ url('/administrativo/ordenPagos/'.$id) }}">ORDENES DE PAGO</a>
            </li>
            <li class="nav-item pillPri">
                <a class="tituloTabs" href="{{ url('/administrativo/registros/'.$id) }}">REGISTROS</a>
            </li>
        @endif
    </ul>
    <div class="tab-content" style="background-color: white">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div id="tabTareas" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($pagosTarea) > 0)
                    <table class="table table-bordered" id="tabla_Fin">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Num Identi Tercero</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagosTarea as $pagoT)
                            <tr class="text-center">
                                <td>{{ $pagoT['info']->code }}</td>
                                <td>{{ $pagoT['info']->concepto}}</td>
                                <td>$<?php echo number_format($pagoT['info']->valor,0) ?></td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($pagoT['info']->estado == "0")
                                            Pendiente
                                        @elseif($pagoT['info']->estado == "1")
                                            Finalizado
                                        @else
                                            Anulado
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">{{ $pagoT['cc'] }}</td>
                                <td class="text-center">{{ $pagoT['persona'] }}</td>
                                <td>
                                    <a href="{{ url('administrativo/pagos/show/'.$pagoT['info']->id) }}" title="Ver Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/pagos/asignacion/'.$pagoT['info']->id) }}" title="Asignar Monto" class="btn-sm btn-success"><i class="fa fa-usd"></i></a>
                                    <a href="{{ url('administrativo/pagos/banks/'.$pagoT['info']->id) }}" title="Asignar Banco" class="btn-sm btn-success"><i class="fa fa-bank"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay pagos pendientes.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade"><br>
            <div class="table-responsive">
                @if(count($pagos) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">Id Pago</th>
                            <th class="text-center">Orden de Pago</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor Pago</th>
                            <th class="text-center">Num Identi Tercero</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagos as $pago)
                            <tr class="text-center">
                                <td>{{ $pago['info']->code }}</td>
                                <td>
                                    @if($pago['info']->reteFuente == "1")
                                        <a href="{{ url('administrativo/ordenPagos/pdf/'.$pago['info']->orden_pago_id) }}" title="Ver Orden de Pago" class="btn-sm btn-success" target="_blank"><i class="fa fa-eye"></i></a>
                                    @else
                                        <a href="{{ url('administrativo/ordenPagos/show/'.$pago['info']->orden_pago_id) }}" title="Ver Orden de Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    @endif
                                </td>
                                <td>{{ $pago['info']->concepto }}</td>
                                <td>$<?php echo number_format($pago['info']->valor,0) ?></td>
                                <td>{{ $pago['info']->persona->num_dc }}</td>
                                <td>{{ $pago['info']->persona->nombre }}</td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($pago['info']->estado == "0")
                                            Pendiente
                                        @elseif($pago['info']->estado == "1")
                                            Finalizado
                                        @else
                                            Anulado
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('administrativo/pagos/show/'.$pago['info']->id) }}" title="Ver Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    @if($pago['info']->estado == "1")
                                        <a href="{{ url('/administrativo/egresos/pdf/'.$pago['info']->id) }}" title="Comprobante de Egresos" class="btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                        @if($pago['info']->type_pay == 'CHEQUE')
                                            <a href="{{ url('administrativo/pago/'.$pago['info']->id.'/cheque') }}" title="Descargar Cheque" class="btn-sm btn-success" target="_blank"><i class="fa fa-print"></i></a>
                                            <a onclick="showFormCheque({{ $pago['info']->id }}, {{ $pago['info']->code }},'{{ $pago['info']->num }}')" title="Editar Cheque" class="btn-sm btn-success"><i class="fa fa-edit"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay pagos finalizados.
                        </center>
                    </div>
                @endif
            </div>
        </div>
@stop
@section('js')
    <script>

        function showFormCheque(id, code, cheque){
            document.getElementById('idPagoChange').value = parseInt(id);
            document.getElementById('codePago').innerHTML = code;
            document.getElementById('cheque').value = cheque;
            $("#chequePagoedit").modal('show');
        }

        function editCheque(){
            var id = document.getElementById('idPagoChange').value;
            var cheque = document.getElementById('cheque').value;

            $.ajax({
                method: "POST",
                url: "/administrativo/changeCheque/pago/"+id,
                data: { "id": id, "cheque": cheque,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function() {
                location.reload();
                toastr.success('SE CAMBIO EL NÚMERO DE CHEQUE CORRECTAMENTE.');
            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL CAMBIAR EL NÚMERO DE CHEQUE DEL PAGO.');
            });
        }

        $('#tabla_Fin').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons:[
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-clone"></i> ',
                    titleAttr: 'Copiar',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> ',
                    titleAttr: 'Exportar a PDF',
                    message : 'SIEX-Providencia',
                    header :true,
                    orientation : 'landscape',
                    pageSize: 'LEGAL',
                    className: 'btn btn-primary',
                },
                {
                    extend:    'print',
                    text:      '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary'
                },
            ]
        } );

        $('#tabla_Historico').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons:[
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-clone"></i> ',
                    titleAttr: 'Copiar',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> ',
                    titleAttr: 'Exportar a PDF',
                    message : 'SIEX-Providencia',
                    header :true,
                    orientation : 'landscape',
                    pageSize: 'LEGAL',
                    className: 'btn btn-primary',
                },
                {
                    extend:    'print',
                    text:      '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary'
                },
            ]
        } );
    </script>
@stop