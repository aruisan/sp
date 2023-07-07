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
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Balance Prueba Mes {{$meses[Session::get('mes-informe-inicial')]}}
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="{{route('balance.prueba', '01')}}">Enero</a></li>
                    <li><a href="{{route('balance.prueba', '02')}}">Febrero</a></li>
                    <li><a href="{{route('balance.prueba', '03')}}">Marzo</a></li>
                    <li><a href="{{route('balance.prueba', '04')}}">Abril</a></li>
                    <li><a href="{{route('balance.prueba', '05')}}">Mayo</a></li>
                    <li><a href="{{route('balance.prueba', '06')}}">Junio</a></li>
                </ul>
            </div>
            </strong>
        </div>
        <div class="table-responsive">
            <br>
            <table class="table" id="tabla">
                <thead>
                    <tr>
                        <th colspan="14" class="text-center"><b>Balance Prueba {{ $añoActual }}-{{ $mesActual }}-{{ $diaActual }}</b></th>
                    </tr>
                        {{--
                    <tr>
                        <th colspan="2" class="text-center"><b></b></th>
                        <th colspan="2" class="text-center"><b>Balance Inicial</b></th>
                        <th colspan="2" class="text-center"><b>Balance Movimientos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Saldos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Inicial</b></th>
                        <th colspan="2" class="text-center"><b>Balance Movimientos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Saldos</b></th>
                    </tr>
                        --}}
                    <tr>
                        <th class="text-center">Codigo</th>
                        <th class="text-center">Concepto</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        <th class="text-center">Debito</th>
                        <th class="text-center">Credito</th>
                        {{--
                        --}}
                    </tr>
                </thead>
                <tbody>
                @foreach($pucs as $puc)
                            @php 
                                $debito = $puc->naturaleza == "DEBITO" ? $puc->v_inicial : 0;
                                $credito= $puc->naturaleza != "DEBITO" ? $puc->v_inicial : 0;
                                $m_debito = $puc->m_debito;
                                $m_credito = $puc->m_credito;
                                $s_debito = $puc-> naturaleza == "DEBITO" ? $debito + $m_debito - $m_credito:0;
                                $s_credito = $puc-> naturaleza == "CREDITO" ? $credito + $m_credito - $m_debito: 0;
                            @endphp
                    <tr>
                        <td class="text-left">{{$puc->code}}</td>
                        <td class="text-center">{{$puc->concepto}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($debito  ,0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($credito ,0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$debito}}</td>
                        <td class="text-right" style="width=200px;">{{$credito}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($m_debito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($m_credito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$m_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$m_credito}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($s_debito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($s_credito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$s_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$s_credito}}</td>
                        {{--
                        <td>{{$puc->naturaleza}}</td>
                        <td>{{$puc->saldo_inicial}}</td>
                        <td>{{is_null($puc->padre) ? 'no tiene' : $puc->padre->code}}</td>
                        <td>{{$puc->hijos->pluck('id')}}</td>
                        --}}
                    </tr>
                    {!!$puc['format_hijos_prueba']!!}
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
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> ',
                        titleAttr: 'Balance Inicial {{$añoActual}} - {{$mesActual}} - {{$diaActual}}',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0,1,4,5,8,9,12,13]
                        }
                    },
                    {
                            extend:    'pdfHtml5',
                            text:      '<i class="fa fa-file-pdf-o"></i> ',
                            titleAttr: 'Balance Inicial {{$añoActual}} - {{$mesActual}} - {{$diaActual}}',
                            exportOptions: {
                                columns: [0,1,4,5,8,9,12,13]
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