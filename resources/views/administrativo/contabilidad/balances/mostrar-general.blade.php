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
                <a class="btn btn-danger" href="{{route('balance-general.pdf', [$age, $mes, 'vista'])}}">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                Balance General Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}<span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @foreach($meses as $m => $mes_)
                        <li><a href="{{route('balance-general.pdf', [$age, $m+1, 'mostrar_pdf'])}}">{{$mes_}}</a></li>
                    @endforeach
                </ul> 
            </div>
        </div>
    </div>
    <div class="row"> 
    <center>
    <embed src="{{route('balance-general.pdf', [$age, $mes, 'pdf'])}}" 
               width="1200"
               height="700">
    </center>
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