@extends('layouts.dashboard')

@section('titulo')
    Predios
@stop
@section('sidebar')
  {{-- @include('cobro.predios.cuerpo.aside') --}}
@stop
@section('content')

 

	  <div class="breadcrumb text-center">
        <strong>
            <h3><b>Predios</b></h3>
        </strong>
    </div>
			
			<ul class="nav nav-pills">
    		  <li class="nav-item">
			@isset($predios)	<li role="presentation" class="nav-item active"><a class="nav-link"  data-toggle="pill"  href="#datos">Predios</a></li>	@endisset	
				<li role="presentation" class="nav-item"><a class="nav-link " data-toggle="pill" href="#importar">Importar Predios</a></li>
				<li role="presentation" class="nav-item"><a class="nav-link" href="{{url('predios/create')}}">Crear Predio</a></li>
				

					@if (Auth::user()->isCobrocoactivo())
						<li role="presentation"><a href="{{route('unnassigned')}}">Predios sin Asignar</a></li>
					<li role="presentation"><a href="{{route('assignor')}}">Predios Asignados</a></li> 

					@endif
					</li>
				</ul>
				
	
	 <div class="col-lg-12 " style="background-color:white;">
            <div class="tab-content">
				<br>
                 <div id="datos" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane fade in active">

							@isset($predios)
							<div class="table-responsive">
								<table class="table table-bordered cell-border table-hover" id="example"  data-form="deleteForm">
								<thead>
									<tr class="active">
										<th class="text-center">Ficha Catastral</th>
										<th class="text-center">Matricula Inmobiliaria</th>
										<th class="text-center">Dirección</th>
										<th class="text-center">Nombre</th>
										{{-- @if(Auth::user()->type->expediente == 1) --}}
											<th class="text-center"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span></th>
										{{-- @endif --}}
										<th class="text-center"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></th>
										<th class="text-center"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></th>
										<th class="text-center"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></th>
									</tr>
								</thead>

								<tbody>
								@foreach($predios as $predio)
									<tr>
										<td>@if(!$predio->predio) {{$predio->ficha_catastral}}
											@else {{$predio->predio->ficha_catastral}} @endif</td>
										<td>@if(!$predio->predio) {{$predio->matricula_inmobiliaria}}
											@else {{$predio->predio->matricula_inmobiliaria}} @endif</td>
										<td>@if(!$predio->predio) {{$predio->direccion_predio}}
											@else {{$predio->predio->direccion_predio}} @endif</td>
										<td>@if(!$predio->predio) {{$predio->nombre_predio}}
											@else {{$predio->predio->nombre_predio}} @endif</td>
										<td>						{{-- @if ($predio->expediente == NULL)  --}}
											<a class="btn btn-sm btn-primary" href="{{ route('predio.detail', $predio->id) }}" title="Detalles de predio">
													<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
												</a>
											{{-- @endif --}}
										</td>

										<td>
											<a class="btn btn-sm btn-primary" href="{{ asset('/personas-predios/'.$predio->id) }}" title="Asignar Dueños">
												<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
											</a>
										</td>

										<td><a href="{{ url("predios/".$predio->id."/edit")}}" class="btn btn-sm btn-primary" title="Editar">
										<span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>
										
										<td>
											@include('cobro.predios.delete', ['predio' => $predio])
										</td>
									</tr>
								@endforeach
								</tbody>
								</table>
								{{-- {{ $predios->links() }} --}}
							@endisset	
						</div>
					</div>


	        <div id="importar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane ">

				<div class="col-md-6 pull-left mt-2">


					<form action="{{route('importar.predios')}}" enctype="multipart/form-data" method="POST">
					{{ csrf_field() }}
					
					<div class="form-group">
						<label for="">Importar Predios</label>
						<input  id="archivo" accept=".csv" name="archivo" type="file" required="" />
						<input name="MAX_FILE_SIZE" type="hidden" value="20000" /> 
						<br>
						<button class="btn btn-sm btn-primary mb-0 mx-0" type="submit">
							Importar
						</button>
					</div>
					</form>
			  </div>
			
			</div>

	
</div>
@stop

@section('js')
   <script>
		$(document).ready(function() {
		 

               $('#example').DataTable({
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
			   "sProcessing":"Procesando...",
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
			  className: 'btn btn-primary',
			  title: 'Predios'
		  },
		  {
			  extend:    'pdfHtml5',
			  text:      '<i class="fa fa-file-pdf-o"></i> ',
			  titleAttr: 'Exportar a PDF',     
			  message : 'SIEX-Providencia',
			  header :true,
			  orientation : 'landscape',
			  pageSize: 'LEGAL',
			  className: 'btn btn-primary',
			  title: 'Predios'
			   },
		  {
			  extend:    'print',
			  text:      '<i class="fa fa-print"></i> ',
			  titleAttr: 'Imprimir',
			  className: 'btn btn-primary',
			  title: 'Predios'
		  },
	  ]	             

		 });
		} );
   </script>
@stop
		            {{-- _token: '{{csrf_token()}}', --}}
