@extends('layouts.dashboard')
@section('titulo') Delineación y Urbanismo @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Delineación y Urbanismo</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuesto') }}" >Volver a Presupuesto</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Delineación y Urbanismo</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <br>
            <div class="table-responsive">
                <br>
                <div class="col-md-4 text-center">
                    <button class="btn btn-success text-center"><i class="fa fa-user-circle-o"></i>&nbsp;Listado de Usuarios (Propietarios de Predios)</button>
                </div>
                <div class="col-md-4 text-center">
                    <button class="btn btn-success text-center"><i class="fa fa-list"></i>&nbsp;Recibos</button>
                </div>
                <div class="col-md-4 text-center">
                    <button class="btn btn-success text-center"><i class="fa fa-upload"></i>&nbsp;Cargar Recibo</button>
                </div>
            </div>
        </div>
    </div>
@stop
