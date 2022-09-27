@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: INSTITUCION EDUCATIVA JUNIN</h3>
        <h3>Fecha del Reporte: </h3>
        <form id="form">
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
        </form>
    </div>
@stop

@section('js')
    <script>
        const array_items = ['Número de alumnos matriculados', 'Doscentes con posgrados', 'Doscentes con maestría', 'Docentes con pregrado', 'total docentes activos', 
                            'vacantes de docentes', 'Déficit de docentes', 'Deserción de docentes', 'Deserción escolar', 'Número de salones de clase', 'Número de sedes educativas',
                            , 'Número de escuela de padres', 'Número de padres atendidos'];
        let headers = [2020,2021,2022,2023,2024,2025];
        let coleccion = "colegio";
    </script>
    @include('estadistica.components.gestion_data')
@stop