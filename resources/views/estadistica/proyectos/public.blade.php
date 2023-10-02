@extends('layouts.frontend')
@section('titulo')
    EStadisticas de Proyectos
@stop
@section('contenido')
    <div class="container">
    <div id="barchart_material" style="width: 1100px; height: 900px; margin:10px;"></div>
        <div class="row">
            <h3>Estadistica de Proyectos</h3>
            {{--
            <div>
                <div class="dropdown col-lg-12 marginbottom10" style="">
                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Columnas
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu" style="margin-left: 10px; padding:20px;" id="ul-li">
                    </ul>
                </div>
            </div>
            --}}
            <div class="table-responsive">
                <table class="table" id="tabla">
                    <thead>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Ejecución</th>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $proyecto)
                            <tr>
                                <td>{{$proyecto->cod_proyecto}}</td>
                                <td>{{$proyecto->nombre_proyecto}}</td>
                                <td>% {{number_format($proyecto->porcentaje_ejecucion, 2, ',', '.')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>   
            </div>
        </div>
        
    </div>
@stop

@section('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        console.log('d', [['Codigo de proyecto', '% Ejecución'],
          @foreach($proyectos as $proyecto)
            [{{$proyecto->cod_proyecto}}, {{round($proyecto->porcentaje_ejecucion)}}],
          @endforeach
        ])

      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.arrayToDataTable([
          ['Codigo de proyecto', '% Ejecución'],
          @foreach($proyectos as $proyecto)
            ["{{$proyecto->cod_proyecto}}", {{round($proyecto->porcentaje_ejecucion)}}],
          @endforeach
        ]);

        var options = {
          chart: {
            title: 'Alcaldia de Providencia y Santa catalina Islas',
            subtitle: 'Porcentajes de Ejecución Proyectos',
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));

        chart.visualization.events.addListener(table, 'page', myPageEventHandler);

        function myPageEventHandler(e) {
        alert('The user is navigating to page ' + e['page']);
        }
      }
    </script>
@stop