@extends('layouts.dashboard')
@section('titulo')
	Pago Retención en la Fuente No.{{$pago->id}}
@stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center"> Pago Retención en la Fuente No.{{$pago->id}}</h2>
            </div>
        </div>
		<div class="row inputCenter"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
			<ul class="nav nav-pills">
				<li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/tesoreria/retefuente/pago/'.$vigencia->id) }}">Pagos</a></li>
				<li class="nav-item active">
					<a class="nav-link " data-toggle="pill" href="#ver">Pago Retención en la fuente {{$pago->id}}</a>
				</li>
			</ul>
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
						<th class="text-center">BASE SUJETA A RETENCIÓN</th>
						<th class="text-center">RETENCIONES</th>
					</tr>
					</thead>
					<tbody>
					@foreach($pago->formularios as $index => $dato)
						<tr>
							<td class="text-center">{{ $dato['concepto'] }}</td>
							<td class="text-center">
								@if($dato['base'] > 0) $ <?php echo number_format($dato['base'],0);?> @endif
							</td>
							<td class="text-center">
								@if($dato['retencion'] > 0) $ <?php echo number_format($dato['retencion'],0);?> @endif
							</td>
						</tr>
					@endforeach
					<tr>
						<td class="text-center" colspan="2"><b>TOTAL A PAGAR</b></td>
						<td class="text-center"><b>$ <?php echo number_format($pago->pago,0);?></b></td>
					</tr>
					</tbody>
				</table>
			</div>
			<center>
				<h3>Contabilización</h3>
			</center>
			<hr>
			<br>
			<div class="table-responsive">
				<table class="table table-bordered" id="tablaP">
					<thead>
					<tr>
						<th class="text-center">Codigo</th>
						<th class="text-center">Nombre Cuenta</th>
						<th class="text-center">Debito</th>
						<th class="text-center">Credito</th>
					</tr>
					</thead>
					<tbody>
					@for($z = 0; $z < $pago->contas->count(); $z++)
						<tr class="text-center">
							<td>{{$pago->contas[$z]->puc->code}}</td>
							<td>{{$pago->contas[$z]['concepto']}}</td>
							<td>$ <?php echo number_format($pago->contas[$z]['debito'],0);?></td>
							<td>$ <?php echo number_format($pago->contas[$z]['credito'],0);?></td>
						</tr>
					@endfor
					<tr class="text-center">
						<td>{{$pago->puc->code}}</td>
						<td>{{$pago->puc->concepto}}</td>
						<td>$ 0</td>
						<td>$ <?php echo number_format($pago->pago,0);?></td>
					</tr>
					<tr class="text-center">
						<td colspan="2"><b>SUMAS IGUALES</b></td>
						<td><b>$ <?php echo number_format($pago->contas->sum('debito'),0);?></b></td>
						<td><b>$ <?php echo number_format($pago->pago + $pago->contas->sum('credito'),0);?></b></td>
					</tr>
					</tbody>
				</table>
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

			this.submit();
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