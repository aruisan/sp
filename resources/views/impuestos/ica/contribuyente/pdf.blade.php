@extends('layouts.ica_pdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table text-center table-bordered">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<td colspan="3">INFORMACIÓN DEL CONTRIBUYENTE</td>
				</tr>
				<tr>
					<td>Naturaleza Juridica:{{$rit->natJuridiContri}}</td>
					<td>{{ $rit->tipoDocContri }}{{ $rit->numDocContri }}</td>
					<td>Clasificación Contribuyente:{{ $rit->claseContribuyente }}</td>
				</tr>
				<tr>
					<td colspan="3">Nombre y apellidos o razón Social: {{ $rit->apeynomContri }}</td>
				</tr>
				<tr>
					<td colspan="3">Dirección: {{ $rit->dirNotifContri }}</td>
				</tr>
				<tr>
					<td>Teléfono Móvil: {{ $rit->movilContri }}</td>
					<td colspan="2">Correo electrónico: {{ $rit->emailContri }}</td>
				</tr>
				<tr>
					<td colspan="3"><b>No. Formulario: {{$ica->numReferencia}}</b></td>
				</tr>
				</tbody>
			</table>
			<br>
			<table class="table text-center table-bordered">
				<tr style="background-color: #0e7224; color: white"><td colspan="3">PAGO</td></tr>
				<tr><td colspan="3">Fecha de presentación <br> {{ $ica->presentacion }}</td></tr>
				<tr>
					<td>Valor a Pagar <br> $<?php echo number_format($ica->valoraPagar,0) ?></td>
					<td>Valor Descuentos <br> $<?php echo number_format($ica->valorDesc,0) ?></td>
					<td>Valor Intereses Mora <br> $<?php echo number_format($ica->interesesMora,0) ?></td>
				</tr>
				<tr><td colspan="3">Total a Pagar <br> $<?php echo number_format($ica->totPagar,0) ?></td></tr>
			</table>
		</div>
	</div>
@stop
