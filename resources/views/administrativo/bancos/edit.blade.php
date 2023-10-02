@extends('layouts.dashboard')
@section('titulo')
    Editar Banco
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Editar Banco</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos') }}"><i class="fa fa-bank"></i>&nbsp;Volver a Bancos</a>
        </li>
        <li class="nav-item active">
            <a class="tituloTabs" data-toggle="pill" href="#tabHome">Editar Banco</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos/create') }}"><i class="fa fa-plus"></i>&nbsp;Nuevo Banco</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos/'.$item->id) }}"><i class="fa fa-eye"></i>&nbsp;Ver Banco</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/bancos/'.$item->id)}}" method="POST" enctype="multipart/form-data">
                    {!! method_field('PUT') !!}
                    {{ csrf_field() }}
                    <div class="col-md-2 align-self-center"></div>
                    <div class="col-md-8 align-self-center">
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Número de Cuenta:</label>
                                <div class="col-lg-6 text-center">
                                    <input type="text" class="form-control" name="num" value="{{$item->numero_cuenta}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Descripción:</label>
                                <div class="col-lg-6 text-center">
                                    <input type="text" class="form-control" name="descripcion" value="{{$item->descripcion}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Cuenta del PUC:</label>
                                <div class="col-lg-6 text-center">
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
                    <div class="form-group row">
                        <div class="col-lg-12 ml-auto">
                            <br>
                            <center>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Actualizar Banco</button>
                            </center>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop