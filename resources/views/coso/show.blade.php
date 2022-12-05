@extends('layouts.dashboard')
@section('titulo')
    Coso Galeria
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Coso Galeria</b></h4>
        </strong>
    </div>
    <div class="row">
        @include('coso.components.tab', ['url' => 'ver'])
    </div>
    <div class="row">
        <br>
        @include('coso.components.data')
        <br>
        @foreach($individuo->archivos->chunk(4) as $chunk)
            <div class="row">
                @foreach($chunk as $foto)
                    <div class="col-md-3">
                        <img src="{{$foto->url}}" class="img-responsive"/>
                    </div>
                @endforeach
            </div>
        @endforeach
        
    </div>

    <div class="row">
        <table class="table">
            <thead>
                <th>#</th>
                <th>Procion Diaria (Kg)</th>
                <th>Mañana (Kg)</th>
                <th>Tarde (Kg)</th>
                <th>Noche (Kg)</th>
            </thead>
            <tbody id="body">
                @foreach($individuo->comidas as $k => $comida)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$comida->porcion_diaria}}</td>
                        <td>{{$comida->porciones[0]}}</td>
                        <td>{{$comida->porciones[1]}}</td>
                        <td>{{$comida->porciones[2]}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

     <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            @include('coso.components.data')
            
            <div class="panel-group" id="accordion">
                @foreach($individuo->veterinarios as $k => $item)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$k}}">
                                    {{$item->nombre_veterinario}} {{$item->created_at->format('Y-m-d')}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$k}}" class="panel-collapse collapse {{$k == 0 ? 'in': ''}}">
                            <div class="panel-body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><b>Tarjeta Profesional:</b></td>
                                            <td>{{$item->tarjeta_profesional}}</td>
                                            <td><b>Cedula:</b></td>
                                            <td>{{$item->cedula}}</td>
                                            <td><b>Celular:</b></td>
                                            <td>{{$item->celular}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <table class="table">
                                    <thead>
                                        <th>Medicamento</th>
                                        <th>Dosis Diaria</th>
                                        <th>Hora</th>
                                        <th>Termino</th>
                                        <th>Aplica</th>
                                    </thead>
                                    <tbody>
                                        @foreach($item->medicinas as $medicina)
                                        <tr>
                                            <td>{{$medicina->medicamento}}</td>
                                            <td>{{$medicina->dosis_diaria}}</td>
                                            <td>{{$medicina->hora}}</td>
                                            <td>{{$medicina->termino}}</td>
                                            <td>{{$medicina->aplica}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                @endforeach
            </div>
            
        </div>
    </div>


        @stop
        @section('css')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">
        @stop
        @section('js')
            <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
            <script>
                $('#tabla_INV').DataTable( {
                    responsive: true,
                    "searching": true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'print'
                    ]
                } );
            </script>
        @stop