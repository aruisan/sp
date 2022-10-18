@extends('layouts.dashboard')

@section('titulo')
    Crear empleado
@stop

@section('content')
    <form class="container" method="POST">
        <div class="row">
            <div class="col-sm-3 col-md-4"></div>
            <div class="col-sm-6 col-md-4">
                <h2> Nuevo Empleado </h2>
            </div>
            <div class="col-sm-3 col-md-4"></div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-4">
            </div>
            <div class="col-sm-6 col-md-4 input-group">
                <label for="employe_doc_number">
                    Número de documento
                </label>
                <input type="text" class="form-control short-input" id="employe_doc_number" name="doc_number"/>
            </div>
            <div class="col-sm-3 col-md-4">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-4">
            </div>
            <div class="col-sm-6 col-md-4 input-group">
                <label for="employe_email">
                    Email
                </label>
                <input type="text" class="form-control short-input" id="employe_email" name="employe_email"/>
            </div>
            <div class="col-sm-3 col-md-4">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-4">
            </div>
            <div class="col-sm-6 col-md-4 input-group">
                <label for="employe_address">
                    Dirección
                </label>
                <input type="text" class="form-control short-input" id="employe_address" name="address"/>
            </div>
            <div class="col-sm-3 col-md-4">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-4">
            </div>
            <div class="col-sm-6 col-md-4 input-group">
                <label for="employe_birth_date">
                    Fecha de nacimiento
                </label>
                <input type="text" class="form-control short-input" id="employe_birth_date" name="birth_date"/>
            </div>
            <div class="col-sm-3 col-md-4">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-4">
            </div>
            <div class="col-sm-6 col-md-4 input-group">
                <label for="employe_phone">
                    Teléfono
                </label>
                <input type="text" class="form-control short-input" id="employe_phone" name="phone"/>
            </div>
            <div class="col-sm-3 col-md-4">
            </div>
        </div>
        <div class="divider" style="height: 15px">

        </div>
        <div class="row">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary btn-block" action="submit">
                    Crear
                </button>
            </div>
            <div class="col-sm-4">
            </div>
        </div>
    </div>
@stop