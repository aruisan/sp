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
				<tr style="background-color: #0e7224; color: white"><td colspan="2">PAGO</td></tr>
				<tr>
					<td>Fecha de presentación <br> {{ $ica->presentacion }}</td>
					<td>Total a Pagar <br> <h4>$<?php echo number_format($ica->pagoTotal,0) ?></h4></td>
				</tr>
				<tr>
					<td colspan="2">
						{!! DNS1D::getBarcodeHTML('(415)7709998144460(8020)'.$ica->numReferencia.'(3900)'.$ica->pagoTotal.'(96)'. \Carbon\Carbon::parse($ica->presentacion)->format('Ymd'), 'C128',1.04,45) !!}
						(415)7709998144460(8020){{$ica->numReferencia}}(3900){{$ica->pagoTotal}}(96){{ \Carbon\Carbon::parse($ica->presentacion)->format('Ymd') }}
						<br>
						Señor Cajero por favor no colocar el sello en el código de barras
					</td>
				</tr>
			</table>
			<br>
			PUNTOS DE PAGO <br>
			Banco de Bogota No. 540047529 Cuente Corriente <br>
			Banco Agrario No.381100000557 Cuenta Corriente
		</div>
	</div>
@stop
