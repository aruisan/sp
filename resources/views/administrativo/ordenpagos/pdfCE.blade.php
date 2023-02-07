@extends('layouts.CEPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td>COMPROBANTE DE EGRESO No: {{ $Egreso_id }}</td>
					<td><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
				</tr>
				<tr class="text-center">
					<td>Beneficiario: {{$OrdenPago->pago->persona->nombre}}</td>
					<td>Nit o Cedula: {{ $OrdenPago->pago->persona->num_dc }}</td>
				</tr>
				<tr class="text-center">
					<td>Orden de Pago No: {{$OrdenPago->code}}</td>
					<td>Fecha Orden de Pago: <?=$dias[$fechaO->format('w')]." ".$fechaO->format('d')." ".$meses[$fechaO->format('n')-1]. " ".$fechaO->format('Y')?></td>
				</tr>
			</table>
		</div>
		<div class="br-black-1">
			<center>
				<h4>CONCEPTO</h4>
				<p>
					<h5>{{ $OrdenPago->pago->concepto }}</h5>
				</p>
			</center>
		</div>
		<br>
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
							<div class="col-md-6">$<?php echo number_format($OrdenPagoDescuentos->sum('valor'),0) ?></div>
						</div>
					</td>
					<td>
						<?php
						$pay= $OrdenPago->valor - $OrdenPagoDescuentos->sum('valor');
						?>
						<div class="col-md-12">
							<div class="col-md-6">A CANCELAR</div>
							<div class="col-md-6">$<?php echo number_format($pay,0) ?></div>
						</div>
					</td>
				</tr>
				<tr class="text-center">
					<td colspan="3">
						SON: {{\NumerosEnLetras::convertir($pay)}} Pesos M/CTE
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table table-bordered" >
				<thead>
				<tr>
					<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">MOVIMIENTO BANCARIO</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Banco / Cuenta</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
				@for($y = 0; $y < count($banks); $y++)
					<tr class="text-center">
						<td>{{ $banks[$y]->data_puc->code }}</td>
						<td>{{ $banks[$y]->data_puc->concepto }}</td>
						@if($OrdenPago->pago->type_pay == "ACCOUNT")
							@php( $date = strftime("%d of %B %Y", strtotime($OrdenPago->pago->created_at)))
							<td> Núm Cuenta: {{$OrdenPago->pago->num}} - Fecha: {{$date}}</td>
						@elseif($OrdenPago->pago->type_pay == "CHEQUE")
							@php( $date = strftime("%d of %B %Y", strtotime($OrdenPago->pago->created_at)))
							<td> Núm Cheque: {{$OrdenPago->pago->num}} - Fecha: {{$date}}</td>
						@endif
						<td>$<?php echo number_format($banks[$y]->valor,0);?></td>
					</tr>
				@endfor
					<tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
						<td colspan="3"><b>Total</b></td>
						<td><b>$<?php echo number_format($OrdenPago->pago->valor,0);?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaDesc" style="width: 100%">
				<thead>
				<tr>
					<th colspan="5" class="text-center" style="background-color: rgba(19,165,255,0.14)"> DESCUENTOS</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripcion</th>
					<th class="text-center">%</th>
					<th class="text-center">Base</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
				@foreach($OrdenPagoDescuentos as  $PagosDesc)
					<tr class="text-center">
						@if($PagosDesc->retencion_fuente_id == null)
							<td>{{ $PagosDesc->descuento_mun['codigo'] }}</td>
							<td>{{ $PagosDesc->descuento_mun['concepto'] }}</td>
							<td>{{ $PagosDesc->descuento_mun['tarifa'] }}</td>
						@else
							<td>{{ $PagosDesc->descuento_retencion->codigo}}</td>
							<td>{{ $PagosDesc->descuento_retencion->concepto }}</td>
							<td>{{ $PagosDesc->descuento_retencion->tarifa }}</td>
						@endif
						<td>$ <?php echo number_format($OrdenPago->valor - $OrdenPagoDescuentos->sum('valor'),0);?></td>
						<td>$ <?php echo number_format($PagosDesc['valor'],0);?></td>
					</tr>
				@endforeach
				<tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
					<td colspan="4"><b>Total Descuentos</b></td>
					<td><b>$ <?php echo number_format($OrdenPagoDescuentos->sum('valor'),0);?></b></td>
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
					<th class="text-center">Tercero</th>
					<th class="text-center">Debito</th>
					<th class="text-center">Credito</th>
				</tr>
				</thead>
				<tbody>
				@for($z = 0; $z < $OrdenPago->pucs->count(); $z++)
					@if($OrdenPago->pucs[$z]->valor_credito > 0)
						<tr class="text-center">
							<td>{{$OrdenPago->pucs[$z]->data_puc->code}}</td>
							<td>{{$OrdenPago->pucs[$z]->data_puc->concepto}}</td>
							<td>{{ $OrdenPago->registros->persona->num_dc }} {{ $OrdenPago->registros->persona->nombre }}</td>
							<td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_credito,0);?></td>
							<td>$0</td>
						</tr>
					@endif
				@endfor
				@for($y = 0; $y < count($banks); $y++)
					<tr class="text-center">
						<td>{{ $banks[$y]->data_puc->code }}</td>
						@if($OrdenPago->pago->type_pay == "ACCOUNT")
							@php( $date = strftime("%d of %B %Y", strtotime($OrdenPago->pago->created_at)))
							<td> Núm Cuenta: {{$OrdenPago->pago->num}} - Fecha: {{$date}}</td>
						@elseif($OrdenPago->pago->type_pay == "CHEQUE")
							@php( $date = strftime("%d of %B %Y", strtotime($OrdenPago->pago->created_at)))
							<td> Núm Cheque: {{$OrdenPago->pago->num}} - Fecha: {{$date}}</td>
						@endif
						<td>{{ $banks[$y]->data_puc->concepto }}</td>
						<td>0$</td>
						<td>$<?php echo number_format($banks[$y]->valor,0);?></td>
					</tr>
				@endfor
				</tbody>
			</table>
		</div>
	</div>
	<div style="font-size: 10px;">
		<div class="col-md-12 align-self-center">
			<div class="table table-borderless">
				<table class="table table-borderless" style="border: hidden">
					<tr class="text-center">
						<td>
							<center>
								_____________________ <br>
								JUSTINO BRITTON HENRY 	<br>
								TESORERO
							</center>
						</td>
						<td>
							<center>
								_____________________ <br>
								{{$OrdenPago->registros->persona->nombre}} 	<br>
								{{ $OrdenPago->registros->persona->num_dc }}
							</center>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@stop
