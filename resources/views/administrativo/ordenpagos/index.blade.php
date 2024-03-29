@extends('layouts.dashboard')
@section('titulo')
    Ordenes de Pago
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Ordenes de Pago</b></h4>
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
            <a class="tituloTabs" href="{{ url('/administrativo/ordenPagos/create/'.$id) }}">NUEVA ORDEN DE PAGO</a>
        </li>
        <li class="nav-item pillPri">
            <a class="tituloTabs" href="{{ url('/administrativo/pagos/'.$id) }}">PAGOS</a>
        </li>
        <li class="nav-item pillPri">
            <a class="tituloTabs" href="{{ url('/administrativo/registros/'.$id) }}">REGISTROS</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabTareas" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($ordenPagoTarea) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Num Ident Tercero</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ordenPagoTarea as $ordenPagoT)
                            <tr class="text-center">
                                <td>{{ $ordenPagoT->code }}</td>
                                <td>{{ $ordenPagoT->nombre }}</td>
                                <td>$<?php echo number_format($ordenPagoT->valor,0) ?></td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($ordenPagoT->estado == "0")
                                            Pendiente
                                        @elseif($ordenPagoT->estado == "1")
                                            Finalizado
                                        @else
                                            Anulado
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">{{ $ordenPagoT->registros->objeto }}</td>
                                <td class="text-center">{{ $ordenPagoT->registros->persona->nombre }}</td>
                                <td class="text-center">{{ $ordenPagoT->registros->persona->num_dc }}</td>
                                <td>
                                    <a href="{{ url('administrativo/ordenPagos/'.$ordenPagoT->id.'/edit') }}" title="Editar" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/show/'.$ordenPagoT->id) }}" title="Ver Orden de Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/monto/create/'.$ordenPagoT->id) }}" title="Asignación de Monto" class="btn-sm btn-primary"><i class="fa fa-usd"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/descuento/create/'.$ordenPagoT->id) }}" title="Descuentos" class="btn-sm btn-success"><i class="fa fa-usd"></i><i class="fa fa-arrow-down"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/liquidacion/create/'.$ordenPagoT->id) }}" title="Contabilización" class="btn-sm btn-primary"><i class="fa fa-calculator"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay ordenes de pago pendientes.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade"><br>
            <div class="table-responsive">
                @if(count($oPH) > 0)
                    <select name="terceroFind" id="terceroFind" class="terceroFindClass" style="width: 100%" onchange="findTercero()">
                        <option value="0">BUSQUEDA POR TERCERO</option>
                        @foreach($personas as $persona)
                            <option value="{{$persona->id}}">{{ $persona->num_dc }} - {{ $persona->nombre }}</option>
                        @endforeach
                    </select>
                    <br>
                    <table class="table table-bordered" id="tabla_Historico">
                        <br>
                        <div class="text-center" id="cargando" style="display: none">
                            <br><br><h4>Buscando informacion del tercero...</h4>
                        </div>
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Num Ident Tercero</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($oPH as $ordenPago)
                            <tr class="text-center">
                                <td>{{ $ordenPago['code'] }}</td>
                                <td>{{ $ordenPago['nombre'] }}</td>
                                <td>
                                    @if(isset($ordenPago->registros->cdpsRegistro))
                                        {{ $ordenPago->registros->persona->nombre }}
                                    @else
                                        DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN
                                    @endif
                                </td>
                                <td>
                                    @if(isset($ordenPago->registros->cdpsRegistro))
                                        {{ $ordenPago->registros->persona->num_dc }}
                                    @else
                                        800197268
                                    @endif
                                </td>
                                <td>$<?php echo number_format($ordenPago['valor'],0) ?></td>
                                <td>$<?php echo number_format($ordenPago['saldo'],0) ?></td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($ordenPago['estado'] == "0")
                                            Pendiente
                                        @elseif($ordenPago['estado'] == "1")
                                            Finalizada
                                        @else
                                            Anulada
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if(isset($ordenPago->registros->cdpsRegistro))
                                        <a href="{{ url('administrativo/ordenPagos/show/'.$ordenPago['id']) }}" title="Ver Orden de Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    @endif
                                    @if($ordenPago['estado'] == "1")
                                        <a href="{{ url('administrativo/ordenPagos/'.$ordenPago->id.'/edit') }}" title="Editar" class="btn-sm btn-success"><i class="fa fa-edit"></i></a>
                                        <a href="{{ url('administrativo/ordenPagos/pdf/'.$ordenPago['id']) }}" title="Orden de Pago" class="btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $oPH->links() }}
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay ordenes de pago finalizados.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        @stop
        @section('js')
            <script>
                $('.terceroFindClass').select2();

                $('#tabla_CDP').DataTable( {
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

                function findTercero(){
                    idTercero = document.getElementById("terceroFind").value;

                    $("#cargando").show();
                    var table = $('#tabla_Historico').DataTable();
                    $.ajax({
                        method: "POST",
                        url: "/administrativo/ordenPagos/findTercero",
                        data: { "id": idTercero, "vigencia_id": {{$id}},
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        }
                    }).done(function(datos) {
                        if (datos.length > 0){
                            console.log(datos);
                            $("#tabla_Historico").show();
                            table.destroy();
                            $("#cargando").hide();
                            table = $('#tabla_Historico').DataTable( {
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
                                "pageLength": 15,
                                responsive: true,
                                "searching": true,
                                ordering: false,
                                "lengthMenu": [ 10, 25, 50, 75, 100, "ALL" ],
                                dom: 'Bfrtip',
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
                                ],
                                data: datos,
                                columns: [
                                    { title: "#", data: "code"},
                                    { title: "Concepto", data: "nombre"},
                                    { title: "Tercero", data: "nombreTer"},
                                    { title: "Num Ident Tercero", data: "ccTer"},
                                    { title: "Valor", data: "valor"},
                                    { title: "Saldo", data: "saldo"},
                                    { title: "Estado", data: "estadoSpan"},
                                    { title: "Acciones", data: "acciones"},
                                ]
                            } );

                        } else {
                            $("#tabla_Historico").hide();
                            table.destroy();
                            $("#cargando").hide();
                            toastr.warning('NO SE OBTUVIERON DATOS DE ESE TERCERO, INTENTE NUEVAMENTE CON OTRO');
                        }
                    }).fail(function() {
                        $("#tabla_Historico").hide();
                        table.destroy();
                        $("#cargando").hide();
                        toastr.warning('NO SE OBTUVIERON DATOS DE ESA CUENTA');
                    });
                    console.log(idTercero);
                }

            </script>
@stop