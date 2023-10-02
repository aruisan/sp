
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
		<a class="nav-link" href="{{route('nomina.'.$nomina->tipo.'s.index')}}"> {{ucfirst($nomina->tipo)}}s</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link" data-toggle="pill" href="#personas"> Nominas</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3><b>Nomina de Descuentos Reporte a Terceros</b></h3>
		</strong>
	</div>
	<div class="container-fluid">
        <ul class="nav nav-tabs">
            @foreach(App\Helpers\FechaHelper::bancos_terceros()[0] as $k => $tercero)
                <li class="{{!$k ? 'active' : ''}}"><a data-toggle="tab" href="#tercero_{{$k}}">{{$tercero}}</a></li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach(App\Helpers\FechaHelper::bancos_terceros()[1] as $i => $tercero_id)
                <div id="tercero_{{$i}}" class="tab-pane fade {{!$i ? 'in active' : ''}}">
                <div class="table-responsive">
                    <div class="box">
                        <div class="box-body">
                                <table class="table table-bordered cell-border table-hover "  data-form="deleteForm">
                                    <thead>
                                        <tr class="active">
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">{{ucfirst($nomina->tipo)}}</th>
                                            <th class="text-center">Descuento</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    @php 
                                        $total = 0;
                                    @endphp
                                    @foreach($nomina->empleados_nominas as $j => $movimiento)
                                        @foreach($movimiento->descuentos->filter(function($d)use($tercero_id){ return $d->tercero_id == $tercero_id;}) as $key => $descuento)
                                        <tr>
                                            <td>{{$nomina->mes}}</td>
                                            <td>{{$movimiento->empleado->nombre}}</td>
                                            <td>{{$descuento->tercero->nombre}}</td>
                                            <td>${{number_format($descuento->valor, 2)}}</td>
                                        </tr>
                                        @php 
                                            $total += $descuento->valor;
                                        @endphp
                                        @endforeach

                                    @endforeach
                                    
                                    </tbody>
									<tfoot>
									<tr>
                                        <td colspan="3" class="text-right"><b>TOTAL</b></td>
                                        <td><b>${{number_format($total, 2)}}</b></td>
                                    </tr>
									</tfoot>
                                </table>
                          
                        </div>
                    </div>
                </div>
                </div>
            @endforeach
        </div>
	</div>
</div>



@stop

@section('js')
<script>
	

	$('.table').DataTable( {
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
		"pageLength": 200,
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
