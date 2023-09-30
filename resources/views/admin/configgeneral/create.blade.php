@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Creación de firmas
@stop

@section('content')
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link regresar" href="{{ route('configGeneral.index') }}"><i class="fa fa-home"></i></a>
        </li>

        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#crear">Nueva Firma</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="lista" class="tab-pane active">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                         @include('admin.configgeneral.partials._form', ['firma' => $firma, 'route' => 'configGeneral.store', 'method' => 'POST'])
                    </div>
                </div>
            </div>
		</div>
	</div>
@stop

