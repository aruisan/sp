@extends('layouts.dashboard')
@section('titulo')
    Productos
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Productos</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/productos/create') }}">NUEVO PRODUCTO</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($items) > 0)
                    <table class="table table-bordered" id="tabla_PROD">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Metodo</th>
                            <th class="text-center">Cantidad Actual</th>
                            <th class="text-center">Valor Actual</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr class="text-center">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->nombre }}</td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($item->tipo == "0")
                                            Consumo
                                        @else($item->tipo == "1")
                                            Devolutivo
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                     <span class="badge badge-info">
                                         @if($item->metodo == "0")
                                             U.E.P.S
                                         @else($item->metodo == "1")
                                             P.E.P.S
                                         @endif
                                    </span>
                                </td>
                                <td><?php echo number_format($item->cant_actual,0) ?></td>
                                <td>$<?php echo number_format($item->valor_actual,0) ?></td>
                                <td>
                                    <a href="{{ url('administrativo/productos/'.$item->id.'/edit') }}" title="Editar" class="btn-sm btn-success"><i class="fa fa-edit"></i></a>
                                    <a href="{{ url('administrativo/productos/'.$item->id) }}" title="Ver" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun producto almacenado en la plataforma.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        @stop
        @section('js')
            <script>
                $('#tabla_PROD').DataTable( {
                    responsive: true,
                    "searching": true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'print'
                    ]
                } );
            </script>
        @stop