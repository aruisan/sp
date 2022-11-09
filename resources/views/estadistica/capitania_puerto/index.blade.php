@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <div class="row">
        <h3>Entidad que reporta: CAPITANIA DE PUERTO - PROVIDENCIA ISLAS</h3>
        <h3>Fecha del Reporte: </h3>

        <table class="table">
            <thead>
                <th>VIGENCIA</th>
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

        <table class="table">
            <thead>
                <th>REGISTRO DE EMBARCACIONES MATRICULADAS</th>
                <th>2020</th>
                <th>2021</th>
                <th>2022</th>
                <th>2023</th>
                <th>2024</th>
                <th>2025</th>
            </thead>
            <tbody id="tbody2">
                
            </tbody>
        </table>   
    </div>
@stop

@section('js')
    <script>
        const reportes = ['Menos de 30 Ton', '30 - 100 Ton', 'Mas 100 Ton', '<b>2021</b>', 'Menos de 30 Ton', '30 - 100 Ton', 'Mas 100 Ton', '<b>2022</b>', 'Menos de 30 Ton', '30 - 100 Ton', 'Mas 100 Ton',];
        const reportes2 = ['Menores 20 Pies', '20 a 30 Pies', 'Mas de 30 Pies'];

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

            reportes2.forEach(e => {
                $('#tbody2').append(`<tr>
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