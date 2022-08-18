@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
        <div class="col-md-12 align-self-center">
            <div class="breadcrumb text-center">
                <strong>
                    <h4><b>PAGOS</b></h4>
                </strong>
            </div>
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link" href="{{ url('/impuestos') }}" ><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" data-toggle="pill" href="#tabPagos">Para Pagar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#tabHistorico">Historico de Pagos</a>
                </li>
            </ul>
            <div class="tab-content" >
                <div id="tabPagos" class="tab-pane fade in active"><br>
                    <br>
                    <div class="table-responsive">
                        @if(count($pagosPendientes) > 0)
                            <table class="table table-bordered" id="tabla_pagos">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Formulario</th>
                                    <th class="text-center">Fecha Presentación</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Ver Detalle</th>
                                    <th class="text-center">Corregir</th>
                                    <th class="text-center">Obtener Recibo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pagosPendientes as $index => $pago)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $pago->modulo }}</td>
                                        <td class="text-center"> {{ \Carbon\Carbon::parse($pago->fechaCreacion)->format('d-m-Y') }}</td>
                                        <td class="text-center">$<?php echo number_format($pago->valor,0) ?></td>
                                        <td class="text-center">
                                            <a href="{{ url('impuestos/Pagos/'.$pago->id) }}" title="Ver Detalle" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <td class="text-center">
                                            @if($pago->modulo == "ICA-Contribuyente")
                                                <a href="{{ url('impuestos/ICA/contri/update/'.$pago->entity_id) }}" title="Corregir" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-edit"></i></a>
                                            @elseif($pago->modulo == "ICA-AgenteRetenedor")
                                                <a href="{{ url('impuestos/ICA/retenedor/update/'.$pago->entity_id) }}" title="Corregir" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-edit"></i></a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($pago->modulo == "ICA-Contribuyente")
                                                <a href="{{ url('impuestos/ICA/contri/pdf/'.$pago->entity_id) }}" target="_blank" title="Descargar Recibo" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                            @elseif($pago->modulo == "PREDIAL")
                                                <a href="{{ url('impuestos/PREDIAL/pdf/'.$pago->entity_id) }}" target="_blank" title="Descargar Recibo" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                            @elseif($pago->modulo == "ICA-AgenteRetenedor")
                                                <a href="{{ url('impuestos/ICA/retenedor/pdf/'.$pago->entity_id) }}" target="_blank" title="Descargar Recibo" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-danger">
                                <center>
                                    No tiene pagos pendientes.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>
                <div id="tabHistorico" class="tab-pane fade"><br>
                    <br>
                    <div class="table-responsive">
                        @if(count($pagosHistoricos) > 0)
                            <table class="table table-bordered" id="tabla_historicos">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Formulario</th>
                                    <th class="text-center">Fecha Presentación</th>
                                    <th class="text-center">Fecha de Pago</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Ver</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pagosHistoricos as $index => $pago)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $pago->modulo }}</td>
                                        <td class="text-center"> {{ \Carbon\Carbon::parse($pago->fechaCreacion)->format('d-m-Y') }}</td>
                                        <td class="text-center"> {{ \Carbon\Carbon::parse($pago->fechaPago)->format('d-m-Y') }}</td>
                                        <td class="text-center">$<?php echo number_format($pago->valor,0) ?></td>
                                        <td class="text-center">
                                            <a href="{{ url('impuestos/ICA/PDF/'.$pago->id) }}" title="Ver Factura" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-danger">
                                <center>
                                    No hay pagos anteriores registrados en el sistema.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')

    <script>
        $('#tabla_pagos').DataTable( {
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing":"Procesando...",
            },
            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
        $('#tabla_historicos').DataTable( {
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing":"Procesando...",
            },
            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
