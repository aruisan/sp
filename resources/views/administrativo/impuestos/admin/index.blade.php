@extends('layouts.dashboard')
@section('titulo') Administracion de Impuestos @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Administracion de Impuestos</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#tabUsersPred">Usuarios Predial</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabPagos">Pagos</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabRIT">RIT</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabComunicados">Comunicados</a></li>
    </ul>

    <div class="tab-content">
        <div id="tabUsersPred" class="tab-pane fade in active">
            <br>
            <div class="table-responsive">
                @if(count($usersPredial) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">Número Catastral</th>
                            <th class="text-center">Número Identificación</th>
                            <th class="text-center">Contribuyente</th>
                            <th class="text-center">Area Terreno</th>
                            <th class="text-center">Dirección Predio</th>
                            <th class="text-center">Dirección Notificación</th>
                            <th class="text-center">Municipio</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Valor Deuda</th>
                            <th class="text-center">Años Deuda</th>
                            <th class="text-center">Editar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($usersPredial as $index => $predUser)
                            <tr>
                                <td class="text-center">{{ $predUser->numCatastral }}</td>
                                <td class="text-center">{{ $predUser->numIdent }}</td>
                                <td class="text-center">{{ $predUser->contribuyente }}</td>
                                <td class="text-center">{{ $predUser->areaTerreno }}</td>
                                <td class="text-center">{{ $predUser->dir_predio }}</td>
                                <td class="text-center">{{ $predUser->dir_notificacion }}</td>
                                <td class="text-center">{{ $predUser->municipio }}</td>
                                <td class="text-center">{{ $predUser->email }}</td>
                                <td class="text-center">$<?php echo number_format($predUser->valor_deuda,0) ?></td>
                                <td class="text-center">{{ $predUser->años_deuda}}</td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/impuestos/admin/predial/user/edit/'.$predUser->id) }}" title="Editar Usuario" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay registro de usuarios de predial.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabPagos" class="tab-pane fade">
            <br>
            <div class="table-responsive">
                @if(count($pagos) > 0)
                    <table class="table table-bordered" id="tabla_pagos">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Formulario</th>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Correo Usuario</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha Presentación</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Formulario</th>
                            <th class="text-center">Fecha Pago</th>
                            <th class="text-center">Comprobante Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagos as $index => $pago)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $pago->modulo }}</td>
                                <td class="text-center">{{ $pago->user->name }}</td>
                                <td class="text-center">{{ $pago->user->email }}</td>
                                <td class="text-center">{{ $pago->estado }}</td>
                                <td class="text-center"> {{ \Carbon\Carbon::parse($pago->fechaCreacion)->format('d-m-Y') }}</td>
                                <td class="text-center">$<?php echo number_format($pago->valor,0) ?></td>
                                <td class="text-center">
                                    @if($pago->modulo == "ICA-Contribuyente")
                                        <a href="{{ url('impuestos/ICA/contri/form/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @elseif($pago->modulo == "PREDIAL")
                                        <a href="{{ url('impuestos/PREDIAL/form/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @elseif($pago->modulo == "ICA-AgenteRetenedor")
                                        <a href="{{ url('impuestos/ICA/retenedor/form/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado == "Pagado")
                                        {{ \Carbon\Carbon::parse($pago->fechaPago)->format('d-m-Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado == "Pagado")
                                        <a href="" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-usd"></i></a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay pagos de impuestos registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabRIT" class="tab-pane fade">
            <div class="table-responsive">
                <br>
                @if(count($rits) > 0)
                    <table class="table table-bordered" id="tabla_RIT">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Usuario Creador</th>
                            <th class="text-center">Correo Usuario</th>
                            <th class="text-center">Nombre Contribuyente</th>
                            <th class="text-center">Fecha Radicación</th>
                            <th class="text-center">Ultimo Estado</th>
                            <th class="text-center">RUT</th>
                            <th class="text-center">Camara de Comercio</th>
                            <th class="text-center"><i class="fa fa-file-pdf-o"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rits as $index => $rit)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $rit->user->name }}</td>
                                <td class="text-center">{{ $rit->user->email }}</td>
                                <td class="text-center">{{ $rit->apeynomContri }}</td>
                                <td class="text-center"> {{ \Carbon\Carbon::parse($rit->radicacion)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $rit->opciondeUso }}</td>
                                <td class="text-center">
                                    @if($rit->rut_resource_id)
                                        <a href="" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rit->cc_resource_id)
                                        <a href="" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                                <td class="text-center"><a href="" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay RITs registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabComunicados" class="tab-pane fade">
            <div class="table-responsive">
                <br>
                @if(count($comunicados) > 0)
                        <a href="{{ url('administrativo/impuestos/comunicado/create') }}" class="btn btn-primary btn-block m-b-6"><i class="fa fa-plus"></i>
                            <i class="fa fa-envelope"></i> NUEVO COMUNICADO</a>
                        <br><br>
                    <table class="table table-bordered" id="tabla_Comunicados">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Enviado</th>
                            <th class="text-center">Destinatario</th>
                            <th class="text-center">Remitente</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center"><i class="fa fa-eye"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($comunicados as $index => $comunicado)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($comunicado->enviado)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $comunicado->destinatario->name}} - {{$comunicado->destinatario->email}}</td>
                                <td class="text-center">{{ $comunicado->remitente->name}} - {{$comunicado->remitente->email}}</td>
                                <td class="text-center"> {{ $comunicado->estado }}</td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/impuestos/comunicado/'.$comunicado->id) }}" title="Ver Comunicado" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay comunicados registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $('#tabla_CDP').DataTable( {
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
            //para usar los botones

            responsive: "true",
            "ordering": true,
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
            //para usar los botones

            responsive: "true",
            "ordering": true,
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

        $('#tabla_RIT').DataTable( {
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
            //para usar los botones

            responsive: "true",
            "ordering": true,
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

        $('#tabla_Comunicados').DataTable( {
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
            //para usar los botones

            responsive: "true",
            "ordering": true,
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
