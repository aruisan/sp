@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Editar articulo {{$articulo->nombre_articulo}}</b></h4>
        </strong>
    </div>
    <div class="row">
        <form action="{{route('almacen.articulo.update', $articulo->id)}}" method="post">
            {{ csrf_field() }}
            {!! method_field('PUT') !!} 
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">NOMBRE DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="nombre_articulo" value="{{$articulo->nombre_articulo}}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="codigo">CODIGO DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="codigo" value="{{$articulo->codigo}}" required>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="referencia">REFERENCIA DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="referencia" value="{{$articulo->referencia}}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="presentacion">PRESENTACION DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="presentacion" value="{{$articulo->presentacion}}" required>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="marca">MARCA DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="marca" value="{{$articulo->marca}}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="vida_util">VIDA UTIL DEL ARTICULO:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="number" class="form-control" name="vida_util" value="{{$articulo->vida_util}}" required>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <a class="btn btn-danger">Cancelar</a>
            </div>
            
        </form>
    </div>
@stop
@section('js')
    <script>
       
    </script>
@stop