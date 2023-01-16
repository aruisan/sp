@extends('layouts.dashboard')
@section('titulo')
	Certificado Retención en la Fuente
@stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> Generar Certificado Retención en la Fuente</h2>
            </div>
        </div>
		<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
			<ul class="nav nav-pills">
				<li class="nav-item active">
					<a class="nav-link " data-toggle="pill" href="#ver">Certificado Retención en la fuente</a>
				</li>
			</ul>
			<div class="table-responsive"><br>
				<div class="col-md-12 align-self-center">
					<h4>Seleccione el tercero a generar el certificado</h4>
					<select class="select-persona" name="persona_id">
						@foreach($personas as $persona)
							<option value="{{$persona->id}}">{{ $persona->num_dc }} - {{$persona->nombre}}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
	</div>
            
@stop

@section('js')
    <script>

		$(document).ready(function() {
			$('.select-persona').select2();
		});

            
           $('#tabla_corrE').DataTable({
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
			  className: 'btn btn-primary',
               exportOptions: {
				  columns: [ 0,1,2,3,4,5,6]
					}
		  },
		  {
			  extend:    'excelHtml5',
			  text:      '<i class="fa fa-file-excel-o"></i> ',
			  titleAttr: 'Exportar a Excel',
			  className: 'btn btn-primary',
               exportOptions: {
				  columns: [ 0,1,2,3,4,5,6]
					}
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
             exportOptions: {
				  columns: [ 0,1,2,3,4,5,6]
					}
			   },
		  {
			  extend:    'print',
			  text:      '<i class="fa fa-print"></i> ',
			  titleAttr: 'Imprimir',
			  className: 'btn btn-primary',
               exportOptions: {
				  columns: [ 0,1,2,3,4,5,6]
					}
		  },
	  ]	             

		 });
		
    </script>
@stop