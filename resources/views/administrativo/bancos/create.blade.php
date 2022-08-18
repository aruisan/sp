@extends('layouts.dashboard')
@section('titulo')
    Creación de Banco
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Creación de Bancos</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos') }}"><i class="fa fa-bank"></i>&nbsp;Volver a Bancos</a>
        </li>
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">Nuevo Banco</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/bancos')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-md-12 align-self-center">
                        <div class="col-md-2 align-self-center"></div>
                        <div class="col-md-8 align-self-center">
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Número de Cuenta <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="num" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Descripción <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="descripcion" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Valor <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="value" required min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cuenta<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" id="PUC" name="PUC" required>
                                            <option>Selecciona una cuenta del PUC</option>
                                            @foreach($codigos as $code)
                                                <option value="{{$code['id']}}" @if($code['naturaleza'] == null) disabled @endif>{{$code['codigo']}} - {{$code['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 ml-auto">
                            <br>
                            <center>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-bank"></i>&nbsp;&nbsp;Almacenar Banco</button>
                            </center>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop