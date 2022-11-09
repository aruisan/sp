@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: SENA - SEDE PROVIDENCIA ISLAS</h3>
        <h3>Fecha del Reporte: </h3>

        <table class="table">
            <thead>
                <th>REPORTE</th>
                <th>2020</th>
                <th>2021</th>
                <th>2022</th>
                <th>2023</th>
                <th>2024</th>
                <th>2025</th>
            </thead>
            <tbody id="tbody">
                
            </tbody>
        </table>   

    </div>
@stop
@section('js')
    <script>
        const array_items = ['Número de estudiantes matriculados', 'Número de instructores', 'Número de programas ofrecidos',
                            , 'Deserción de estudiantes', 'Presupuesto de la Sede Entidad', 'Déficit de programas a Ofertar'];
        let headers = [2020,2021,2022,2023,2024,2025];
        let coleccion = "sena";
    </script>
    @include('estadistica.components.gestion_data')
@stop