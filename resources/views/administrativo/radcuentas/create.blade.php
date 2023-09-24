@extends('layouts.dashboard')
@section('titulo') Radicación de Cuentas @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="breadcrumb text-center"><strong><h4><b>NUEVA RADICACIÓN DE CUENTA</b></h4></strong></div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$id) }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVA RADICACIÓN</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="form-validation">
                    <form class="form-valide" action="{{url('/administrativo/radCuentas')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <div class="text-center" id="cargando" style="display: none">
                            <h4>Buscando informacion del tercero...</h4>
                        </div>
                        <div class="text-center" id="cargandoRP" style="display: none">
                            <h4>Buscando informacion del registro...</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-12 align-self-center" id="FindTercero">
                                <div class="form-group">
                                    <label class="col-lg-12 col-form-label text-center" for="persona_id">Seleccione el tercero: <span class="text-danger">*</span></label>
                                    <div class="col-lg-12 text-center">
                                        <select class="select-tercero" name="persona_id" onchange="changeTer(this.value)">
                                            <option value="0">Seleccione el tercero</option>
                                            @foreach($personas as $persona)
                                                <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center" id="tableRPs" style="display: none; background-color: white">
                            <h5><b>De click al registro a radicar.</b></h5>
                            <div class="col-md-12 align-self-center">
                                <br>
                                <table class="display" id="tabla_Registros">
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
                                    <tbody id="cuerpoRPs"></tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12 " style="display: none; background-color: white" id="formRP" name="formRP">
                            <table id="TABLA1" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">1. IDENTIFICACIÓN DEL CONTRATO</th></tr>
                                <tr>
                                    <td colspan="3">
                                        Interventor:
                                        <select class="select-interventor" name="interventor_id">
                                            <option>NO POSEE</option>
                                            @foreach($personas as $persona)
                                                <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Tipo De Contrato: <b><span id="tipoConSel"></span></b>
                                        <select name="tipo_contrato" class="form-control">
                                            <option value="0">CAMBIAR EL TIPO DE CONTRATO</option>
                                            <option value="3">3 - DE OBRA PUBLICA</option>
                                            <option value="4">4 - DE CONSULTORIA</option>
                                            <option value="5">5 - DE INTERVENTORIA</option>
                                            <option value="6">6 - DE SUMINISTRO</option>
                                            <option value="7">7 - TRANSPORTE PASAJEROS TERRESTRE, HOTEL Y RESTAURANTE</option>
                                            <option value="8">8 - TRANSPORTE DE CARGA</option>
                                            <option value="10">10 - DE PRESTACION DE SERVICIOS</option>
                                            <option value="11">11 - DE ENCARGO FIDUCIARIO Y FIDUCIA PUBLICA</option>
                                            <option value="12">12 - ALQUILER O ARRENDAMIENTO</option>
                                            <option value="13">13 - DE CONCESION</option>
                                            <option value="20">20 - DEUDA PUBLICA</option>
                                            <option value="21">21 - CONVENIO INTERADMINISTRATIVO</option>
                                            <option value="22">22 - OTROS NO ESPECIFICADOS ANTERIORMENTE</option>
                                        </select>
                                    </td>
                                    <td>Modalidad de Seleccion: <b><span id="modSel"></span></b>
                                        <select name="mod_seleccion" class="form-control">
                                            <option>CAMBIAR LA MODALIDAD</option>
                                            <option value="0">NO APLICA</option>
                                            <option value="1">1 - LICITACION PUBLICA</option>
                                            <option value="2">2 - CONCURSO DE MERITOS</option>
                                            <option value="3">3 - SELECCION ABREVIADA</option>
                                            <option value="4">4 - CONTRATACION DIRECTA</option>
                                            <option value="8">8 - CUANTIA MINIMA</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Contrato No. <b><span id="contNum"></span></b></td>
                                    <td>Fecha Contrato. <input type="date" name="fecha_cont" id="fecha_cont" class="form-control"></td>
                                </tr>
                                <tr><td colspan="3">Objeto Contrato: <b><span id="objetoContrato"></span></b></td></tr>
                                </tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">CDPs</th></tr>
                                <tbody id="cdps"></tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">Ordenes de Pago</th></tr>
                                <tbody id="ordenesPago"></tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">Pagos</th></tr>
                                <tbody id="pagos"></tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tbody>
                                <tr>
                                    <td colspan="2">Fecha de Inicio
                                        <input type="date" class="form-control" name="fecha_inicio" required>
                                    </td>
                                    <td>Plazo Ejecucion Dias
                                        <input type="number" min="0" class="form-control" name="plazo_ejecu_dias" value="0" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Prorroga en Dias
                                        <input type="number" class="form-control" name="prorroga" min="0" value="0" required>
                                    </td>
                                    <td colspan="2">Fecha de Terminación
                                        <input type="date" class="form-control" name="fecha_fin">
                                    </td>
                                </tr>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">2. IDENTIFICACIÓN DEL BENEFICIARIO</th></tr>
                                <tr>
                                    <td colspan="2">CONTRATISTA o CESIONARIO:
                                        <input type="text" class="form-control" name="contratista" id="contratista">
                                    </td>
                                    <td>CÉDULA O NIT:
                                        <input type="number" class="form-control" name="cedula" id="cedula">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">RÉGIMEN TRIBUTARIO DIAN
                                        <b><span id="regTrib"></span></b>
                                        <select name="regimen_tributario" class="form-control">
                                            <option>CAMBIAR EL REGIMEN TRIBUTARIO</option>
                                            <option value="ordinario">ORDINARIO</option>
                                            <option value="simple tributacion">SIMPLE TRIBUTACIÓN</option>
                                            <option value="Especial">ESPECIAL</option>
                                        </select>
                                    </td>
                                    <td>PORCENTAJE RETENCIÓN FUENTE
                                        <input type="number" min="0" class="form-control" name="retefuente" id="retefuente">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">DIRECCIÓN CONTRATISTA O PROVEEDOR:
                                        <input type="text" class="form-control" name="dir" id="dir">
                                    </td>
                                    <td>TELÉFONO FIJO
                                        <input type="number" class="form-control" name="telFijo" id="telFijo">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">CORREO ELECTRÓNICO:
                                        <input type="email" class="form-control" name="email" id="email">
                                    </td>
                                    <td>CELULAR:
                                        <input type="number" class="form-control" name="cel" id="cel">
                                    </td>
                                </tr>
                                <tr>
                                    <td>CUENTA BANCARIA:
                                        <input type="number" class="form-control" name="cuentaBanc" id="cuentaBanc">
                                    </td>
                                    <td>ENTIDAD BANCARIA:
                                        <b><span id="entidadBanc"></span></b>
                                        <select name="banco" class="form-control">
                                            <option value="0">CAMBIAR ENTIDAD BANCARIA</option>
                                            <option value="BANCO DE BOGOTA">BANCO DE BOGOTA</option>
                                            <option value="BANCO AGRARIO">BANCO AGRARIO</option>
                                            <option value="BANCO DAVIVIENDA">BANCO DAVIVIENDA</option>
                                            <option value="BANCO POPULAR">BANCO POPULAR</option>
                                            <option value="BANCO BANCOLOMBIA">BANCO BANCOLOMBIA</option>
                                            <option value="BANCO OCCIDENTE">BANCO OCCIDENTE</option>
                                            <option value="BANCO AVVILLAS">BANCO AVVILLAS</option>
                                            <option value="BANCO BBVA">BANCO BBVA</option>
                                            <option value="BANCO CAJA SOCIAL">BANCO CAJA SOCIAL</option>
                                            <option value="BANCO FALABELLA">BANCO FALABELLA</option>
                                            <option value="BANCO SUDAMERIS">BANCO SUDAMERIS</option>
                                            <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
                                            <option value="BANCO CITIBANK">BANCO CITIBANK</option>
                                            <option value="BANCO SANTANDER">BANCO SANTANDER</option>
                                        </select>
                                    </td>
                                    <td>TIPO CUENTA:
                                        <b><span id="tipoCuenta"></span></b>
                                        <select name="tipo_cuenta" class="form-control">
                                            <option >CAMBIAR EL TIPO DE CUENTA</option>
                                            <option value="Ahorros">AHORROS</option>
                                            <option value="Corriente">CORRIENTE</option>
                                        </select>
                                        <input type="hidden" name="registro_id" id="registro_id">
                                        <input type="hidden" name="vigencia_id" id="vigencia_id" value="{{ $id }}">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row" id="buttonSend" style="display: none; background-color: white">
                            <div class="col-lg-12 ml-auto text-center">
                                <button type="submit" class="btn btn-primary">Generar Radicación y Continuar al Siguiente Paso</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        const vigencia_id = @json($id);
        $('.select-tercero').select2();
        $('.select-interventor').select2();

        function changeTer(id){
            if(id == '0') toastr.warning('DEBE SELECCIONAR UN TERCERO DE LA LISTA.');
            else {
                $("#cargando").show();
                $("#FindTercero").hide()
                $("#buttonSend").hide()
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/findDataPer",
                    data: { "idPer": id, "vigencia_id": vigencia_id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    console.log(data);

                    if (data['history'].length > 0){

                    }

                    if (data.registros.length > 0){
                        $("#tabla_Registros").show();
                        $("#tableRPs").show();
                        var table = $('#tabla_Registros').DataTable();
                        table.destroy();

                        table = $('#tabla_Registros').DataTable( {
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
                                    extend:    'excelHtml5',
                                    text:      '<i class="fa fa-file-excel-o"></i> ',
                                    titleAttr: 'Exportar a Excel',
                                    className: 'btn btn-primary'
                                },
                            ]
                        } );

                        $("#cuerpoRPs").html("");
                        for(var i=0; i<data.registros.length; i++){
                            data.registros[i].objeto = data.registros[i].objeto.replace(/[\r\n|\n|\r]+/,' ');
                            var tr = `<tr class="text-center" onclick="getRP(`+data.registros[i].id+`)" style="cursor:pointer">
                              <td>`+data.registros[i].code+`</td>
                              <td>`+data.registros[i].objeto+`</td>
                              <td>`+data.registros[i].persona.nombre+`</td>
                              <td>`+data.registros[i].persona.num_dc+`</td>
                              <td>`+formatter.format(data.registros[i].val_total)+`</td>
                              <td>`+formatter.format(data.registros[i].saldo)+`</td>
                            </tr>`;
                            $("#cuerpoRPs").append(tr)
                        }
                    }

                    $("#cargando").hide();
                    $("#FindTercero").show();
                }).fail(function() {
                    $("#cargando").hide();
                    $("#FindTercero").show();
                    toastr.warning('OCURRIO UN ERROR AL BUSCAR LA INFORMACION DEL TERCERO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

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
			  extend:    'excelHtml5',
			  text:      '<i class="fa fa-file-excel-o"></i> ',
			  titleAttr: 'Exportar a Excel',
			  className: 'btn btn-primary'
		  },
	  ]
		 });

        $(document).ready(function() {
            var table = $('#tabla_Registros').DataTable();

            $('#tabla_Registros tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            } );

            $('#button').click( function () {
                table.row('.selected').remove().draw( false );
            } );
        } );

        function ver(col, Obj, Name, CC, Val, ValTo, Iva, Sal){
            console.log(col);
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

        function getRP(registro_id){
            $("#cargandoRP").show();
            $("#buttonSend").hide()
            $.ajax({
                method: "POST",
                url: "/administrativo/radCuentas/findRP",
                data: { "idRP": registro_id, "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(data) {
                console.log(data);
                document.getElementById('registro_id').value = registro_id;

                if(data.registro.tipo_contrato == 3) var tipoCont = "3 - DE OBRA PUBLICA";
                else if(data.registro.tipo_contrato == 4) var tipoCont = "4 - DE CONSULTORIA";
                else if(data.registro.tipo_contrato == 5) var tipoCont = "5 - DE INTERVENTORIA";
                else if(data.registro.tipo_contrato == 6) var tipoCont = "6 - DE SUMINISTRO";
                else if(data.registro.tipo_contrato == 10) var tipoCont = "10 - DE PRESTACION DE SERVICIOS";
                else if(data.registro.tipo_contrato == 11) var tipoCont = "11 - DE ENCARGO FIDUCIARIO Y FIDUCIA PUBLICA";
                else if(data.registro.tipo_contrato == 12) var tipoCont = "12 - ALQUILER O ARRENDAMIENTO";
                else if(data.registro.tipo_contrato == 13) var tipoCont = "13 - DE CONCESION";
                else if(data.registro.tipo_contrato == 20) var tipoCont = "20 - DEUDA PUBLICA";
                else if(data.registro.tipo_contrato == 21) var tipoCont = "21 - CONVENIO INTERADMINISTRATIVO";
                else if(data.registro.tipo_contrato == 22) var tipoCont = "22 - OTROS NO ESPECIFICADOS ANTERIORMENT";
                document.getElementById('tipoConSel').innerHTML = tipoCont;

                if(data.registro.mod_seleccion == 0) var tipoSel = "NO APLICA";
                else if(data.registro.mod_seleccion == 1) var tipoSel = "1 - LICITACION PUBLICA";
                else if(data.registro.mod_seleccion == 2) var tipoSel = "2 - CONCURSO DE MERITOS";
                else if(data.registro.mod_seleccion == 3) var tipoSel = "3 - SELECCION ABREVIADA";
                else if(data.registro.mod_seleccion == 4) var tipoSel = "4 - CONTRATACION DIRECTA";
                else if(data.registro.mod_seleccion == 8) var tipoSel = "8 - CUANTIA MINIMA";
                document.getElementById('modSel').innerHTML = tipoSel;
                document.getElementById('contNum').innerHTML = data.registro.num_doc;
                document.getElementById('fecha_cont').value = data.registro.ff_doc;
                document.getElementById('objetoContrato').innerHTML = data.registro.objeto;

                $("#cdps").html("");
                for(var i=0; i<data.cdps.length; i++){
                    data.cdps[i].name = data.cdps[i].name.replace(/[\r\n|\n|\r]+/,' ');
                    if (data.cdps[i].tipo == "Funcionamiento"){
                        var tr = `<tr><td>#`+data.cdps[i].code+` - `+data.cdps[i].name+`</td>
                              <td>`+data.cdps[i].rubro.cod+` - `+data.cdps[i].rubro.name+`</td>
                              <td>`+data.cdps[i].dep.name+`</td></tr>`;
                    } else {
                        var tr = `<tr><td>#`+data.cdps[i].code+` - `+data.cdps[i].name+`</td>
                              <td>`+data.cdps[i].bpin.cod_actividad+` - `+data.cdps[i].bpin.actividad +` - `+data.cdps[i].rubro.cod+` - `+data.cdps[i].rubro.name+`</td>
                              <td>`+data.cdps[i].dep.name+`</td></tr>`;
                    }

                    $("#cdps").append(tr)
                }

                if(data.ops.length > 0){
                    $("#ordenesPago").html("");
                    for(var i=0; i<data.ops.length; i++){
                        data.ops[i].nombre = data.ops[i].nombre.replace(/[\r\n|\n|\r]+/,' ');
                        var tr = `<tr><td>#`+data.ops[i].code+`</td>
                          <td>`+data.ops[i].nombre+`</td>
                          <td>Valor:`+formatter.format(data.ops[i].valor)+`</td></tr>`;

                        $("#ordenesPago").append(tr)

                        if (data.pagos[i].length > 0){
                            $("#pagos").html("");
                            for(var y=0; y<data.pagos[i].length; y++){
                                data.pagos[i][y].concepto = data.pagos[i][y].concepto.replace(/[\r\n|\n|\r]+/,' ');
                                var tr = `<tr><td>#`+data.pagos[i][y].code+`</td>
                                          <td>`+data.pagos[i][y].concepto+`</td>
                                          <td>Valor: `+formatter.format(data.pagos[i][y].valor)+`</td></tr>`;
                                $("#pagos").append(tr)
                            }
                        }
                    }
                }

                document.getElementById('contratista').value = data.registro.persona.nombre;
                document.getElementById('cedula').value = data.registro.persona.num_dc;
                document.getElementById('regTrib').innerHTML = data.registro.persona.regimen;
                document.getElementById('retefuente').value = data.registro.persona.reteFuente;
                document.getElementById('dir').value = data.registro.persona.direccion;
                document.getElementById('telFijo').value = data.registro.persona.telefono_fijo;
                document.getElementById('email').value = data.registro.persona.email;
                document.getElementById('cel').value = data.registro.persona.cel;
                document.getElementById('cuentaBanc').value = data.registro.persona.numero_cuenta_bancaria;
                document.getElementById('tipoCuenta').innerHTML = data.registro.persona.tipo_cuenta_bancaria;
                document.getElementById('entidadBanc').innerHTML = data.registro.persona.banco_cuenta_bancaria;

                $("#formRP").show();
                $("#cargandoRP").hide();
                $("#buttonSend").show();
            }).fail(function() {
                $("#cargandoRP").hide();
                $("#buttonSend").show();
                toastr.warning('OCURRIO UN ERROR AL BUSCAR LA INFORMACION DEL REGISTRO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
            });
        }


    </script>
@stop
