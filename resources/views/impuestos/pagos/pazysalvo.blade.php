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
		<center><h3><b>EL SUSCRITO TESORERO MUNICIPAL DE PROVIDENCIA Y SANTA CATALINA, ISLAS</b></h3></center>
		<br><br>
		<center><h3><b>PAZ Y SALVO PREDIAL No. 001</b></h3></center>
		<br><br>
	</div>
	<div>
		<center>
			<h3>CERTIFICA</h3>
			<br><br><br><br>
			<h4>Que el predio que se especifica a continuación se encuentra a PAZ Y SALVO por concepto de pago del impuesto
				predial unificado hasta el 31 de diciembre de {{ \Carbon\Carbon::today()->format('Y') }}, y se expide a solicitud del interesado.</h4>
			<br><br><br>
			Ficha catastral No. {{ $pazysalvo->id }}
			denominado:
			Dirección: EL VALLE
			Propietario: ROGELIO ARCHBOLD NEWBALL
			Cédula o NIT: 991878
			Área: 1 Hc-4375 M2
			Área construida: 0 M2

			Formulario declaración No: 00013
			Fecha declaración: 2/2/2023
			Valor Declaración: $1,741,488

			Costo del paz y salvo: $0
			Recibo de pago:
			Fecha Recibo de pago:

			Validez del Paz y Salvo: 31/12/2023
			Fecha de expedición: 2/2/2023

			<h4>
				Se certifica en Providencia Isla, a <?=$dias[$fecha->format('w')]." ".$fecha->format('d')." de ".$meses[$fecha->format('n')-1]. " del ".$fecha->format('Y').'  Hora:'.$fecha->format('h:i:s')?>
			</h4>
		</center>
	</div>
	<div style="margin-top: 10px; font-size: 17px;">
		<center>
			<br><br><br><br>
			<b>JUSTINO BRITTON HENRY</b><br>
			TESORERO MUNICIPAL
		</center>
	</div>
</div>
</body>
</html>
		
	
