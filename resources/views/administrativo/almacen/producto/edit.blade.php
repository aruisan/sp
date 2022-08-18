@extends('layouts.dashboard')
@section('titulo')
    Editar Producto
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>{{ $item->nombre }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/productos') }}">Volver a Productos</a>
        </li>
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">EDITAR PRODUCTO</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/productos/'.$item->id)}}" method="POST" enctype="multipart/form-data">
                    {!! method_field('PUT') !!}
                    {{ csrf_field() }}
                    <div class="col-md-12 align-self-center">
                        <div class="col-md-4 align-self-center text-center">
                            <img src="{{ asset('img/productos/'.$item->id.'.jpg')}}" width="auto" height="auto">
                            <div class="col-md-12 align-self-center">
                                <br>
                                <center>
                                    <h3>Actualizar Foto del Producto</h3>
                                </center>
                                <hr>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <center>
                                            <input type="file" name="file" accept="image/*" class="form-control text-center">
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 align-self-center">
                            <div class="col-md-12 align-self-center">
                                <center>
                                    <h3>Información del Producto</h3>
                                </center>
                                <hr>
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="name" value="{{ $item->nombre }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Inicial</label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="cant_inicial" value="{{ $item->cant_inicial }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Minima<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="cant_min" value="{{ $item->cant_minima }}" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Maxima<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="cant_max" value="{{ $item->cant_maxima }}" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Metodo<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="metodo">
                                            <option value="0" @if($item->metodo == 0) selected @endif>U.E.P.S</option>
                                            <option value="1" @if($item->metodo == 1) selected @endif>P.E.P.S</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Tipo<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="tipo">
                                            <option value="0" @if($item->tipo == 0) selected @endif>Consumo</option>
                                            <option value="1" @if($item->tipo == 1) selected @endif>Devolutivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cuenta<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" id="PUC" name="PUC" required>
                                            <option>Selecciona una cuenta del PUC</option>
                                            @foreach($codigos as $code)
                                                <option value="{{$code['id']}}" @if($code['naturaleza'] == null) disabled @endif @if($code['id'] == $item->rubros_puc_id and $code['naturaleza'] != null) selected @endif>{{$code['codigo']}} - {{$code['name']}}</option>
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
                                <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Actualizar Producto</button>
                            </center>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop