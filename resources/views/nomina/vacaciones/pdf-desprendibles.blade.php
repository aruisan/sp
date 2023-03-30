
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

        .ml-4{
            margin-left:4em;
        }

		.br-black-1{
			border: 1px solid black;
            padding: 5px;
            border-radius:5px;
		}

        hr {
            margin:2em;
        }
	</style>
</head>
<body>
	<div class="container-fluid">
            <div class="row">
				@foreach($nomina->empleados_nominas->chunk(3) as  $chunk):
                    @foreach($chunk as  $movimiento)   
                        <div class="col-xs-3 br-black-1 ml-4">
                            <table class="">
                                <tr>
                                 m     +
                                 
                                    <td>
                                        <b>FECHA INGRESO</b>
                                    </td>
                                    <td>
                                        1-{{$nomina->mes}}-{{date('Y')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>SUELDO BASICO</b>
                                    </td>
                                    <td>
                                        ${{number_format($movimiento->sueldo, 0, ',', '.')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>DIAS VACACIONES</b>
                                    </td>
                                    <td>
                                        {{$movimiento->dias_vacaciones}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Sueldo Básico
                                    </td>
                                    <td>
                                        {{number_format($movimiento->v_dias_laborados, 0, ',', '.')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        VACACIONES
                                    </td>
                                    <td>
                                        {{number_format($movimiento->v_vacaciones, 0, ',', '.')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        PRIMA VACACIONES
                                    </td>
                                    <td>
                                    {{number_format($movimiento->v_prima_vacaciones, 0, ',', '.')}}
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td>
                                        <b>TOTAL VACACIONES</b>
                                    </td>
                                    <td>
                                        {{number_format($movimiento->total_vacaciones, 0, ',', '.')}}
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <table class="">
                                <tr>
                                    <td>CUENTA DE AHORROS</td>
                                    <td>{{$movimiento->empleado->numero_cuenta_bancaria}}</td>
                                </tr>
                                <tr>
                                    <td>ENTIDAD BANCARIA</td>
                                    <td>{{$movimiento->empleado->banco_cuenta_bancaria}}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                    <div style="page-break-after:always;"></div>
                @endforeach
            </div>
        </div>
    </body>
</html>
