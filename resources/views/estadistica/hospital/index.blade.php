@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: HOSPITAL LOCAL</h3>
        <h3>Fecha del Reporte: </h3>

        <table class="table">
            <thead>
                <th>HOSPITAL</th>
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
    </div>
@stop

@section('js')
    <script>
        const array_items = ['Número de consultas', 'Tipo de consultas', 'Accidentes de transito', 'Número de fallecimientos', 'Número de niños', 'Fallecimientos niños y niñas'];
        let headers = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        let coleccion = "hospital";
    </script>
    @include('estadistica.components.gestion_data')
@stop