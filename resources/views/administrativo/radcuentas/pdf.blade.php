@extends('layouts.radCuenta_pdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table text-center table-bordered table-condensed">
				<tr style="background-color: #6c0e03; color: white">
					<td colspan="3">I. IDENTIFICACIÓN DEL CONTRATO</td>
				</tr>
				<tr>
					<td colspan="3">NOMBRE INTERVENTOR SI POSEE	<b>{{ $radCuenta->interventor->num_dc }} - {{ $radCuenta->interventor->nombre }} </td>
				</tr>
				<tr>
					<td colspan="2">Tipo De Contrato: <b>
						@if($radCuenta->registro->tipo_contrato == '3') DE OBRA PUBLICA
						@elseif($radCuenta->registro->tipo_contrato == '4') DE CONSULTORIA
						@elseif($radCuenta->registro->tipo_contrato == '5') DE INTERVENTORIA
						@elseif($radCuenta->registro->tipo_contrato == '6') DE SUMINISTRO
						@elseif($radCuenta->registro->tipo_contrato == '7') TRANSPORTE PASAJEROS TERRESTRE, HOTEL Y RESTAURANTE
						@elseif($radCuenta->registro->tipo_contrato == '8') TRANSPORTE DE CARGA
						@elseif($radCuenta->registro->tipo_contrato == '10') DE PRESTACION DE SERVICIOS
						@elseif($radCuenta->registro->tipo_contrato == '11') DE ENCARGO FIDUCIARIO Y FIDUCIA PUBLICA
						@elseif($radCuenta->registro->tipo_contrato == '12') ALQUILER O ARRENDAMIENTO
						@elseif($radCuenta->registro->tipo_contrato == '13') DE CONCESION
						@elseif($radCuenta->registro->tipo_contrato == '20') DEUDA PUBLICA
						@elseif($radCuenta->registro->tipo_contrato == '21') CONVENIO INTERADMINISTRATIVO
						@elseif($radCuenta->registro->tipo_contrato == '22') OTROS NO ESPECIFICADOS ANTERIORMENTE
						@endif
						</b>
					</td>
					<td>Modalidad de Seleccion: <b>
							@if($radCuenta->registro->mod_seleccion == '0') NO APLICA
							@elseif($radCuenta->registro->mod_seleccion == '1') LICITACION PUBLICA
							@elseif($radCuenta->registro->mod_seleccion == '2') CONCURSO DE MERITOS
							@elseif($radCuenta->registro->mod_seleccion == '3') SELECCION ABREVIADA
							@elseif($radCuenta->registro->mod_seleccion == '4') CONTRATACION DIRECTA
							@elseif($radCuenta->registro->mod_seleccion == '8') CUANTIA MINIMA
							@endif
						</b>
					</td>
				</tr>
				<tr>
					<td colspan="2">Contrato No. <b>{{ $radCuenta->registro->num_doc }}</b></td>
					<td>Fecha Contrato. <b>{{ \Carbon\Carbon::parse($radCuenta->registro->ff_doc)->format('d-m-Y') }}</b></td>
				</tr>
				<tr><td colspan="3">Objeto Contrato: <b>{{ $radCuenta->registro->objeto }}</b></td></tr>
				<tr style="background-color: #6c0e03; color: white"><td colspan="3">CDPs</td></tr>
				@foreach($cdps as $cdp)
					<tr>
						<td>{{$cdp->code}} - {{$cdp->name}}</td>
						@if($cdp->tipo == "Funcionamiento")
							<td>{{$cdp->rubro->cod}} - {{$cdp->rubro->name}} - {{$cdp->rubro->fuente}} - {{$cdp->fuente->code}} - {{$cdp->fuente->description}}</td>
						@else
							<td>{{$cdp->bpin->cod_actividad}} - {{$cdp->bpin->actividad}} - {{$cdp->rubro->cod}} - {{$cdp->rubro->name}} - {{$cdp->fuente->code}} - {{$cdp->fuente->description}}</td>
						@endif
						<td>{{$cdp->dep->name}} - {{$cdp->dep->name}}</td>
					</tr>
				@endforeach
				@if(count($ordenesPago) > 0)
					<tr style="background-color: #6c0e03; color: white"><td colspan="3">Ordenes de Pago</td></tr>
					@foreach($ordenesPago as $ordenPago)
						<tr id="ordenesPago">
							<td>{{ $ordenPago->code }}</td>
							<td>{{ $ordenPago->nombre }}</td>
							<td>$<?php echo number_format($ordenPago->valor,0) ?></td>
						</tr>
					@endforeach
				@endif
				@if(count($pagos) > 0)
					<tr style="background-color: #6c0e03; color: white"><td colspan="3">Pagos</td></tr>
				@foreach($ordenesPago as $index => $op)
						@foreach($pagos[$index] as $pago)
							<tr id="ordenesPago">
								<td>{{ $pago->code }}</td>
								<td>{{ $pago->concepto }}</td>
								<td>$<?php echo number_format($pago->valor,0) ?></td>
							</tr>
						@endforeach
				@endforeach
				@endif
				<tr>
					<td colspan="2">Fecha de Inicio: <b>{{ \Carbon\Carbon::parse($radCuenta->fecha_inicio)->format('d-m-Y') }}</b></td>
					<td>Plazo Ejecucion Dias: <b>{{ $radCuenta->plazo_ejec }}</b></td>
				</tr>
				<tr>
					<td>Prorroga en Dias: <b>{{ $radCuenta->prorroga }}</b></td>
					<td colspan="2">Fecha de Terminación: <b>{{ \Carbon\Carbon::parse($radCuenta->fecha_fin)->format('d-m-Y') }}</b></td>
				</tr>
				<tr style="background-color: #6c0e03; color: white"><td colspan="3">2. IDENTIFICACIÓN DEL BENEFICIARIO</td></tr>
				<tr>
					<td colspan="2">CONTRATISTA o CESIONARIO: <b>{{ $radCuenta->registro->persona->nombre }}</b></td>
					<td>CÉDULA O NIT:<b>{{ $radCuenta->registro->persona->num_dc }}</b></td>
				</tr>
				<tr>
					<td colspan="2">RÉGIMEN TRIBUTARIO DIAN:
						<b>
							@if($radCuenta->registro->regimen == 'ordinario') ORDINARIO
							@elseif($radCuenta->registro->regimen == 'simple tributacion') SIMPLE TRIBUTACIÓN
							@else Especial
							@endif
						</b>
					</td>
					<td>RETENCIÓN FUENTE: <b>{{ $radCuenta->registro->persona->reteFuente }}%</b></td>
				</tr>
				<tr>
					<td colspan="2">DIRECCIÓN CONTRATISTA O PROVEEDOR: <b>{{ $radCuenta->registro->persona->direccion }}</b></td>
					<td>TELÉFONO FIJO: <b>{{ $radCuenta->registro->persona->telefono_fijo }}</b></td>
				</tr>
				<tr>
					<td colspan="2">CORREO ELECTRÓNICO: <b>{{ $radCuenta->registro->persona->email }}</b></td>
					<td>CELULAR: <b>{{ $radCuenta->registro->persona->telefono }}</b></td>
				</tr>
				<tr>
					<td>CUENTA BANCARIA: <b>{{ $radCuenta->registro->persona->numero_cuenta_bancaria }}</b></td>
					<td>ENTIDAD BANCARIA:
						<b>
							@if($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO DE BOGOTA') BANCO DE BOGOTA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO AGRARIO') BANCO AGRARIO
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO DAVIVIENDA') BANCO DAVIVIENDA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO POPULAR') BANCO POPULAR
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO BANCOLOMBIA') BANCO BANCOLOMBIA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO OCCIDENTE') BANCO OCCIDENTE
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO AVVILLAS') BANCO AVVILLAS
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO BBVA') BANCO BBVA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO CAJA SOCIAL') BANCO CAJA SOCIAL
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO FALABELLA') BANCO FALABELLA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO SUDAMERIS') BANCO SUDAMERIS
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO PICHINCHA') BANCO PICHINCHA
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO CITIBANK') BANCO CITIBANK
							@elseif($radCuenta->registro->persona->banco_cuenta_bancaria == 'BANCO SANTANDER') BANCO SANTANDER
							@endif
						</b>
					</td>
					<td>CUENTA:
						<b>
							@if($radCuenta->registro->persona->tipo_cuenta_bancaria == 'Ahorros') AHORROS
							@else CORRIENTE
							@endif
						</b>
					</td>
				</tr>
				<tr style="background-color: #6c0e03; color: white"><td colspan="3">3. INFORMACION FINANCIERA</td></tr>
				<tr>
					<td>VALOR INICIAL DEL CONTRATO: <b>$<?php echo number_format( $radCuenta->valor_ini,0) ?></b></td>
					<td>
						@foreach($cdps as $cdp)
							CDP: #{{ $cdp->code }}
						@endforeach
					</td>
					<td>RP: #{{$radCuenta->registro->code }}</td>
				</tr>
				@if(count($radCuenta->adds) > 0)
					@foreach($radCuenta->adds as $add)
						<tr>
							<td>ADICION AL CONTRATO: <b>{{$add->valor}}</b></td>
							<td>
								@foreach($add->registro->cdpRegistroValor as $cdpRegValue)
									CDP: #<b>{{$cdpRegValue->cdps->code}}</b>
								@endforeach
							</td>
							<td>RP: #<b>{{$add->registro->code}}</b></td>
						</tr>
					@endforeach
				@endif
				<tr>
					<td>VALOR FINAL DEL CONTRATO: <b>$<?php echo number_format( $radCuenta->valor_fin,0) ?></b></td>
					<td>NUMERO DE PAGOS: <b>{{$radCuenta->num_pagos}}</b></td>
					<td>VALOR PAGO MENSUAL: $<?php echo number_format( $radCuenta->valor_mensual,0) ?></b></td>
				</tr>
				<tr>
					<td>INGRESO BASE RETENCIÓN: <b>$<?php echo number_format( $radCuenta->ing_retencion,0) ?></b></td>
					<td>ANTICIPO: <b>$<?php echo number_format( $radCuenta->anticipo,0) ?></b></td>
					<td>FECHA ANTICIPO: <b>{{ \Carbon\Carbon::parse($radCuenta->fecha_anticipo)->format('d-m-Y') }}</b></td>
				</tr>
				<tr>
					<td colspan="2">AMORTIZACIÓN ANTICIPO: <b>$<?php echo number_format( $radCuenta->amortizacion,0) ?></b></td>
					<td>FECHA: <b>{{ \Carbon\Carbon::parse($radCuenta->fecha_amort)->format('d-m-Y') }}</b></td>
				</tr>
			</table>
			@if(count($ordenesPago) > 0)
				<table id="tablaPagos" class="table text-center table-bordered table-responsive">
					<thead>
					<tr class="text-center">
						<th>Pago No.</th>
						<th>Valor</th>
						<th>Orden de Pago No.</th>
						<th>Fecha Pago</th>
						<th>Periodo de Pago</th>
						<th>Factura No.</th>
						<th>Planilla SSS.</th>
					</tr>
					</thead>
					<tbody>
					@foreach($ordenesPago as $index => $ordenPago)
						<tr>
							<td style="vertical-align: middle">{{$index + 1}}</td>
							<td style="vertical-align: middle">$<?php echo number_format($ordenPago->valor,0) ?></td>
							<td style="vertical-align: middle">{{$ordenPago->code}}</td>
							<td style="vertical-align: middle">{{ \Carbon\Carbon::parse($ordenPago->created_at)->format('d-m-Y')}}</td>
							<td style="vertical-align: middle">{{ $ordenPago->periodo_pago }}</td>
							<td>{{ $ordenPago->factura }}</td>
							<td>{{ $ordenPago->planilla }}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			@endif
			<table class="table text-center table-bordered">
				<tbody>
				<tr style="background-color: #6c0e03; color: white"><td colspan="3">4. DATOS PARA EL PAGO</td></tr>
				<tr>
					<td>Número trabajadores asociados actividad contractual: <b>{{$radCuenta->pago->num_trabajadores}}</b></td>
					<td>Número Planilla: <b>{{$radCuenta->pago->num_planilla}}</b></td>
					<td>Número de contratos con el municipio u otras entidades PoP: <b>{{$radCuenta->pago->num_contratos}}</b>
					</td>
				</tr>
				<tr>
					<td>Periodo Salud: <b>{{ $radCuenta->pago->periodo_salud }}</b></td>
					<td colspan="2">Valor Salud: <b>$<?php echo number_format($radCuenta->pago->valor_salud,0) ?></b></td>
				</tr>
				<tr>
					<td>Periodo Pensión: <b>{{$radCuenta->pago->periodo_pension}}</b></td>
					<td colspan="2"> Valor Pensión: <b>$<?php echo number_format($radCuenta->pago->valor_pension,0) ?></b></td>
				</tr>
				<tr>
					<td>ARL: <b>{{$radCuenta->pago->arl}}</b></td>
					<td colspan="2">Valor ARL: <b>$<?php echo number_format($radCuenta->pago->valor_arl,0) ?></b></td>
				</tr>
				<tr>
					<td>Caja Compensación <b>{{ $radCuenta->pago->caja }}</b></td>
					<td colspan="2">Valor Caja: <b>$<?php echo number_format($radCuenta->pago->valor_caja,0) ?></b></td>
				</tr>
				<tr><td colspan="3">Valor a Pagar: <b>$<?php echo number_format($radCuenta->pago->valor_pago,0) ?></b></td></tr>
				<tr style="background-color: #6c0e03; color: white"><td colspan="3">DESCUENTOS</td></tr>
				<tr>
					<td>Retencion en la Fuente DIAN: {{ $radCuenta->pago->reteDIAN }}%</td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->reteDIANValue ,0) ?></td>
				</tr>
				<tr>
					<td>Estampilla Adulto Mayor: {{ $radCuenta->pago->adulto }}%</td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->adultoValue ,0) ?></td>
				</tr>
				<tr>
					<td>Sobretasa Deportiva: {{ $radCuenta->pago->sobretasa }}%</td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->sobretasaValue) ?></td>
				</tr>
				<tr>
					<td>Estampilla Justicia: {{ $radCuenta->pago->estampilla }}%</td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->estampillaValue ,0) ?></td>
				</tr>
				<tr>
					<td>Industria y comercio ICA: {{ $radCuenta->pago->ica }}x1000 </td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->icaValue ,0) ?></td>
				</tr>
				<tr>
					<td>Contribución contrato de Obra pública: {{ $radCuenta->pago->obraPub }}%</td>
					<td colspan="2">Valor: $<?php echo number_format( $radCuenta->pago->obraPubValue ,0) ?></td>
				</tr>
				</tbody>
			</table>
			<table class="table text-center table-bordered">
				<tbody>
				<tr>
					<td>TOTAL DESCUENTOS: <b>$<?php echo number_format( $radCuenta->pago->totalDesc ,0) ?></b></td>
					<td>NETO A PAGAR: <b>$<?php echo number_format( $radCuenta->pago->netoPago ,0) ?></b></td>
				</tr>
				</tbody>
			</table>
			@if($radCuenta->estado_rev == '1')
				<table class="table text-center table-bordered">
					<tbody>
					<tr style="background-color: #6c0e03; color: white"><td colspan="2">5. CERTIFICACIÓN PARA PAGO</td></tr>
					<tr>
						<td colspan="2">
							Certificó que el contratista en mención, cumplió a cabalidad con las obligaciones establecidas en el contrato suscrito con la Alcaldia del municipio,  para el presente periodo y que se  verificó que el contratista realizó los pagos de Aportes al Sistema de Seguridad  Social o Parafiscales a que esta obligado si  hubo lugar a ello. En consecuencia se puede tramitar el pago correspondiente del contrato en mención.
						</td>
					</tr>
					<tr>
						<td>FECHA DE ESTA AUTORIZACION DE PAGO</td>
						<td> <b>{{ \Carbon\Carbon::parse($radCuenta->ff_fin_rev)->format('d-m-Y') }}</b></td>
					</tr>
					<tr>
						<td>NOMBRE INTERVENTOR</td>
						<td><b>{{$radCuenta->interventor->nombre}}</b></td>
					</tr>
					<tr>
						<td>NOMBRE SUPERVISOR</td>
						<td><b>{{$radCuenta->supervisor->nombre}}</b></td>
					</tr>
				</table>
			@else
				<table class="table text-center table-bordered">
					<tbody>
					<tr style="background-color: #6c0e03; color: white"><td colspan="2">RADICACIÓN DE CUENTA PENDIENTE DE APROBAR.</td></tr>
					<tr>
						<td>Radicación Elaborada Por: {{ $radCuenta->elaborador->name }} - {{ $radCuenta->elaborador->email }}</td>
						<td>Fecha de Elaboración: {{ \Carbon\Carbon::parse($radCuenta->ff_fin_elaborador)->format('d-m-Y') }}</td>
					</tr>
					</tbody>
				</table>
			@endif
		</div>
	</div>
@stop