@extends('layouts.dashboard')
@section('titulo')
    Balance de Prueba
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <div class="btn-group">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    Balance General Mes {{$meses[$mes-1]}} <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        @foreach($meses as $m => $mes_)
                            <li><a href="{{route('balance-general.pdf', [$age, $m+1, 'vista'])}}">{{$mes_}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <a class="btn btn-danger pull-right" href="{{route('balance-general.pdf', [$age, $mes, 'pdf'])}}">
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
                        <td><b>{{$activo->puc_alcaldia->codigo_punto}} - {{$activo->puc_alcaldia->concepto}}</b></td>
                    </tr>
                    {!!$activo->format_hijos_general_vista!!}
                </tbody>
            </table>
        </div>    
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td><b>{{$pasivo->puc_alcaldia->codigo_punto}} - {{$pasivo->puc_alcaldia->concepto}}</b></td>
                    </tr>
                    {!!$pasivo->format_hijos_general_vista!!}
                    <tr></tr>
                    <tr></tr>
                    <tr>
                        <td><b>{{$patrimonio->puc_alcaldia->codigo_punto}} - {{$patrimonio->puc_alcaldia->concepto}}</b></td>
                    </tr>
                    {!!$patrimonio->format_hijos_general_vista!!}
                </tbody>
            </table> 
        </div>    
    </div>

    <div class="row">
        <div class="col-md-2">
            <h3>Sumas Iguales:</h3>
        </div>    
        <div class="col-md-5">
            <center>
                <h3>{{$activo->s_final}}</h3>
            </center>
        </div>  
        <div class="col-md-5">
            <center>
                <h3>{{$pasivo->s_final + $patrimonio->s_final}}</h3>
            </center>
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