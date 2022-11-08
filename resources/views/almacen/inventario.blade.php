@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Inventario</b></h4>
        </strong>
    </div>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            <div class="table-responsive">
                @if($articulos->count() > 0)
                    <table class="table table-bordered" id="tabla_INV">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nombre del Articulo</th>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Referencia</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Valor Unitario</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">No. Factura</th>
                            <th class="text-center">Proovedor</th>
                            <th class="text-center">ccd</th>
                            <th class="text-center">ccc</th>
                            <th class="text-center">Comprobante de Ingreso</th>
                            <th class="text-center">Comprobante de Egreso</th>
                            <th class="text-center">Dependencia</th>
                            <th class="text-center">Responsable</th>
                            <th class="text-center">Estado</th>
                            <th>Opcion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articulos as $key => $item)
                            <tr class="text-center">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->nombre_articulo}}</td>
                                <td>{{ $item->codigo }}</td>
                                <td>{{ $item->referencia}}</td>
                                <td>{{ $item->cantidad}}</td>
                                <td>{{ $item->stock}}</td>
                                <td>{{ $item->valor_unitario}}</td>
                                <td>{{ $item->total}}</td>
                                <td>{{ $item->factura->numero_factura}}</td>
                                <td>{{ $item->factura->proovedor->nombre}}</td>
                                <td>{{ $item->ccd}}</td>
                                <td>{{ $item->ccc}}</td>
                                <td></td>
                                <td>
                                    <a class="btn btn-success" href="{{route('almacen.edit', $item->id)}}">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun movimiento en el inventario.
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