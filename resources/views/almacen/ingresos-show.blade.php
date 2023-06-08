@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de {{$ingreso->nombre}}</b></h4>
        </strong>
    </div>
    <div class="row">
            <div class="row">
                <embed src="{{route('almacen.comprobante.ingreso.pdf', $ingreso->id)}}" type="application/pdf" width="100%" height="1000px" />
            </div>
    </div>
@stop
@section('js')
    <script>
       
    </script>
@stop