<!DOCTYPE html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<title>
		@yield('title')
	</title>
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

		 .s7{width: 17%; display: inline-block;}
		 .s17{width: 17%; display: inline-block;}
		 .s57{width: 60%; display: inline-block;}

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
        
        .caja {
            display: inline-block;
            width: 40%;
            height: auto;
        }

        .content {
            width: 100%;
        }

		hr{margin:0}

		.col-md-6 {
			width:50%;
		}

		.text-right{
			text-align: right !important;
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
				<center>
                    <h4>
                        <b>
                            Republica de Colombia
                        MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS <br>
                        SECRETARIA DE HACIENDA<br>
                        </b>
                        NIT: 800.103.021-1
                    </h4>
                </center>
			</div>
			<div class="col-md-6 s17">
				<img src="https://www.siex-concejoprovidenciaislas.com.co/img/masporlasislas.png"  height="60">
			</div>
		</div>

		@yield('contenido')
		<div style="margin-top: 100px; font-size: 17px;">
			@yield('firma')
		</div>
	</div>

</body>
</html>
