@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $añoActual }}
@stop
@section('content')
    @if($V != "Vacio")
        @include('modal.Informes.reporte')
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            <li class="nav-item principal">
                <a class="nav-link"  href="#editar"> Presupuesto de Ingresos {{ $añoActual + 1 }}</a>
            </li>
            <li class="nav-item pillPri">
                <a class="nav-link "  href="{{ url('/presupuesto') }}">Presupuesto de Egresos {{ $añoActual - 1 }}</a>
            </li>
    @if($V != "Vacio")
        {{--
        <li class="dropdown">
            <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Informes&nbsp;<i class="fa fa-caret-down"></i></a>
            <ul class="dropdown-menu ">
                <li class="dropdown-submenu">
                    <a class="test btn btn-drop text-left" href="#">Contractual &nbsp;</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('/presupuesto/informes/contractual/homologar/'.$V) }}" class="btn btn-drop text-left">Homologar</a></li>
                        <li><a data-toggle="modal" data-target="#reporteHomologar" class="btn btn-drop text-left">Reporte</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="btn btn-drop text-left">FUT </a>
                </li>
                <li>
                    <a href="{{ url('/presupuesto/informes/lvl/1') }}" class="btn btn-drop text-left">Niveles</a>
                </li>
                <li>
                    <a href="#" class="btn btn-drop text-left">Comparativo (Ingresos - Gastos)</a>
                </li>
                <li>
                    <a href="#" class="btn btn-drop text-left">Fuentes</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Historico&nbsp;<i class="fa fa-caret-down"></i></a>
            <ul class="dropdown-menu ">
                @foreach($years as $year)
                    <li>
                        <a href="{{ url('/presupuesto/historico/'.$year['id']) }}" class="btn btn-drop text-left">{{ $year['info'] }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        --}}
    @endif
    @if($V == "Vacio")
        <li class="nav-item pillPri">
            <a href="{{ url('/presupuesto/vigencia/create/1') }}" class="btn btn-drop">
                <i class="fa fa-plus"></i>
                <span class="hide-menu"> Nuevo Presupuesto de Ingresos</span></a>
        </li>
    @endif
</ul>
<div class="col-md-12 align-self-center">
    @if($V != "Vacio")
        <div class="row" >
            <div class="breadcrumb col-md-12 text-center" >
                <strong>
                    <h4><b>Presupuesto de Ingresos {{ $añoActual }}</b></h4>
                </strong>
            </div>
        </div>
        <ul class="nav nav-pills">
            <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
            </li>
        </ul>
        <div class="tab-content" style="background-color: white">
            <div id="tabHome" class="tab-pane active"><br>
                <div class="table-responsive">
                    <div class="text-center" id="cargando" style="display: none">
                        <h4>Buscando informacion para cargar el presupuesto...</h4>
                    </div>
                    <div class="text-center" id="noFind" style="display: none">
                        <h4>Se esta realizando la carga del presupuesto, intenta nuevamente en unos minutos por favor.</h4>
                    </div>
                    <div class="text-center" id="refresPrep" style="display: none">
                        <h4>Se esta enviando la solicitud de actualización del presupuesto, un momento por favor....</h4>
                    </div>
                    <div class="text-center" id="refresPrepOK" style="display: none">
                        <h4>Se envió la solicitud de actualización del presupuesto exitosamente, en unos minutos
                            actualice la pagina para visualizar el estado actual del presupuesto.</h4>
                    </div>
                    <div class="text-center" id="infoPrep" style="display: none"></div>
                    <table class="table table-hover table-bordered" align="100%" id="tabla" style="text-align: center">
                        <thead>
                        <tr>
                            <th class="text-center">Rubro</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Inicial</th>
                            <th class="text-center">Adición</th>
                            <th class="text-center">Reducción</th>
                            <th class="text-center">Anulados</th>
                            <th class="text-center">Definitivo</th>
                            <th class="text-center">Total Recaudado</th>
                            <th class="text-center">Saldo Por Recaudar</th>
                            <th class="text-center">Fuente</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABLA DE COMPROBANTES DE INGRESOS -->

            <div id="tabFuente" class="tab-pane fade"><br>
                <div class="table-responsive">
                    @if(count($comprobanteIng) >= 1)
                        <a href="{{ url('administrativo/CIngresos/'.$V) }}" class="btn btn-primary btn-block m-b-12">Comprobantes de Contabilidad</a>
                        <br><br>
                        <table class="table table-bordered" id="tabla_CIng">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Concepto</th>
                                <th class="text-center">Ver</th>
                                <th class="text-center">PDF</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($comprobanteIng as $key => $data)
                                <tr>
                                    <td class="text-center">{{ $data->code }}</td>
                                    <td class="text-center">{{ $data->ff }}</td>
                                    <td class="text-center">{{ $data->concepto }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('administrativo/CIngresos/'.$data->id.'/edit') }}" title="Ver Comprobante de Contabilidad" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('administrativo/CIngresos/pdf/'.$data->id) }}" target="_blank" title="Generar PDF" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <br>
                        <div class="alert alert-danger">
                            <center>
                                No hay comprobantes de contabilidad.<br><br>
                                <a href="{{ url('administrativo/CIngresos/create/'.$V) }}" class="btn btn-danger ">Crear Comprobante de Contabilidad</a>
                            </center>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TABLA DE RUBROS -->

            <div id="tabRubros" class="tab-pane fade"><br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabla_Rubros_Ingresos">
                        <thead>
                        <tr>
                            <th class="text-center">Rubro</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rubros as  $Rubro)
                            <tr>
                                <td>{{ $Rubro['cod'] }}</td>
                                <td>{{ $Rubro['name'] }}</td>
                                <td class="text-center">
                                    <a href="{{ url('presupuesto/rubro/'.$Rubro['id']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABLA DE PAC -->

            <div id="tabPAC" class="tab-pane fade"><br>
                <div class="table-responsive">
                    @if(count($PACdata) > 0)
                        <table class="table table-bordered" id="tabla_PAC">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor a Asignar</th>
                                <th class="text-center">Total Asignado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($PACdata as $val)
                                <tr class="text-center">
                                    <td>{{ $val['id'] }}</td>
                                    <td>{{ $val['rubro'] }}</td>
                                    <td>{{ $val['name'] }}</td>
                                    <td>$<?php echo number_format($val['valorD'],0) ?></td>
                                    <td>$<?php echo number_format($val['totalD'],0) ?></td>
                                    <td><a href="{{ url('administrativo/pac/'.$val['id'].'/edit') }}" title="Editar" class="btn btn-success"><i class="fa fa-edit"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <br><br>
                        <div class="alert alert-danger">
                            <center>
                                No se encuentra ningun PAC almacenado en la plataforma.
                                <!-- para crearlo de click al siguiente link: <a href="{{ url('administrativo/pac/create') }}" class="alert-link">Crear PAC</a>. -->
                            </center>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @else
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Presupuesto de Ingresos Año {{ $añoActual }}</b></h4>
            </strong>
        </div>
        <br><br>
        <div class="alert alert-danger">
            No se ha creado un presupuesto actual de ingresos, para crearlo de click al siguiente link:
            <a href="{{ url('presupuesto/vigencia/create/1') }}" class="alert-link">Crear Presupuesto de Ingresos</a>.
        </div>
    @endif
</div>
</div>
@stop
@section('js')
<!-- Datatables personalizadas buttons-->
<script src="{{ asset('/js/datatableCustom.js') }}"></script>
<script>

    const vigencia_id = @json($V);
    const prepSaved = @json($prepSaved);
    const añoPrep = @json(\Carbon\Carbon::parse($fechaData)->year);
    const mesPrep = @json(\Carbon\Carbon::parse($fechaData)->month);
    const diaPrep = @json(\Carbon\Carbon::parse($fechaData)->day);

    window.onload = function () {
        findPrep();
    };

    function findPrep(){
        $("#cargando").show();
        $("#noFind").hide();
        $("#infoPrep").hide();
        $("#refresPrepOK").hide();

        var table = $('#tabla').DataTable();

        $.ajax({
            method: "POST",
            url: "/presupuesto/getPrepSaved",
            data: { "id": vigencia_id, "prepSaved": prepSaved,
                "_token": $("meta[name='csrf-token']").attr("content"),
            }
        }).done(function(datos) {
            $("#tabla").show();
            table.destroy();
            $("#infoPrep").show();
            $("#cargando").hide();
            $("#noFind").hide();

            table = $('#tabla').DataTable( {
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
                        className: 'btn btn-primary',
                        title: 'Presupuesto Ingresos '+añoPrep+'-'+mesPrep+'-'+diaPrep
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
                    { title: "Rubro", data: "rubroLink"},
                    { title: "Nombre", data: "nombre"},
                    { title: "Inicial", data: "p_inicial"},
                    { title: "Adición", data: "adicion"},
                    { title: "Reducción", data: "reduccion"},
                    { title: "Anulados", data: "credito"},
                    { title: "Definitivo", data: "ccredito"},
                    { title: "Total Recaudado", data: "p_def"},
                    { title: "Saldo Por Recaudar", data: "cdps"},
                    { title: "Fuente", data: "fuente"},
                ]
            } );

        }).fail(function() {
            $("#tabla").hide();
            table.destroy();
            $("#cargando").hide();
            $("#noFind").show();
        });
    }

    function getModalToMakeInforme(){
        $('#modalMakeInforme').modal('show');
    }

$('#tabla_CIng').DataTable( {
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