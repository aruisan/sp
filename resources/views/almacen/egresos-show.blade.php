@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de Egreso</b></h4>
        </strong>
    </div>
    <div class="row">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Comprobante de Egreso No. {{$egreso->id}}<span class="text-danger">*</span></label>
                    </div>
                </div>
            </div><br>
            <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Dependencia: {{$egreso->dependencia->name}}</label>
                    </div>
                </div>
            </div><br>

             <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Responsable: {{$egreso->responsable->nombre}}</label>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha: {{$egreso->fecha}}</label>
                    </div>
                </div>
            </div><br>
            
            <div class="row">
                <table class="table">
                    <thead>
                        <th>Codigo</th>
                        <th>Articulo</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach($egreso->salidas as $item)
                            <tr>
                                <td>{{$item->codigo}}</td>
                                <td>{{$item->nombre_articulo}}</td>
                                <td>{{$item->referencia}}</td>
                                <td>{{$item->pivot->cantidad}}</td>
                                <td>{{$item->valor_unitario}}</td>
                                <td>{{$item->pivot->cantidad * $item->valor_unitario}}</td>
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
        } );
    </script>
@stop