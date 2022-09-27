
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
			<br>
			<center>
				<h2>CERTIFICA</h2>
				<br>
				<p>
					Que en la fecha el presupuesto de Gastos para la vigencia fiscal del año {{$vigencia->vigencia}} se le ha efectuado Registro Presupuestal por:
				</p>
			</center>
		</div>
		<div class="br-black-1">
			<table style="margin: 5px 10px;">
				<tbody>
					@if($registro->cdpsRegistro->first()->cdp->tipo == "Funcionamiento")
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Por valor de : </td>
							<td>{{number_format($registro->valor)}}</td>
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Programa: </td>
							@foreach($infoRubro as $rubro)
								@if($rubro['name'] == $rubroNameCDP)
									<td>{{$rubro['last_code']}} - {{$rubro['register']}}</td>
								@endif
							@endforeach
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Sub Programa: </td>
							@foreach($infoRubro as $rubro)
								@if($rubro['name'] == $rubroNameCDP)
									<td>{{$rubro['codigo']}} - {{$rubro['name']}}</td>
								@endif
							@endforeach
						</tr>
						<tr style="font-size: 16px;">
							<td style="width: 30px;">Objeto: </td>
							<td> {{$registro->objeto}}</td>
						</tr>
					@else
						@foreach($bpins as $bpin)
							<tr style="font-size: 16px;">
								<td style="width: 30px;">Proyecto: </td>
								<td>{{$bpin->actividad->cod_proyecto}} - {{$bpin->actividad->nombre_proyecto}}</td>
							</tr>
							<tr style="font-size: 16px;">
								<td style="width: 30px;">Actividad: </td>
								<td>{{$bpin->actividad->cod_actividad}} - {{$bpin->actividad->actividad}}</td>
							</tr>
							<tr style="font-size: 16px;">
								<td style="width: 30px;">Valor usado: </td>
								<td>$ {{number_format($bpin->valor)}} ({{\NumerosEnLetras::convertir($bpin->valor)}})</td>
							</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
		<div class="br-black-1">
			<table style="margin: 5px 10px;">
				<tbody>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">VALOR TOTAL: </td>
						<td> $ {{number_format($registro->valor)}} ({{\NumerosEnLetras::convertir($registro->valor)}})</td>
					</tr>
					<tr style="font-size: 16px;">
						<td style="width: 30px;">Beneficiario: </td>
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

@stop
		
	