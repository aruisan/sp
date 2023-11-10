@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Almacen Entradas Items</b></h4>
        </strong>
    </div>
    <div class="row">
        <div class="btn-group" role="group" aria-label="Basic example">
            <a class="btn btn-primary" href="{{route('almacen.comprobante.egreso.index')}}">Comprobantes de Salidas</a>
        </div>
        <br>
        <br>
            <table class="table">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Nombre del Articulddo</th>
                    <th class="text-center">Codigo</th>
                    <th class="text-center">Marca</th>
                    <th class="text-center">Presentación</th>
                    <th class="text-center">Referencia</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Valor Unitario</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">ccd</th>
                    <th class="text-center">ccc</th>
                    <th class="text-center">Comprobante de Salida</th>
                </tr>
                </thead>
                <tbody>
                    @if($salidas->count() > 0)
                        @foreach($salidas as $salida)
                            @foreach($salida->salidas_pivot as $item)
                            <tr class="text-center">
                                <td>{{ $item->articulo->index }}</td>
                                <td>{{ $item->articulo->nombre_articulo}}</td>
                                <td>{{ $item->articulo->codigo }}</td>
                                <td>{{ $item->articulo->marca }}</td>
                                <td>{{ $item->articulo->presentacion }}</td>
                                <td>{{ $item->articulo->referencia}}</td>
                                <td>{{ $item->cantidad}}</td>
                                <td>{{ $item->articulo->valor_unitario}}</td>
                                <td>{{ $item->total}}</td>
                                <td>{{ $item->articulo->puc_ccd->code}}</td>
                                <td>{{ $salida->ccd}}</td>
                                <td><a href="{{ route('almacen.egreso.show', $salida->id)}}" target="_blank">{{ $salida->nombre}}</a></td>
                            </tr>
                            @endforeach
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No tiene Salidas</td></tr>
                    @endif
                </tbody>
            </table>

        
    </div>
@stop
@section('js')
    <script>
        $('.table').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop