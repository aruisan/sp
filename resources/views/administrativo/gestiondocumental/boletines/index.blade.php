@extends('layouts.dashboard')
@section('titulo')
    Boletines
@stop
@section('sidebar')
    <li> <a data-toggle="modal" data-target="#modal-busquedaBoletines" class="btn btn-primary hidden"><i class="fa fa-search"></i><span class="hide-menu">&nbsp; Buscar</span></a></li>
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Boletines</b></h4>
            </strong>
        </div>
            <br>
                <div class="table-responsive">
                    <br>
                    @if(count($Boletines) > 0)
                    <table class="table table-hover table-bordered" align="100%" id="tabla_corrE">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Ubicacion Fisica</th>
                            <th class="text-center">Fecha de creación</th>
                            <th class="text-center">Cuantia</th>
                            <th class="text-center">Responsable</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($Boletines as $key => $data)
                            <tr class="text-center">
                                <td>{{ $data->id }}</td>
                                <td>
                                    <a href="{{route('carpetas.show', $data->id)}}" class="btn btn-link">
                                        {{ $data->nombre }}
                                    </a>
                                </td>
                                <td>{{ $data->ubicacion_fisica }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->cuantia }}</td>
                                <td>{{ $data->owner->name }}</td>
                                <td>
                                    <button class="btn-sm btn-primary" 
                                            onclick="editar('{{route('carpetas.edit', $data->id)}}', 'Boletines')">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-primary" onclick="borrar('delete{{$data->id}}')">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>
                                    <form action="{{route('carpetas.destroy', $data->id)}}" method="post" id="delete{{$data->id}}">
                                        <input type="hidden" name="_method" value="delete" />
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                Actualmente no hay boletines almacenados.
                            </div>
                        </div>
                    @endif
                </div>
                <center>
                    <button class="btn btn-primary" onclick="nuevo('Boletines')">
                        <i class="fa fa-plus"></i>&nbsp; Nuevo Boletin
                    </button>
                </center>
            </div>
@stop

@section('js')
    <script>

    function nuevo(tipo){
        localStorage.setItem("tipoCarpeta", tipo);
        localStorage.setItem("rutaIndexCarpeta", "{{Request::url()}}");
        window.location.href = "{{route('carpetas.create')}}";
    }

    function editar(url, tipo){
        localStorage.setItem("tipoCarpeta", tipo);
        localStorage.setItem("rutaIndexCarpeta", "{{Request::url()}}");
        window.location.href = url;
    }

    function borrar(formId){
        $(`#${formId}`).submit();
    }



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
