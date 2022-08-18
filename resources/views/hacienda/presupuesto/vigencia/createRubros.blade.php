@extends('layouts.dashboard')
@section('titulo')
    Creación de Rubros
@stop
@section('sidebar')
	{{-- @if($vigencia->tipo == 0)
		@if($vigencia->vigencia == Carbon\Carbon::now()->year)
    		<li> <a href="{{ url('/presupuesto') }}" class="btn btn-success"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
		@else
			<li> <a href="{{ url('/newPre/'.$vigencia->tipo.'/'.$vigencia->vigencia) }}" class="btn btn-success"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
		@endif
	@elseif($vigencia->tipo == 1)
		@if($vigencia->vigencia == Carbon\Carbon::now()->year)
			<li> <a href="{{ url('/presupuestoIng') }}" class="btn btn-success"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
		@else
			<li> <a href="{{ url('/newPreIng/'.$vigencia->tipo.'/'.$vigencia->vigencia) }}" class="btn btn-success"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
		@endif
	@endif --}}
    {{-- <li class="dropdown">
        <a class="dropdown-toggle btn btn btn-primary" data-toggle="dropdown" href="#">
            <span class="hide-menu">Niveles</span>
            &nbsp;
            <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            @foreach($niveles as $level)
                <li><a href="/presupuesto/registro/create/{{ $level->vigencia_id }}" class="btn btn-primary">Nivel {{ $level->level }}</a></li>
            @endforeach
            <li><a href="/presupuesto/font/create/{{ $vigencia_id }}" class="btn btn-primary">Fuentes</a></li>
            <li><a href="/presupuesto/rubro/create/{{ $vigencia_id }}" class="btn btn-primary">Rubros</a></li>
            <li><a href="/presupuesto/level/create/{{ $vigencia_id }}" class="btn btn-primary">Nuevo Nivel</a></li>
        </ul>
    </li> --}}
@stop
@section('content')
    <div class="col-md-12 align-self-center" id="crud">
        <div class="row justify-content-center">
         <div class="breadcrumb text-center">
        <strong>
            <h2>  Creación de Rubros para la Vigencia {{ $vigencia->vigencia }}</h2>
        </strong>
    </div>

 	<ul class="nav nav-pills">
		@if($vigencia->tipo == 0)
			@if($vigencia->vigencia == Carbon\Carbon::now()->year)
				<li class="nav-item regresar"> <a href="{{ url('/presupuesto') }}" class="nav-link"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
			@else
				<li class="nav-item regresar"> <a href="{{ url('/newPre/'.$vigencia->tipo.'/'.$vigencia->vigencia) }}" class="nav-link"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
			@endif
		@elseif($vigencia->tipo == 1)
			@if($vigencia->vigencia == Carbon\Carbon::now()->year)
				<li class="nav-item regresar"> <a href="{{ url('/presupuestoIng') }}" class="nav-link"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
			@else
				<li class="nav-item regresar"> <a href="{{ url('/newPreIng/'.$vigencia->tipo.'/'.$vigencia->vigencia) }}" class="nav-link"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
			@endif
		@endif
		<li class="nav-item active"> <a href="#crear" class="nav-link">Seleccionar Rubro</a></li>
			@if(count($rubrosChecked) > 0)
				<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/1',$vigencia_id) }}" class="nav-link"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></li>
			@endif
	</ul>
		<form action="{{ url('/presupuesto/rubro') }}" method="POST"  class="form">
			{{ csrf_field() }}
			<input type="hidden" id="vigencia_id" name="vigencia_id" value="{{ $vigencia_id }}">
			<div class="table-responsive">   <br><br>
				<table class="table table-bordered" id="tabla">
					<thead>
					<th class="text-center">Codigo</th>
					<th class="text-center">Nombre</th>
					<th class="text-center"><i class="fa fa-check"></i></th>
					</thead>
					<tbody>
					@foreach($plantilla as $data)
						<tr>
							<td>{{$data['code']}}</td>
							<td>{{$data['name']}}</td>
							<td class="text-center">
								@if($data['hijo'] == 1)
									@foreach($rubrosChecked as $checked)
										@if($checked['plantilla_cuipos_id'] == $data['id'])
											@php
												$validate = true;
												break;
											@endphp
										@else
											@php
												$validate = false;
											@endphp
										@endif
									@endforeach
										@if($validate)
											<input type="checkbox" v-on:click.prevent="eliminarDatos({{ $data->id }},{{$vigencia_id}})" name="checkedIDs[]" value="{{$data['id']}}" checked/>
										@else
											<input type="checkbox" name="checkedIDs[]" value="{{$data['id']}}"/>
										@endif
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<br><center><button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> &nbsp; Guardar Rubros Actuales</button></center>
			@if(count($rubrosChecked) > 0)
				<center><a href="{{ url('/presupuesto/rubro/CUIPO/1',$vigencia_id) }}" class="btn btn-primary"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></center>
			@endif
		</form>
 </div>
 </div>
@stop

@section('js')
<script>

$(document).ready(function() {
$('#tabla').DataTable( {
	responsive: true,
	"searching": false,
	paging: false,
	"oLanguage": {"sZeroRecords": "", "sEmptyTable": ""
	}
} );
} );

//funcion para borrar una celda
$(document).on('click', '.borrar', function (event) {
event.preventDefault();
$(this).closest('tr').remove();
});

new Vue({
el: '#crud',

methods:{

eliminarDatos: function(dato, vigencia){
 var urlVigencia = '/presupuesto/rubro/'+dato+'/'+vigencia;
 axios.delete(urlVigencia).then(response => {
     toastr.error('Rubro eliminado correctamente');
	 document.location.reload(true);
 });
},

nuevaFila: function(){

 $('#tabla tr:last').after("<tr><td><select name='register_id[]' class='form-control'>@foreach($registers as $register)<option value='{{ $register->id }}'>@foreach($codigos as $codigo)@if($codigo['id'] == $register->id) {{$codigo['codigo'] }} @endif @endforeach - {{ $register->name }}</option>@endforeach</select></td><td><select name='subproyecto_id[]'  class='form-control'>@foreach($subProy as $subProys)<option value='{{ $subProys->id }}'>{{ $subProys->name }}</option>@endforeach</select></td><td><input type='hidden' name='rubro_id[]'><input type='text' name='code[]'></td><td><input type='text' name='nombre[]'></td></td><td></td><td></td><td style='vertical-align:middle;' class='text-center'><input type='button' class='borrar btn-sm btn-danger' value='-'/></td></tr>");
}
}
});
</script>
@stop