@extends('layouts.dashboard')
@section('titulo') REGISTROS DE ATRAQUES @stop
@section('content')
    @include('modal.impuestos.pagomuellaje')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="breadcrumb text-center"><strong><h4><b>REGISTROS DE ATRAQUES</b></h4></strong></div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Atraques Pendientes de Pago</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">Atraques Pagados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/administrativo/impuestos/admin">Administración de Impuestos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/impuestos/muellaje/create') }}"><i class="fa fa-plus"></i>
                <i class="fa fa-ship"></i>NUEVO REGISTRO</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tabTareas" class="tab-pane fade in active">
            <br>
            <div class="table-responsive">
                @if(count($atraquesPend) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">Fecha Impuesto</th>
                            <th class="text-center">Registro de ingreso No.</th>
                            <th class="text-center">Nombre Embarcación</th>
                            <th class="text-center">Propietario Embarcación</th>
                            <th class="text-center">Bandera</th>
                            <th class="text-center">Tripulantes</th>
                            <th class="text-center">Tipo de Carga</th>
                            <th class="text-center">Eslora</th>
                            <th class="text-center">Nombre Naviera</th>
                            <th class="text-center">Rep Legal</th>
                            <th class="text-center">NIT/CC</th>
                            <th class="text-center">Rep del Impuesto</th>
                            <th class="text-center">Fecha Ingreso</th>
                            <th class="text-center">Fecha Salida</th>
                            <th class="text-center">Valor Impuesto</th>
                            <th class="text-center">Ver Registro</th>
                            <th class="text-center">Cargar Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($atraquesPend as $index => $atraque)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($atraque->fecha)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $atraque->numRegistroIngreso }}</td>
                                <td class="text-center">{{ $atraque->name }}</td>
                                <td class="text-center">{{ $atraque->nameCap }}</td>
                                <td class="text-center">{{ $atraque->bandera }}</td>
                                <td class="text-center">{{ $atraque->tripulantes }}</td>
                                <td class="text-center">{{ $atraque->tipoCarga }}</td>
                                <td class="text-center">{{ $atraque->piesEslora }}</td>
                                <td class="text-center">{{ $atraque->nameNaviera }}</td>
                                <td class="text-center">{{ $atraque->nameRep }}</td>
                                <td class="text-center">{{ $atraque->numIdent }}</td>
                                <td class="text-center">{{ $atraque->nameRepPago }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($atraque->fechaAtraque)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($atraque->fechaSalida)->format('d-m-Y') }}</td>
                                <td class="text-center">$<?php echo number_format($atraque->valorPago,0) ?></td>
                                <td class="text-center">
                                    <div>
                                        <!-- <a href="{{ url('administrativo/impuestos/muellaje/'.$atraque->id.'/formulario/pdf') }}" title="Formulario" target="_blank" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a> -->
                                        <a href="{{ url('administrativo/impuestos/muellaje/'.$atraque->id) }}" title="Ver registro" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                        <a href="{{ url('administrativo/impuestos/muellaje/edit/'.$atraque->id) }}" title="Editar registro" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                        <a onclick="eliminarRegistro('{{$atraque->id}}')" class="btn btn-sm btn-primary"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button onclick="getModalPago({{$atraque->numRegistroIngreso}}, {{$atraque->id}})" class="btn btn-danger"><i class="fa fa-usd"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay registro de atraques pendientes de pago.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade">
            <div class="table-responsive">
                @if(count($atraquesPay) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">Registro de ingreso No.</th>
                            <th class="text-center">Nombre Embarcación</th>
                            <th class="text-center">Fecha Impuesto</th>
                            <th class="text-center">Fecha Pago</th>
                            <th class="text-center">Valor Impuesto</th>
                            <th class="text-center">Ver Registro</th>
                            <th class="text-center">Ver Pago</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($atraquesPay as $atraque)
                            <tr>
                                <td class="text-center">{{ $atraque->numRegistroIngreso }}</td>
                                <td class="text-center">{{ $atraque->name }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($atraque->fecha)->format('d-m-Y') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($atraque->fechaPago)->format('d-m-Y') }}</td>
                                <td class="text-center">$<?php echo number_format($atraque->valorPago,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/impuestos/muellaje/'.$atraque->id) }}" title="Ver registro" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    <a href="{{Storage::url($atraque->rutaFile)}}" target="_blank" title="Ver" class="btn btn-success"><i class="fa fa-eye"></i>&nbsp;<i class="fa fa-usd"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay registros de atraques pagados
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

        function getModalPago(numRefPago, id){
            $('#formPagoMuellaje').modal('show');
            $('#regIngreso').val(numRefPago);
            $('#regId').val(id);
            document.getElementById("regIngresoText").innerHTML = numRefPago;
        }

        function eliminarRegistro(id){
            var opcion = confirm("ESTA SEGURO DE ELIMINAR EL REGISTRO?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/impuestos/muellaje/deleteMuellaje/"+id,
                    data: { "id": id,
                        "_token": $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function (result){
                        console.log(result);
                        toastr.success('REGISTRO ELIMINADO');
                        location. reload();
                    },
                    error: function (error){
                        console.log(error);
                        toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR EL REGISTRO.');
                    }
                });
            }

        }
    </script>
@stop
