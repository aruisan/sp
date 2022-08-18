@extends('layouts.dashboard')
@section('titulo') Liquidador @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Liquidador</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuesto') }}" >Volver a Presupuesto</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Liquidador</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/impuestospredial/liquidador/create') }}" >Nuevo Mes</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <br>
            <div class="table-responsive">
                @if(count($liquidadores) > 0)
                    <table class="table table-bordered" id="tabla_Liquidador">
                        <thead>
                        <tr>
                            <th class="text-center">Año</th>
                            <th class="text-center">Mes</th>
                            <th class="text-center">Fecha Vencimiento</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center"><i class="fa fa-trash"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($liquidadores as $index => $liquidador)
                            <tr>
                                <td class="text-center">{{ $liquidador->año }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($liquidador->mes == 0)
                                            Antes
                                        @elseif($liquidador->mes == 1)
                                            Enero
                                        @elseif($liquidador->mes == 2)
                                            Febrero
                                        @elseif($liquidador->mes == 3)
                                            Marzo
                                        @elseif($liquidador->mes == 4)
                                            Abril
                                        @elseif($liquidador->mes == 5)
                                            Mayo
                                        @elseif($liquidador->mes == 6)
                                            Junio
                                        @elseif($liquidador->mes == 7)
                                            Julio
                                        @elseif($liquidador->mes == 8)
                                            Agosto
                                        @elseif($liquidador->mes == 9)
                                            Septiembre
                                        @elseif($liquidador->mes == 10)
                                            Octubre
                                        @elseif($liquidador->mes == 11)
                                            Noviembre
                                        @else
                                            Diciembre
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">{{ $liquidador->vencimiento }}</td>
                                <td class="text-center">{{ $liquidador->valor }}%</td>
                                <td class="text-center">
                                    <form action="liquidador/{{ $liquidador->id }}" method="post">
                                        {{method_field('DELETE')}}
                                        {{ csrf_field() }}
                                        <div class="row text-center">
                                            <button class="btn btn-success text-center" title="Eliminar mes del liquidador"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay liquidadores almacenados.
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
        $('#tabla_Liquidador').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtilp',
            "ordering": false,
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
