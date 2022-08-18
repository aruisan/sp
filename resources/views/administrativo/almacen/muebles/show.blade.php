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
            <a class="tituloTabs" href="{{ url('/administrativo/muebles') }}">Volver a Bienes, Muebles e Inmuebles</a>
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
            <a class="tituloTabs" href="{{ url('/administrativo/muebles/create') }}">Nuevo Comprobante de Ingreso</a>
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
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Cantidad</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="<?php echo number_format($movimiento->cantidad,0) ?>" disabled>
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
                                <label class="col-lg-4 col-form-label text-right">Avaluo</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->avaluo,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Estado</label>
                                <div class="col-lg-6">
                                    @if($movimiento->estado == "0")
                                        <input type="text" class="form-control" value="Bueno" disabled>
                                    @elseif($movimiento->estado == "1")
                                        <input type="text" class="form-control" value="Regular" disabled>
                                    @else
                                        <input type="text" class="form-control" value="Malo" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Depreciación</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->depreciacion,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Vida Util</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="{{ $movimiento->vida_util }} Años" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Valor Actual</label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->nuevo_valor,0) ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 align-self-center">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right">Descripción</label>
                                <div class="col-lg-6">
                                    <textarea  class="form-control" disabled>{{ $movimiento->descripcion }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <br>
                            <br>
                            <hr>
                            <center>
                                <h4>Variación del Avaluo con Respecto a la Vida Util y su Depreciación</h4>
                            </center>
                            <hr>
                            <br>
                            <br>
                            <table class="table-bordered" width="100%">
                                <tbody>
                                <tr class="text-center">
                                    <td><b>AÑO</b></td>
                                    @foreach($años as $año)
                                        <td>{{ $año }}</td>
                                    @endforeach
                                </tr>
                                <tr class="text-center">
                                    <td><b>VALOR</b></td>
                                    @foreach($values as $value)
                                        <td>$ <?php echo number_format($value,0) ?></td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12 ml-auto">
                                <br>
                                <br>
                                <br>
                                <center>
                                    <button type="submit" class="btn btn-primary" disabled><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;PDF</button>
                                    <a class="btn btn-primary" disabled="" href="{{ url('/administrativo/muebles/'.$movimiento->ruta) }}"><i class="fa fa-file-photo-o"></i>&nbsp;&nbsp;Factura</a>
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
                                    <input type="text" class="form-control" value="$<?php echo number_format($movimiento->nuevo_valor,0) ?>" disabled>
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