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
			<center><h3>PRESUPUESTO DE EGRESOS {{ $dia }}-{{ $mesActual }}-{{ $añoActual }}</h3></center>
		</div>
		<div class="br-black-1">
			<table class="table table-bordered table-striped">
				<thead>
				<tr>
					<th class="text-center">Codigo BPIN</th>
					<th class="text-center">Codigo Actividad</th>
					<th class="text-center">Nombre Actividad</th>
					<th class="text-center">Rubro</th>
					<th class="text-center">Nombre</th>
					<th class="text-center">P. Inicial</th>
					<th class="text-center">Adición</th>
					<th class="text-center">Reducción</th>
					<th class="text-center">Credito</th>
					<th class="text-center">CCredito</th>
					<th class="text-center">P.Definitivo</th>
					<th class="text-center">CDP's</th>
					<th class="text-center">Registros</th>
					<th class="text-center">Saldo Disponible</th>
					<th class="text-center">Saldo de CDP</th>
					<th class="text-center">Ordenes de Pago</th>
					<th class="text-center">Pagos</th>
					<th class="text-center">Cuentas Por Pagar</th>
					<th class="text-center">Reservas</th>
					<th class="text-center">Cod Dependencia</th>
					<th class="text-center">Dependencia</th>
					<th class="text-center">Fuente</th>
				</tr>
				</thead>
				<tbody>
				@foreach($presupuesto as $codigo)
					<tr>
						<td>{{ $codigo['codBpin']}}</td>
						<td>{{ $codigo['codActiv']}}</td>
						<td>{{ $codigo['nameActiv']}}</td>
						<td>{{ $codigo['cod'] }}</td>
						<td>{{ $codigo['name']}}</td>
						<td>{{ $codigo['presupuesto_inicial']}}</td>
						<td>{{ $codigo['adicion']}}</td>
						<td>{{ $codigo['reduccion']}}</td>
						<td>{{ $codigo['credito']}}</td>
						<td>{{ $codigo['ccredito']}}</td>
						<td>{{ $codigo['presupuesto_def']}}</td>
						<td>{{ $codigo['cdps']}}</td>
						<td>{{ $codigo['registros']}}</td>
						<td>{{ $codigo['saldo_disp']}}</td>
						<td>{{ $codigo['saldo_cdp']}}</td>
						<td>{{ $codigo['ordenes_pago']}}</td>
						<td>{{ $codigo['pagos']}}</td>
						<td>{{ $codigo['cuentas_pagar']}}</td>
						<td>{{ $codigo['reservas']}}</td>
						<td>{{ $codigo['codDep']}}</td>
						<td>{{ $codigo['dep']}}</td>
						<td>{{ $codigo['fuente']}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
</div>

</body>
</html>

