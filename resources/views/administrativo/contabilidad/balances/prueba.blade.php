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
                    Balance Prueba Mes {{$meses[Session::get(auth()->id().'-mes-informe-contable-mes')]}} <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('balance.pre-prueba', '01')}}">Enero</a></li>
                        <li><a href="{{route('balance.pre-prueba', '02')}}">Febrero</a></li>
                        <li><a href="{{route('balance.pre-prueba', '03')}}">Marzo</a></li>
                        <li><a href="{{route('balance.pre-prueba', '04')}}">Abril</a></li>
                        <li><a href="{{route('balance.pre-prueba', '05')}}">Mayo</a></li>
                        <li><a href="{{route('balance.pre-prueba', '06')}}">Junio</a></li>
                        <li><a href="{{route('balance.pre-prueba', '07')}}">Julio</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        nivel {{Session::get(auth()->id().'-mes-informe-contable-nivel')}}<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('balance.prueba-nivel', [1,$informe->id])}}">Nivel 1</a></li>
                        <li><a href="{{route('balance.prueba-nivel', [2,$informe->id])}}">Nivel 2</a></li>
                        <li><a href="{{route('balance.prueba-nivel', [3,$informe->id])}}">Nivel 3</a></li>
                        <li><a href="{{route('balance.prueba-nivel', [4,$informe->id])}}">Nivel 4</a></li>
                        <li><a href="{{route('balance.prueba-nivel', [5,$informe->id])}}">Nivel 5</a></li>
                    </ul>
                </div>
                <a class="btn btn-danger pull-right" href="{{route('balance.prueba-informe-reload', $informe->id)}}"><i class="fa fa-refresh" aria-hidden="true"></i></a>
            </div>
        </div>
        <div class="table-responsive">
            <br>
            <table class="table" id="tabla">
                <thead>
                    <tr>
                        <th colspan="15" class="text-center"><b>Balance Prueba {{$meses[Session::get(auth()->id().'-mes-informe-contable-mes')]}}</b></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-center"><b>Puc</b></th>
                        <th colspan="2" class="text-center"><b>Balance Inicial</b></th>
                        <th colspan="2" class="text-center"><b>Balance Inicial</b></th>
                        <th colspan="2" class="text-center"><b>Balance Movimientos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Movimientos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Saldos</b></th>
                        <th colspan="2" class="text-center"><b>Balance Saldos</b></th>
                        @if(auth()->id() == 1)
                        <th class="text-center">movimientos</th>
                        @endif
                    </tr>
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
                        @if(auth()->id() == 1)
                        <th class="text-center">movimientos</th>
                        @endif
                        {{--
                        --}}
                    </tr>
                    
                </thead>
                <tbody>
                @foreach($pucs as $puc)
                            @php 
                            $s_debito = $puc->naturaleza == "DEBITO" ? $puc->i_debito + $puc->m_debito + $puc->a_debito - $puc->m_credito + $puc->a_credito: 0;
                            $s_credito = $puc->naturaleza == "CREDITO" ?  $puc->i_credito + $puc->m_credito + $puc->a_credito - $puc->m_debito + $puc->a_debito: 0;
                            @endphp
                    <tr>
                    <td class="text-left">{{is_null($puc->puc_alcaldia) ? "Se Elimino {$puc->id}" : $puc->puc_alcaldia->code}}</td>
                        <td class="text-center">{{is_null($puc->puc_alcaldia) ? "Se Elimino {$puc->id}" : $puc->puc_alcaldia->concepto}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($puc->i_debito  ,0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($puc->i_credito ,0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$puc->i_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$puc->i_credito}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($puc->m_debito + $puc->a_debito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($puc->m_credito + $puc->a_credito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$puc->m_debito + $puc->a_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$puc->m_credito + $puc->a_credito}}</td>

                        <td class="text-right" style="width=200px;">${{number_format($s_debito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">${{number_format($s_credito, 0,",", ".")}}</td>
                        <td class="text-right" style="width=200px;">{{$s_debito}}</td>
                        <td class="text-right" style="width=200px;">{{$s_credito}}</td>
                        @if(auth()->id() == 1)
                        <td class="text-right" style="width=200px;">
                            @if(!is_null($puc->puc_alcaldia) )
                                @if($puc->puc_alcaldia->level == 5)
                                    <a class="btn btn-primary" href='{{route("chip.contable.puc.ver", $puc->puc_alcaldia->id)}}' target="_blank">Movimientos</a>
                                @endif
                            @endif
                        </td>
                        @endif
                    </tr>
                    {!!$puc->format_hijos_prueba!!}
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><b>Sumas Iguales</b></td>
                        <td><b>${{number_format($pucs->sum('i_debito')  ,0,",", ".")}}</b></td>
                        <td><b>${{number_format($pucs->sum('i_credito')  ,0,",", ".")}}</b></td>
                        <td><b>{{$pucs->sum('i_debito')}}</b></td>
                        <td><b>{{$pucs->sum('i_credito')}}</b></td>

                        <td><b>${{number_format($pucs->sum('m_debito') + $pucs->sum('a_debito')  ,0,",", ".")}}</b></td>
                        <td><b>${{number_format($pucs->sum('m_credito') + $pucs->sum('a_credito')   ,0,",", ".")}}</b></td>
                        <td><b>{{$pucs->sum('m_debito')}}</b></td>
                        <td><b>{{$pucs->sum('m_credito')}}</b></td>

                        <td><b>${{number_format($pucs->sum('s_debito')  ,0,",", ".")}}</b></td>
                        <td><b>${{number_format($pucs->sum('s_credito')  ,0,",", ".")}}</b></td>
                        <td><b>{{$pucs->sum('s_debito')}}</b></td>
                        <td><b>{{$pucs->sum('s_credito')}}</b></td>
                        @if(auth()->id() == 1)
                        <td></td>
                        @endif
                    </tr>
                </tfoot>
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