@extends('layouts.dashboard')
@section('titulo')
    Reserva viaje
@stop
@section('content')
<form class="row" method="POST">
    {{csrf_field()}}
    <div class="col-sm-3"></div>
    <div class="col-sm-2">
        <div class="form-check">
            <label class="form-check-label">
                PVS-SAI <input type="radio" class="form-check-input" name="travel_type_radio">
            </label>
        </div>
        <div class="form-check">
            <label class="form-check-label">
                SAI-PVS <input type="radio" class="form-check-input" name="travel_type_radio">
            </label>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="row form-group form-inline field-turista">
            <div class="col-sm-5">
                <label for="travel_number_input">Viaje No.</label>
            </div>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="travel_number_input" name="travel"/>
            </div>
        </div>
        <div class="row form-group form-inline field-turista">
            <div class="col-sm-5">
                <label for="boat_name_input">Nombre barco</label>
            </div>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="boat_name_input" name="boatName"/>
            </div>
        </div>
        <div class="row form-group form-inline field-turista">
            <div class="col-sm-5">
                <label for="transportation_company_input">Empresa transportadora</label>
            </div>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="transportation_company_input" name="transportationCompany"/>
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
                    Abrir viaje
                </button>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>
    <div class="col-sm-4"></div>
</form>
@stop