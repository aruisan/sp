@extends('layouts.dashboard')
@section('titulo') Administracion de Impuestos @stop
@section('content')
    @include('modal.impuestos.constanciapagoadmin')
    @include('modal.impuestos.pazysalvo')
    @include('modal.impuestos.sml')
    @include('modal.impuestos.uvt')
    @include('modal.impuestos.usd')
    @include('modal.impuestos.confirmarpago')
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
        <li class="nav-item"><a class="nav-link" href="/administrativo/impuestos/muellaje">Muellaje</a></li>
        <li class="nav-item"><a class="nav-link" href="/administrativo/impuestos/delineacion">Delineación y Urbanismo</a></li>
        <li class="dropdown-submenu">
            <a class="dropdown-item item-menu"><i class="fa fa-cogs"></i></a>
            <ul class="dropdown-menu">
                <li><a class="item-menu" style="cursor: pointer" onclick="getModalSML()">SALARIO MINIMO</a></li>
                <li><a class="item-menu" style="cursor: pointer" onclick="getModalUVT()">UVT</a></li>
                <li><a class="item-menu" style="cursor: pointer" onclick="getModalUSD()">USD</a></li>
            </ul>
        </li>
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
                            <th class="text-center">Dirección Predio</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Avaluo 2023</th>
                            <th class="text-center">Editar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($usersPredial as $index => $predUser)
                            <tr>
                                <td class="text-center">{{ $predUser->numCatastral }}</td>
                                <td class="text-center">{{ $predUser->numIdent }}</td>
                                <td class="text-center">{{ $predUser->contribuyente }}</td>
                                <td class="text-center">{{ $predUser->dir_predio }}</td>
                                <td class="text-center">{{ $predUser->email }}</td>
                                <td class="text-center">$<?php echo number_format($predUser->a2023,2) ?></td>
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
                            <th class="text-center">Cargar Pago</th>
                            <th class="text-center">Eliminar Formulario</th>
                            <th class="text-center">Confirmar Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagos as $index => $pago)
                            <tr>
                                <td class="text-center">{{ $pago->id }}</td>
                                <td class="text-center">{{ $pago->modulo }}</td>
                                <td class="text-center">
                                    @if($pago->modulo == "MUELLAJE")
                                        Embarcación: {{ $pago->detalleBarco->name }}
                                    @else
                                        {{ $pago->user->name }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $pago->user->email }}</td>
                                <td class="text-center">{{ $pago->estado }}</td>
                                <td class="text-center"> {{ \Carbon\Carbon::parse($pago->fechaCreacion)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    @if($pago->modulo == "MUELLAJE")
                                        USD $<?php echo number_format($pago->detalleBarco->valorPago,2) ?><br>
                                        @if($pago->detalleBarco->valorDolar)
                                            COP $<?php echo number_format($pago->detalleBarco->valorPago * $pago->detalleBarco->valorDolar,2) ?>
                                       @endif
                                    @else
                                        $<?php echo number_format($pago->valor,0) ?>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->modulo == "ICA-Contribuyente")
                                        <a href="{{ url('impuestos/ICA/contri/form/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @elseif($pago->modulo == "PREDIAL")
                                        <a href="{{ url('impuestos/PREDIAL/pdf/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @elseif($pago->modulo == "ICA-AgenteRetenedor")
                                        <a href="{{ url('impuestos/ICA/retenedor/form/'.$pago->entity_id) }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                    @elseif($pago->modulo == "MUELLAJE")
                                        <a href="{{ url('administrativo/impuestos/muellaje/'.$pago->entity_id.'/formulario/pdf') }}" target="_blank" title="Descargar Formulario" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
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
                                        @if($pago->Resource)
                                            <a href="{{Storage::url($pago->Resource->ruta)}}" target="_blank" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-usd"></i></a>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado != "Pagado")
                                        <button onclick="getModalPago('{{$pago->modulo}}', '{{\Carbon\Carbon::parse($pago->fechaCreacion)->format('d-m-Y')}}','$<?php echo number_format($pago->valor,0) ?>', {{$pago->id}})" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-arrow-up"></i><i class="fa fa-usd"></i></button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado != "Pagado")
                                        <button onclick="eliminarPago('{{$pago->id}}')" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-trash"></i></button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->estado == "Pagado" and $pago->confirmed == "FALSE")
                                        @if($pago->modulo == "ICA-Contribuyente" or $pago->modulo == "PREDIAL" or $pago->modulo == "MUELLAJE")
                                            <button onclick="confirmarPago('{{$pago->id}}','{{ \Carbon\Carbon::parse($pago->fechaPago)->format('Y-m-d') }}')" class="btn btn-sm btn-primary-impuestos">Confirmar Pago</button>
                                        @endif
                                    @elseif($pago->estado == "Pagado" and $pago->confirmed == "TRUE")
                                        CONFIRMADO
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
                                        @if($rit->ResourceRUT)
                                            <a href="{{ Storage::url($rit->ResourceRUT->ruta) }}" target="_blank" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                        @else
                                            Revisar Archivo
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rit->cc_resource_id)
                                        @if($rit->ResourceCC)
                                            <a href="{{ Storage::url($rit->ResourceCC->ruta) }}" target="_blank" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a>
                                        @else
                                            Revisar Archivo
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center"><a href="/impuestos/RIT/{{$rit->user->id}}" target="_blank" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-file-pdf-o"></i></a></td>
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
            <div class="table-responsive text-center">
                <br>
                <!-- <a href="{{ url('administrativo/impuestos/comunicado/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i><i class="fa fa-envelope"></i> NUEVO COMUNICADO</a><br> -->
                @if(count($comunicados) > 0)
                    <br>
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
                    <br><br>
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

        $('.findUserPred').select2();

        //VALIDACION DE LOS DINEROS A TOMAR NO SEAN SUPERIORES DE LOS PERMITIDOS POR LA FUENTE
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("pazysalvoform").addEventListener('submit', descargarPazySalvo);
        });

        function eliminarPago(id){
            var opcion = confirm("ESTA SEGURO DE ELIMINAR EL PAGO JUNTO CON EL CORRESPONDIENTE FORMULARIO?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/impuestos/Pagos/deletePay",
                    data: { "payId": id,
                        "_token": $("meta[name='csrf-token']").attr("content"),
                    }
                }).done(function(response) {
                    if (response == "OK"){
                        toastr.warning('PAGO Y SU FORMULARIO ELIMINADO');
                        location. reload();
                    } else {
                        toastr.warning('EL PAGO NO EXISTE. ACTUALICE LA PAGINA.');
                    }

                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL ELIMINAR EL PAGO Y SU FORMULARIO.');
                });
            }

        }

        function confirmarPago(id, fecha){
            $('#pago_id').val(id);
            $('#fechaComp').val(fecha);
            $('#formConfirmarPago').modal('show');
        }

        function getModalUVT(){
            $('#formUVT').modal('show');
        }

        function getModalSML(){
            $('#formSML').modal('show');
        }

        function getModalUSD(){
            $('#formUSD').modal('show');
        }

        function descargarPazySalvo(evento) {
            evento.preventDefault();

            const payid = document.getElementById("paySelected").value;

            $.ajax({
                method: "POST",
                url: "/impuestos/Pagos/validatePay",
                data: { "payId": payid,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(response) {
                if (response == "OK"){
                    var opcion = confirm("SOLO SE PUEDE GENERAR UN PAZ Y SALVO POR USUARIO, ESTA SEGURO DE GENERARLO?");
                    if (opcion == true) {
                        console.log(payid);
                        window.open('/impuestos/Pagos/certPyS/'+payid, '_blank');
                        return;
                    }
                } else toastr.warning('YA SE HA GENERADO EL PAZ Y SALVO DE ESE USUARIO. NO SE PUEDE GENERAR UNO NUEVO');

            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL VALIDAR SI SE PUEDE GENERAR EL PAZ Y SALVO.');
            });

            //this.submit();
        }

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
                {
                    text: '<i onclick="getModalPazySalvo()">Generar Paz y Salvo</i>',
                    titleAttr: 'Paz y Salvo',
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

        function getModalPago(form, fecha, value, id){
            $('#formConstanciaPago').modal('show');
            $('#regId').val(id);
            document.getElementById("form").innerHTML = form;
            document.getElementById("fecha").innerHTML = fecha;
            document.getElementById("value").innerHTML = value;
        }

        function getModalPazySalvo(){
            $('#formPazySalvo').modal('show');
        }
    </script>
@stop
