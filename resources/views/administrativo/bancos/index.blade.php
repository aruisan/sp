@extends('layouts.dashboard')
@section('titulo')
    Bancos
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Bancos</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-bank"></i></a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos/create') }}"><i class="fa fa-plus"></i>&nbsp;Nuevo Banco</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($items) > 0)
                    <table class="table table-bordered" id="tabla_Banks">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Número Cuenta</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Valor Actual</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr class="text-center">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->numero_cuenta }}</td>
                                <td>{{ $item->descripcion }}</td>
                                <td>$<?php echo number_format($item->valor_actual,0) ?></td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($item->estado == "0")
                                            Activa
                                        @else
                                            Inactiva
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('administrativo/bancos/'.$item->id) }}" title="Ver" class="btn btn-success"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/bancos/'.$item->id.'/edit') }}" title="Editar" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun banco almacenado en la plataforma.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @stop
    @section('js')
        <script>
            $('#tabla_Banks').DataTable( {
                responsive: true,
                "searching": true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            } );
        </script>
    @stop