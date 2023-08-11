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
        @include('coso.components.tab', ['url' => 'galeria'])
    </div>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            @include('coso.components.data')
            <br>
                <button data-toggle="modal" data-target="#myModal" class="btn btn-success">
                    Nuevo +
                </button>
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


        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Nuevo Foto</h4>
                </div>
                <form method="post" action="{{route('coso.archivo.store', $individuo->id)}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Archivo:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="file" name="archivo">
                                    </div>
                                </div>
                            </div>
                        </div><br>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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