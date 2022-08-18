@extends('layouts.dashboard')
@section('titulo')
    Crear Concejal
@stop
@section('sidebar')
    {{-- <li> <a class="btn btn-primary" href="{{ asset('/dashboard/concejales') }}"><span class="hide-menu">CONCEJALES</span></a></li> --}}
@stop
@section('content')
   <div class="col-md-12 align-self-center">
     <div class="breadcrumb text-center">
            <strong>
                <h4><b>Nuevo Concejal</b></h4>
            </strong>
        </div>
            <ul class="nav nav-pills">
                  <li class="nav-item ">
                    <a class="nav-link" href="{{ asset('/dashboard/concejales') }}">Concejales</a>
                </li>
               
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo">Nuevo Concejal</a>
                </li>
             
            </ul>
    
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="background-color: white">
    <br>
    <hr>
    {!! Form::open(array('route' => 'concejales.store','method'=>'POST','enctype'=>'multipart/form-data')) !!}
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Usuario: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-building" aria-hidden="true"></i></span>
                <select class="form-control" name="dato_id">
                    @foreach($Usuarios as $usuario)
                        <option value="{{$usuario['id']}}">{{$usuario['name']}}</option>
                    @endforeach
                </select    >
            </div>
            <small class="form-text text-muted">Comisión asignada al acuerdo</small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Partido: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-university" aria-hidden="true"></i></span>
                <input type="text" name="partido" class="form-control" required>
            </div>
            <small class="form-text text-muted">Partido del concejal</small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Periodo: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                <input type="text" name="periodo" class="form-control" required>
            </div>
            <small class="form-text text-muted">Periodo del concejal</small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Foto del Concejal: </label>
            <div class="input-group">
                <input type="file" name="file" accept="image/png" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
        <button class="btn btn-primary btn-raised btn-lg" id="storeRegistro">Guardar</button>
    </div>
    {!! Form::close() !!}
</div>
</div>


@endsection