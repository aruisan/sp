
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
		<?php $sumRubros = 0;?>
		@if($cdp->tipo == "Funcionamiento")
			@foreach($cdp->rubrosCdp as $rubrosCdp)
			<div class="br-black-1">
				<table style="margin: 5px 10px;">
					<tbody>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Proyecto: </td>
							<td> {{$rubrosCdp->rubros->subProyecto->proyecto->name}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Sub-Proyecto: </td>
							<td> {{$rubrosCdp->rubros->subProyecto->name}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Programa: </td>
							@foreach($infoRubro as $rubro)
								@if($rubro['name'] == $rubrosCdp->rubros->name)
									<td>{{$rubro['last_code']}} - {{$rubro['register']}}</td>
								@endif
							@endforeach
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Sub Programa: </td>
							@foreach($infoRubro as $rubro)
								@if($rubro['name'] == $rubrosCdp->rubros->name)
									<td>{{$rubro['codigo']}} - {{$rubro['name']}}</td>
								@endif
							@endforeach
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Tipo: </td>
							<td> {{$cdp->tipo}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Valor: </td>
							<td> {{number_format($cdp->valor)}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php $sumRubros = $sumRubros+$rubrosCdp->rubrosCdpValor->sum('valor');?>
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
					<?php $sumRubros = $cdp->valor;?>
			@endforeach
		@endif
		<div class="br-black-1">
			<table style="margin: 5px 10px;">
				<tbody>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">VALOR TOTAL: </td>
						<td> {{number_format($sumRubros)}} ({{\NumerosEnLetras::convertir($sumRubros)}})</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">Objeto: </td>
						<td>{{$cdp->name}}</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">SOLICITADO POR: </td>
						<td> {{$cdp->cdpsSecretaria->name}}</td>
					</tr>
				</tbody>
			</table>
			
		</div>
@stop
