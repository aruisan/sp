{!! Form::Open(['route' => $route, 'method' => $method]) !!}
<div class="col-12 formularioTerceros">
    <div class="row justify-content-center"><br>
        <div class="breadcrumb text-center">
            <center><h2>Formulario de Firmas</h2></center>
        </div><br>
    </div>
    <div class="row inputCenter">
        <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
            <div class="form-group">
                {{ Form::label('Nombre Completo', 'Nombre Completo')}}
                {{ Form::text('nombres', $firma->nombres, ['class' => 'form-control', 'placeholder' => 'Nombre Completo', 'required', 'value' => old("title")]) }}
            </div>
        </div>
        <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
            <div class="form-group">
                {{ Form::label('Cargo', 'Cargo')}}
                {{ Form::select('tipo', ['PRESIDENTE'=> 'PRESIDENTE', 'SECRETARIA GENERAL' =>'SECRETARIA GENERAL', 'PRIMER VICEPRESIDENTE' =>
                    'PRIMER VICEPRESIDENTE', 'SEGUNDO VICEPRESIDENTE' => 'SEGUNDO VICEPRESIDENTE', 'CONTADOR' => 'CONTADOR/A'], $firma->tipo, ['class' => 'form-control','placeholder' => 'Selecciona El Cargo de la Persona', 'required']) }}
            </div>
        </div>
    </div>

    <div class="row inputCenter" >

        <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
            <div class="form-group">
                    {{ Form::label('Fecha de Inicio', 'Fecha de Inicio')}}
                    {{ Form::date('fecha_inicio', $firma->fecha_inicio, ['class' => 'form-control','placeholder' => 'Selecciona la Fecha de Inicio', 'required']) }}
            </div>
        </div>
        <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
            <div class="form-group">
                {{ Form::label('Fecha de Fin', 'Fecha de Fin')}}
                {{ Form::date('fecha_fin', $firma->fecha_fin, ['class' => 'form-control','placeholder' => 'Selecciona la Fecha Final', 'required']) }}
            </div>
        </div>
        <div class="form-group text-center">
            <input type="submit" value="Guardar" class="btn-danger  btn-lg" >
        </div>
    </div>

{!! Form::close()!!}