@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de Salida No. {{$egreso->index}}</b></h4>
        </strong>
    </div>
    <div class="row">
        <div class="row">
                <embed src="{{route('almacen.comprobante.egreso.pdf', $egreso->id)}}" type="application/pdf" width="100%" height="1000px" />
        </div>
    </div>
@stop
@section('js')
@stop