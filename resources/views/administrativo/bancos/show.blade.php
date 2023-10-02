@extends('layouts.dashboard')
@section('titulo')
    Banco
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>{{ $item->numero_cuenta }} - {{ $item->descripcion }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos') }}"><i class="fa fa-bank"></i>&nbsp;Volver a Bancos</a>
        </li>
        <li class="nav-item active">
            <a class="tituloTabs" data-toggle="pill" href="#tabHome">Banco</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos/create') }}"><i class="fa fa-plus"></i>&nbsp;Nuevo Banco</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/bancos/'.$item->id.'/edit') }}"><i class="fa fa-edit"></i>&nbsp;Editar Banco</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide"  enctype="multipart/form-data">
                    <div class="col-md-2 align-self-center">
                    </div>
                    <div class="col-md-8 align-self-center">
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Número de Cuenta:</label>
                                <div class="col-lg-6 text-center">
                                    {{$item->numero_cuenta}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Descripción:</label>
                                <div class="col-lg-6 text-center">
                                    {{$item->descripcion}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Valor Inicial:</label>
                                <div class="col-lg-6 text-center">
                                    $<?php echo number_format($item->valor_inicial,0) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Valor Actual:</label>
                                <div class="col-lg-6 text-center">
                                    $<?php echo number_format($item->valor_actual,0) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Cuenta del PUC:</label>
                                <div class="col-lg-6 text-center">
                                    @foreach($codigos as $code)
                                        @if($code['id'] == $item->rubros_puc_id and $code['naturaleza'] != null)
                                            {{$code['codigo']}} - {{$code['name']}}
                                        @endif
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">

                    </div>
                </form>
            </div>
        </div>
    </div>
@stop