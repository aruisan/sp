@extends('layouts.dashboard')
@section('titulo')
    Radicación de Cuentas
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Radicación de Cuentas</b></h4>
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
            <a class="tituloTabs" href="{{ url('/administrativo/radCuentas/create/'.$id) }}">NUEVA RADICACIÓN</a>
        </li>
        <li class="nav-item pillPri">
            <a class="tituloTabs" href="{{ url('/administrativo/registros/'.$id) }}">REGISTROS</a>
        </li>
        <li class="nav-item pillPri">
            <a class="tituloTabs" href="{{ url('/administrativo/ordenPagos/'.$id) }}">ORDENES DE PAGO</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabTareas" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($radCuentasPend) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Persona</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($radCuentasPend as $radPend)
                            <tr class="text-center">
                                <td>{{ $radPend->code }}</td>
                                <td>{{ $radPend->persona->num_dc }} - {{ $radPend->persona->nombre }}</td>
                                <td>{{ $radPend->created_at }}</td>
                                <td><a href="{{ url('administrativo/radCuentas/'.$radPend->id.'/2') }}" class="btn-sm btn-info" title="Paso 2">2</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay radiciones de cuentas pendientes.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade"><br>
            <div class="table-responsive">
                @if(count($radCuentasHist) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Num Ident Tercero</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($radCuentasHist as $radicacion)
                            <tr class="text-center">
                                <td>{{ $radicacion }}</td>
                                <td>{{ $radicacion }}</td>
                                <td>{{ $radicacion }}</td>
                                <td>{{ $radicacion }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay radicaciones de cuentas finalizadas.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        @stop
        @section('js')
            <script>
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

            </script>
@stop