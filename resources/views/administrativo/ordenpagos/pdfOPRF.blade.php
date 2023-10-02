@extends('layouts.OPPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td>ORDEN DE PAGO No: {{ $OrdenPago->code }}</td>
					<td><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
				</tr>
				<tr class="text-center">
					<td>Beneficiario: DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN</td>
					<td>Nit o Cedula: 800197268</td>
				</tr>
			</table>
		</div>
		<div>
			<center>
				<h5>CONCEPTO</h5>
				<p>{{ $OrdenPago->nombre }}</p>
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
							<div class="col-md-6">VALOR BRUTO</div>
							<div class="col-md-6">$ <?php echo number_format($OrdenPago->valor - $OrdenPago->iva,0);?></div>
						</div>
					</td>
					<td>
						<div class="col-md-12">
							<div class="col-md-6">VALOR IVA</div>
							<div class="col-md-6">$ <?php echo number_format($OrdenPago->iva,0);?></div>
						</div>
					</td>
					<td>
						<div class="col-md-12">
							<div class="col-md-6">VALOR TOTAL</div>
							<div class="col-md-6">$ <?php echo number_format($OrdenPago->valor,0);?></div>
						</div>
					</td>
				</tr>
				<tr class="text-center">
					<td>
						<div class="col-md-12">
							<div class="col-md-6">ANTICIPO</div>
							<div class="col-md-6">$ 0</div>
						</div>
					</td>
					<td>
						<div class="col-md-12">
							<div class="col-md-6">OTROS DESCUENTOS</div>
							<div class="col-md-6">$ 0</div>
						</div>
					</td>
					<td>
						<?php
							$pay= $OrdenPago->valor
						?>
						<div class="col-md-12">
							<div class="col-md-6">A CANCELAR</div>
							<div class="col-md-6">$<?php echo number_format($pay,0) ?></div>
						</div>
					</td>
				</tr>
				<tr class="text-center">
					<td colspan="3">
						SON: {{\NumerosEnLetras::convertir($pay)}} M/CTE
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
					<th class="text-center">Cuenta</th>
					<th class="text-center">Valor</th>
					<th class="text-center">NIT/CED</th>
					<th class="text-center">Tercero</th>
				</tr>
				</thead>
				<tbody>
				@foreach($tesoreriaRetefuentePago->contas as  $contabilizacion)
					<tr class="text-center">
						<td>{{ $contabilizacion->puc->code}}</td>
						<td>{{ $contabilizacion->puc->concepto}}</td>
						<td>$ <?php echo number_format($contabilizacion->debito,0);?></td>
						<td>{{ $contabilizacion->persona->num_dc }}</td>
						<td>{{ $contabilizacion->persona->nombre }}</td>
					</tr>
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
