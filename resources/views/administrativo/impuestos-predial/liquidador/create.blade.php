@extends('layouts.dashboard')
@section('titulo')  Nuevo Mes Liquidador @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Nuevo Mes</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/impuestospredial/liquidador') }}">Volver al liquidador</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >Nuevo Mes</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/administrativo/impuestospredial/liquidador')}}" method="POST" enctype="multipart/form-data">
                            <hr>
                            {{ csrf_field() }}
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="año">Año <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input class="form-control" name="año" value="{{ Carbon\Carbon::today()->Format('Y')}}" type="number" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="mes">Mes <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="mes" value="{{ Carbon\Carbon::today()->Format('m')}}" min="1" max="12" disabled>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="vencimiento">Fecha Vencimiento <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input class="form-control" name="vencimiento" type="date" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}" min="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="valor">Valor <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input class="form-control" step="0.01" type="number" name="valor" min="0.1" max="100" value="0.1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 align-self-center text-center">
                                <center>
                                    <div class="form-group row text-center">
                                        <div class="col-lg-12 ml-auto">
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </div>
                                </center>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  </div>
@stop
