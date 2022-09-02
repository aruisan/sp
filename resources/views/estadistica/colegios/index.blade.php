@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: INSTITUCION EDUCATIVA JUNIN</h3>
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
        const reportes = ['Número de alumnos matriculados', 'Doscentes con posgrados', 'Doscentes con maestría', 'Docentes con pregrado', 'total docentes activos', 
                            'vacantes de docentes', 'Déficit de docentes', 'Deserción de docentes', 'Deserción escolar', 'Número de salones de clase', 'Número de sedes educativas',
                            , 'Número de escuela de padres', 'Número de padres atendidos'];
        $(document).ready(function(){
            load_tr()
        })

        const load_tr = () =>{
            reportes.forEach(e => {
                $('#tbody').append(`<tr>
                    <td>${e}</td>
                    <td><input class="form-control"></td>
                    <td><input class="form-control"></td>
                    <td><input class="form-control"></td>
                    <td><input class="form-control"></td>
                    <td><input class="form-control"></td>
                    <td><input class="form-control"></td>
                </tr>`);
            });
        }
    </script>

@stop