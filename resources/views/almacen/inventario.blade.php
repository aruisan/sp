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
                    <table class="table table-bordered" id="otabla_INV">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nombre del Articulddo</th>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Presentación</th>
                            <th class="text-center">Referencia</th>
                            <th class="text-center">Entrada</th>
                            <th class="text-center">Salida</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Valor Unitario</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Vida Util</th>
                            <th class="text-center">Depreciación</th>
                            <th class="text-center">No. Factura</th>
                            <th class="text-center">Proovedor</th>
                            <th class="text-center">ccd</th>
                            <th class="text-center">ccc</th>
                            <th class="text-center">Comprobante de Entrada</th>
                            <th class="text-center">Comprobante de Salida</th>
                            <th class="text-center">Dependencia</th>
                            <th class="text-center">Responsable</th>
                            <th class="text-center">Estado</th>
                            <th>Opcion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articulos as $key => $item)
                            <tr class="text-center">
                                <td>{{ $item->index }}</td>
                                <td>{{ $item->nombre_articulo}}</td>
                                <td>{{ $item->codigo }}</td>
                                <td>{{ $item->marca }}</td>
                                <td>{{ $item->presentacion }}</td>
                                <td>{{ $item->referencia}}</td>
                                <td>{{ $item->cantidad}}</td>
                                <td>{{ $item->articulos_salida->count() > 0 ? $item->articulos_salida->sum('cantidad') : 0}}</td>
                                <td>{{ $item->stock}}</td>
                                <td>{{ $item->valor_unitario}}</td>
                                <td>{{ $item->total}}</td>
                                <td>{{ $item->vida_util}}</td>
                                <td>{{ $item->depreciacion}}</td>
                                <td>{{ $item->comprobante_ingreso->factura}}</td>
                                <td>{{ $item->comprobante_ingreso->proovedor->nombre}}</td>
                                <td>{{ $item->puc_ccd->code}}</td>
                                <td>{{ $item->puc_ccd->almacen_puc_credito->code}}</td>
                                <td><a href="{{ route('almacen.ingreso.show', $item->comprobante_ingreso->id)}}" target="_blank">{{ $item->comprobante_ingreso->nombre}}</a></td>
                                <td>
                                    @foreach($item->comprobante_egresos as $egreso)
                                        - <a href="{{ route('almacen.egreso.show', $egreso->id)}}" target="_blank">{{ $egreso->nombre}}</a> </br>
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
                                    <a class="btn btn-success" href="{{route('almacen.articulo.edit', $item->id)}}" title="Editar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
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
            {{ $articulos->links() }}
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