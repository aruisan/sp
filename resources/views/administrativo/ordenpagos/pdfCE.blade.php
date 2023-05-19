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
					<td>Beneficiario: {{ $Pago->persona->nombre }}</td>
					<td>Nit o Cedula: {{ $Pago->persona->num_dc }}</td>
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
				<p><h5>{{ $Pago->concepto }}</h5>
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
						<div class="col-md-12">
							<div class="col-md-6">A CANCELAR</div>
							<div class="col-md-6">$<?php echo number_format($Pago->valor,0) ?></div>
						</div>
					</td>
				</tr>
				<tr class="text-center">
					<td colspan="3">
						SON: {{\NumerosEnLetras::convertir($Pago->valor)}} Pesos M/CTE
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" style="width: 100%">
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
						@if($Pago->id == 7087)
							<td>Bco Agrario - Concejo Mpal Cta No. 381100000565</td>
						@else
							<td>{{ $banks[$y]->data_puc->concepto }}</td>
						@endif
						@if($Pago->type_pay == "ACCOUNT")
							@php( $date = strftime("%d of %B %Y", strtotime($Pago->created_at)))
							<td> Núm Cuenta: {{$Pago->num}} - Fecha: {{$date}}</td>
						@elseif($Pago->type_pay == "CHEQUE")
							@php( $date = strftime("%d of %B %Y", strtotime($Pago->created_at)))
							<td> Núm Cheque: {{$Pago->num}} - Fecha: {{$date}}</td>
						@endif
						<td>$<?php echo number_format($banks[$y]->valor,0);?></td>
					</tr>
				@endfor
					<tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
						<td colspan="3"><b>Total</b></td>
						<td><b>$<?php echo number_format($Pago->valor,0);?></b></td>
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
					<th class="text-center">Base</th>
					<th class="text-center">%</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
				@foreach($OrdenPagoDescuentos as  $PagosDesc)
					<tr class="text-center">
						@if($PagosDesc->desc_municipal_id != null)
							<td>{{ $PagosDesc->descuento_mun['codigo'] }}</td>
							<td>{{ $PagosDesc->descuento_mun['concepto'] }}</td>
							<td>$ <?php echo number_format($OrdenPago->valor - $OrdenPago->iva,0);?></td>
							@if($PagosDesc->descuento_mun['id'] == 5)
								<td>7 X 1000</td>
							@else
								<td>{{ $PagosDesc->descuento_mun['tarifa'] }}</td>
							@endif
						@elseif($PagosDesc->retencion_fuente_id != null)
							<td>{{ $PagosDesc->descuento_retencion->codigo}}</td>
							<td>{{ $PagosDesc->descuento_retencion->concepto }}</td>
							<td>$ <?php echo number_format($OrdenPago->valor - $OrdenPago->iva,0);?></td>
							<td>{{ $PagosDesc->descuento_retencion->tarifa }}</td>
						@else
							<td>{{ $PagosDesc->puc->code}}</td>
							<td>{{ $PagosDesc->puc->concepto}}</td>
							<td></td>
							<td></td>

						@endif
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
							<td>{{ $Pago->persona->num_dc }} {{ $Pago->persona->nombre }}</td>
							<td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_credito,0);?></td>
							<td>$0</td>
						</tr>
					@endif
				@endfor
				@for($y = 0; $y < count($banks); $y++)
					<tr class="text-center">
						<td>{{ $banks[$y]->data_puc->code }}</td>
						@if($Pago->type_pay == "ACCOUNT")
							@php( $date = strftime("%d of %B %Y", strtotime($Pago->created_at)))
							<td> Núm Cuenta: {{$Pago->num}} - Fecha: {{$date}}</td>
						@elseif($Pago->type_pay == "CHEQUE")
							@php( $date = strftime("%d of %B %Y", strtotime($Pago->created_at)))
							<td> Núm Cheque: {{$Pago->num}} - Fecha: {{$date}}</td>
						@endif
						@if($Pago->id == 7087)
							<td>Bco Agrario - Concejo Mpal Cta No. 381100000565</td>
						@else
							<td>{{ $banks[$y]->data_puc->concepto }}</td>
						@endif
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
								{{ $Pago->persona->nombre}} 	<br>
								{{ $Pago->persona->num_dc }}
							</center>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@stop
