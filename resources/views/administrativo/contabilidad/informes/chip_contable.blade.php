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
                <h4><b>Balance Prueba </b></h4>
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
                        <td></td>
                        <td>2023</td>
                        <td>CGN2015_001_SALDOS_Y_MOVIMIENTOS_CONVERGENCIA</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @foreach($pucs as $puc)
                            @php 
                                $m_debito = $puc->m_debito;
                                $m_credito = $puc->m_credito;
                                $s_final = $puc-> naturaleza == "DEBITO" ? $puc->v_inicial + $m_debito - $m_credito:$puc->v_inicial + $m_credito - $m_debito;
                            @endphp
                    <tr>
                        <td class="text-left">D</td>
                        <td class="text-center">{{$puc->codigo_punto}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($puc->v_inicial  ,0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$puc->v_inicial}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($m_debito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($m_credito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$m_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$m_credito}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($s_final, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$s_final}}</td>
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
                        "targets": [3,6,7,9],
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
                        titleAttr: 'Balance Inicial {{$añoActual}} - {{$mesActual}} - {{$diaActual}}',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0,1,3,6,7,9]
                        }
                    },
                    {
                            extend:    'pdfHtml5',
                            text:      '<i class="fa fa-file-pdf-o"></i> ',
                            titleAttr: 'Balance Inicial {{$añoActual}} - {{$mesActual}} - {{$diaActual}}',
                            exportOptions: {
                                columns: [0,1,3,6,7,9]
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