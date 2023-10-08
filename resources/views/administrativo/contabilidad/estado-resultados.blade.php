@extends('layouts.dashboard')
@section('titulo')
    Balance de General
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <div class="btn-group">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    Estado de Resultados Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        @foreach($meses as $m => $mes_)
                            <li><a href="{{route('estado-resultado', [$age, $m+1, 'vista'])}}">{{$mes_}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <a class="btn btn-danger pull-right" href="{{route('estado-resultado', [$age, $mes, 'mostrar_pdf'])}}">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>
            </divingresos
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$ingresos->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$ingresos->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    {!!$ingresos->format_hijos_general_vista!!}
                </tbody>
            </table>
        </div>    
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$gastos->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$gastos->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    {!!$gastos->format_hijos_general_vista!!}
                </tbody>
            </table> 
        </div>    
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3 text-left"><b>Totales:</b></div>
                            <div class="col-md-6"><b></b></div>
                            <div class="col-md-3 text-right"><b>${{number_format($ingresos->s_final,0,",", ".")}}</b></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="col-md-9 text-left" ><b>Resultado del ejercicio periodo</b></div>
                            <div class="col-md-3 text-right"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="col-md-3 text-left"><b>Sumas Iguales:</b></div>
                            <div class="col-md-6"><b></b></div>
                            <div class="col-md-3 text-right"><b>${{number_format($ingresos->s_final,0,",", ".")}}</b></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>    
        <div class="col-md-6">
            <table class="table">
                    <tr>
                        <td>
                            <div class="col-md-3"></div>
                            <div class="col-md-6"></div>
                            <div class="col-md-3 text-right"><b>${{number_format($gastos->s_final,0,",", ".")}}</b></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="col-md-3"><b></b></div>
                            <div class="col-md-6"><b></b></div>
                            <div class="col-md-3 text-right"><b>${{number_format($ingresos->s_final,0,",", ".")}}</b></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="col-md-3"><b></b></div>
                            <div class="col-md-6"><b></b></div>
                            <div class="col-md-3 text-right"><b>${{number_format($ingresos->s_final,0,",", ".")}}</b></div>
                        </td>
                    </tr>
                </tbody>
            </table> 
        </div>    
    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {
          let tbl =  $('#tabla').DataTable({
                fixedHeader: {
                    header: true,
                    footer: true
                },
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "sProcessing": "Procesando...",
                },
                "columnDefs": [
                    {
                        "targets": [4,5,8,9,12,13],
                        "visible": false,
                        "searchable": false
                    }
                ],
                pageLength: 2000,
                responsive: true,
                "searching": true,
                ordering: false,
                dom: 'Bfrtip',
                
            })
        })


    </script>
@stop