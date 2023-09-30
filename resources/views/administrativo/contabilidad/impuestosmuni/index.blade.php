@extends('layouts.dashboard')
@section('titulo')
    Impuestos Municipales
@stop
@section('sidebar')
    {{-- <li><a href="{{ url('/administrativo/contabilidad/impumuni/create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Nuevo impuesto municipal</a></li> --}}
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Impuestos Municipales</b></h4>
            </strong>
        </div>
          	<ul class="nav nav-pills">
                 <li class="nav-item active"> <a href="#impuesto" class="nav-link">Impuestos Municipales</a></li>
                <li class="nav-item "> <a href="{{ url('/administrativo/contabilidad/impumuni/create') }}" class="nav-link">Crear Impuesto</a></li>

                </ul>
	
                <div class="table-responsive">
                    <br>
                    @if(count($data) > 0)
                    <table class="table table-hover table-bordered" align="100%" id="tabla_corrE">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Base</th>
                            <th class="text-center">Tarifa</th>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Cuenta</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $impu)
                            <tr class="text-center">
                                <td>{{ $impu->id }}</td>
                                <td>{{ $impu->concepto }}</td>
                                <td> $<?php echo number_format($impu->base,0);?></td>
                                <td>{{ $impu->tarifa }}</td>
                                <td>{{ $impu->codigo }}</td>
                                <td>{{ $impu->cuenta }}</td>
                                <td>
                                    <a href="{{ url('administrativo/contabilidad/impumuni/'.$impu->id.'/edit') }}" title="Editar" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                Actualmente no hay ningún impuesto municipal almacenado.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
@stop

@section('js')
    <script>
          //Spanish
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
	   "ordering": false,
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
			  message : 'SIEX-Providencia',
			  header :true,
			  orientation : 'landscape',
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
    </script>
@stop