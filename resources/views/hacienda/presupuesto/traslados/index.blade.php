@extends('layouts.dashboard')
@section('titulo') Movimientos {{ $año }}@stop
@section('content')
    @include('modal.objetoCDP')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Movimientos {{ $año }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuesto') }}" >
                Volver a Presupuesto</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link hidden" href="{{ url('/presupuesto/traslados/'.$año.'/create/') }}"><i class="fa fa-plus"></i> NUEVO TRASLADO</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabHome" class="tab-pane fade in active"><br>
            <br>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="table-responsive">
                @if(count($traslados) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Presupuesto</th>
                            <th class="text-center">Ver</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($traslados as $index => $traslado)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($traslado->created_at)->format('Y-m-d') }}</td>
                                <td class="text-center">$<?php echo number_format($traslado->valor,0) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($traslado->movimiento == "1")
                                            Traslado
                                        @elseif($traslado->movimiento == "2")
                                            Adición
                                        @elseif($traslado->movimiento == "3")
                                            Reducción
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($traslado->Vigencia->tipo == "0")
                                            Egresos
                                        @elseif($traslado->Vigencia->tipo == "1")
                                            Ingresos
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('presupuesto/traslados/show/'.$traslado->id) }}" title="Ver Traslado" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay traslados en el año actual
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

            $('.nav-tabs a[href="#tabHome"]').tab('show')
        });
    </script>

    <script>
        $('#tabla_Historico').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[1, 'desc']],
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
