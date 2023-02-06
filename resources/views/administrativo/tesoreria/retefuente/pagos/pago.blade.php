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
				<li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/tesoreria/retefuente/pago/'.$vigencia_id) }}">Pagos</a></li>
				<li class="nav-item active">
					<a class="nav-link " data-toggle="pill" href="#ver">Pago Retención en la fuente</a>
				</li>
			</ul>
			<div class="table-responsive" id="revtable">
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
							<td class="text-center">$ <?php echo number_format($dato['valorDesc'],0);?></td>
							<td class="text-center">{{ $dato['cc'] }}</td>
							<td class="text-center">{{ $dato['nameTer'] }}</td>
							<td class="text-center">{{ $dato['codeDeb'] }}</td>
							<td class="text-center">{{ $dato['conceptoDeb'] }}</td>
							<td class="text-center">$ <?php echo number_format($dato['valorDeb'],0);?></td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<div class="table-responsive" id="formulario">
				<table class="table table-bordered" id="tabla_form">
					<thead>
					<tr><th class="text-center" colspan="3">FORMULARIO 350</th></tr>
					<tr><th class="text-center" colspan="3">MUNICIPIO DE PROVIDENCIA ISLAS</th></tr>
					<tr><th class="text-center" colspan="3">NIT. 800103021-1</th></tr>
					<tr><th class="text-center" colspan="3">DECLARACION DE RETENCION EN LA FUENTE MES: {{ $mes }}</th></tr>
					<tr><th class="text-center" colspan="3">Periodo comprendido entre 1 de {{ $mes }} al {{ $days }} de {{$mes}} de {{ $vigencia->vigencia }}</th></tr>
					<tr>
						<th class="text-center">CONCEPTO</th>
						<th class="text-center">BASE SUJETA A RETECIÓN</th>
						<th class="text-center">RETENCION</th>
					</tr>
					</thead>
					<tbody>
					@foreach($form as $index => $dato)
						<tr>
							<td class="text-center">{{ $dato['concepto'] }}</td>
							<td class="text-center">$ <?php echo number_format($dato['base'],0);?></td>
							<td class="text-center">$ <?php echo number_format($dato['reten'],0);?></td>
						</tr>
					@endforeach
					<tr>
						<td class="text-center" colspan="2"><b>TOTAL A PAGAR</b></td>
						<td class="text-center"><b>$ <?php echo number_format($total,0);?></b></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="table-responsive" id="pago">
				<form class="form" action="{{ url('administrativo/tesoreria/retefuente/pago/7/1/make') }}" method="POST"
					  enctype="multipart/form-data" id="makePayReteFuente">
					{!! method_field('POST') !!}
					{{ csrf_field() }}
					@foreach($form as $index => $dato)
						<input type="hidden" name="concepto[]" value="{{ $dato['concepto'] }}">
						<input type="hidden" name="base[]" value="{{ $dato['base'] }}">
						<input type="hidden" name="reten[]" value="{{ $dato['reten'] }}">
					@endforeach
					@foreach($tableRT as $index => $dato)
						@if($dato['nameTer'] != null)
							<input type="hidden" name="codeForm[]" value="{{ $dato['code'] }}">
							<input type="hidden" name="conceptoForm[]" value="{{ $dato['concepto'] }}">
							<input type="hidden" name="debitoForm[]" value="{{ $dato['valorDesc'] }}">
						@endif
					@endforeach
					<div class="text-center">
						<h4>Cuenta bancaria objeto del pago</h4>
						<div class="col-lg-8">
							<select name="cuenta" id="cuenta" class="form-control" required>
								@foreach($bancos as $banco)
									<option value="{{ $banco->id }}">{{ $banco->code }} - {{ $banco->concepto }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-lg-4">
							<input type="number" class="form-control" required name="valorPago" id="valorPago"
								   value="{{ $total }}" max="{{ $total }}">
						</div>
						<br><br>
						<button type="submit" class="btn-sm btn-primary"> Enviar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@stop

@section('js')
    <script>

		//VALIDACION DE LOS DINEROS A TOMAR NO SEAN SUPERIORES DE LOS PERMITIDOS POR LA FUENTE
		document.addEventListener("DOMContentLoaded", function() {
			document.getElementById("makePayReteFuente").addEventListener('submit', validarFormulario);
		});

		function validarFormulario(evento) {
			evento.preventDefault();

			const valores = document.querySelectorAll('input[name="debitoForm[]"]');
			//const totDeb = valores['value'].reduce((partialSum, a) => partialSum + a, 0);
			//console.log(sum);

			//this.submit();
		}

		function add(accumulator, a) {
			return accumulator + a;
		}

		function generateForm(){
			console.log("GENERAR FORM");
		}

		function makePay(){
			console.log("HACER PAGO");
		}

            
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