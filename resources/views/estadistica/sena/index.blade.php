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
        const reportes = ['Número de estudiantes matriculados', 'Número de instructores', 'Número de programas ofrecidos',
                            , 'Deserción de estudiantes', 'Presupuesto de la Sede Entidad', 'Déficit de programas a Ofertar'];
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