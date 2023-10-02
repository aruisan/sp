
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
		<a class="nav-link" href="{{route('nomina.'.$nomina_old->tipo.'s.index')}}"> {{ucfirst($nomina_old->tipo)}}s</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link" data-toggle="pill" href="#personas"> Nominas</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3><b>descuentos del mes {{$nomina_old->mes}} al mes de {{$nomina->mes}}</b></h3>
		</strong>
	</div>
	<div class="container-fluid">
		<div class="table-responsive">
			<div class="box">
				<div class="box-body">
                <form action="{{route('nomina-descuentos.pasar-store', $nomina->id)}}" method="post">
                {{ csrf_field() }}
						<table class="table table-bordered cell-border table-hover" id="example"  data-form="deleteForm">
							<thead>
								<tr class="active">
                                    <th class="text-center">#</th>
									<th class="text-center">Empleado</th>
									<th class="text-center">Tercero</th>
                                    <th class="text-center">Descuento</th>
                                    <th class="text-center">Cuotas</th>
                                    <th class="text-center">Valor</th>
									<th>X</th>
								</tr>
							</thead>

							<tbody>
                                
                                @php 
                                    $contador = 0;
                                @endphp
                                @foreach($nomina_old->empleados_nominas as $key => $movimiento)
                                    @foreach($movimiento->descuentos as $descuento)
                                    <tr>
                                        <td>{{$contador +=1}}</td>
                                        <td>{{$movimiento->empleado->nombre}}</td>
                                        <td>{{$descuento->tercero->nombre}}</td>
                                        <td>{{$descuento->nombre}}</td>
                                        <td><input type="number" value="{{$descuento->n_cuotas == 1 ? 10 : $descuento->n_cuotas}}" name="n_cuotas[]"></td>
                                        <td><input type="number" value="{{$descuento->valor}}" name="valor[]"></td>
                                        <td>
                                            <input type="hidden" name="descuento_id[]" value="{{$descuento->id}}">
                                            <input type="hidden" name="empleado_id[]" value="{{$movimiento->empleado->id}}">
                                            <input type="hidden" name="tercero_id[]" value="{{$descuento->tercero->id}}">
                                            <input type="hidden" name="nombre[]" value="{{$descuento->nombre}}">
                                            <button type="button" class="borrar btn btn-danger">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                @foreach($nomina->empleados_nominas as $key => $movimiento)
                                    @foreach($movimiento->descuentos as $descuento)
                                    <tr>
                                        <td>{{$contador +=1}}</td>
                                        <td>{{$movimiento->empleado->nombre}}</td>
                                        <td>{{$descuento->tercero->nombre}}</td>
                                        <td>{{$descuento->nombre}}</td>
                                        <td><input type="number" value="{{$descuento->n_cuotas == 1 ? 10 : $descuento->n_cuotas}}" name="n_cuotas[]"></td>
                                        <td><input type="number" value="{{$descuento->valor}}" name="valor[]"></td>
                                        <td>
                                            <input type="hidden" name="descuento_id[]" value="{{$descuento->id}}">
                                            <input type="hidden" name="empleado_id[]" value="{{$movimiento->empleado->id}}">
                                            <input type="hidden" name="tercero_id[]" value="{{$descuento->tercero->id}}">
                                            <input type="hidden" name="nombre[]" value="{{$descuento->nombre}}">
                                            <button type="button" class="borrar btn btn-danger">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach

                                   
							</tbody>
						</table>
                        <button class="btn btn-primary" > Guardar</button>
                    </form>
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
		"pageLength": 500,
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

    $(document).on('click', '.borrar', function(event) {
        event.preventDefault();
        $(this).closest('tr').remove();
    });

		
   </script>
@stop
