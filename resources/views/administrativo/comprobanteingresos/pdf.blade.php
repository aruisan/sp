@extends('layouts.OPPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td>COMPROBANTE CONTABLE No: {{ $comprobante->code }}</td>
					<td><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
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
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaP" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center" colspan="5" style="background-color: rgba(19,165,255,0.14)">CONTABILIZACIÓN</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Debito</th>
					<th class="text-center">Credito</th>
				</tr>
				</thead>
				<tbody>
				<tr class="text-center">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaP" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center" colspan="5" style="background-color: rgba(19,165,255,0.14)">PRESUPUESTO DE INGRESOS</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Fuente Financiación</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
					<tr class="text-center">
						<td></td>
						<td></td>
						<td></td>
						<td>$ <?php echo number_format(0,0);?></td>
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
								<!-- Presidente 2020 Léri Aniseto Henry Taylor  -->
								HELLEN GARCIA ALEGRIA<br>
								PROFESIONAL UNIVERSITARIO
							</center>
						</td>
						<td>
							<center>
								_______________________ <br>
								<!-- Presidente 2020 Léri Aniseto Henry Taylor  -->
								&nbsp;<br>
								SECRETARIO DE HACIENDA
							</center>
						</td>
						<td>
							<center>
								_________________________ <br>
								JIM HENRY BENT<br>
								RESPONSABLE PRESUPUESTO
							</center>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@stop
