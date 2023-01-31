@extends('layouts.dashboard')
@section('titulo')
    Notas Credito
@stop
@section('sidebar')
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Notas Creditos Vigencia {{ $vigencia->vigencia }}</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuestoIng') }}" >Presupuesto de Ingresos</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">TAREAS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">HISTORICO</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/CIngresos/create/'.$vigencia->id) }}" >NUEVA NOTA CREDITO</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active">
            <div class="table-responsive">
                @if(count($CIngresosT) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Objeto</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Valor Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($CIngresosT as $index => $CI)
                            <tr>
                                <td class="text-center">{{ $CI->code }}</td>
                                <td class="text-center">{{ $CI->concepto }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($CI->estado == "0")
                                            Pendiente
                                        @elseif($CI->estado == "1")
                                            Rechazado
                                        @elseif($CI->estado == "2")
                                            Anulado
                                        @else
                                            Enviado
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">$<?php echo number_format($CI->val_total,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/CIngresos/show/'.$CI->id) }}" title="Ver Comprobante de Ingreso" class="btn-sm btn-primary"><i class="fa fa-usd"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay Notas Creditos Pendientes.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade">
            <div class="table-responsive">
                @if(count($CIngresos) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Ver</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($CIngresos as $historico)
                            <tr>
                                <td class="text-center">{{ $historico->code }}</td>
                                <td class="text-center">{{ $historico->concepto }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($historico->estado == "0")
                                            Pendiente
                                        @elseif($historico->estado == "1")
                                            Rechazado
                                        @elseif($historico->estado == "2")
                                            Anulado
                                        @elseif($historico->estado == "3")
                                            Aprobado
                                        @else
                                            En Espera
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">$<?php echo number_format($historico->valor,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/CIngresos/show/'.$historico->id) }}" title="Ver Comprobante de Ingreso" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay Notas Credito finalizadas
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
        $('#tabla_CDP').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
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

    </script>
@stop
