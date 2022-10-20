@extends('layouts.dashboard')

@section('titulo')
    Crear empleado
@stop

@section('content')
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link"  href="{{route('nomina.empleados.index')}}"> Terceros</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="{{route('nomina.empleados.create')}}">Nuevo Tercero</a>
        </li>
    </ul>
    <form class="container" method="POST">
        <div class="row">
            <div class="col-sm-3 col-md-4"></div>
            <div class="col-sm-6 col-md-4">
                <h2> Nuevo Empleado </h2>
            </div>
            <div class="col-sm-3 col-md-4"></div>
        </div>
        @include('nomina.empleados.partials.employee_form_fields')
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