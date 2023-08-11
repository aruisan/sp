@extends('layouts.dashboard')
@section('titulo')
    Chip Contable
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4>
                @php 
                    $mes = ($trimestre*3)+3;
                    $fecha_final = date("t", strtotime("2023-{$mes}-01")) @endphp
                    <b>Chip Contable Trimestre {{$trimestre+1}} del año {{$age}} (1 de {{$meses[($trimestre*3)]}} al {{$fecha_final}} de {{$meses[($trimestre*3)+2]}} del {{$age}})</b>
                    <a class="btn btn-danger pull-right" href="{{route('chip.contable.actualizacion', [$age, $trimestre])}}"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                </h4>
            </strong>
        </div>
        <div class="table-responsive">
            <br>
            <table class="table" id="tabla">
                <tbody>
                    <tr>
                        <td>S</td>
                        <td>216488564</td>
                        <td>10103</td>
                        <td>10103</td>
                        <td>2023</td>
                        <td>CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA</td>
                        <td>2023</td>
                        <td>CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @if(in_array('Administrador', auth()->user()->getRoleNames()->toArray()))
                            <td>Ver</td>
                        @endif  
                    </tr>
                @foreach($pucs as $puc)
                    {!!$puc!!}
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
                        "targets": [3,6,7,9,12,13],
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
                        titleAttr: 'Chip Contable primer trimestre 2023-01-01 - 2023-03-31',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0,1,3,6,7,9,12,13]
                        }
                    },
                    {
                            extend:    'pdfHtml5',
                            text:      '<i class="fa fa-file-pdf-o"></i> ',
                            titleAttr: 'Chip Contable primer trimestre 2023-01-01 - 2023-03-31',
                            exportOptions: {
                                columns: [0,1,3,6,7,9,12,13]
                            },
                            className: 'btn btn-primary',
                            customize : function(doc){ 
                                doc.content[1].table.widths = [55,190,75,75,75,75,75,75]; //120 440 7 costringe le colonne ad occupare un dato spazio per gestire il baco del 100% width che non si concretizza mai
                            }
                        },
                ]
            })
        })
    </script>
@stop