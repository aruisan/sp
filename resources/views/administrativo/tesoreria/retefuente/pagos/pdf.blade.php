@extends('layouts.OPPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td>COMPROBANTE DE CONTABILIDAD No: {{ $pago->compcontable->id }}</td>
					<td><?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
				</tr>
			</table>
		</div>
		<div class="table-responsive" id="formulario">
			<table class="table table-bordered" id="tabla_form">
				<thead>
				<tr><th class="text-center" colspan="3">FORMULARIO 350</th></tr>
				<tr><th class="text-center" colspan="3">MUNICIPIO DE PROVIDENCIA ISLAS</th></tr>
				<tr><th class="text-center" colspan="3">NIT. 800103021-1</th></tr>
				<tr><th class="text-center" colspan="3">DECLARACION DE RETENCION EN LA FUENTE MES: {{ $mes }}</th></tr>
				<tr><th class="text-center" colspan="3">Periodo comprendido entre 1 de {{ $mes }} al {{ $days }} de {{$mes}} de {{ $vigencia->vigencia }}</th></tr>
				<tr>
					<th class="text-center">CONCEPTO</th>
					<th class="text-center">BASE SUJETA A RETENCIÓN</th>
					<th class="text-center">RETENCIONES</th>
				</tr>
				</thead>
				<tbody>
				@foreach($pago->formularios as $index => $dato)
					<tr>
						<td class="text-center">{{ $dato['concepto'] }}</td>
						<td class="text-center">
							@if($dato['base'] > 0) $ <?php echo number_format($dato['base'],0);?> @endif
						</td>
						<td class="text-center">
							@if($dato['retencion'] > 0) $ <?php echo number_format($dato['retencion'],0);?> @endif
						</td>
					</tr>
				@endforeach
				<tr>
					<td class="text-center" colspan="2"><b>TOTAL A PAGAR</b></td>
					<td class="text-center"><b>$ <?php echo number_format($pago->pago,0);?></b></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="table-responsive" style="width: 100%">
			<table class="table table-bordered" id="tablaP">
				<thead>
				<tr>
					<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">CONTABILIZACIÓN</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Nombre Cuenta</th>
					<th class="text-center">Debito</th>
					<th class="text-center">Credito</th>
				</tr>
				</thead>
				<tbody>
				@for($z = 0; $z < $pago->contas->count(); $z++)
					<tr class="text-center">
						<td>{{$pago->contas[$z]->puc->code}}</td>
						<td>{{$pago->contas[$z]['concepto']}}</td>
						<td>$ <?php echo number_format($pago->contas[$z]['debito'],0);?></td>
						<td></td>
					</tr>
				@endfor
				<tr class="text-center">
					<td>{{$pago->puc->code}}</td>
					<td>{{$pago->puc->concepto}}</td>
					<td></td>
					<td>$ <?php echo number_format($pago->pago,0);?></td>
				</tr>
				<tr>
					<td colspan="2">SUMAS IGUALES</td>
					<td>$ <?php echo number_format($pago->contas->sum('debito'),0);?></td>
					<td>$ <?php echo number_format($pago->pago,0);?></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
@stop
