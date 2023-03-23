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
		<h4>{{ $conciliacion->puc->code }} - {{ $conciliacion->puc->concepto }}</h4>
	</center><br>
	<div class="table-responsive br-black-1">
		<table class="table table-bordered">
			<thead>
			<tr>
				<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">RESUMEN DE LA INFORMACION</th>
			</tr>
			<tr>
				<th class="text-center">Saldo Libros</th>
				<th class="text-center">$<?php echo number_format($totalLastMonth,0) ?></th>
				<th class="text-center">Saldo inicial bancos</th>
				<th class="text-center">$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></th>
			</tr>
			</thead>
			<tbody>
			<tr class="text-center">
				<td>Ingresos</td>
				<td>$<?php echo number_format($totDeb,0) ?></td>
				<td>Abonos</td>
				<td>$<?php echo number_format($totDeb,0) ?></td>
			</tr>
			<tr class="text-center">
				<td>Egresos</td>
				<td>$<?php echo number_format($totCredAll,0) ?></td>
				<td>Cargos</td>
				<td>$<?php echo number_format($totCred,0) ?></td>
			</tr>
			<tr class="text-center">
				<td>Comisiones</td>
				<td></td>
				<td>Total IVA:</td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Impuestos</td>
				<td></td>
				<td>Total Retención:</td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Chequeras</td>
				<td></td>
				<td>Total Intereses:</td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Saldo siguiente</td>
				<td>$<?php echo number_format($rubroPUC->saldo_inicial + $totDeb  - $totCredAll,0) ?></td>
				<td> Saldo final</td>
				<td>$<?php echo number_format($totDeb  - $totCred,0) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
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
			@foreach($cuentas as $data)
				@if($data->aprobado == "ON")
				<tr class="text-center">
					<td>{{ \Carbon\Carbon::parse($data->fecha)->format('d-m-Y') }}</td>
					<td>{{$data->referencia}}</td>
					<td>$<?php echo number_format($data->debito,0) ?></td>
					<td>$<?php echo number_format($data->credito,0) ?></td>
					<td>$<?php echo number_format($data->valor,0) ?></td>
					<td>APROBADO</td>
				</tr>
				@endif
			@endforeach
			</tbody>
		</table>
	</div>
	<div class="table-responsive br-black-1">
		<table class="table table-bordered">
			<thead>
			<tr style="background-color: rgba(19,165,255,0.14)">
				<th></th>
				<th class="text-center">DEBITO</th>
				<th class="text-center">CREDITO</th>
				<th class="text-center">VALOR BANCO</th>
			</tr>
			</thead>
			<tbody>
			<tr class="text-center">
				<td>SUBTOTAL</td>
				<td>$<?php echo number_format($totDeb,0) ?></td>
				<td>$<?php echo number_format($totCredAll,0) ?></td>
				<td>$<?php echo number_format($conciliacion->subTotBancoFinal,0) ?></td>
			</tr>
			<tr class="text-center">
				<td>Egresos</td>
				<td>$<?php echo number_format($totCredAll,0) ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Cobros pendientes</td>
				<td></td>
				<td></td>
				<td>$<?php echo number_format($totCredAll - $totCred,0) ?></td>
			</tr>
			<tr class="text-center">
				<td>Valor sin conciliar</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Abonos en curso</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Cargos en curso</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr class="text-center">
				<td>Saldo inicial</td>
				<td>$<?php echo number_format($totalLastMonth,0) ?></td>
				<td></td>
				<td>$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></td>
			</tr>
			<tr class="text-center">
				<td>SUMAS IGUALES</td>
				<td>$<?php echo number_format($totDeb - $totCredAll + $totalLastMonth,0) ?></td>
				<td></td>
				<td>$<?php echo number_format($totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial ,0) ?></td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="table-responsive br-black-1">
		<table class="table table-bordered">
			<thead>
			<tr>
				<th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">Relación de pagos pendientes</th>
			</tr>
			<tr>
				<th class="text-center">FECHA</th>
				<th class="text-center">REFERENCIA</th>
				<th class="text-center">DEBITO</th>
				<th class="text-center">CREDITO</th>
			</tr>
			</thead>
			<tbody id="bodyTabla">
			@foreach($cuentas as $data)
				@if($data->aprobado == "OFF")
					<tr class="text-center">
						<td>{{ $data->fecha }}</td>
						<td>{{ $data->referencia }}</td>
						<td>$<?php echo number_format($data->debito,0) ?></td>
						<td>$<?php echo number_format($data->credito,0) ?></td>
					</tr>
				@endif
			@endforeach
			</tbody>
		</table>
	</div>
	<br><br><br>
	<div style="margin-top: 10px; font-size: 17px;">
		<center>
			___________________________<br>
			JUSTINO BRITTON HENRY 	<br>
			TESORERO
		</center>
	</div>
</div>

</body>
</html>
