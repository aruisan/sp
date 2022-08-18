@extends('layouts.rit_pdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table text-center table-bordered table-condensed">
				<tr style="background-color: #0e7224; color: white">
					<td colspan="2">I. ENCABEZADO</td>
				</tr>
				<tr>
					<td>1. Opción de uso <br> {{ $rit->opciondeUso }} </td>
					<td>2. Clase de Contribuyente <br> {{ $rit->claseContribuyente }} </td>
				</tr>
				<tr>
					<td colspan="2">
						3. Datos del Revisor fiscal y/o contador
						<br>
						&nbsp;
						<table class="table text-center table-condensed">
							<tr>
								<td>Nombre y apellidos Revisor fiscal<br>{{ $rit->nameRevFisc }}</td>
								<td>Indentificacion<br>{{ $rit->idRevFisc }}</td>
								<td>T.P.<br>{{ $rit->TPRevFisc }}</td>
								<td>Email<br>{{ $rit->emailRevFisc }}</td>
								<td>Móvil<br>{{ $rit->movilRevFisc }}</td>
							</tr>
							@if($rit->nameCont)
								<tr>
									<td>Nombre y apellidos Contador<br>{{ $rit->nameCont }}</td>
									<td>Indentificacion<br>{{ $rit->idCont }}</td>
									<td>T.P.<br>{{ $rit->TPCont }}</td>
									<td>Email<br>{{ $rit->emailCont }}</td>
									<td>Móvil<br>{{ $rit->movilCont }}</td>
								</tr>
							@endif
						</table>
					</td>
				</tr>
			</table>
			<br>
			<table class="table text-center table-bordered table-condensed">
				<tr style="background-color: #0e7224; color: white">
					<td colspan="3">II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR</td>
				</tr>
				<tr>
					<td colspan="2">4. Tipo y Número de Documento <br> {{ $rit->tipoDocContri }} {{ $rit->numDocContri }} - DV {{ $rit->DVDocContri }} </td>
					<td>5. Naturaleza Jurídica <br> {{ $rit->natJuridiContri }} </td>
				</tr>
				<tr>
					<td>6. Tipo de Sociedad <br> {{ $rit->tipSociedadContri }} </td>
					<td>7. Tipo de Entidad <br> {{ $rit->tipEntidadContri }} </td>
					<td>8. Clase de Entidad  <br> {{ $rit->claEntidadContri }} </td>
				</tr>
				<tr>
					<td colspan="2">9. Apellidos y Nombres ó Razón Social <br>{{ $rit->apeynomContri }}</td>
					<td >10. Avisos <br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input class="form-check-input text-center" type="checkbox" @if($rit->avisos == 1) checked @endif >
					</td>
				</tr>
				<tr>
					<td>11. Dirección de Notificación <br> {{ $rit->dirNotifContri }}</td>
					<td>12. Barrio / Vereda <br> {{ $rit->barrioContri }}</td>
					<td>13. Ciudad <br> {{ $rit->ciudadContri }}</td>
				</tr>
				<tr>
					<td>14. Teléfono <br> {{ $rit->telContri }}</td>
					<td>15. Sitio web <br> {{ $rit->webPageContri }}</td>
					<td>16. Teléfono Móvil (*) <br> {{ $rit->movilContri }}</td>
				</tr>
				<tr>
					<td colspan="3">17. Correo Electrónico<br> {{ $rit->emailContri }}</td>
				</tr>
			</table>
			<br>
			<table class="table text-center table-bordered table-condensed">
				<tr style="background-color: #0e7224; color: white">
					<td colspan="4">III. REPRESENTACIÓN LEGAL</td>
				</tr>
				<tr>
					<td>18. Nombres y Apellidos <br> {{ $rit->nombreRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->nombreRepLegal2 }}
						@endif
					</td>
					<td>TD <br> {{ $rit->TDRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->TDRepLegal2 }}
						@endif
					</td>
					<td>19. Identificación número <br> {{ $rit->IDNumRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->IDNumRepLegal2 }}
						@endif
					</td>
					<td>20. CR <br> {{ $rit->CRRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->CRRepLegal2 }}
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="3">21. Correo Electrónico <br> {{ $rit->emailRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->emailRepLegal2 }}
						@endif
					</td>
					<td>Teléfono Movil o WhatsApp <br> {{ $rit->telRepLegal }}
						@if($rit->nombreRepLegal2)
							<hr>
							{{ $rit->telRepLegal2 }}
						@endif
					</td>
				</tr>
			</table>
			<br>
			<table class="table text-center table-bordered">
				<tr style="background-color: #0e7224; color: white">
					<td colspan="4">IV. DATOS DE ESTABLECIMIENTOS DE COMERCIO UBICADOS EN PROVIDENCIA</td>
				</tr>
				<tr>
					<td>22. Nombre comercial del establecimiento</td>
					<td>23. Matricula Mercantil</td>
					<td>24. Teléfono</td>
					<td>25. Fecha de inicio de actividades</td>

				</tr>
				<tbody>
				@foreach($establecimientos as $establecimiento)
					<tr>
						<td>{{ $establecimiento->nombre }}</td>
						<td>{{ $establecimiento->matMercantil }}</td>
						<td>{{ $establecimiento->telefono }}</td>
						<td>{{ $establecimiento->fechaInicio }}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="2">26. Dirección del establecimiento</td>
					<td>27. Barrio</td>
					<td>28. Fecha solicitada de cancelación</td>
				</tr>
				@foreach($establecimientos as $establecimiento)
					<tr>
						<td colspan="2">{{ $establecimiento->direccion }}</td>
						<td>{{ $establecimiento->barrio }}</td>
						<td>{{ $establecimiento->fechaCancel }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<br>
			<table class="table text-center table-bordered table-condensed">
				<tr style="background-color: #0e7224; color: white">
					<td colspan="4">V. DATOS DE ACTIVIDADES ECONÓMICAS</td>
				</tr>
				<thead>
					<tr>
						<td>31. Cód.Activ.</td>
						<td>Cód. CIIU</td>
						<td>32. Descripción de la Actividad Económica</td>
						<td>33. Base Gravable Mensual</td>
					</tr>
				</thead>
				@foreach($actividades as $actividad)
					<tr>
						<td>{{ $actividad->codActividad }}</td>
						<td>{{ $actividad->codCIIU }}</td>
						<td>{{ $actividad->descripción }}</td>
						<td>$<?php echo number_format($actividad->baseGravable,0) ?></td>
					</tr>
				@endforeach
			</table>
			<br>
			<table class="table text-center table-bordered">
				<tr style="background-color: #0e7224; color: white"><td >VII. FIRMAS Y FECHA DE RECEPCIÓN</td></tr>
				<tr><td>37. Fecha de radicación <br> {{ $rit->radicacion }}</td></tr>
			</table>
			<br>
			<table class="table text-center table-bordered"><tr style="background-color: #0e7224; color: white"><td><h4>ESTE FORMULARIO NO TIENE COSTO ALGUNO</h4></td></tr></table>
		</div>
	</div>
@stop
