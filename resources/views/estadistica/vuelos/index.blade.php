@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <form class="row" method="POST">
        {{csrf_field()}}
        <div class="col-sm-3"></div>
        <div class="col-sm-2">
            <div class="form-check">
                <label class="form-check-label">
                    Vuelo PVS-SAI <input type="radio" class="form-check-input" name="fly_type_radio">
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    Vuelo SAI-PVS <input type="radio" class="form-check-input" name="fly_type_radio">
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="row form-group form-inline field-turista">
                <div class="col-sm-5">
                    <label for="fly_number_input">Vuelo No.</label>
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="fly_number_input" name="n_vuelo"/>
                </div>
            </div>
            <div class="row form-group form-inline field-turista">
                <div class="col-sm-5">
                    <label for="airline_input">Aerolinea</label>
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="airline_input" name="airline"/>
                </div>
            </div>
            <div class="row form-group form-inline field-turista">
                <div class="col-sm-5">
                    <label for="date_input">Fecha</label>
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="date_input" name="date"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" type="submit">
                        Abrir vuelo
                    </button>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
        <div class="col-sm-4"></div>
    </form>
@stop
