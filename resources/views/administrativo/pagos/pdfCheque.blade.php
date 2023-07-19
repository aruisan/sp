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
			border: 1px solid white;
		}
	</style>
</head>
<body>
<table style="width: 100%">
	<tr>
		<td class="text-right" style="width: 76%; font-size: 22px">&nbsp;{{ \Carbon\Carbon::parse($pago->ff_fin)->format('Y')}} {{ \Carbon\Carbon::parse($pago->ff_fin)->format('m')}} {{ \Carbon\Carbon::parse($pago->ff_fin)->format('d')}}</td>
		<td class="text-center" style="font-size: 22px"><?php echo number_format($pago->valor,0);?></td>
	</tr>
</table>
<br>
<br>
<br>
<br>
<br>
<table class="text-left" style="width: 100%">
	<tr><td style="font-size: 15px">{{ $pago->persona->nombre }}</td></tr>
</table>
<br>
<table class="text-left" style="width: 100%">
	<tr>
		<td style="font-size: 15px; width: 75%">{{\NumerosEnLetras::convertir($pago->valor)}} Pesos MC</td>
		<td>&nbsp;</td>
	</tr>
</table>
</body>
</html>

