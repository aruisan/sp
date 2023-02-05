@extends('layouts.dashboard')
@section('titulo')
	Pago Retención en la Fuente
@stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> Pago Retención en la Fuente</h2>
            </div>
        </div>
		<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
			<ul class="nav nav-pills">
				<li class="nav-item active">
					<a class="nav-link " data-toggle="pill" href="#ver">Pago Retención en la fuente</a>
				</li>
			</ul>
			<div class="table-responsive">
				<table class="table table-bordered" id="tabla_pagos">
					<thead>
					<tr>
						<th class="text-center" rowspan="2">CODIGO</th>
						<th class="text-center" rowspan="2">CONCEPTO</th>
						<th class="text-center" rowspan="2">$</th>
						<th class="text-center" rowspan="2">CC</th>
						<th class="text-center" rowspan="2">TERCERO</th>
						<th class="text-center" colspan="3">VALOR ORDEN DE PAGO</th>
					</tr>
					<tr>
						<th class="text-center">CODIGO</th>
						<th class="text-center">CONCEPTO</th>
						<th class="text-center">$</th>
					</tr>
					</thead>
					<tbody>
					@foreach($tableRT as $index => $dato)
						<tr>
							<td class="text-center">{{ $dato['code'] }}</td>
							<td class="text-center">{{ $dato['concepto'] }}</td>
							<td class="text-center">{{ $dato['valorDesc'] }}</td>
							<td class="text-center">{{ $dato['cc'] }}</td>
							<td class="text-center">{{ $dato['nameTer'] }}</td>
							<td class="text-center">{{ $dato['codeDeb'] }}</td>
							<td class="text-center">{{ $dato['conceptoDeb'] }}</td>
							<td class="text-center">{{ $dato['valorDeb'] }}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
            
@stop

@section('js')
    <script>

		$(document).ready(function() {
			$('.select-persona').select2();
		});

            
           $('#tabla_pagos').DataTable({
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
			   "pageLength": 500,
			   responsive: "true",
			   dom: 'Bfrtilp',
			   "ordering": false,
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