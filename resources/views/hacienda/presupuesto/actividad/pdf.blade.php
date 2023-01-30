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
		<center><h3><b>EL SUSCRITO SECRETARIO DE PLANEACION DEL MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA, ISLAS</b></h3></center>
		<br><br><br><br>
	</div>
	<div>
		<center>
			<h4>CERTIFICA</h4>
			<br><br><br>
			Que el proyecto <b>"{{$proyecto}}"</b> SE ENCUENTRA REGISTRADO EN EL SISTEMA UNIFICADO DE INVERSIONS Y Finanzas
			Públicas - SUIFP TERRITORIO. código BPIN No.{{$proyecto->cod_proyecto}}
			<br><br><br>
			Se certifica en Providencia Isla, a {{ $hoy }}
			<?=$hoy[$hoy->format('w')]." ".$hoy->format('d')." de ".$meses[$hoy->format('n')-1]. " del ".$hoy->format('Y').'  Hora:'.$hoy->format('h:i:s')?>
		</center>
	</div>
	<div style="margin-top: 10px; font-size: 17px;">
		<center>
			Atentamente,
			<br><br><br><br>
			<b>Gregg Ambrosio Huffington</b><br>
			Secretario de Planeación
		</center>
	</div>
</div>
</body>
</html>
		
	
