
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
    <li class="nav-item">
		<a class="nav-link" href="{{route('nomina.empleados.index')}}"> Empleados</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link" data-toggle="pill" href="#personas"> Nominas</a>
	</li>
	<li class="nav-item">
		<a class="nav-link"  href="{{route('nomina.create', 'empleado')}}">Nueva Nomina</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3><b>Nomina</b></h3>
		</strong>
	</div>
	<div class="container-fluid">
		<div class="table-responsive">
			<div class="box">
				<div class="box-body">
						<table class="table table-bordered cell-border table-hover" id="example"  data-form="deleteForm">
							<thead>
								<tr class="active">
									<th class="text-center">#</th>
									<th class="text-center">Nomina</th>
									<th class="text-center"># Pagos</th>
									<th class="text-center">Valor Total</th>
									<th>opciones</th>
								</tr>
							</thead>

							<tbody>
							@foreach($nominas as $key => $nomina)
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$nomina->mes}}</td>
									<td>{{$nomina->empleados_nominas->count()}}</td>
									<td>${{number_format($nomina->empleados_nominas->sum('neto_pagar'), 2)}}</td>
									<td>
										<a href="{{route('nomina.show', $nomina->id)}}" class="btn btn-sm btn-primary" title="ver nomina">
											<i class="fa fa-eye" aria-hidden="true"></i>
										</a>
										@if(!$nomina->finalizado)
											<a href="{{route('nomina.edit', $nomina->id)}}" class="btn btn-sm btn-primary" title="editar nomina">
												<i class="fa fa-pencil" aria-hidden="true"></i>
											</a>
										@else
											<a href="{{route('nomina.pdf', $nomina->id)}}" class="btn btn-sm btn-primary" title="pdf nomina" target="_blank">
												<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
											</a>
											<a href="{{route('nomina.pdf-desprendibles', $nomina->id)}}" class="btn btn-sm btn-primary" title="pdf desprendibles" target="_blank">
												<i class="fa fa-address-card-o" aria-hidden="true"></i>
											</a>
											<a href="{{route('nomina.pdf-contabilidad')}}" class="btn btn-sm btn-primary" title="pdf contable" target="_blank">
												<i class="fa fa-calculator" aria-hidden="true"></i>
											</a>
											<a href="{{route('nomina.empleados-cuentas', $nomina->id)}}" class="btn btn-sm btn-primary" title="pdf Empleados cuentas bancarias" target="_blank">
												<i class="fa fa-cc-diners-club" aria-hidden="true"></i>
											</a>
										@endif
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

		
   </script>
@stop
