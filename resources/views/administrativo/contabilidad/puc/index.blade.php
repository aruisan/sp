@extends('layouts.dashboard')
@section('titulo')
    PUC
@stop
@section('sidebar')
    {{-- @if($data)
        <li><a href="/administrativo/contabilidad/puc/level/create/{{ $data->id }}" class="btn btn-primary"><i class="fa fa-edit"></i> &nbsp; Modificar PUC</a></li>
    @endif --}}
@stop
@section('content')
    <div class="col-md-12 align-self-center">
       


           <div class="row">
            
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> PUC</h2>
            </div>
        </div>
        
<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
        
        <ul class="nav nav-pills">
          
              
                   <li class="nav-item active">
                    <a class="nav-link "  href="#" >PUC </a>
                </li>


            @if($data)
               <li class="nav-item">
                    <a class="nav-link "  href="/administrativo/contabilidad/puc/level/create/{{ $data->id }}"> Modificar PUC</a>
                </li>
          
             @endif
              </ul>
              
                <div class="table-responsive">
                    <br>
                    @if($data)
                    <table class="table table-hover table-bordered" align="100%" id="tabla_corrE">
                        <thead>
                        <tr>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Nombre Cuenta</th>
                            <th class="text-center">Codigo NIPS</th>
                            <th class="text-center">Nombre NIPS</th>
                            <th class="text-center">Naturaleza</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($codigos as $codigo)
                            <tr>
                                <td class="text-dark">{{ $codigo['codigo']}}</td>
                                <td class="text-dark">{{ $codigo['name'] }}</td>
                                <td class="text-dark">{{ $codigo['code_N'] }}</td>
                                <td class="text-dark">{{ $codigo['name_N'] }}</td>
                                <td class="text-dark">{{ $codigo['naturaleza'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                Actualmente no hay un PUC almacenado.
                                <a href="{{ url('administrativo/contabilidad/puc/create') }}" title="Crear" class="btn-sm btn-primary"><i class="fa fa-plus"></i> Crear nuevo PUC</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
             </div>
@stop

@section('js')
    <script>

       $('#tabla_corrE').DataTable( {
             language: {
			  "lengthMenu": "Mostrar _MENU_ registros",
			  "zeroRecords": "No se encontraron resultados",
			  "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
			  "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
			  "infoFiltered": "(filtrado de un total de _MAX_ registros)",
			  "sSearch": "Buscar:",
			  "oPaginate": {
				  "sFirst": "Primero",
				  "sLast":"??ltimo",
				  "sNext":"Siguiente",
				  "sPrevious": "Anterior"
			   },
			   "sProcessing":"Procesando...",
		  },
	  //para usar los botones   
      "pageLength": 5,
         ordering: false,
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
            var table = $('#tabla_corrE').DataTable();
        });
         
    </script>
@stop