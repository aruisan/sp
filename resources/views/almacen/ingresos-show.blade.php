@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de ingreso</b></h4>
        </strong>
    </div>
    <div class="row">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Comprobante de Ingreso No. {{$ingreso->id}}</label>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">No. Factura: {{$ingreso->factura}}</label>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha Factura: {{$ingreso->fecha_factura}}</label>

                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Contrato: {{$ingreso->contrato}}</label>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha del Contrato: {{$ingreso->fecha_contrato}}</label>
                    </div>
                </div>
            </div><br>
           
             <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Proovedor: {{$ingreso->proovedor->nombre}}</label>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Cuenta Contable Debito: {{is_null($ingreso->puc_ccd) ? "No tiene" : $ingreso->puc_ccd->concepto}}</label>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Cuenta Contable Credito: {{is_null($ingreso->puc_ccc) ? "No tiene" : $ingreso->pud_ccc->concepto}}</label>
                    </div>
                </div>
            </div><br>
            
            <div class="row">
                <table class="table">
                    <thead>
                        <th>Nombre</th>
                        <th>Codigo</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>estado</th>
                        <th>tipo</th>
                    </thead>
                    <tbody id="body">
                        @foreach($ingreso->articulos as $item)
                           <tr>
                                <td>{{$item->nombre_articulo}}</td>
                                <td>{{$item->codigo}}</td>
                                <td>{{$item->referencia}}</td>
                                <td>{{$item->cantidad}}</td>
                                <td>{{$item->valor_unitario}}</td>
                                <td>{{$item->estado}}</td>
                                <td>{{$item->tipo}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        });
    </script>
@stop