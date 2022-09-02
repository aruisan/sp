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
    </div>
@stop

@section('js')
    <script>
        const reportes = ['Número de contravenciones', 'Número de delitos hurto', 'Número de delitos de lesiones personales', 'Número de homicidios', 'Número de delitos narcotrafico'];

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