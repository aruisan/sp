@extends('layouts.dashboard')
@section('titulo')
    Creación Orden de Pago
@stop
@section('sidebar')
    <li>
        <a href="{{ url('/administrativo/ordenPagos/'.$id) }}" class="btn btn-success">
            <span class="hide-menu">Ordenes de Pago</span></a>
    </li>
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="row justify-content-center">
            <br>
            <center><h2 class="tituloOrden">Nueva Orden de Pago</h2></center>
            <div class="form-validation">
                <form class="form-valide" action="{{url('/administrativo/ordenPagos')}}" method="POST" enctype="multipart/form-data">
                    <hr>
                    {{ csrf_field() }}
                    <div class="col-md-12 text-center">
                        <div class="table-responsive">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Seleccione el registro correspondiente:</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                            @if(count($Registros) >= 1)
                                <br>
                                <table class="display" width="100%" id="tabla_Registros">
                                    <thead>
                                    <tr>
                                        <th class="text-center hidden"><i class="fa fa-hashtag"></i></th>
                                        <th class="text-center"><i class="fa fa-hashtag"></i></th>
                                        <th class="text-center">Objeto</th>
                                        <th class="text-center">Tercero</th>
                                        <th class="text-center">NIT/CED</th>
                                        <th class="text-center">Valor Total</th>
                                        <th class="text-center">Saldo Disponible</th>
                                        <th class="text-center hidden">Valor</th>
                                        <th class="text-center hidden">Valor</th>
                                        <th class="text-center hidden">iva</th>
                                        <th class="text-center hidden">ValorTot</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($Registros as $key => $data)
                                        @php($data['info']->objeto = preg_replace("/[\r\n|\n|\r]+/", " ", $data['info']->objeto))
                                        <tr onclick="ver('col{{$data['info']->id}}','Obj{{$data['info']->objeto}}','Name{{$data['info']->persona->nombre}}','Cc{{$data['info']->persona->num_dc}}','Sal{{$data['info']->saldo}}','Val{{$data['info']->valor}}','Iva{{$data['info']->iva}}','ValTo{{ $data['info']->val_total}}');" style="cursor:pointer">
                                            <td id="col{{$data['info']->id}}" class="text-center hidden">{{ $data['info']->id }}</td>
                                            <td class="text-center">{{ $data['info']->code }}</td>
                                            <td id="Obj{{$data['info']->objeto}}" class="text-center">{{ $data['info']->objeto }}</td>
                                            <td id="Name{{$data['info']->persona->nombre}}" class="text-center">{{ $data['info']->persona->nombre }}</td>
                                            <td id="Cc{{$data['info']->persona->num_dc}}" class="text-center">{{ $data['info']->persona->num_dc }}</td>
                                            <td class="text-center">$<?php echo number_format($data['info']->val_total,0) ?></td>
                                            <td class="text-center">$<?php echo number_format($data['info']->saldo,0) ?></td>
                                            <td id="Sal{{$data['info']->saldo}}" class="text-center hidden">{{ $data['info']->saldo }}</td>
                                            <td id="Val{{$data['info']->valor}}" class="text-center hidden">{{ $data['info']->valor }}</td>
                                            <td id="Iva{{$data['info']->iva}}" class="text-center hidden">{{ $data['info']->iva }}</td>
                                            <td id="ValTo{{$data['info']->val_total}}" class="text-center hidden">{{ $data['info']->val_total}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            @else
                                <br>
                                <div class="alert alert-danger">
                                    <center>
                                        No hay Registros.
                                        <a href="{{ url('administrativo/registros/create/'.$id) }}" class="btn btn-success btn-block">Crear Registro</a>
                                    </center>
                                </div>
                            @endif
                                    @if(count($radCuentas) > 0)
                                    <br>
                                    <div class="box">
                                        <div class="box-header"><h3 class="box-title">Seleccione la radicación de cuenta:</h3></div>
                                        <div class="box-body"><br>
                                            <table class="display" width="100%" id="tabla_Radicacion">
                                                <thead>
                                                <tr>
                                                    <th class="text-center"><i class="fa fa-hashtag"></i></th>
                                                    <th class="text-center">Objeto</th>
                                                    <th class="text-center">Tercero</th>
                                                    <th class="text-center">NIT/CED</th>
                                                    <th class="text-center">Valor Total</th>
                                                    <th class="text-center">Saldo Disponible</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($radCuentas as $key => $radicacion)
                                                    @php($radicacion->registro->objeto = preg_replace("/[\r\n|\n|\r]+/", " ", $radicacion->registro->objeto))
                                                    <tr onclick="findRadicacion({{ $radicacion }});" style="cursor:pointer">
                                                        <td class="text-center">{{ $radicacion->code }}</td>
                                                        <td class="text-center">{{ $radicacion->registro->objeto }}</td>
                                                        <td class="text-center">{{ $radicacion->persona->nombre }}</td>
                                                        <td class="text-center">{{ $radicacion->persona->num_dc }}</td>
                                                        <td class="text-center">{{ $radicacion->valor_fin }}</td>
                                                        <td class="text-center">{{ $radicacion->saldo }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 " style="display: none; background-color: white" id="form" name="form"><br>
                                <div class="row">
                                    <div class="col-md-5 formularioRegistro"><br>
                                        <h2 class="text-center  formularioRegistoTitulo">Registro</h2><hr>
                                        <div class="row">
                                            <div class="col-md-3 "><h4 class="formularioRegistoLabel"><b>Objeto:</b></h4></div>
                                            <div class="col-md-9">
                                                <textarea type="text" style="text-align: center" class="form-control formularioRegistoLabel" name="Objeto" id="Objeto" columns="20" rows="7" disabled></textarea>
                                            </div>
                                        </div>
                                        <div class="row"><br>
                                            <div class="col-md-3 "><h4 class="formularioRegistoLabel"><b>Tercero:</b></h4></div>
                                            <div class="col-md-9">
                                                <input type="text" style="text-align: center" class="form-control formularioRegistoLabel" name="Name" id="Name" disabled>
                                                <input type="hidden" name="vigencia" id="vigencia" value="{{ $id }}">
                                            </div>
                                        </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="formularioRegistoLabel"><b>NIT/CED:</b></h4>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" style="text-align: center" 
                                    class="form-control formularioRegistoLabel" name="CC" id="CC" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 ">
                                    <h4 class="formularioRegistoLabel"><b>Valor Registro:</b></h4>
                                </div>

                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioRegistoLabel" name="ValRegistro" id="ValRegistro" disabled>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-7 ">
                                        <h4 class="formularioRegistoLabel"><b>IVA:</b></h4>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" style="text-align: center" class="form-control formularioRegistroLabel" name="iva" id="iva" disabled>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 ">
                                    <h4 class="formularioRegistoLabel"><b>Valor Total del Registro:</b></h4>
                                </div>
                               
                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioRegistoLabel" name="Val" id="Val" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-7 ">
                                    <h4 class="formularioRegistoLabel"><b>Saldo del Registro:</b></h4>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioRegistoLabel" name="ValS" id="ValS" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"><div class="row"><br></div></div>
                        <div class="col-md-5 formularioOrden">
                            <br>
                            <h2 class="text-center formularioOrdenTitulo">Orden de Pago</h2>
                            <hr>
                            <input type="hidden"  class="form-control" name="IdR" id="IdR" value="0">
                            <input type="hidden"  class="form-control" name="IdRadCuenta" id="IdRadCuenta">
                            <br> <br>
                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="formularioOrdenLabel"><b>Fecha:</b></h4>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" name="fecha" style="text-align: center" class="form-control formularioOrdenLabel" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="formularioOrdenLabel"><b>Concepto:</b></h4>
                                </div>
                                <div class="col-md-9">
                                    <textarea type="text" class="form-control formularioOrdenLabel" id="concepto" name="concepto" rows="5" required></textarea>
                                </div>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-7">
                                    <h4 class="formularioOrdenLabel"><b>Valor Orden de Pago sin IVA:</b></h4>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioOrdenLabel" name="ValOP" id="ValOP" required onchange="sumar()">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-7 ">
                                    <h4 class="formularioOrdenLabel"><b>Valor IVA:</b></h4>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioOrdenLabel" name="ValIOP" id="ValIOP" required onchange="sumar()">
                                </div>
                                </div>

                                <div class="row">
                                <div class="col-md-7">
                                    <h4 class="formularioOrdenLabel"><b>Valor Total Orden de Pago:</b></h4>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" style="text-align: center" class="form-control formularioOrdenLabel" name="ValTOP" id="ValTOP" required>
                                </div>
                            </div>
                        </div>
                      </div>
                        <input type="hidden" class="form-control" name="estado" value="0">
                        <div class="form-group row">
                            <div class="col-lg-12 ml-auto text-right">
                            <br>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @stop
@section('js')
    <script type="text/javascript">

        function findRadicacion(radicacion){
            $("#IdR").val(0);
            $("#form").show();
            $("#ValS").val();
            $("#Objeto").val(radicacion['registro']['objeto']);
            $("#Name").val(radicacion['persona']['nombre']);
            $("#CC").val(radicacion['persona']['num_dc']);
            $("#Val").val(radicacion['registro']['saldo']);
            $("#ValOP").val(radicacion['valor_fin']);
            $("#ValRegistro").val(radicacion['registro']['val_total']);
            $("#iva").val(radicacion['registro']['iva']);
            $("#ValIOP").val(radicacion['registro']['iva']);
            $("#IdRadCuenta").val(radicacion['id']);
            $("#ValTOP").val(radicacion['registro']['saldo']);
            $("#concepto").val(radicacion['registro']['objeto']);
            document.getElementById("concepto").focus();
        }

        $('#tabla_Registros').DataTable( {
             language: {
			  "lengthMenu": "Mostrar _MENU_ registros",
			  "zeroRecords": "No se encontraron resultados",
			  "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
			  "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
			  "infoFiltered": "(filtrado de un total de _MAX_ registros)",
			  "sSearch": "Buscar:",
			  "oPaginate": {
				  "sFirst": "Primero",
				  "sLast":"Último",
				  "sNext":"Siguiente",
				  "sPrevious": "Anterior"
			   },
			   "sProcessing":"Procesando...",
		  },
	  //para usar los botones   
      "pageLength": 5,
      responsive: "true",
	  dom: 'Bfrtilp',       
	  buttons:[ 
			  {
			  extend:    'copyHtml5',
			  text:      '<i class="fa fa-clone"></i> ',
			  titleAttr: 'Copiar',
			  className: 'btn btn-primary'
		  },
		  {
			  extend:    'excelHtml5',
			  text:      '<i class="fa fa-file-excel-o"></i> ',
			  titleAttr: 'Exportar a Excel',
			  className: 'btn btn-primary'
		  },
		  {
			  extend:    'pdfHtml5',
			  text:      '<i class="fa fa-file-pdf-o"></i> ',
			  titleAttr: 'Exportar a PDF',
			  message : 'SIEX',
			  header :true,
              	exportOptions: {
				  columns: [ 0,1,2,3,4]
					},
			  pageSize: 'LEGAL',
			  className: 'btn btn-primary',
			   },
		  {
			  extend:    'print',
			  text:      '<i class="fa fa-print"></i> ',
			  titleAttr: 'Imprimir',
			  className: 'btn btn-primary'
		  },
	  ]	             

		 });


        $('#tabla_Radicacion').DataTable( {
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing":"Procesando...",
            },
            //para usar los botones
            "pageLength": 5,
            responsive: "true",
            dom: 'Bfrtilp',
            buttons:[
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-clone"></i> ',
                    titleAttr: 'Copiar',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> ',
                    titleAttr: 'Exportar a PDF',
                    message : 'SIEX',
                    header :true,
                    exportOptions: {
                        columns: [ 0,1,2,3,4]
                    },
                    pageSize: 'LEGAL',
                    className: 'btn btn-primary',
                },
                {
                    extend:    'print',
                    text:      '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary'
                },
            ]

        });

        $(document).ready(function() {
            var table = $('#tabla_Registros').DataTable();
            var tablaRad = $('#tabla_Radicacion').DataTable();

            $('#tabla_Registros tbody').on( 'click', 'tr', function () {
                tablaRad.$('tr.selected').removeClass('selected');
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            } );

            $('#tabla_Radicacion tbody').on( 'click', 'tr', function () {
                table.$('tr.selected').removeClass('selected');
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    tablaRad.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            } );

            $('#button').click( function () {
                table.row('.selected').remove().draw( false );
            } );
        } );

        function ver(col, Obj, Name, CC, Val, ValTo, Iva, Sal){
            content = document.getElementById(col);
            var Obj = document.getElementById(Obj);
            var Name = document.getElementById(Name);
            var CC = document.getElementById(CC);
            var Sal = document.getElementById(Sal);
            var Val = document.getElementById(Val);
            var ValTo = document.getElementById(ValTo);
            var Iva = document.getElementById(Iva);
            var data = content.innerHTML;
             
            if (data) {
                $("#form").show();
                $("#Objeto").val(Obj.innerHTML);
                $("#Name").val(Name.innerHTML);
                $("#CC").val(CC.innerHTML);
                $("#Val").val(Sal.innerHTML);
                $("#ValOP").val(ValTo.innerHTML);
                $("#ValRegistro").val(ValTo.innerHTML);
                $("#iva").val(Iva.innerHTML);
                $("#ValIOP").val(Iva.innerHTML);
                $("#IdR").val(content.innerHTML);
                $("#ValTOP").val(Sal.innerHTML);
                $("#ValS").val(Val.innerHTML);
                $("#concepto").val(Obj.innerHTML);

                document.getElementById("concepto").focus(); 
                
               } else {
                $("#form").hide();
            }
            
        }

        function sumar() {

            var num1 = document.getElementById("ValOP").value;
            var num2 = document.getElementById("ValIOP").value;

            var resultado = parseInt(num1) + parseInt(num2);
            document.getElementById("ValTOP").value = resultado;
        }


    </script>
@stop
