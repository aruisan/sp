@extends(Auth::guest() ? 'layouts.frontend' : 'layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('css')
    <style>
       .btn-margin{
            margin:5px 0;
       }
    </style>
@stop
@section(Auth::guest() ? 'contenido' :'content')
    <ul class="nav nav-tabs">
        @foreach($items as $i => $item)
            <li class="{{$i == 0 ? 'active' : ''}}">
                <a data-toggle="tab" href="#coleccion_{{$i}}" onclick="setTimeout(() => {select_data_all({{$i}})}, 500)">{{$item->coleccion}}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($items as $k => $item)
            <div class="tab-pane fade {{$k == 0 ? 'in active' : ''}} container" id="coleccion_{{$k}}">
                <div class="col-md-2">
                    <div class="btn-group-vertical">
                        <button type="button" class="btn btn-primary btn-block btn-margin" onclick="select_data_all({{$k}})" title="Todas">Todas</button>
                        @foreach($item->data as $d => $row)
                            <button type="button" class="btn btn-primary btn-block btn-margin" onclick="select_data({{$k}},'{{$d}}' )" title="{{str_replace("_"," ", $d)}}">{{\Illuminate\Support\Str::limit(str_replace("_"," ", $d), 18)}}</button>
                        @endforeach
                    </div>
                </div>
                <div id="charts_{{$k}}" class="col-md-10">
                    <div id="charts_{{$k}}" style="margin-left:50px;">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('js')
     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        const data = @json($items);
        const ages = ['2020','2021','2022','2023','2024','2025'];
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        let item_select = null;
        let data_line = [];
        let title_line = '';
        let index = 0;
        let index_item_data = null;
        
        

        $(document).ready(function(){
            console.log('data', data)
          //  line_chart();
             select_data_all(0);
             //load_data(0);
            //load_tabs 
        })

/*
        const load_data = index => { 
            item_select = data[index];
            let keys = Object.keys(item_select.data);
            select_data(index, keys[0])
        }; 
*/

        const select_data_all = (index_item) => {
            data_line = [];
            item_select = data[index_item];
            index = index_item
            let keys = Object.keys(item_select.data);
            let n_values = item_select.data[keys[0]].length;
            $(`#charts_${index_item}`).empty().append(`<div id="line_chart_${index}" style="width: ${n_values == 6 ? '900' : '1200'}px; height: 500px"></div>`)
                                            .append(`<div id="bart_chart_${index}" style="width: ${n_values == 6 ? '900' : '1200'}pxpx; height: 500px"></div>`)
            title_line = item_select.coleccion;

            google.charts.load('current', {'packages':['line']});
            google.charts.setOnLoadCallback(eval(`format_line_charts`));
        }


        const select_data = (index_item, index_data) => {
            item_select = data[index_item];
            index = index_item
            index_item_data = index_data
            let n_values = item_select.data[index_data].length;
            $(`#charts_${index_item}`).empty().append(`<div id="line_chart_${index}" style="width: ${n_values == 6 ? '900' : '1200'}px; height: 500px"></div>`)
                                            .append(`<div id="bart_chart_${index}" style="width: ${n_values == 6 ? '900' : '1200'}pxpx; height: 500px"></div>`)
            title_line = index_data.split("_").join(" ");
            data_line = [['Añoo', 'Cantidad']];
            let keys = Object.keys(item_select.data);
            let valores = n_values == 6 ? ages : months;

            valores.forEach((v,m) => {
                data_line.push([v, parseInt(item_select.data[index_data][m])]);
            });
            google.charts.load('current', {'packages':['line']});
            //google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(eval(`format_line_chart`));
        }

        const format_line_charts = () =>{
            var data = new google.visualization.DataTable();
            let values = Object.values(item_select.data);
            let keys = Object.keys(item_select.data);
            let n_values = item_select.data[keys[0]].length;
            let valores = n_values == 6 ? ages : months;
            
            if(n_values == 6 ){
                data.addColumn('string', 'Año');
            }else{
                data.addColumn('string', 'Meses');
            }
            keys.forEach((k,l) => {
                data.addColumn('number', k.split("_").join(" "));
            });

            valores.forEach((v,m) => {
                let rows = [];
                rows.push(v);
                console.log('ll', rows)
                values.forEach((v,l) => {
                    console.log('xx', v)
                    rows.push(parseInt(v[m]))
                    console.log('vv', rows)
                })
                console.log('hh', rows)
                data_line.push(rows);
                console.log('ii', data_line)
            });
            console.log('gg', data_line)
            data.addRows(data_line);
            var options = {
                chart: {
                    title: item_select.coleccion,
                   // subtitle: 'in millions of dollars (USD)'
                },
                width: 900,
                height: 500,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };
            var chart = new google.charts.Line(document.getElementById(`line_chart_${index}`));

            chart.draw(data, google.charts.Line.convertOptions(options))
        }


        const format_line_chart = () =>{
            console.log('0', data_line)
            var data = google.visualization.arrayToDataTable(data_line)
            var options = {
                title: title_line,
                curveType: 'function',
                legend: { position: 'bottom' }
            };
            //var chart = new google.visualization.LineChart(document.getElementById(`line_chart_${index}`));
            var chart = new google.charts.Line(document.getElementById(`line_chart_${index}`));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
@stop