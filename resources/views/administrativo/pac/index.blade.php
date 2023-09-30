@extends('layouts.dashboard')
@section('titulo')
    PAC
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>PAC</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-calendar-check-o"></i></a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/pac/create') }}"><i class="fa fa-plus"></i>&nbsp;Nuevo PAC</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($data) > 0)
                    <table class="table table-bordered" id="tabla_PAC">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Rubro</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Valor a Asignar</th>
                            <th class="text-center">Total Asignado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $val)
                            <tr class="text-center">
                                <td>{{ $val['id'] }}</td>
                                <td>{{ $val['rubro'] }}</td>
                                <td>{{ $val['name'] }}</td>
                                <td>$<?php echo number_format($val['valorD'],0) ?></td>
                                <td>$<?php echo number_format($val['totalD'],0) ?></td>
                                <td><a href="{{ url('administrativo/pac/'.$val['id'].'/edit') }}" title="Editar" class="btn btn-success"><i class="fa fa-edit"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun PAC almacenado en la plataforma.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @stop
    @section('js')
        <script>
            $('#tabla_PAC').DataTable( {
                responsive: true,
                "searching": true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            } );
        </script>
    @stop