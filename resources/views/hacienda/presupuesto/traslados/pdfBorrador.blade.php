
@extends('layouts.certificadosPdf')
@section('contenido')
		<div class="row">
			<center><h3>BORRADOR DE CDP</h3></center>
		</div>
		<div style="border:1px solid black;">
			<div style="width: 78%;   display: inline-block; margin-left: 3%">
				<h4>Fecha: <?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y').'  Hora:'.$fecha->format('h:i:s')?></h4>
			</div>
			
			<div style="width: 12%;  display: inline-block; border:1px solid black; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Número {{ $cdp->code }}</h4>
			</div> 
		</div>
				
		<div class="br-black-1">
			<br>
			<center>
				<h2>BORRADOR DE CDP</h2>
				<br>
				@if($cdp->alcalde_e == "0")
					<p>PENDIENTE POR APROBAR EL ALCALDE</p>
				@endif
				@if($cdp->jefe_e == "0")
					<p>PENDIENTE POR APROBAR EL JEFE</p>
				@endif
			</center>
		</div>
		@if($cdp->tipo == "Funcionamiento")
			@foreach($cdp->rubrosCdp as $rubrosCdp)
			<div class="br-black-1">
				<table style="margin: 5px 10px;">
					<tbody>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Rubro: </td>
							<td>{{$rubrosCdp->rubros->cod}} - {{$rubrosCdp->rubros->name}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Tipo: </td>
							<td> {{$cdp->tipo}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Valor: </td>
							<td> {{number_format($rubrosCdp->rubrosCdpValor->first()->valor)}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			@endforeach
		@else
			@foreach($cdp->bpinsCdpValor as $bpinsCDP)
				<div class="br-black-1">
					<table style="margin: 5px 10px;">
						<tbody>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Proyecto: </td>
							<td>{{$bpinsCDP->actividad->cod_proyecto}} - {{$bpinsCDP->actividad->nombre_proyecto}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Actividad: </td>
							<td>{{$bpinsCDP->actividad->cod_actividad}} - {{$bpinsCDP->actividad->actividad}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Tipo: </td>
							<td> {{$cdp->tipo}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Valor: </td>
							<td> {{number_format($bpinsCDP->valor)}}</td>
						</tr>
						</tbody>
					</table>
				</div>
			@endforeach
		@endif
		<div class="br-black-1">
			<table style="margin: 5px 10px;">
				<tbody>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">VALOR TOTAL: </td>
						<td> {{number_format($cdp->valor)}} ({{\NumerosEnLetras::convertir($cdp->valor)}})</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">Objeto: </td>
						<td>{{$cdp->name}}</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">SOLICITADO POR: </td>
						<td> {{!is_null($cdp->cdpsSecretaria) ? $cdp->cdpsSecretaria->name : ''}}</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">DEPENDENCIA: </td>
						<td> {{!is_null($cdp->cdpsSecretaria) ? $cdp->dependencia->name : ''}}</td>
					</tr>
				</tbody>
			</table>
			
		</div>
@stop
