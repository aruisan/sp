@extends('layouts.dashboard')
@section('titulo') CDP's @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>CDP's</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuesto') }}" >
                Volver a Presupuesto</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">PENDIENTES</a>
        </li>
        @if(count($cdProcess) > 0)
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#tabProcess">EN PROCESO</a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">HISTORICO</a>
        </li>
        @if( $rol == 2)
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/administrativo/cdp/create/'.$vigencia_id) }}" >NUEVO CDP</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <br>
            <div class="table-responsive">
                @if(count($cdpTarea) > 0)
                    <form class="form-valide" action="{{url('/administrativo/cdp/check')}}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="countCDPs" value="{{count($cdpTarea)}}">
                        <table class="table table-bordered" id="tabla_CDP">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Objeto</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Estado Secretaria</th>
                                <th class="text-center">Estado Alcalde</th>
                                <th class="text-center">Estado Jefe</th>
                                <th class="text-center">Valor</th>
                                @if($rol == 2)
                                    <th class="text-center"><i class="fa fa-usd"></i></th>
                                    <th class="text-center"><i class="fa fa-edit"></i></th>
                                @elseif ($rol == 3)
                                    <th class="text-center">Ver</th>
                                    <th class="text-center"><label>Aprobar &nbsp;<input type="checkbox" onclick="approve(this.checked, {{count($cdpTarea)}}, {{$cdpTarea}})"></label> </th>
                                @elseif ($rol == 5)
                                    <th class="text-center">Ver</th>
                                    <th class="text-center"><label>Aprobar &nbsp;<input type="checkbox" onclick="approve(this.checked, {{count($cdpTarea)}}, {{$cdpTarea}})"></label></th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cdpTarea as $index => $cdp)
                                <tr>
                                    <td class="text-center">{{ $cdp->code }}</td>
                                    <td class="text-center">{{ $cdp->name }}</td>
                                    <td class="text-center">{{ $cdp->tipo }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($cdp->secretaria_e == "0")
                                                Pendiente
                                            @elseif($cdp->secretaria_e == "1")
                                                Rechazado
                                            @elseif($cdp->secretaria_e == "2")
                                                Anulado
                                            @else
                                                Enviado
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($cdp->alcalde_e == "0")
                                                Pendiente
                                            @elseif($cdp->alcalde_e == "1")
                                                Rechazado
                                            @elseif($cdp->alcalde_e == "2")
                                                Anulado
                                            @else
                                                Enviado
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($cdp->jefe_e == "0")
                                                Pendiente
                                            @elseif($cdp->jefe_e == "1")
                                                Rechazado
                                            @elseif($cdp->jefe_e == "2")
                                                Anulado
                                            @elseif($cdp->jefe_e == "3")
                                                Aprobado
                                            @else
                                                En Espera
                                            @endif
                                        </span>
                                    </td>
                                    @if($cdp->valor == 0)
                                        <td class="text-center">$<?php echo number_format($cdp->rubrosCdpValor->sum('valor_disp'),0) ?></td>
                                    @else
                                        <td class="text-center">$<?php echo number_format($cdp->valor,0) ?></td>
                                    @endif
                                    @if($rol == 2)
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id) }}" title="Ingresar Dinero al CDP" class="btn-sm btn-primary"><i class="fa fa-usd"></i></a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id.'/edit') }}" title="Editar CDP" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                        </td>
                                    @elseif($rol == 3)
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id) }}" title="Ver CDP" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <td class="text-center"><input type="checkbox" id="check{{$index}}" onclick="approveUnidad(this.checked, {{$index}}, {{$cdp->id}})"><input type="hidden" id="checkInput{{$index}}" name="checkInput{{$index}}"></td>
                                    @elseif($rol == 5)
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id) }}" title="Ver CDP" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <td class="text-center"><input type="checkbox" id="check{{$index}}" onclick="approveUnidad(this.checked, {{$index}}, {{$cdp->id}})"><input type="hidden" id="checkInput{{$index}}" name="checkInput{{$index}}"></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            <button class="btn btn-sm btn-primary">APROBAR CDPS</button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay CDP's pendientes.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabProcess" class="tab-pane fade"><br>
            <br>
            <div class="table-responsive">
                @if(count($cdProcess) > 0)
                    <table class="table table-bordered" id="tabla_Process">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Objeto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado Secretaria</th>
                            <th class="text-center">Fecha Envio Secretaria</th>
                            <th class="text-center">Estado Alcalde</th>
                            <th class="text-center">Estado Jefe</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Ver CDP</th>
                            <th class="text-center">Borrador</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cdProcess as $cdp)
                            <tr>
                                <td class="text-center">{{ $cdp->code }}</td>
                                <td class="text-center">{{ $cdp->name }}</td>
                                <td class="text-center">{{ $cdp->tipo }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($cdp->secretaria_e == "0")
                                            Pendiente
                                        @elseif($cdp->secretaria_e == "1")
                                            Rechazado
                                        @elseif($cdp->secretaria_e == "2")
                                            Anulado
                                        @else
                                            Enviado
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($cdp->ff_secretaria_e)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($cdp->alcalde_e == "0")
                                            Pendiente
                                        @elseif($cdp->alcalde_e == "1")
                                            Rechazado
                                        @elseif($cdp->alcalde_e == "2")
                                            Anulado
                                        @else
                                            Enviado - {{ \Carbon\Carbon::parse($cdp->ff_alcalde_e)->format('d-m-Y') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($cdp->jefe_e == "0")
                                            Pendiente
                                        @elseif($cdp->jefe_e == "1")
                                            Rechazado
                                        @elseif($cdp->jefe_e == "2")
                                            Anulado
                                        @elseif($cdp->jefe_e == "3")
                                            Aprobado
                                        @else
                                            En Espera
                                        @endif
                                    </span>
                                </td>
                                @if($cdp->valor == 0)
                                    <td class="text-center">$<?php echo number_format($cdp->rubrosCdpValor->sum('valor_disp'),0) ?></td>
                                @else
                                    <td class="text-center">$<?php echo number_format($cdp->valor,0) ?></td>
                                @endif
                                <td class="text-center">
                                    <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id) }}" title="Ver" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/cdp/pdfBorrador/'.$cdp['id'].'/'.$vigencia_id) }}" target="_blank" title="File" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade">
            <div class="table-responsive">
                @if(count($cdps) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Objeto</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Ver</th>
                            <th class="text-center">PDF</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cdps as $cdp)
                            <tr>
                                <td class="text-center">{{ $cdp->code }}</td>
                                <td class="text-center">{{ $cdp->name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($cdp->jefe_e == "0")
                                            Pendiente
                                        @elseif($cdp->jefe_e == "1")
                                            Rechazado
                                        @elseif($cdp->jefe_e == "2")
                                            Anulado
                                        @elseif($cdp->jefe_e == "3")
                                            Aprobado
                                        @else
                                            En Espera
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">$<?php echo number_format($cdp->valor,0) ?></td>
                                <td class="text-center">$<?php echo number_format($cdp->saldo,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/cdp/'.$vigencia_id.'/'.$cdp->id) }}" title="Ver CDP" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    @if($cdp->jefe_e == "2")
                                        <span class="badge badge-pill badge-danger">Anulado</span>
                                    @else
                                        <a href="{{ url('administrativo/cdp/pdf/'.$cdp['id'].'/'.$vigencia_id) }}" target="_blank" title="File" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay CDP's finalizados
                        </center>
                    </div>
                @endif
            </div>
        </div>

    </div>
@stop
@section('js')

    <script type="text/javascript" >

        $(document).ready(function(){

            $('.nav-tabs a[href="#tabTareas"]').tab('show')
        });

    </script>

    <script>

        function approve(value, num, cdps){
            if (value == true){
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = cdps[i]['id'];
                }
            } else{
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = null;
                }
            }
        }

        function approveUnidad(value, num, cdpId){
            var id = "checkInput"+num;
            if (value == true){
                document.getElementById(id).value = cdpId;
            } else {
                document.getElementById(id).value = null;
            }
        }

        $('#tabla_CDP').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons:[
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-clone"></i> ',
                    titleAttr: 'Copiar',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> ',
                    titleAttr: 'Exportar a PDF',
                    message : 'SIEX-Providencia',
                    header :true,
                    orientation : 'landscape',
                    pageSize: 'LEGAL',
                    className: 'btn btn-primary',
                },
                {
                    extend:    'print',
                    text:      '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary'
                },
        ]
        } );

        $('#tabla_Historico').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_Process').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop
