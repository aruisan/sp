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
                </tr>
                <thead>
                    <th scope="col">@lang('posts.index.columns.index')</th>
                    <th scope="col">@lang('posts.index.columns.nombre')</th>
                    <th scope="col">@lang('posts.index.columns.codigo')</th>
                    <th scope="col">@lang('posts.index.columns.marca')</th>
                    <th scope="col">@lang('posts.index.columns.presentacion')</th>
                    <th scope="col">@lang('posts.index.columns.referencia')</th>
                </thead>
                <thead>
                    <tr>
                        <th></th>
                        <th>
                            <input type="text" data-column="1"
                                class="data-table-search-input-text form-control"
                                style="width: 100%">
                        </th>
                        <th>
                            <input type="text" data-column="2"
                                class="data-table-search-input-text form-control"
                                style="width: 100%">
                        </th>
                        <th>
                            <input type="text" data-column="3"
                                class="data-table-search-input-text form-control"
                                style="width: 100%">
                        </th>
                        <th>
                            <input type="text" data-column="4"
                                class="data-table-search-input-text form-control"
                                style="width: 100%">
                        </th>
                        <th></th>
                    </tr>
                </thead>
                </thead>
                <tbody>
                    @if($items->count() > 0)
                        @foreach($items as $item)
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
                                <td>{{ is_null($item->egreso->puc_credito) ? "no tiene" : $item->egreso->puc_credito->code}}</td>
                            </tr>
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
    $.ajaxSetup ({
        headers: {
            'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content')
        }
    });

    var postsTable =  $('.table').DataTable( {
            responsive: true,
            "ordering": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('almacen.egresos.items.data') }}",
                "dataType": "json",
                "type": "POST"
            },
            "columns": [
                {"data": "checkbox"},
                {"data": "id"},
                {"data": "title"},
                {"data": "description"},
                {"data": "created_at"},
                {"data": "options"}
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop