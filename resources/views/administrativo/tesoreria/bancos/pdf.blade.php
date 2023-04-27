<!DOCTYPE html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<title></title>
	<style type="text/css">
		body {
			margin: 4px;
			font-size: 10px;
		}

		.amarillo{
			border: 1px solid yellow;
		}
		.azul{
			border: 1px solid blue;
		}

		.rojo{
			border: 1px solid red;
		}

		.s7{width: 7%; display: inline-block;}
		.s17{width: 17%; display: inline-block;}
		.s57{width: 57%; display: inline-block; bottom-top: 10px; bottom:10px;}

		.s57 p{font-size: 12px; }
		.br-black-1 p{font-size: 15px; }

		.hrFecha {
			border-style: double;

		}

		.hr0margin{
			margin-bottom: 0px;
			margin-bottom: 0px;
		}

		.br-black-1{
			border: 1px solid black;
		}
	</style>
</head>
<body>
	

<div class="container-fluid">
	<div class="row">
		<div class="col-md-2 s7" >
			<img src="https://www.siex-concejoprovidenciaislas.com.co/img/escudoIslas.png"  height="60">
		</div>
		<div class="col-md-4 s57">
			<p>
				<b>
					DEPARTAMENTO DE SAN ANDRES, PROVIDENCIA Y STA CATALINA <br>
					MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS <br>
				</b>
			</p>
		</div>
		<div class="col-md-6 s17">
			<img src="https://www.siex-concejoprovidenciaislas.com.co/img/logoSiex.png"  height="50">
		</div>
	</div>
	<div class="row">
		<center><h3>CONCILIACIÓN BANCARIA</h3></center>
	</div>
	<br>
	<div style="border:1px solid black;">
		<div style="width: 78%;   display: inline-block; margin-left: 3%">
			<h4>Fecha: <?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y')?></h4>
		</div>
		<div style="width: 12%;  display: inline-block; border:1px solid black; margin: 6px 0px 0px 0px;" class="col-md-2">
			<h4>Número {{ $conciliacion->id }}</h4>
		</div>
	</div>
	<br><center>
		<h4>Periodo ({{$periodo_inicial}} - {{$periodo_final}}) -- {{ $conciliacion->puc->code }} - {{ $conciliacion->puc->concepto }}</h4>
	</center><br>
		@php
			$s_libros = is_null($conciliacion->conciliacion_anterior) ? $totalLastMonth:  $conciliacion->saldo_libros;
			$s_inicial = $conciliacion->saldo_inicial;
			$libro_debito = 0;
			$libro_credito = 0;
			$banco_debito = 0;
			$banco_credito = 0;
			$banco_diferencia = 0;
			$banco_credito_anterior = 0;
			$banco_diferencia_anterior = 0;
			$data_cheque_mano = $cuentas;
			$data_mano_select = $cuentas->filter(function($c){ return $c->aprobado == "ON";});
			$data_mano_no_select = $cuentas->filter(function($c){ return $c->aprobado == "OFF";});
			$data_cobro_select =  $conciliacion->cuentas_temporales->filter(function($e){ return $e->check;});
			$data_cobro_no_select =  $conciliacion->cuentas_temporales->filter(function($e){ return !$e->check;});

			foreach($data_cheque_mano as $a):
				$libro_credito += $a->credito;
				$libro_debito += $a->debito;
			endforeach;

			foreach($data_mano_select as $a):
				$banco_credito += $a->credito;
				$banco_debito += $a->debito;
			endforeach;

			foreach($data_mano_no_select as $a):
				$banco_diferencia += $a->credito;
			endforeach;

			foreach($data_cobro_select as $a):
				$banco_credito_anterior += $a->comprobante_ingreso_temporal->valor;
			endforeach;

			foreach($data_cobro_no_select as $a):
				$banco_diferencia_anterior += $a->comprobante_ingreso_temporal->valor;
			endforeach;

			$diferencia = $banco_diferencia +$banco_diferencia_anterior;
			$saldo_siguiente = $s_libros + $libro_debito - $libro_credito;
			$saldo_final = $s_inicial + $banco_debito - $banco_credito - $banco_credito_anterior;
			$sumas_iguales_libros = $saldo_siguiente + $diferencia;
			$sumas_iguales_bancos = $saldo_final;
		@endphp
	<div class="table-responsive br-black-1">
		<table class="table table-bordered">
			<thead>

			<tr>
				<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">RESUMEN DE LA INFORMACION</th>
			</tr>
			<tr>
				<th class="text-center">Saldo Libros</th>
				<th class="text-center">$<?php echo number_format($s_libros,0) ?></th>
				<th class="text-center">Saldo inicial bancos</th>
				<th class="text-center">$<?php echo number_format($s_inicial,0) ?></th>
			</tr>
			</thead>
			<tbody>
			<tr class="text-center">
				<td>Saldo siguiente</td>
				<td>$<?php echo number_format($saldo_siguiente,0) ?></td>
				<td> Saldo final</td>
				<td>$<?php echo number_format($saldo_final,0) ?></td>
			</tr>
			<tr class="text-center">
				<td colspan="2">Diferencia a conciliar</td>
				<td colspan="2">$<?php echo number_format($diferencia,0) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
		<br>
	<div class="table-responsive br-black-1">
		<table class="table table-condensed">
			<thead>
			<tr style="background-color: rgba(19,165,255,0.14)">
				<th class="text-center">FECHA</th>
				<th class="text-center">REFERENCIA</th>
				<th class="text-center">DEBITO</th>
				<th class="text-center">CREDITO</th>
				<th class="text-center">VALOR BANCO</th>
				<th class="text-center">ESTADO</th>
			</tr>
			</thead>
			<tbody>
			@foreach($cuentas->filter(function($e){ return $e->aprobado == 'ON'; }) as $data)
				<tr class="text-center">
					<td>{{ \Carbon\Carbon::parse($data->fecha)->format('d-m-Y') }}</td>
					<td>{{$data->referencia}}</td>
					<td>$<?php echo number_format($data->debito,0) ?></td>
					<td>$<?php echo number_format($data->credito,0) ?></td>
					<td>$<?php echo number_format($data->valor,0) ?></td>
					<td>APROBADO</td>
				</tr>
			@endforeach
			@foreach($cuentas->filter(function($e){ return $e->aprobado == 'OFF';}) as $data)
				<tr class="text-center">
					<td>{{ \Carbon\Carbon::parse($data->fecha)->format('d-m-Y') }}</td>
					<td>{{$data->referencia}}</td>
					<td>$<?php echo number_format($data->debito,0) ?></td>
					<td>$<?php echo number_format($data->credito,0) ?></td>
					<td>$<?php echo number_format($data->valor,0) ?></td>
					<td class="text-danger">NO APROBADO</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<div class="text-center">
		<table class="table table-bordered table-hover" id="tablaBank">
			<thead>
			<tr>
				<th class="text-center" colspan="6">CHEQUES COBRADOS</th>
			</tr>
			<tr>
				<th class="text-center">FECHA</th>
				<th class="text-center">REFERENCIA</th>
				<th class="text-center">DEBITO</th>
				<th class="text-center">CREDITO</th>
				<th class="text-center">VALOR BANCO</th>
				<th class="text-center">APROBADO</th>
			</tr>
			</thead>
			<tbody id="bodyTabla">
			@foreach($conciliacion->cuentas_temporales->filter(function($e){ return $e->check;}) as $index => $item)
				<tr class="text-center">
					<td>{{$item->comprobante_ingreso_temporal->fecha}}</td>
					<td>{{$item->comprobante_ingreso_temporal->referencia}} - {{$item->comprobante_ingreso_temporal->cc}} - {{$item->comprobante_ingreso_temporal->tercero}}</td>
					<td>$<?php echo number_format(0,0) ?></td>
					<td>$<?php echo number_format(0,0) ?></td>
					<td>$<?php echo number_format($item->comprobante_ingreso_temporal->valor,0)?></td>
					<td>Aprobado</td>
				</tr>
			@endforeach
			@foreach($conciliacion->cuentas_temporales->filter(function($e){ return !$e->check;}) as $index => $item)
				<tr class="text-center">
					<td>{{$item->comprobante_ingreso_temporal->fecha}}</td>
					<td>{{$item->comprobante_ingreso_temporal->referencia}} - {{$item->comprobante_ingreso_temporal->cc}} - {{$item->comprobante_ingreso_temporal->tercero}}</td>
					<td>$<?php echo number_format(0,0) ?></td>
					<td>$<?php echo number_format(0,0) ?></td>
					<td>$<?php echo number_format($item->comprobante_ingreso_temporal->valor,0)?></td>
					<td class="text-danger">NO APROBADO</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<table class="table table-bordered table-hover">
		<hr>
		<thead>
		<tr>
			<th colspan="4" class="text-center">CUADRO RESUMEN</th>
		</tr>
		</thead>
		<tbody id="bodyTabla">
		
			
		<tr class="text-center">
			<td>Saldo siguiente</td>
			<td>$<?php echo number_format($saldo_siguiente,0) ?></td>
			<td> Saldo Final</td>
			<td id="td_saldo_final">{{number_format($saldo_final,0)}}</td>
		</tr>
		<tr class="text-center">
			<td>Diferencia</td>
			<td id="td-restar-checke-mano">
				{{number_format($diferencia, 0)}}
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr class="text-center">
			
			<td>SUMAS IGUALES</td>
			<td id="td-dumas-iguales-libros">
				{{number_format($sumas_iguales_libros, 0)}}
			</td>
			<td></td>
			<td id="td-dumas-iguales-bancos">
				{{number_format($sumas_iguales_bancos,0)}}
			</td>
		</tr>
		</tbody>
	</table>

	<br><br><br>
	<div style="margin-top: 10px; font-size: 17px;">
		<table>
			<tr>
				<td>
					___________________________
				</td>
				<td>
					___________________________
				</td>
			</tr>
			<tr>
				<td>
					JUSTINO BRITTON HENRY
				</td>
				<td>
					HELEN GARCIA ALEGRIA
				</td>
			</tr>
			<tr>
				<td>
					TESORERO
				</td>
				<td>
					Profesional Universitario
				</td>
			</tr>
		</table>
	</div>

	<div style="margin-top: 50px; font-size: 17px;">
		Elaborado por: {{$conciliacion->responsable->name}}
	</div>
</div>

</body>
</html>
