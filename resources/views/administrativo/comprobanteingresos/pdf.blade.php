@extends('layouts.OPPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td>COMPROBANTE CONTABLE No: {{ $comprobante->code }}</td>
					<td><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
				</tr>
				<tr class="text-center">
					<td>Beneficiario: {{ $persona->nombre }}</td>
					<td>Nit o Cedula: {{ $persona->num_dc }}</td>
				</tr>
				<tr class="text-center">
					<td colspan="2">Tipo de Documento: {{ $comprobante->tipoCI }}</td>
				</tr>
			</table>
		</div>
		<div>
			<center>
				<h5>CONCEPTO</h5>
				<p>{{ $comprobante->concepto }}</p>
			</center>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaP" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">CONTABILIZACIÓN</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Debito</th>
					<th class="text-center">Credito</th>
				</tr>
				</thead>
				<tbody>
				@foreach($comprobante->movs as $mov)
					@if(isset($mov->cuenta_banco))
						<tr class="text-center">
							<td>{{ $mov->banco->code}}</td>
							<td>{{ $mov->banco->concepto}}</td>
							<td>$ <?php echo number_format($mov->debito,0);?></td>
							<td>$ <?php echo number_format($mov->credito,0);?></td>
						</tr>
					@endif
					@if(isset($mov->cuenta_puc_id))
						<tr class="text-center">
							<td>{{ $mov->puc->code}}</td>
							<td>{{ $mov->puc->concepto}}</td>
							<td>$ <?php echo number_format($mov->debito,0);?></td>
							<td>$ <?php echo number_format($mov->credito,0);?></td>
						</tr>
					@endif
				@endforeach
				</tbody>
			</table>
		</div>
		<br>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaP" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">PRESUPUESTO</th>
				</tr>
				<tr>
					<th class="text-center">Código</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Fuente Financiación</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
				@foreach($comprobante->movs as $mov)
					@if(isset($mov->rubro_font_ingresos_id))
						<tr class="text-center">
							<td>{{ $mov->fontRubro->rubro->cod}}</td>
							<td>{{ $mov->fontRubro->rubro->name}}</td>
							<td>{{ $mov->fontRubro->sourceFunding->code}} - {{$mov->fontRubro->sourceFunding->description}}</td>
							<td>$ <?php echo number_format($mov->debito,0);?></td>
						</tr>
					@endif
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div style="font-size: 10px;">
		<div class="col-md-12 align-self-center">
			<div class="table-borderless">
				<table class="table table-borderless" style="border: hidden">
					<tr class="text-center">
						<td>
							<center>
								_______________________ <br>
								HELLEN GARCIA ALEGRIA<br>
								PROFESIONAL UNIVERSITARIO
							</center>
						</td>
						<td>
							<center>
								_______________________ <br>
								JUSTINO BRITTON<br>
								TESORERO
							</center>
						</td>
						<td>
							<center>
								_________________________ <br>
								ELIANA<br>
								ELABORADOR
							</center>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@stop
