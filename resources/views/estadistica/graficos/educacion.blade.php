@extends('layouts.dashboard')
@section('titulo')
    Reserva vuelo
@stop
@section('content')
    <ul class="nav nav-tabs">
        @foreach($items as $i => $item)
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#coleccion_{{$i}}">{{$item->coleccion}}</a>
            </li>
            <li class="{{$i == 0 ? 'active' : ''}}">
                <a data-toggle="tab" href="#home">Home</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($items as $k => $item)
            <div class="tab-pane {{$k == 0 ? 'active' : ''}} container" id="coleccion_{{$k}}">
                <div class="col-md-2">
                    <div class="btn-group-vertical">
                        @foreach($item->data as $d => $row)
                            <button type="button" class="btn btn-primary btn-sm" onclick="select_data({{$k}},'{{$d}}' )">{{str_replace("_"," ", $d)}}</button>
                        @endforeach
                    </div>
                </div>
                <div id="charts_{{$k}}" class="col-md-10"></div>
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
        google.charts.load('current', {'packages':['corechart']});
        

        $(document).ready(function(){
          //  line_chart();
            load_data();
            //load_tabs 
        })

        const load_data = () => { 
            item_select = data[0];
            let keys = Object.keys(item_select.data);
            select_data(0, keys[0])
        }; 


        const select_data = (index_item, index_data) => {
            item_select = data[index_item];
            index = index_item
            $(`#charts_${index_item}`).empty().append(`<div id="line_chart_${index}" style="width: 900px; height: 500px"></div>`)
                                            .append(`<div id="bart_chart_${index}" style="width: 900px; height: 500px"></div>`)
            title_line = index_data.replace('_', ' ');
            data_line = [['Ano', 'Cantidad']];
            let keys = Object.keys(item_select.data);
            let values = Object.values(item_select.data);
            let valores = values.length == 6 ? ages : months;
            valores.forEach((v,m) => {
                data_line.push([v, item_select.data[index_data][m] == 0 ? 1: item_select.data[index_data][m]]);
            });
            google.charts.setOnLoadCallback(eval(`format_line_chart`));
        }


        const format_line_chart = () =>{
            console.log('0', data_line)
            var data = google.visualization.arrayToDataTable(data_line)
            var options = {
                title: title_line,
                curveType: 'function',
                legend: { position: 'bottom' }
            };
            var chart = new google.visualization.LineChart(document.getElementById(`line_chart_${index}`));
            chart.draw(data, options);
        }
    </script>
@stop