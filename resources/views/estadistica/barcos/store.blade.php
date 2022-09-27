@extends('layouts.dashboard')
@section('titulo')
    viaje
@stop
@section('content')
<link href="{{ asset('css/estadisticas/vuelos/store.css') }}" rel="stylesheet" type="text/css" />
    <div class="row">
        <div class="col-sm-4 appbar-component">
            <div class="row">
                <div class="col-sm-6 appbar-component appbar-active-component">
                    <button class="btn btn-sm btn-block appbar-button" disabled>
                        <h4>No. {{$travel}}</h4>
                    </button>
                </div>
                <div class="col-sm-6 appbar-component appbar-inactive-component">
                    <button class="btn btn-sm btn-block appbar-button" disabled>
                        <h4>Relación de vuelos</h4>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-6 appbar-component appbar-static-component">

        </div>
        <div class="col-sm-2 appbar-component appbar-static-component">
            <button class="appbar-button little-appbar-button">
                informes
            </button>
            <button class="appbar-button little-appbar-button">
                Histórico
            </button>
        </div>
    </div>
    <div class="row vertical-spacer"></div>
    <div class="row">
        Barco: {{$boatName}}
    </div>
    <div class="row">
        Aerolínea: {{$transportationCompany}}
    </div>
    <div class="row">
        <div class="col-sm-2 col-md-offset-2">
            <label for="tipo_pasajero">TURISTA / RESIDENTE / TRABAJADOR:</label>
        </div>
        <div class="col-sm-4 form-group form-inline field-trabajador">
            <label for="job_permission_input" class="control-label">Permiso de trabajo No.</label>
            <input type="text" class="form-control input-sm" id="job_permission_input" />
        </div>
        <div class="col-sm-3 form-group form-inline field-trabajador">
            <label for="date_input">Fecha</label>
            <input type="text" class="form-control input-sm" id="date_input" />
        </div>
        <div class="col-sm-2 field-turista">
            <div class="form-check">
                <label class="form-check-label">
                    Cuenta propia <input type="radio" class="form-check-input" name="count_type_radio">
                </label>
            </div>
        </div>
        <div class="col-sm-1 field-trabajador"></div>
    </div>
    <div class="row">
        <div class="col-sm-2 text-right">
            <label for="tipo_pasajero">Tipo de pasajero:</label>
        </div>
        <div class="col-sm-2">
            <select id="tipo_pasajero" class="form-control">
                <option value="Turista" selected>Turista</option>
                <option value="Trabajador">Trabajador</option>
                <option value="Residente">Residente</option>
            </select>
        </div>
        <div class="col-sm-2 field-turista">
            <div class="form-check">
                <label class="form-check-label">
                    Agencia de viajes <input type="radio" class="form-check-input" name="count_type_radio">
                </label>
            </div>
        </div>
        <div class="col-sm-3 form-group form-inline field-turista">
            <label for="agency_name_input">Nombre Agencia</label>
            <input type="text" class="form-control short-input" id="agency_name_input" />
        </div>
        <div class="col-sm-1">
            <label for="company_input">Empresa</label>
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" id="company_input" />
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col-sm-2  text-right">
            <label for="name_input">Nombre:</label>
        </div>
        <div class="col-sm-3 form-group">
            <input type="text" class="form-control" id="name_input" />
        </div>
        <div class="col-sm-2">
            <div class="form-check-inline">
                <label class="form-check-label">
                    CC<input type="radio" class="form-check-input" name="doc_type_radio">
                </label>
                <label class="form-check-label">
                    CE<input type="radio" class="form-check-input" name="doc_type_radio">
                </label>
                <label class="form-check-label">
                    TI<input type="radio" class="form-check-input" name="doc_type_radio">
                </label>
            </div>
        </div>
        <div class="col-sm-2 form-group">
            <input type="text"  class="form-control" id="agency_name_input" />
        </div>
        <div class="col-sm-3 form-group form-inline">
            <label for="sex_input">Sexo:</label>
            <input type="text"  class="form-control" id="sex_input" />
        </div>
    </div>
    <div class="row field-turista-trabajador">
        <div class="col-sm-2 text-right">
            <label for="nacionality_input">Nacionalidad:</label>
        </div>
        <div class="col-sm-2 form-group">
            <input type="text"  class="form-control" id="nacionality_input" />
        </div>
        <div class="col-sm-3 form-group form-inline">
            <label for="hometown_input">Ciudad de origen</label>
            <input type="text"  class="form-control" id="hometown_input" />
        </div>
        <div class="col-sm-2 form-group form-inline">
            <div class="row">
                <div class="col-xs-2">
                    <div class="vertical-spacer"></div>
                    <label for="country_input">País</label>
                </div>
                <div class="col-xs-10">
                    <input type="text"  class="form-control" id="country_input" />
                </div>
            </div>
        </div>
        <div class="col-sm-3 form-group form-inline">
            <label for="birthday_input">Fecha nacimiento</label>
            <input type="text"  class="form-control" id="birthday_input" />
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 text-right">
            <label for="in_date_input">Fecha de ingreso::</label>
        </div>
        <div class="col-sm-2 form-group">
            <input type="text"  class="form-control" id="in_date_input" />
        </div>
        <div class="col-sm-3 form-group form-inline">
            <label for="out_date_input">Fecha de salida</label>
            <input type="text"  class="form-control" id="out_date_input" />
        </div>
        <div class="col-sm-5 form-group form-inline field-turista-trabajador">
            <label for="host_place_input">Lugar de hospedaje</label>
            <input type="text"  class="form-control" id="host_place_input" />
        </div>
        <div class="col-sm-5 field-residente"></div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <button type="button" class="btn btn-primary btn-block">Nuevo viaje</button>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-1">
            <button type="button" class="btn btn-primary">Siguiente</button>
        </div>
        <div class="col-sm-1">
            <button type="button" class="btn btn-primary">Cerrar viaje</button>
        </div>
        <div class="col-sm-2"></div>
    </div>
@stop
@section('js')
    <script type="text/javascript">

        $(document).ready(function(){
            change_passenger();
        });
           $('#tipo_pasajero').on('change', function(){
                change_passenger();
           }) 

        const change_passenger = () => {
            tipo = $('#tipo_pasajero').val();
            if(tipo == 'Turista'){
                $('.field-turista').show();
                $('.field-trabajador').hide();
                $('.field-turista-trabajador').show();
                $('.field-residente').hide();
            }else if(tipo == 'Trabajador'){
                $('.field-turista').hide();
                $('.field-trabajador').show();
                $('.field-turista-trabajador').show();
                $('.field-residente').hilde();
            }else{
                $('.field-turista').hide();
                $('.field-trabajador').hide();
                $('.field-turista-trabajador').hide();
                $('.field-residente').show();
            }
        }
    </script>
@stop