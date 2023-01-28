
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
			<div>
                <div class="row">
                    <table class="table text-center table-bordered">
                        <tr>
                            <td colspan="5">FINALIZACIÓN NOMINA</td>
                            <td colspan="8">RUTA PARA PAGO</td>
                        </tr>
                        <tr>
                            <td>CAUSACIONES DE NOMINA</td>
                            <td rowspan="2" colspan="2">EMPLEADOS</td>
                            <td rowspan="2">PENSIONADOS</td>
                            <td rowspan="2"></td>
                            <td colspan="2">PRESUPUESTO</td>
                            <td colspan="6">CONTABILIDAD</td>
                        </tr>
                        <tr>
                            <td>APORTE EMPLEADOR</td>
                            <td>DEPENDENCIA</td>
                            <td>RUBRO</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>DEBITO</td>
                            <td>CTA</td>
                            <td>NOMBRE</td>
                            <td>CREDITO</td>
                        </tr>
                        <tr>
                            <td>Sueldo Básico</td>
                            <td colspan="2">${{number_format($nomina_empleados->basico, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->basico, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->basico+$nomina_pensionados->basico, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.01</td>
                            <td>5101010101</td>
                            <td>Sueldos</td>
                            <td>${{number_format($nomina_empleados->basico+$nomina_pensionados->basico, 0, ',', '.')}}</td>
                            <td>2511010101</td>
                            <td>Nómina por pagar</td>
                            <td>${{number_format(($nomina_empleados->basico+$nomina_pensionados->basico)-($nomina_empleados->total_deduccion+$nomina_pensionados->total_deduccion), 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Retroactivo</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->basico, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.01</td>
                            <td>5101010101</td>
                            <td>Sueldos</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511010101</td>
                            <td>Nómina por pagar</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>H Extras, Recargos y Festivos</td>
                            <td colspan="2">${{number_format($nomina_empleados->extras_recargos, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->extras_recargos, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.02</td>
                            <td>5101010101</td>
                            <td>Horas extras y Festivos</td>
                            <td>${{number_format($nomina_empleados->extras_recargos, 0, ',', '.')}}</td>
                            <td>2511010101</td>
                            <td>Nómina por pagar</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Bonificación y Dirección</td>
                            <td colspan="2">${{number_format($nomina_empleados->bonificacion_direccion, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bonificacion_direccion, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.03.003</td>
                            <td>5101010101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format($nomina_empleados->bonificacion_direccion, 0, ',', '.')}}</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Bonificación por Servicios prestados</td>
                            <td colspan="2">${{number_format($nomina_empleados->bonificacion_servicios, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bonificacion_servicios, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.07</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format($nomina_empleados->bonificacion_servicios, 0, ',', '.')}}</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Bonificación Recreación</td>
                            <td colspan="2">${{number_format($nomina_empleados->bonificacion_recreacion, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bonificacion_recreacion, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.03.001.03</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format($nomina_empleados->bonificacion_recreacion, 0, ',', '.')}}</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Prima Antiguedad</td>
                            <td colspan="2">${{number_format($nomina_empleados->prima_antiguedad, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->prima_antiguedad, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.002.12.01</td>
                            <td>5107900101</td>
                            <td>Otras Primas</td>
                            <td>${{number_format($nomina_empleados->prima_antiguedad, 0, ',', '.')}}</td>
                            <td>2511100101</td>
                            <td>Otras Primas</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Vacaciones</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.03.001.01</td>
                            <td>5107010101</td>
                            <td>Vacaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511040101</td>
                            <td>vacaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Prima de Vacaciones</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.08.02</td>
                            <td>5107040101</td>
                            <td>Prima de Vacaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511050101</td>
                            <td>Prima de Vacaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Indemnización por vacaciones</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.03.001.02</td>
                            <td>5102030101</td>
                            <td>Indemnizaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2513010101</td>
                            <td>Indemnizaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Bonificación especial por recreación</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.03.001.03</td>
                            <td>5107070101</td>
                            <td>Bonificación especial de recreación</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511090101</td>
                            <td>Bonificaciones</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Prima de Servicios</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.06</td>
                            <td>5107060101</td>
                            <td>Prima de servicios</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511060101</td>
                            <td>Prima de servicios</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Prima de Navidad</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.08.01</td>
                            <td>5107050101</td>
                            <td>Prima de navidad</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511070101</td>
                            <td>Prima de navidad</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Prima técnica salarial</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.09</td>
                            <td>5101100101</td>
                            <td>Prima técnica</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511100101</td>
                            <td>otras primas</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Gastos de representación</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.03</td>
                            <td>5101050101</td>
                            <td>Gastos de representación</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2490330101</td>
                            <td>Gastos de representación</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Subsidio de alimentación</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.04</td>
                            <td>5101600101</td>
                            <td>Subsidio de alimentación</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2512900101</td>
                            <td>Otros beneficios a los empleados a largo plazo</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Auxilio de transporte</td>
                            <td colspan="2">${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.01.001.05</td>
                            <td>5101230101</td>
                            <td>Auxilio de transporte</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2512900101</td>
                            <td>Otros beneficios a los empleados a largo plazo</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td colspan="13">Pago a Terceros</td>
                        </tr>
                        <tr>
                            <td>Aportes al ICBF</td>
                            <td>3%</td>
                            <td>${{number_format($nomina_empleados->icbf, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->icbf, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.006</td>
                            <td>5104010101</td>
                            <td>Aportes al ICBF</td>
                            <td>${{number_format($nomina_empleados->icbf, 0, ',', '.')}}</td>
                            <td>2490500101</td>
                            <td>Aportes al ICBF Y Sena</td>
                            <td>${{number_format($nomina_empleados->icbf, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes al SENA</td>
                            <td>0.5%</td>
                            <td>${{number_format($nomina_empleados->sena, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->sena, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.007</td>
                            <td>5104010101</td>
                            <td>Aportes al SENA</td>
                            <td>${{number_format($nomina_empleados->sena, 0, ',', '.')}}</td>
                            <td>2490500101</td>
                            <td>Aportes al ICBF Y Sena</td>
                            <td>${{number_format($nomina_empleados->sena, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes al ESAP</td>
                            <td>0.5%</td>
                            <td>${{number_format($nomina_empleados->esap, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->esap, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.008</td>
                            <td>5104030101</td>
                            <td>Aportes al ESAP</td>
                            <td>${{number_format($nomina_empleados->esap, 0, ',', '.')}}</td>
                            <td>2490340101</td>
                            <td>Aportes a escuelas industriales, institutos técnicos y ESAP</td>
                            <td>${{number_format($nomina_empleados->esap, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Ministerio Educación Nacional MEN</td>
                            <td>1%</td>
                            <td>${{number_format($nomina_empleados->men, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->men, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.009</td>
                            <td>5104040101</td>
                            <td>Aportes a escuelas industriales e institutos técnicos</td>
                            <td>${{number_format($nomina_empleados->men, 0, ',', '.')}}</td>
                            <td>2490340101</td>
                            <td>Aportes a escuelas industriales, institutos técnicos y ESAP</td>
                            <td>${{number_format($nomina_empleados->men, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes a la seguridad social en pensiones</td>
                            <td>12%</td>
                            <td>${{number_format($nomina_empleados->pension, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->pension, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.001</td>
                            <td>5103060101</td>
                            <td>Cotizaciones a entidades administradoras del régimen de prima media</td>
                            <td>${{number_format($nomina_empleados->pension, 0, ',', '.')}}</td>
                            <td>2511220101</td>
                            <td>Aportes a fondos pensionales - empleador</td>
                            <td>${{number_format($nomina_empleados->pension, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes a la seguridad social en salud</td>
                            <td>8.5%</td>
                            <td>${{number_format($nomina_empleados->salud+$nomina_pensionados->salud, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->salud, 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->salud+$nomina_pensionados->salud, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.002</td>
                            <td>5103030101</td>
                            <td>Cotizaciones a seguridad social en salud</td>
                            <td>${{number_format($nomina_empleados->salud+$nomina_pensionados->salud, 0, ',', '.')}}</td>
                            <td>2511230101</td>
                            <td>Aportes a seguridad social en salud - empleador</td>
                            <td>${{number_format($nomina_empleados->salud+$nomina_pensionados->salud, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes de cesantías </td>
                            <td></td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.003</td>
                            <td>5107020101</td>
                            <td>Cesantías</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>5107020101</td>
                            <td>Cesantías</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes de cesantías </td>
                            <td></td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.003</td>
                            <td>5107030101</td>
                            <td>Intereses a las cesantías</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2511030101</td>
                            <td>Intereses a las cesantías</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes a cajas de compensación familiar </td>
                            <td>4%</td>
                            <td>${{number_format($nomina_empleados->caja_compensacion, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.004</td>
                            <td>5103020101</td>
                            <td>Aportes a cajas de compensación familiar</td>
                            <td>${{number_format($nomina_empleados->caja_compensacion, 0, ',', '.')}}</td>
                            <td>2511240101</td>
                            <td>Aportes a cajas de compensación familiar</td>
                            <td>${{number_format($nomina_empleados->caja_compensacion, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Aportes generales al sistema de riesgos laborales</td>
                            <td>2.436%</td>
                            <td>${{number_format($nomina_empleados->riesgos, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>${{number_format(0, 0, ',', '.')}}</td>
                            <td>2,1</td>
                            <td>2.1.1.01.02.005</td>
                            <td>5103050101</td>
                            <td>Cotizaciones a riesgos laborales</td>
                            <td>${{number_format($nomina_empleados->riesgos, 0, ',', '.')}}</td>
                            <td>2511110101</td>
                            <td>Aportes a riesgos laborales</td>
                            <td>${{number_format($nomina_empleados->riesgos, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>BANCO POPULAR</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1940], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1940], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1940] + $nomina_pensionados->bancos[1940], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1940] + $nomina_pensionados->bancos[1940], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>BANCO DE BOGOTA</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1854], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1854], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1854] + $nomina_pensionados->bancos[1854], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1854] + $nomina_pensionados->bancos[1854], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>BANCO DAVIVIENDA</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1856], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1856], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1856] + $nomina_pensionados->bancos[1940], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1856] + $nomina_pensionados->bancos[1940], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>BANCO AGRARIO</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1855], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1855], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1855] + $nomina_pensionados->bancos[1855], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1855] + $nomina_pensionados->bancos[1855], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>COOSERPARK</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1858], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1858], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1858] + $nomina_pensionados->bancos[1858], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1858] + $nomina_pensionados->bancos[1858], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>COOCASA LTDA</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1859], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1859], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1859] + $nomina_pensionados->bancos[1859], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1859] + $nomina_pensionados->bancos[1859], 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>JUZGADO PROMISCUO MUNICIPAL</td>
                            <td colspan="2">${{number_format($nomina_empleados->bancos[1866], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_pensionados->bancos[1866], 0, ',', '.')}}</td>
                            <td>${{number_format($nomina_empleados->bancos[1866] + $nomina_pensionados->bancos[1866], 0, ',', '.')}}</td>
                            <td colspan="5"></td>
                            <td>2424070101</td>
                            <td>Libranzas</td>
                            <td>${{number_format($nomina_empleados->bancos[1866] + $nomina_pensionados->bancos[1866], 0, ',', '.')}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
