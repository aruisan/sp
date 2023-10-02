@extends('layouts.dashboard')
@section('titulo') Delineación y Urbanismo @stop
@section('content')
    @include('modal.impuestos.pagodelineacion')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Delineación y Urbanismo</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Delineación y Urbanismo Pendiente de Pago</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">Delineación y Urbanismo Pagados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/administrativo/impuestos/admin">Administración de Impuestos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/impuestos/delineacion/create') }}"><i class="fa fa-plus"></i>
                <i class="fa fa-home"></i>NUEVO REGISTRO</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <div class="table-responsive">
                @if(count($delineacionPend) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">Registro de impuesto No.</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Tramite</th>
                            <th class="text-center">Tipo de Tramite</th>
                            <th class="text-center">No. Matricula</th>
                            <th class="text-center">No. Catastral</th>
                            <th class="text-center">Ver Formulario</th>
                            <th class="text-center">Cargar Constancia de Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($delineacionPend as $index => $del)
                            <tr>
                                <td class="text-center">{{$del->numRegistroIngreso}}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($del->fecha)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $del->tramite }}</td>
                                <td class="text-center">{{ $del->tipoTramite }}</td>
                                <td class="text-center">{{ $del->matricula }}</td>
                                <td class="text-center">{{ $del->idCatastral }}</td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/impuestos/delineacion/'.$del->id) }}" title="Ver formulario" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    <button onclick="getModalPago({{$del->numRegistroIngreso}}, {{$del->id}})" class="btn btn-danger"><i class="fa fa-usd"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay registros de delineacion registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade"><br>
            <div class="table-responsive">
                @if(count($delineacionPay) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">Registro de impuesto No.</th>
                            <th class="text-center">Fecha Impuesto</th>
                            <th class="text-center">Fecha Pago</th>
                            <th class="text-center">Valor Impuesto</th>
                            <th class="text-center">Ver Formulario</th>
                            <th class="text-center">Ver Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($delineacionPay as $del)
                            <tr>
                                <td class="text-center">{{ $del->numRegistroIngreso }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($del->fecha)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($del->fechaPago)->format('d-m-Y') }}</td>
                                <td class="text-center">$<?php echo number_format($del->valorPago,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/impuestos/delineacion/'.$del->id) }}" title="Ver registro" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    <a href="{{Storage::url($del->rutaFile)}}" target="_blank" title="Ver" class="btn btn-success"><i class="fa fa-eye"></i>&nbsp;<i class="fa fa-usd"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay registros de delineación y urbanismo pagados
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>

        function getModalPago(numRefPago, id){
            $('#formPagoDelineacion').modal('show');
            $('#regIngreso').val(numRefPago);
            $('#regId').val(id);
            document.getElementById("regIngresoText").innerHTML = numRefPago;
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

        $('#tabla_Historico').DataTable( {
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
