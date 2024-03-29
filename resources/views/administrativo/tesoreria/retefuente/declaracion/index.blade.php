@extends('layouts.dashboard')
@section('titulo')
    Declarar Retención en la Fuente
@stop
@section('sidebar')
    {{-- <li><a href="{{ url('/administrativo/contabilidad/retefuente/create') }}" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Nueva Retención en la Fuente</a></li> --}}
@stop
@section('content')
    <div class="col-md-12 align-self-center">
    
        <div class="row">
            
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> Declarar Retención en la Fuente</h2>
            </div>
        </div>
        
<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
        
        <ul class="nav nav-pills">
          
                <li class="nav-item active">
                    <a class="nav-link " data-toggle="pill" href="#ver">Declaraciones</a>
                </li>
                   <li class="nav-item">
                    <a class="nav-link regresar"  href="{{ url('/administrativo/contabilidad/retefuente/declarar/create') }}" >Nueva Declaración</a>
                </li>
             
            </ul>

                <div class="table-responsive">
                    <br>
                    @if(count($data) > 0)
                    <table class="table table-hover table-bordered" align="100%" id="tabla_corrE">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">UVT</th>
                            <th class="text-center">Base</th>
                            <th class="text-center">Tarifa</th>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Cuenta</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $retef)
                            <tr class="text-center">
                                <td>{{ $retef->id }}</td>
                                <td>{{ $retef->concepto }}</td>
                                <td>{{ $retef->uvt }}</td>
                                <td> $<?php echo number_format($retef->base,0);?></td>
                                <td>{{ $retef->tarifa }}</td>
                                <td>{{ $retef->codigo }}</td>
                                <td>{{ $retef->cuenta }}</td>
                                <td>
                                    <a href="{{ url('administrativo/contabilidad/retefuente/'.$retef->id.'/edit') }}" title="Editar" class="btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                Actualmente no hay una retencion en la fuente almacenada.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
            
@stop

@section('js')
    <script>

            
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