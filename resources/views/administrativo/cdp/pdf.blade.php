
@extends('layouts.certificadosPdf')
@section('contenido')
		<div class="row">
			<center><h3>CERTIFICADO DE DISPONIBILIDAD PRESUPUESTAL</h3></center>
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
			<center>
				<h4>CERTIFICA</h4>
				Que en la fecha el presupuesto de Gastos para la vigencia fiscal del año {{$vigencia->vigencia}} Existe Disponibilidad Presupuestal por:
			</center>
		</div>
		@if($cdp->tipo == "Funcionamiento")
			<div class="br-black-1">
				<table class="table table-condensed" style="margin: 5px 10px;">
					<thead>
					<tr>
						<th class="text-center" colspan="3">RUBROS ASIGNADOS</th>
					</tr>
					<tr>
						<th class="text-center">Codigo</th>
						<th class="text-center">Nombre</th>
						<th class="text-center">Valor</th>
					</tr>
					</thead>
					<tbody>
					@foreach($infoRubro as $rubro)
						<tr class="text-center">
							<td>{{$rubro['codigo']}} </td>
							<td> {{$rubro['name']}}</td>
							<td>${{number_format($rubro['value'])}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		@else
			@foreach($cdp->bpinsCdpValor as $bpinsCDP)
				<div class="br-black-1">
					<table style="margin: 5px 10px;">
						<tbody>
						<tr>
							<td style="width: 30px;">Proyecto: </td>
							<td>{{$bpinsCDP->actividad->cod_proyecto}} - {{$bpinsCDP->actividad->nombre_proyecto}}</td>
						</tr>
						<tr>
							<td style="width: 30px;">Actividad: </td>
							<td>{{$bpinsCDP->actividad->cod_actividad}} - {{$bpinsCDP->actividad->actividad}}</td>
						</tr>
						<tr>
							<td style="width: 30px;">Tipo: </td>
							<td> {{$cdp->tipo}}</td>
						</tr>
						<tr>
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
					<tr>
						<td style="width: 30px;">Tipo: </td>
						<td> {{$cdp->tipo}}</td>
					</tr>
					<tr>
						<td style="width: 30px;">VALOR TOTAL: </td>
						<td> $ {{number_format($cdp->valor)}} ({{\NumerosEnLetras::convertir($cdp->valor)}})</td>
					</tr>
					<tr>
						<td style="	width: 30px;">Objeto: </td>
						<td>{{$cdp->name}}</td>
					</tr>
					<tr>
						<td style="width: 30px;">SOLICITADO POR: </td>
						<td> {{!is_null($cdp->cdpsSecretaria) ? $cdp->cdpsSecretaria->name : ''}}</td>
					</tr>
					<tr>
						<td style="width: 30px;">DEPENDENCIA: </td>
						<td> {{!is_null($cdp->cdpsSecretaria) ? $cdp->cdpsSecretaria->dependencia->name : ''}}</td>
					</tr>
				</tbody>
			</table>
			
		</div>
@stop
