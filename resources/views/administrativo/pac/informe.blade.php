@extends('layouts.dashboard')
@section('titulo')
    Balance de Prueba
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Informe pac {{$tipo}} 2023</b></h4>
            </strong>
        </div>
        <div class="table-responsive">
            <br>
            <table class="table" id="tabla">
                <thead>
                    <tr>
                        <th class="text-center">Codigo</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Inicial</th>
                        @foreach($meses as $mes)
                            <th class="text-center">{{$mes}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($pacs as $pac)
                    <tr>
                        <td>{{$pac->codigo}}</td>
                        <td>{{$pac->nombre}}</td>
                        <td>{{$pac->inicial}}</td>
                        @foreach($meses as $mes)
                            <td>{{$pac->v_mes}}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
          let tbl =  $('#tabla').DataTable({
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
                        "targets": [4,5],
                        "visible": false,
                        "searchable": false
                    }
                ],
                pageLength: 2000,
                responsive: true,
                "searching": true,
                ordering: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> ',
                        titleAttr: 'Pac {{$tipo}} 2023',
                        className: 'btn btn-primary',
                        /*
                        exportOptions: {
                            columns: [0, 1, 4,5]
                        }*/
                    },
                    /*
                    {
                            extend:    'pdfHtml5',
                            text:      '<i class="fa fa-file-pdf-o"></i> ',
                            titleAttr: 'Pac {{$tipo}} 2023',
                            className: 'btn btn-primary',
                            exportOptions: {
                                columns: [0, 1, 2,3]
                            },*/
                            /*
                            customize : function(doc){ 
                                doc.content[1].table.widths = [65,260,90,90,90,90]; //costringe le colonne ad occupare un dato spazio per gestire il baco del 100% width che non si concretizza mai
                            }
                        },
                        */
                ]
            })
        })


    </script>
@stop