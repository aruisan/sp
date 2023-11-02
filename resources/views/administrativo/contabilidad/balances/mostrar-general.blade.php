@extends('layouts.dashboard')
@section('titulo')
    Balance de General
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            @include('administrativo.contabilidad.components.select')
        </div>
    </div>
    <div class="row"> 
    <center>
    <embed src="{{route('balance-general.pdf', [$age, $elemento, $tipo, 'pdf'])}}" 
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


        const enviar = vista => {
            let y = $('#year').val();
            let p = $('#periodo').val();
            let e = -1;
            if(p != 'anual'){
                e = $('#elementos').val();
            }
            location.href =`/administrativo/contabilidad/blance-general-pdf/${y}/${e}/${p}/${vista}`;
        }


    </script>
    @include('administrativo.contabilidad.components.select_js')
@stop