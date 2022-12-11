@extends('layouts.CEPdf')
@section('contenido')
    <div class="row">
        <br>
        <h2 class="text-center text-primary">Datos</h2>
        <br><br>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>Nombre:</b></td>
                        <td>{{$individuo->nombre}}</td>
                        <td><b>Color:</b></td>
                        <td>{{$individuo->color}}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha y Hora:</b></td>
                        <td>{{$individuo->date_at}}</td>
                            <td><b>Ficha de Ingreso:</b></td>
                        <td>{{$individuo->ficha_ingreso}}</td>
                    </tr>
                    <tr>
                        <td><b>Tipo:</b></td>
                        <td>{{$individuo->tipo}}</td>
                        <td><b>Sexo:</b></td>
                        <td>{{$individuo->sexo}}</td>
                    </tr>
                    <tr>
                        <td><b>Peso:</b></td>
                        <td>{{$individuo->peso}}</td>
                        <td><b>Talla:</b></td>
                        <td>{{$individuo->talla}}</td>
                    </tr>
                        <tr>
                        <td><b>Marcas:</b></td>
                        <td colspan="3">{{$individuo->marcas}}</td>
                    </tr>

                </tbody>
            </table>
        </div>
        <br>
        <h2 class="text-center text-primary">Galeria</h2>
        <br><br>
        @foreach($individuo->archivos->chunk(4) as $chunk)
            <div class="row">
                @foreach($chunk as $foto)
                    <div class="col-md-3">
                        <img src="{{url($foto->url)}}" class="img-responsive"/>
                    </div>
                @endforeach
            </div>
        @endforeach
        
    </div>
    <br>
    <h2 class="text-center text-primary">Alimentación</h2>
        <br><br>
    <div>
        <table class="table">
            <tr>
                <td>#</td>
                <td>Procion Diaria (Kg)</td>
                <td>Mañana (Kg)</td>
                <td>Tarde (Kg)</td>
                <td>Noche (Kg)</td>
            </tr>
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
    <br>
    <h2 class="text-center text-primary">Medicamentos</h2>
    <br><br>
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
                        <div id="collapse{{$k}}" class="panel-collapse collapse in">
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
                                    <tr>
                                        <td>Medicamento</td>
                                        <td>Dosis Diaria</td>
                                        <td>Hora</td>
                                        <td>Termino</td>
                                        <td>Aplica</td>
                                    </tr>
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