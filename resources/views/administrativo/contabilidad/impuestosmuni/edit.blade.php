@extends('layouts.dashboard')
@section('titulo')
    Editar Impuesto Municipal
@stop
@section('content')
<div class="row">

         <div class="breadcrumb text-center">
            <strong>
                <h3><b>Editar Impuesto Municipal</b></h3>
            </strong>
        </div>
          	<ul class="nav nav-pills">
                 <li class="nav-item regresar"> <a href={{ url('/administrativo/contabilidad/impumuni')}} class="nav-link">Impuestos Municipales</a></li>
                <li class="nav-item active"> <a href="#crear" class="nav-link">Editar Impuesto</a></li>

                </ul>
    <div class="tab-content col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <br>
    <hr>
    <form action="{{ asset('/administrativo/contabilidad/impumuni/'.$desc->id) }}" method="POST"  class="form" enctype="multipart/form-data">
        {!! method_field('PUT') !!}
        {{ csrf_field() }}
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label>Concepto: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" name="concept" value="{{ $desc->concepto }}" required>
                </div>
                <small class="form-text text-muted">Concepto de la retención</small>
            </div>
       
            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label>Base: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                    <input type="number" name="base" class="form-control" required value="{{ $desc->base }}">
                </div>
                <small class="form-text text-muted">Base de la retención</small>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label>Tarifa: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                    <input type="number" step="0.01" name="tarifa" class="form-control" required value="{{ $desc->tarifa }}">
                </div>
                <small class="form-text text-muted">Tarifa de la retención</small>
            </div>
       
            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label>Codigo: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-hashtag" aria-hidden="true"></i></span>
                    <input type="number" name="codigo" class="form-control" required value="{{ $desc->codigo }}">
                </div>
                <small class="form-text text-muted">Codigo de la retención</small>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                <label>Cuenta: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                    <input type="text" name="cuenta" class="form-control" required value="{{ $desc->cuenta }}">
                </div>
                <small class="form-text text-muted">Cuenta de la retención</small>
            </div>
        </div>
         <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1" >
            <button class="btn btn-primary btn-raised btn-lg">Guardar</button>
        </div>
    </form>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 text-center">
        <form action="{{ asset('/administrativo/contabilidad/impumuni/'.$desc->id) }}" method="post">
            {!! method_field('DELETE') !!}
            {{ csrf_field() }}
            <button class="btn btn-danger btn-raised btn-lg">Eliminar</button>
        </form>
    </div>
   </div>
</div>
</div>
@endsection