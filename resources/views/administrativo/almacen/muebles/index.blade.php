@extends('layouts.dashboard')
@section('titulo')
    Bienes, Muebles e Inmuebles
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Bienes, Muebles e Inmuebles</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/muebles/create') }}">Nuevo Comprobante de Ingreso</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/salida/create') }}">Nuevo Comprobante de Salida</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($items) > 0)
                    <table class="table table-bordered" id="tabla_INV">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Núm Factura</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Descripcion</th>
                            <th class="text-center">Avaluo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Fecha Ingreso</th>
                            <th class="text-center">Fecha Salida</th>
                            <th class="text-center">Factura</th>
                            <th class="text-center">Comprobante</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr class="text-center">
                                <td>{{ $item->id }}</td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if($item->tipo == "0")
                                            Entrada
                                        @else($item->tipo == "1")
                                            Salida
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $item->num_factura }}</td>
                                <td><a href="{{ url('administrativo/productos/'.$item->producto_id) }}" title="Ver Producto"><span class="badge badge-pill badge-danger">{{ $item->producto->nombre }}</span></a></td>
                                <td>{{ $item->descripcion }}</td>
                                <td>
                                    @if(isset($item->avaluo))
                                        $<?php echo number_format($item->avaluo ,0) ?>
                                    @endif
                                </td>
                                <td><?php echo number_format($item->cantidad ,0) ?></td>
                                <td>{{ $item->fecha_ing }}</td>
                                <td>{{ $item->fecha_baja }}</td>
                                <td>
                                    @if(isset($item->ruta))
                                        <a href="{{Storage::url('Muebles/'.$item->ruta)}}" title="Ver Factura" class="btn btn-success"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('administrativo/muebles/'.$item->id) }}" title="Comprobante" class="btn btn-success"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun movimiento en los bienes, muebles e inmuebles.
                        </center>
                    </div>
                @endif
            </div>
        </div>
        @stop
        @section('js')
            <script>
                $('#tabla_INV').DataTable( {
                    responsive: true,
                    "searching": true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'print'
                    ]
                } );
            </script>
        @stop