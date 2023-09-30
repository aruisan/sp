@extends('layouts.dashboard')

@section('titulo')
    Editando Predio
@stop
@section('sidebar')
  {{-- @include('cobro.predios.cuerpo.aside') --}}
@stop

@section("content")

<div class="breadcrumb text-center">
        <strong>
            <h3><b>Editar Predio</b></h3>
        </strong>
    </div>
			
			<ul class="nav nav-pills">
      <li class="nav-item">
            <li role="presentation" class="nav-item"> <a class="nav-link regresar"  href="{{url('predios')}}">Volver a Predios</a></li>
            <li role="presentation" class="nav-item active"><a class="nav-link " href="#editar">Editar Predio</a></li>
					</li>
				</ul>
			
	 <div class="col-lg-12 " style="background-color:white;">
   <br>  <br>
            <div class="tab-content">

                 <div id="datos" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane fade in active">

        	@include('cobro.predios.partials._form', ['predio' => $predio, 'url' => 'predios/'.$predio->id, 'method' => 'PATCH'])
    	</div>
    </div>  
  </div>

@endsection