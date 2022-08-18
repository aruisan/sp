
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Creacion de personas
@stop

@section('sidebar')
	{{-- <li><a href="{{route('personas.index')}}" class="btn btn-success">Listar Terceros</a></li> --}}
@stop

@section('content')


            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link regresar" href="{{route('personas.index')}}"> Lista Terceros</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" data-toggle="pill" href="#crear">Nuevo Tercero</a>
                </li>
             
            </ul>
     
            <div class="tab-content" style="background-color: white">
                <div id="lista" class="tab-pane active"><br>

	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				 @include('personas.partials._form', ['persona' => $persona, 'route' => 'personas.store', 'method' => 'POST'])
			</div>
		</div>
	</div>
		</div>
	</div>
@stop

