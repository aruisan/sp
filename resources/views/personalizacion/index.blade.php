
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Personas
@stop


@section('content')

<div class="container-fluid">	
    <div class="table-responsive">
    	<div class="box">
            <div class="box-body">
					<table class="table table-bordered cell-border table-hover" id="example"  data-form="deleteForm">
						<thead>
							<tr class="active">
								<th class="text-center">Nombre</th>
								<th class="text-center">email</th>
								<th class="text-center">activo</th>
							</tr>
						</thead>

						<tbody>
						@foreach($users as $user)
							<tr>
								<td>
                                    <a href="{{route('personalizar.start', $user->id)}}">{{$user->name}}</a>
                                </td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->active}}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
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
		  },
	  ]	             

		 });

        $(document).ready(function() {
            var table = $('#example').DataTable();
		} );

		
   </script>
@stop
