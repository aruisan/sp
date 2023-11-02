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
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$ingresos->first()->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$ingresos->first()->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    @foreach($ingresos_h->groupBy('puc_alcaldia_id') as $hijo)
                    <tr>
                        <td>
                            <div class="col-md-3">{{$hijo->first()->puc_alcaldia->codigo_punto}}</div>
                            <div class="col-md-6">{{$hijo->first()->puc_alcaldia->concepto}}</div>
                            <div class="col-md-3">${{number_format($hijo->sum('s_final') ,0,",", ".")}}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>    
        <div class="col-md-6">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="col-md-3"><b>{{$gastos->first()->puc_alcaldia->codigo_punto}}</b></div>
                            <div class="col-md-6"><b>{{$gastos->first()->puc_alcaldia->concepto}}</b></div>
                            <div class="col-md-3"><b></b></div>
                        </td>
                    </tr>
                    @foreach($gastos_h->groupBy('puc_alcaldia_id') as $hijo)
                    <tr>
                        <td>
                            <div class="col-md-3">{{$hijo->first()->puc_alcaldia->codigo_punto}}</div>
                            <div class="col-md-6">{{$hijo->first()->puc_alcaldia->concepto}}</div>
                            <div class="col-md-3">${{number_format($hijo->sum('s_final') ,0,",", ".")}}</div>
                        </td>
                    </tr>
                    @endforeach
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
                            <div class="col-md-3 text-right"><b>${{number_format($ingresos->sum('s_final'),0,",", ".")}}</b></div>
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
                            <div class="col-md-3 text-right"><b>${{number_format($gastos->sum('s_final'),0,",", ".")}}</b></div>
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


        const enviar = vista => {
            let y = $('#year').val();
            let p = $('#periodo').val();
            let e = -1;
            if(p != 'anual'){
                e = $('#elementos').val();
            }
            location.href =`/administrativo/contabilidad/estado-resultado/${y}/${e}/${p}/${vista}`;
        }


    </script>
    @include('administrativo.contabilidad.components.select_js')
@stop