@extends('layouts.dashboard')
@section('titulo')
    @if($movimiento->tipo == 0)
        Comprobante Entrada
    @else
        Comprobante Salida
    @endif
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4>
                <b>
                    @if($movimiento->tipo == 0)
                        Comprobante de Entrada Número {{ $movimiento->id }}
                    @else
                        Comprobante de Salida Número {{ $movimiento->id }}
                    @endif
                </b>
            </h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/inventario') }}">Volver al Inventario</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome">
                @if($movimiento->tipo == 0)
                    Comprobante de Entrada
                @else
                    Comprobante de Salida
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/inventario/create') }}">Nuevo Comprobante de Ingreso</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/salida/create') }}">Nuevo Comprobante de Salida</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation" id="crud">
                <form class="form-valide">
                    @if($movimiento->tipo == 0)
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Número de Factura</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->num_factura }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Fecha de Ingreso</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->created_at->format('d/m/Y')}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Producto</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{$movimiento->producto->nombre}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Unidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->unidad }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Valor Por Unidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->valor_unidad,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Valor Final</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->valor_final,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Cantidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->cantidad }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Descripción</label>
                                <div class="col-lg-6">
                                    <textarea  class="form-control" disabled>{{ $movimiento->descripcion }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 ml-auto">
                                <br>
                                <center>
                                    <button type="submit" class="btn btn-primary" disabled><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;PDF</button>
                                </center>
                            </div>
                        </div>
                    @else
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Fecha de Salida</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->created_at->format('d/m/Y')}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Producto</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{$movimiento->producto->nombre}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Descripción</label>
                                <div class="col-lg-6">
                                    <textarea  class="form-control" disabled>{{ $movimiento->descripcion }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Valor Por Unidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->valor_unidad,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Valor Final</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->valor_final,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Cantidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->cantidad }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 ml-auto">
                                <br>
                                <center>
                                    <button type="submit" class="btn btn-primary" disabled><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;PDF</button>
                                </center>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@stop