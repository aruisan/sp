
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Personas
@stop

@section('sidebar')
	{{-- <li><a href="{{route('personas.create')}}" class="btn btn-success">Nuevo Tercero</a></li> --}}
@stop

@section('content')


<ul class="nav nav-pills">
	<li class="nav-item active">
		<a class="nav-link" data-toggle="pill" href="#personas"> Empleados</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina.empleados.create')}}">Nuevo Empleado</a>
	</li>
	<li class="dropdown-submenu">
		<a class="dropdown-item item-menu">Nomina</a>
		<ul class="dropdown-menu">
			<li><a class="item-menu" href="{{route('nomina.index', 'empleado')}}">Sueldo</a></li>
			<li><a class="item-menu" href="">Prima Navidad</a></li>
			<li><a class="item-menu" href="">Vacaciones</a></li>
			<li><a class="item-menu" href="">Prima de Vacaciones</a></li>
			<li><a class="item-menu" href="">Prima de Antiguedad</a></li>
			<li><a class="item-menu" href="">Bonificación</a></li>
		</ul>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3><b>Empleados</b></h3>
		</strong>
	</div>
	<div class="container-fluid">
		<div class="table-responsive">
			<div class="box">
				<div class="box-body">
						<table class="table table-bordered cell-border table-hover" id="example"  data-form="deleteForm">
							<thead>
								<tr class="active">
									<th class="text-center">Nombre</th>
									<th class="text-center">Numero Documento</th>
									<th class="text-center">Email</th>
									<th class="text-center">Direccion</th>
									<th class="text-center">Telefono</th>
									<th class="text-center">Edad</th>
									<th class="text-center">Cargo</th>
									<th class="text-center">Código cargo</th>
									<th class="text-center">Tipo cargo</th>
									<th class="text-center">Salario</th>
									<th class="text-center">Grado</th>
									<th class="text-center">Certificado bancario</th>
									<th class="text-center"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></th>
									<th class="text-center"><i class="fa fa-lock" aria-hidden="true"></i> | <i class="fa fa-unlock" aria-hidden="true"></i></th>
									<th class="text-center">Hv</th>
								</tr>
							</thead>

							<tbody>
							@foreach($empleados as $persona)
								<tr>
									<td>{{$persona->nombre}}</td>
									<td>{{$persona->num_dc}}</td>
									<td>{{$persona->email}}</td>
									<td>{{$persona->direccion}}</td>
									<td>{{$persona->telefono}}</td>
									<td>{{$persona->edad}}</td>
									<td>{{$persona->cargo}}</td>
									<td>{{$persona->codigo_cargo}}</td>
									<td>{{$persona->tipo_cargo}}</td>
									<td>${{$persona->salario}}</td>
									<td>{{$persona->grado}}</td>
									<td>{{$persona->certificado_cuenta_bancaria}}</td>
									<td><a href="{{ route("nomina.empleados.edit", $persona->id)}}" class="btn btn-xs btn-danger">
									<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>
									<td><button class="btn btn-xs btn-danger" onclick="eliminar({{$persona->id}})" id="btn_eliminar_{{$persona->id}}" title="{{$persona->activo ? 'activo' : 'inactivo'}}">
									<i class="fa {{$persona->activo ? 'fa-unlock' : 'fa-lock'}}" aria-hidden="true" ></i></button></td>
									<td><a href="{{ route("nomina.empleados.edit", $persona->id)}}" class="btn btn-xs btn-danger" title="Hoja de vida">
										<i class="fa fa-book" aria-hidden="true"></i>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



@stop

@section('js')
<script>
	$('#example').DataTable( {
		language: {
			"lengthMenu": "Mostrar _MENU_ registros",
			"zeroRecords": "No se encontraron resultados",
			"info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
			"infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
			"infoFiltered": "(filtrado de un total de _MAX_ registros)",
			"sSearch": "Buscar:",
			"oPaginate": {
				"sFirst": "Primero",
				"sLast":"Último",
				"sNext":"Siguiente",
				"sPrevious": "Anterior"
			},
			"sProcessing":"Procesando..."
		},
	  //para usar los botones   
		"pageLength": 5,
		responsive: "true",
		dom: 'Bfrtilp',       
		buttons:[ 
			{
				extend:    'copyHtml5',
				text:      '<i class="fa fa-clone"></i> ',
				titleAttr: 'Copiar',
				className: 'btn btn-primary'
			},
			{
				extend:    'excelHtml5',
				text:      '<i class="fa fa-file-excel-o"></i> ',
				titleAttr: 'Exportar a Excel',
				className: 'btn btn-primary'
			},
			{
				extend:    'pdfHtml5',
				text:      '<i class="fa fa-file-pdf-o"></i> ',
				titleAttr: 'Exportar a PDF',     
				message : 'SIEX',
				header :true,
				exportOptions: {
					columns: [ 0,1,2,3,4]
				},
				pageSize: 'LEGAL',
				className: 'btn btn-primary',
			},
			{
				extend:    'print',
				text:      '<i class="fa fa-print"></i> ',
				titleAttr: 'Imprimir',
				className: 'btn btn-primary'
			}
	  	]	             

	});

	$(document).ready(function() {
		var table = $('#example').DataTable();
	} );

	const eliminar = async id => {
		console.log('id', id);
		let response =  await fetch(`/nomina/empleados/status/`+id)
							.then(res => res.json())
							.catch(error => console.error('Error:', error))
							.then(res => res);

		console.log(response);
		
			if(response){
				$(`#btn_eliminar_${id}`).attr('title', 'activo').html('<i class="fa fa-unlock" title="activo" aria-hidden="true"></i>');
			}else{
				$(`#btn_eliminar_${id}`).attr('title', 'inactivo').html('<i class="fa fa-lock" title="inactivo" aria-hidden="true"></i>');
			}
	}
		
   </script>
@stop
