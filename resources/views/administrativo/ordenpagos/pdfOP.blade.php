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
					<td>Beneficiario: {{$OrdenPago->registros->persona->nombre}}</td>
					<td>Nit o Cedula: {{ $OrdenPago->registros->persona->num_dc }}</td>
				</tr>
				<tr class="text-center">
					<td>Registro No: {{$R->code}}</td>
					<td>Fecha Registro: <?=$dias[$fechaR->format('w')]." ".$fechaR->format('d')." ".$meses[$fechaR->format('n')-1]. " ".$fechaR->format('Y')?></td>
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
						SON: {{\NumerosEnLetras::convertir($pay)}} M/CTE
					</td>
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
					<th class="text-center" colspan="5" style="background-color: rgba(19,165,255,0.14)">PRESUPUESTO</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Descripción</th>
					<th class="text-center">Fuente Financiación</th>
					<th class="text-center">Registro</th>
					<th class="text-center">Valor</th>
				</tr>
				</thead>
				<tbody>
				@for($i = 0; $i < $R->cdpRegistroValor->count(); $i++)
					@if($R->cdpRegistroValor[$i]->valor > 0)
						@if($R->cdpRegistroValor[$i]->cdps->tipo == "Funcionamiento")
							<tr class="text-center">
								<td>
									@for($x = 0; $x < count($infoRubro); $x++)
										@if($infoRubro[$x]['id_rubro'] == $R->cdpRegistroValor[$i]->fontRubro->rubro->id)
											{{ $infoRubro[$x]['codigo'] }}
										@endif
									@endfor
								</td>
								<td>{{ $R->cdpRegistroValor[$i]->fontRubro->rubro->name}}</td>
								@if($R->cdpRegistroValor[$i]->fontRubro->sourceFunding)
									<td>{{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->description }}</td>
								@else
									<td>{{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->name }}</td>
								@endif
								<td>{{ $OrdenPago->registros->objeto }}</td>
								<td>$ <?php echo number_format($OrdenPago->registros->valor,0);?></td>
							</tr>
						@elseif($R->cdpRegistroValor[$i]->cdps->tipo == "Inversion")
							<tr class="text-center">
								<td>
									{{$R->cdpRegistroValor[$i]->cdps->bpinsCdpValor->first()->actividad->cod_actividad}}
								</td>
								<td>{{ $R->cdpRegistroValor[$i]->fontRubro->rubro->name}}</td>
								@if($R->cdpRegistroValor[$i]->fontRubro->sourceFunding)
									<td>{{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->sourceFunding->description }}</td>
								@else
									<td>{{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->code }} - {{ $R->cdpRegistroValor[$i]->fontRubro->fontVigencia->name }}</td>
								@endif
								<td>{{ $OrdenPago->registros->objeto }}</td>
								<td>$ <?php echo number_format($OrdenPago->registros->valor,0);?></td>
							</tr>
						@endif

					@endif
				@endfor
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
				@foreach($OrdenPagoDescuentos as  $PagosDesc)
					<tr class="text-center">
						@if($PagosDesc->desc_municipal_id != null)
							<td>{{ $PagosDesc->descuento_mun['codigo'] }}</td>
							<td>{{ $PagosDesc->descuento_mun['concepto'] }}</td>
						@elseif($PagosDesc->retencion_fuente_id != null)
							<td>{{ $PagosDesc->descuento_retencion->codigo}}</td>
							<td>{{ $PagosDesc->descuento_retencion->concepto }}</td>
						@else
							<td>{{ $PagosDesc->puc->code}}</td>
							<td>{{ $PagosDesc->puc->concepto}}</td>
						@endif
						<td>{{ $OrdenPago->registros->persona->num_dc }} {{ $OrdenPago->registros->persona->nombre }}</td>
						<td>$0</td>
						<td>$ <?php echo number_format($PagosDesc['valor'],0);?></td>
					</tr>
				@endforeach
				@for($z = 0; $z < $OrdenPago->pucs->count(); $z++)
					<tr class="text-center">
						<td>{{$OrdenPago->pucs[$z]->data_puc->code}}</td>
						<td>{{$OrdenPago->pucs[$z]->data_puc->concepto}}</td>
						<td>{{ $OrdenPago->registros->persona->num_dc }} {{ $OrdenPago->registros->persona->nombre }}</td>
						<td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_debito,0);?></td>
						<td>$<?php echo number_format($OrdenPago->pucs[$z]->valor_credito,0);?></td>
					</tr>
				@endfor
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
