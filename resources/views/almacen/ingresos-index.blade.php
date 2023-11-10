@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Almacen Entradas</b></h4>
        </strong>
    </div>
    <div class="row">
        <div class="btn-group" role="group" aria-label="Basic example">
            <a class="btn btn-primary" href="{{route('almacen.comprobante.ingreso.create')}}">Nuevo</a>
            <a class="btn btn-primary" href="{{route('almacen.entrada.items')}}">Items</a>
        </div>
        <br>
        
            <table class="table">
                <thead>
                    <th>Entrada</th>
                    <th>Fecha</th>
                    <th>Contrato</th>
                    <th>Fecha Contrato</th>
                    <th>Factura</th>
                    <th>Fecha Factura</th>
                    <th>Provedoor</th>
                    <th>Ver</th>
                </thead>
                <tbody>
                    @if($entradas->count() > 0)
                        @foreach($entradas as $entrada)
                                <tr>
                                    <td>{{$entrada->nombre}}</td>
                                    <td>{{$entrada->fecha}}</td>
                                    <td>{{$entrada->contrato}}</td>
                                    <td>{{$entrada->fecha_contrato}}</td>
                                    <td>{{$entrada->factura}}</td>
                                    <td>{{$entrada->fecha_factura}}</td>
                                    <td>{{$entrada->proovedor->nombre}}</td>
                                    <td>
                                        <a href="{{ route('almacen.comprobante.ingreso.pdf', $entrada->id)}}" class="btn btn-primary"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a> </br>
                                    </td>
                                </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No tiene Entradas </td></tr>
                    @endif
                </tbody>
            </table>

        
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