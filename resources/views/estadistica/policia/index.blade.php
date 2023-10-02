@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: POLICIA NACIONAL - ESTACION PROVIDENCIA</h3>
        <h3>Fecha del Reporte: </h3>

        <table class="table">
            <thead>
                <th>LIBRO POBLACIONAL</th>
                <th>ENERO</th>
                <th>FEBRERO</th>
                <th>MARZO</th>
                <th>ABRIL</th>
                <th>MAYO</th>
                <th>JUNIO</th>
                <th>JULIO</th>
                <th>AGOSTO</th>
                <th>SEPTIEMBRE</th>
                <th>OCTUBRE</th>
                <th>NOVIEMBRE</th>
                <th>DICIEMBRE</th>
            </thead>
            <tbody id="tbody">
                
            </tbody>
        </table>   

        <button class="btn btn-primary" onclick="location.reload();">Guardar</button>
    </div>
@stop

@section('js')
    <script>
        const array_items = ['Número de contravenciones', 'Número de delitos hurto', 'Número de delitos de lesiones personales', 'Número de homicidios', 'Número de delitos narcotrafico'];
        let headers = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        let coleccion = "policia";
    </script>
    @include('estadistica.components.gestion_data')
@stop