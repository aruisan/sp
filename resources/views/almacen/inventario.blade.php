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
                                <td>{{ $item->comprobante_ingreso->factura}}</td>
                                <td>{{ $item->comprobante_ingreso->proovedor->nombre}}</td>
                                <td>{{ $item->comprobante_ingreso->ccd}}</td>
                                <td>{{ $item->comprobante_ingreso->ccc}}</td>
                                <td><a href="{{ route('almacen.ingreso.show', $item->comprobante_ingreso->id)}}" target="_blank">{{ $item->comprobante_ingreso->id}}</a></td>
                                <td>
                                    @foreach($item->comprobante_egresos as $egreso)
                                        - <a href="{{ route('almacen.egreso.show', $egreso->id)}}" target="_blank">{{ $egreso->id}}</a> </br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($item->comprobante_egresos as $egreso)
                                        - {{$egreso->dependencia->name}} </br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($item->comprobante_egresos as $egreso)
                                        - {{$egreso->responsable->nombre}} </br>
                                    @endforeach
                                </td>
                                <td>{{ $item->estado}}</td>
                                <td>
                                    <a class="btn btn-success" href="{{route('almacen.articulo.mantenimiento', $item->id)}}" title="Mantenimientos"><i class="fa fa-cogs" aria-hidden="true"></i></a>
                                    <a class="btn btn-success" href="{{route('almacen.edit', $item->id)}}" title="Editar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
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