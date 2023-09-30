@extends('layouts.dashboard')
@section('titulo')
    Crear Carpeta
@stop
@section('sidebar')
    {{-- <li> <a class="btn btn-primary" href="{{ asset('/dashboard/boletines') }}"><span class="hide-menu">Boletines</span></a></li> --}}
@stop
@section('content')

<div class="col-xs-12 col-sm-12 col-md-12 formularioBoletin">


        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> Nueva Carpeta</h2>
            </div>
        </div>
        
<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
        
        <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link regresar" id="btn-volver"  href="" ></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link " data-toggle="pill" href="#ver">Nuevo</a>
                </li>
            </ul>
            
    <div class="tab-content col-sm-12" >
  
    <br>
    <hr>
    {!! Form::open(array('route' => 'carpetas.store','method'=>'POST')) !!}
    <input type="hidden" name="rutaIndex" id="rutaIndexCarpeta">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Nombre: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-folder-o" aria-hidden="true"></i></span>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <small class="form-text text-muted">Nombre que se desee asignar a la carpeta</small>
        </div>
    

        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Ubicación Fisica: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
                <input type="text" class="form-control" name="ubicacion_fisica" required>
            </div>
            <small class="form-text text-muted">Ubicación Fisica</small>
        </div>
    </div>


    <div class="row">
        <div class="form-group col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <label>Cuantia: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money" aria-hidden="true"></i></span>
                <input type="text" name="cuantia" class="form-control" required>
            </div>
            <small class="form-text text-muted">Cuantia</small>
        </div>
        <div class="form-group col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <label>Tipo de Carpeta: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-archive" aria-hidden="true"></i></span>
                <input type="text" name="tipo" id="tipoCarpeta" class="form-control" readonly>
            </div>
        </div>    
    </div>


    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
        <button class="btn btn-primary btn-raised btn-lg" id="storeRegistro">Guardar</button>
    </div>
    {!! Form::close() !!}
</div>

</div>
</div>

@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            let tipoCarpeta = localStorage.getItem("tipoCarpeta");
            let rutaIndexCarpeta = localStorage.getItem("rutaIndexCarpeta");
            $('#btn-volver').attr('href', rutaIndexCarpeta).text(`Volver a ${tipoCarpeta}`);
            $('#tipoCarpeta').val(tipoCarpeta);
            $('#rutaIndexCarpeta').val(rutaIndexCarpeta);
        });
    </script>
@endsection
