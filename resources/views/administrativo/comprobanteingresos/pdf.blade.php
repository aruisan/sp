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
			</table>
		</div>
		<div>
			<center>
				<h5>CONCEPTO</h5>
				<p>{{ $comprobante->concepto }}</p>
			</center>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<thead>
				<tr>
					<th class="text-center" colspan="3" style="background-color: rgba(19,165,255,0.14)">LIQUIDACIÓN</th>
				</tr>
				</thead>
				<tbody>
				<tr class="text-center">
					<td>
						<div class="col-md-12">
							<div class="col-md-6">VALOR DE CONTROL</div>
							<div class="col-md-6">$ <?php echo number_format($comprobante->valor - $comprobante->iva,0);?></div>
						</div>
					</td>
					<td>
						<div class="col-md-12">
							<div class="col-md-6">VALOR IVA</div>
							<div class="col-md-6">$ <?php echo number_format($comprobante->iva,0);?></div>
						</div>
					</td>
					<td>
						<div class="col-md-12">
							<div class="col-md-6">VALOR TOTAL</div>
							<div class="col-md-6">$ <?php echo number_format($comprobante->valor + $comprobante->iva,0);?></div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		@if(isset($comprobante->rubro_font_ingresos_id))
			<div class="table-responsive br-black-1">
				<table class="table-bordered" id="tablaP" style="width: 100%">
					<thead>
					<tr>
						<th class="text-center" colspan="5" style="background-color: rgba(19,165,255,0.14)">PRESUPUESTO</th>
					</tr>
					<tr>
						<th class="text-center">Codigo</th>
						<th class="text-center">Descripción</th>
						<th class="text-center">Fuente Financiación</th>
						<th class="text-center">Concepto</th>
						<th class="text-center">Valor</th>
					</tr>
					</thead>
					<tbody>
					<tr class="text-center">
						<td>{{ $comprobante->fontRubro->rubro->cod }} </td>
						<td>{{ $comprobante->fontRubro->rubro->name }}</td>
						<td>{{ $comprobante->fontRubro->sourceFunding->code }} - {{ $comprobante->fontRubro->sourceFunding->description }}</td>
						<td>{{ $comprobante->concepto }}</td>
						<td>$ <?php echo number_format($comprobante->debito_rubro_ing,0);?></td>
					</tr>
					</tbody>
				</table>
			</div>
		@endif

		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaP" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center" colspan="5" style="background-color: rgba(19,165,255,0.14)">CONTABILIZACIÓN</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Tercero</th>
					<th class="text-center">Debito</th>
					<th class="text-center">Credito</th>
				</tr>
				</thead>
				<tbody>
				<tr class="text-center">
					<td>{{ $banco->code }}</td>
					<td>{{ $banco->concepto }}</td>
					<td>{{ $persona->num_dc }} - {{ $persona->nombre }}</td>
					<td>$ <?php echo number_format($comprobante->debito_banco,0);?></td>
					<td>$ <?php echo number_format($comprobante->credito_banco,0);?></td>
				</tr>
				<tr class="text-center">
					<td>{{ $puc->code }}</td>
					<td>{{ $puc->concepto }}</td>
					<td>{{ $persona->num_dc }} - {{ $persona->nombre }}</td>
					<td>$ <?php echo number_format($comprobante->debito_puc,0);?></td>
					<td>$ <?php echo number_format($comprobante->credito_puc,0);?></td>
				</tr>
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
