@extends('layouts.dashboard')
@section('titulo')
    Creación del Comprobante de Ingresos
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NUEVO COMPROBANTE DE INGRESOS</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/CIngresos/'.$vigencia->id) }}">Volver a Comprobantes de Ingresos</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVO COMPROBANTE DE INGRESOS</a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <br>
                        <form class="form-valide" action="{{url('/administrativo/CIngresos')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Concepto <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <textarea name="concepto" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="file">Subir Archivo: </label>
                                        <div class="col-lg-6">
                                            <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                            <input type="file" name="file" accept="application/pdf" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Valor <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" name="valor" min="0" value="0" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="observacion">Valor Iva <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" name="valorIva" value="0" min="0" max="99999999" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <input type="hidden" class="form-control" name="fecha" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}" min="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
                            <input type="hidden" class="form-control" name="user_id" value="{{ $user_id }}">
                            <input type="hidden" class="form-control" name="vigencia_id" value="{{ $vigencia->id }}">
                            <input type="hidden" class="form-control" name="estado" value="0">
                            <center>

                                <div class="form-group row">
                                    <div class="col-lg-12 ml-auto">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>

                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  </div>
  
@stop
