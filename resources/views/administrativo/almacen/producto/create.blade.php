@extends('layouts.dashboard')
@section('titulo')
    Creación de Producto
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Creación de Producto</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/productos') }}">Volver a Productos</a>
        </li>
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">NUEVO PRODUCTO</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/productos')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Foto del Producto</label>
                            <div class="col-lg-6">
                                <input type="file" name="file" accept="image/*" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Valor Actual <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="valor" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <br>
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Cantidad Inicial<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="cant_inicial" value="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <br>
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Cantidad Minima<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="cant_min" value="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <br>
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Cantidad Maxima<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" name="cant_max" value="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <br>
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Metodo<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select class="form-control" name="metodo">
                                    <option value="0">U.E.P.S</option>
                                    <option value="1">P.E.P.S</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
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
                    <div class="col-md-4 align-self-center">
                        <br>
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Tipo<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select class="form-control" name="tipo">
                                    <option value="0">Consumo</option>
                                    <option value="1">Devolutivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 ml-auto">
                            <br>
                            <center>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Almacenar Producto</button>
                            </center>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop