
@extends('layouts.certificadosPdf')
@section('contenido')
		<div class="row">
			<center><h3>REGISTRO PRESUPUESTAL</h3></center>
		</div>
		<div style="border:1px solid black;">
			<div style="width: 78%;   display: inline-block; margin-left: 3%">
				<h4><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></h4>
			</div>
			<div style="width: 12%;  display: inline-block; border:1px solid black; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Número {{$registro->code}}</h4>
			</div> 
		</div>
		<div class="br-black-1">
			<center>
				<h4>CERTIFICA</h4>
				Que en la fecha el presupuesto de Gastos para la vigencia fiscal del año {{$vigencia->vigencia}} se le ha efectuado Registro Presupuestal por:
			</center>
		</div>
		<div class="br-black-1">
			@if($registro->cdpsRegistro->first()->cdp->tipo == "Funcionamiento")
				<table class="table table-condensed" style="margin: 5px 10px;">
					<thead>
					<tr>
						<th class="text-center" colspan="6">DINERO TOMADO DE LOS RUBROS DE CDPs</th>
					</tr>
					<tr>
						<th class="text-center"># CDP</th>
						<th class="text-center">Nombre CDP</th>
						<th class="text-center">Codigo</th>
						<th class="text-center">Nombre</th>
						<th class="text-center">Fuente</th>
						<th class="text-center">Valor</th>
					</tr>
					</thead>
					<tbody>
					@foreach($infoRubro as $rubro)
						<tr class="text-center">
							<td>{{$rubro['codCDP']}} </td>
							<td>{{$rubro['nameCDP']}} </td>
							<td>{{$rubro['codigo']}} </td>
							<td> {{$rubro['name']}}</td>
							<td> {{$rubro['font']}}</td>
							<td>${{number_format($rubro['value'])}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			@else
				<table style="margin: 5px 10px;">
					<tbody>
					@foreach($bpins as $bpin)
						@if(isset($bpin->depRubroFont->fontRubro))
							<tr>
								<td style="width: 30px;">Rubro: </td>
								<td>{{$bpin->depRubroFont->fontRubro->rubro->cod}} - {{$bpin->depRubroFont->fontRubro->rubro->name}}</td>
							</tr>
							<tr>
								<td style="width: 30px;">Fuente: </td>
								<td>{{$bpin->depRubroFont->fontRubro->sourceFunding->code}} - {{$bpin->depRubroFont->fontRubro->sourceFunding->description}}</td>
							</tr>
						@endif
						<tr>
							<td style="width: 30px;">Proyecto: </td>
							<td>{{$bpin->actividad->cod_proyecto}} - {{$bpin->actividad->nombre_proyecto}}</td>
						</tr>
						<tr>
							<td style="width: 30px;">Actividad: </td>
							<td>{{$bpin->actividad->cod_actividad}} - {{$bpin->actividad->actividad}}</td>
						</tr>
						<tr>
							<td style="width: 30px;">Valor usado: </td>
							<td>$ {{number_format($bpin->valor)}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			@endif

		</div>
		<div class="br-black-1">
			<table style="margin: 5px 10px;">
				<tbody>
					<tr style="font-size: 16px;">
						<td># Documento: </td>
						<td>{{$registro->num_doc}}</td>
					</tr>
					<tr style="font-size: 16px;">
						<td>OBJETO: </td>
						<td> {{$registro->objeto}} </td>
					</tr>
					<tr style="font-size: 16px;">
						<td>VALOR TOTAL: </td>
						<td> $ {{number_format($registro->valor)}}</td>
					</tr>
					<tr style="font-size: 16px;">
						<td>Beneficiario: </td>
						<td>{{$registro->persona->num_dc}} - {{$registro->persona->nombre}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	<div class="br-black-1">
		<div class="text-center">
			<h4>CDP's Asignados al Registro Presupuestal</h4>
		</div>
		<h4 class="text-center">
			@foreach($registro->cdpsRegistro as $data)
				CDP {{ $data->cdp->code }} - {{ $data->cdp->tipo }}
			@endforeach
		</h4>
	</div>
		<div style="margin-top: 10px; font-size: 17px;">
			<center>
				<img src="http://presupuesto.providenciaislas.com.co/img/firmas/JIM_ANDERSON_HENRY_BENT.png"  height="99"><br>
				JIM ANDERSON HENRY BENT<br>
				JEFE DE PRESUPUESTO
			</center>
		</div>

@stop
		
	
