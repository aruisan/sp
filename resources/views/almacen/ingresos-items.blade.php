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
            <a class="btn btn-primary" href="{{route('almacen.comprobante.ingreso.index')}}">Comprobantes de Entradas</a>
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
                    <th class="text-center">Comprobante de Entrada</th>
                </tr>
                </thead>
                <tbody>
                    @if($items->count() > 0)
                        @foreach($items as $item)
                        <tr class="text-center">
                            <td>{{ $item->index }}</td>
                            <td>{{ $item->nombre_articulo}}</td>
                            <td>{{ $item->codigo }}</td>
                            <td>{{ $item->marca }}</td>
                            <td>{{ $item->presentacion }}</td>
                            <td>{{ $item->referencia}}</td>
                            <td>{{ $item->cantidad}}</td>
                            <td>{{ $item->valor_unitario}}</td>
                            <td>{{ $item->total}}</td>
                            <td>{{ $item->puc_ccd->code}}</td>
                            <td>{{ $item->puc_ccd->almacen_puc_credito->code}}</td>
                            <td><a href="{{ route('almacen.ingreso.show', $item->comprobante_ingreso->id)}}" target="_blank">{{ $item->comprobante_ingreso->nombre}}</a></td>
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