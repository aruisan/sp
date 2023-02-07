@extends('layouts.OPPdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table class="table table-borderless">
				<tr class="text-center">
					<td colspan="2">CERTIFICADO DE INGRESOS Y RETENCIONES</td>
				</tr>
				<tr class="text-center">
					<td colspan="2">Periodo reportado 01 de Enero del {{ $añoActual }} al <?=$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></td>
				</tr>
				<tr class="text-center">
					<td>{{ $persona->num_dc }} </td>
					<td>{{ $persona->nombre }} </td>
				</tr>
			</table>
		</div>
		<div>
			<center>
				<h5>CONCEPTO</h5>
				<p></p>
			</center>
		</div>
		<div class="table-responsive br-black-1">
			<table class="table-bordered" id="tablaDesc" style="width: 100%">
				<thead>
				<tr>
					<th class="text-center">Fecha Pago</th>
					<th class="text-center"># CE</th>
					<th class="text-center">Concepto CE</th>
					<th class="text-center">Valor OP</th>
					<th class="text-center">Valor Desc</th>
					<th class="text-center">Cuenta Desc</th>
					<th class="text-center">Concepto Desc</th>
					<th class="text-center">Valor Pago</th>
				</tr>
				</thead>
				<tbody>
				@foreach($values as $data)
					<tr class="text-center">
						<td>{{ $data['Pfecha'] }}</td>
						<td>{{ $data['CEcode'] }}</td>
						<td>{{ $data['CEconcepto'] }}</td>
						<td>$ <?php echo number_format($data['OPvalor'],0);?></td>
						<td>$ <?php echo number_format($data['DESvalor'],0);?></td>
						<td>{{ $data['DEScuenta'] }}</td>
						<td>{{ $data['DESconcepto'] }}</td>
						<td>$ <?php echo number_format($data['Pvalor'],0);?></td>
					</tr>
				@endforeach
				<tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
					<td colspan="4"><b>Total Descuentos</b></td>
					<td colspan="4"><b>$ <?php echo number_format($totDes,0);?></b></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div style="font-size: 10px;">
		<div class="col-md-12 align-self-center">
			<div class="table-borderless">
				<table class="table table-borderless" style="border: hidden">
					<tr class="text-center">
						<td>
						</td>
						<td>
							<center>
								_________________________ <br>
								JUSTINO<br>
								TESORERO
							</center>
						</td>
						<td>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@stop
