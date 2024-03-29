@extends('layouts.dashboard')
@section('titulo')
    Notas Credito
@stop
@section('sidebar')
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Notas Creditos {{ $añoActual }}</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuestoIng') }}" >Presupuesto de Ingresos</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/tesoreria/notasCredito/create/') }}" >NUEVA NOTA CREDITO</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active">
            <div class="table-responsive">
                @if(count($notas) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Objeto</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notas as $index => $nota)
                            <tr>
                                <td class="text-center">{{ $nota->code }}</td>
                                <td class="text-center">{{ $nota->concepto }}</td>
                                <td class="text-center">{{ $nota->fecha }}</td>
                                <td class="text-center">$<?php echo number_format($nota->valor,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/tesoreria/notasCredito/show/'.$nota->id) }}" title="Ver Nota Credito" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay Notas Creditos Registradas en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')

    <script type="text/javascript" >

        $(document).ready(function(){

            $('.nav-tabs a[href="#tabTareas"]').tab('show')
        });

    </script>

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

    </script>
@stop
