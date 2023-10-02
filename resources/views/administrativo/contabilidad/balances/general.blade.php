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
                        Periodo {{$periodos[0]}}<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @foreach($periodos as $periodo)
                                <li><a href="#">{{$periodo}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    Balance General Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        @foreach($meses as $m => $mes_)
                            <li><a href="{{route('balance-general.pdf', [$age, $m+1, 'vista'])}}">{{$mes_}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <a class="btn btn-danger pull-right" href="{{route('balance-general.pdf', [$age, $mes, 'mostrar_pdf'])}}">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$activo->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$activo->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    {!!$activo->format_hijos_general_vista!!}
                </tbody>
            </table>
        </div>    
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$pasivo->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$pasivo->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    {!!$pasivo->format_hijos_general_vista!!}
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$patrimonio->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$patrimonio->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    {!!$patrimonio->format_hijos_general_vista!!}
                    <tr>
                        <td>
                            <div class="col-md-3">{{$puc_opcional->puc_alcaldia->codigo_punto}}</div>
                            <div class="col-md-6">{{$puc_opcional->puc_alcaldia->concepto}}</div>
                            <div class="col-md-3 text-right">${{number_format($iguales_ingresos_gastos ,0,",", ".")}}</div>
                        </td>
                    </tr>
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
                            <div class="col-md-3"><b>Sumas Iguales:</b></div>
                            <div class="col-md-6"><b></b></div>
                            <div class="col-md-3 text-right"><b>${{number_format($activo->s_final,0,",", ".")}}</b></div>
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
                            <div class="col-md-3 text-right"><b>${{number_format($pasivo->s_final + $patrimonio->s_final + $iguales_ingresos_gastos,0,",", ".")}}</b></div>
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