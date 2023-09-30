@extends('layouts.dashboard')

@section('titulo')
    Detalles de Predio
@stop
@section('sidebar')
            {{-- @include('cobro.predios.cuerpo.aside') --}}
@stop

@section("content")

   <div class="breadcrumb text-center">
        <strong>
            <h4><b>Detalles del Predio</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
      <li class="nav-item">
            <a class="nav-link regresar"  href="{{url('predios')}}">Volver a Predios</a>
        </li> 

           <li class="nav-item active">
            <a class="nav-link"  href="#detalles"> Detalles Predio </a>
        </li>
        <li class="nav-item ">
            <a class="nav-link"  href="{{url('predios/create')}}"> Crear Predio </a>
        </li>
     


     
    </ul>
<div style="height: 100%;display: flex;background: white;flex-wrap: wrap;width: 100%;">
  
  <div class="row" style="justify-content: center;display: : flex; width: 100%;">
    <div class="col-lg-12 margin-tb">
            <h2 class="text-center">Detalles del Predio</h2>
    </div>
    <div class="container-fluid">
      <div class="white">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-danger" style="display: flex;flex-direction: column;">
                <div class="panel-heading text-center">Datos Generales</div>
                <div class="panel-body">
                    {{ Form::label('ficha_catastral', 'Ficha Catastral', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('ficha_catastral', $predio->ficha_catastral, ['disabled', 'class' => 'form-control', 'placeholder' => 'Ficha Catastral']) }}
                    </div>
                        {{ Form::label('Matricula Inmobiliaria', 'Matrícula Inmobiliaria', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('matricula_inmobiliaria', $predio->matricula_inmobiliaria, ['disabled', 'class' => 'form-control', 'placeholder' => 'Matricula Inmobiliaria']) }}
                    </div>
                        {{ Form::label('direccion_predio', 'Dirección Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('direccion_predio', $predio->direccion_predio, ['disabled', 'class' => 'form-control', 'placeholder' => 'Direccion Del Predio']) }}            
                    </div>
                        {{ Form::label('nombre_predio', 'Nombre Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('nombre_predio', $predio->nombre_predio, ['disabled', 'class' => 'form-control', 'placeholder' => 'Nombre Del Predio']) }}
                    </div>
                        {{ Form::label('estrato', 'Estrato', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('estrato', $predio->estrato, ['disabled', 'class' => 'form-control', 'placeholder' => 'Estrato']) }}            
                    </div>
                        {{ Form::label('a_hectareas', 'Hectareas del Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('a_hectareas', $predio->a_hectareas, ['disabled', 'class' => 'form-control', 'placeholder' => 'Hectarias Del Predio']) }}            
                    </div>
                        {{ Form::label('a_metros', 'Metros del Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('a_metros', $predio->a_metros, ['disabled', 'class' => 'form-control', 'placeholder' => 'Metros Del Predio']) }}            
                    </div>
                        {{ Form::label('a_construida', 'Área Construida', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('a_construida', $predio->a_construida, ['disabled', 'class' => 'form-control', 'placeholder' => 'Area SConstruida']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-danger" style="display: flex;flex-direction: column;">
                <div class="panel-heading text-center">Datos Económicos</div>
                <div class="panel-body">
                        {{ Form::label('avaluo', 'Avalúo', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('avaluo', $predio->avaluo, ['disabled', 'class' => 'form-control', 'placeholder' => 'Evaluo']) }}            
                    </div>
                        {{ Form::label('v_declarado', 'Valor Declarado', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('v_declarado', $predio->v_declarado, ['disabled', 'class' => 'form-control', 'placeholder' => 'Valor Declarado']) }}            
                    </div>
                        {{ Form::label('impuesto_predial', 'Impuesto Predial', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('impuesto_predial', $predio->impuesto_predial, ['disabled', 'class' => 'form-control', 'placeholder' => 'Impuesto Predial']) }}            
                    </div>            
                        {{ Form::label('interes_predial', 'Interés Predial', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('interes_predial', $predio->interes_predial, ['disabled', 'class' => 'form-control', 'placeholder' => 'Interes Predial']) }}            
                    </div>
                        {{ Form::label('contribucion_car', 'Contribución', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('contribucion_car', $predio->contribucion_car, ['disabled', 'class' => 'form-control', 'placeholder' => 'Contribucion']) }}            
                    </div>
                        {{ Form::label('interes_Car', 'Interés', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('interes_Car', $predio->interes_Car, ['disabled', 'class' => 'form-control', 'placeholder' => 'Interes']) }}            
                    </div>
                        {{ Form::label('otros_conceptos', 'Otros Conceptos', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('otros_conceptos', $predio->otros_conceptos, ['disabled', 'class' => 'form-control', 'placeholder' => 'Otros Conceptos']) }}            
                    </div>
                        {{ Form::label('cuantia', 'Cuantía', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('cuantia', $predio->cuantia, ['disabled', 'class' => 'form-control', 'placeholder' => 'Cuantia']) }}            
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-danger" style="display: flex;flex-direction: column;">
                <div class="panel-heading text-center">Datos Socioeconómicos</div>
                <div class="panel-body">
                        {{ Form::label('tipo_tarifa', 'Tipo de Tarifa', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('tipo_tarifa', $predio->tipo_tarifa, ['disabled', 'class' => 'form-control', 'placeholder' => 'Tipo de Tarifa']) }}       
                    </div>
                        {{ Form::label('destino_economico', 'Destino Económico', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('destino_economico', $predio->destino_economico, ['disabled', 'class' => 'form-control', 'placeholder' => 'Destino Economico']) }}
                    </div>
                        {{ Form::label('porc_tarifa', 'Porcentaje de la Tarifa', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('porc_tarifa', $predio->porc_tarifa, ['disabled', 'class' => 'form-control', 'placeholder' => 'Porcentaje de la Tarifa']) }}          
                    </div>
                        {{ Form::label('incio', 'Año Inicio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('inico', $predio->inico, ['disabled', 'class' => 'form-control', 'placeholder' => 'Año Inicial']) }}            
                    </div>
                        {{ Form::label('fiñal', 'Año Final', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('final', $predio->final, ['disabled', 'class' => 'form-control', 'placeholder' => 'Año Final']) }}            
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-danger" style="display: flex;
    flex-direction: column;">
                <div class="panel-heading text-center">Datos del Proceso</div>
                <div class="panel-body">
                        {{ Form::label('existe', 'Existe', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::select('existe', ['1' => 'SI', '0' => 'NO'], $predio->existe, ['disabled', 'placeholder' => 'Selecciona Si Existe El Predial']) }}            
                    </div>
                        {{ Form::label('ubicacion', 'Ubicación del Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('ubicacion', $predio->ubicacion, ['disabled', 'class' => 'form-control', 'placeholder' => 'Ubicacion del Predial']) }}            
                    </div>
                        {{ Form::label('exento', 'Exento', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::select('exento', ['1' => 'SI', '0' => 'NO'], $predio->exento, ['disabled', 'placeholder' => 'Selecciona Si Esta Exento']) }}            
                    </div>
                        {{ Form::label('semaforo', 'Semáforo', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('semaforo', $predio->semaforo, ['disabled', 'class' => 'form-control', 'placeholder' => 'Semafaro']) }}            
                    </div>
                        {{ Form::label('estado', 'Estado', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('estado', $predio->estado, ['disabled', 'class' => 'form-control', 'placeholder' => 'Estado']) }}            
                    </div>
                    {{ Form::label('observacion', 'Observación del Predio', ['class' => 'control-label col-xs-12 col-sm-6 col-md-6 col-lg-6'])}}
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        {{ Form::text('observacion', $predio->observacion, ['disabled', 'class' => 'form-control', 'placeholder' => 'observacion']) }}            
                    </div>
                </div>
            </div> 
        </div>
    </div>
    <div class="row">
        <center>
            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <a href="{{ url('predios') }}" class="btn btn-default" style="color:black">Regresar al Listado De Predios</a>
            </div>
        </center>
    </div>

          
      </div>
    </div>  
  </div>
</div>
@endsection