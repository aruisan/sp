@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $añoActual }}
@stop
@section('content')
    @if($V != "Vacio")
        @include('modal.Informes.reporte')
        @include('modal.Informes.ejecucionPresupuestal')
        @include('modal.Informes.makeInforme')
        @include('modal.Informes.makeCHIP')
        @include('modal.Proyectos.asignarubro')
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            @if($mesActual == 12)
                <li class="nav-item pillPri">
                    <a href="{{ url('/newPre/0',$añoActual+1) }}" class="nav-link"><span class="hide-menu"> Presupuesto de Egresos {{ $añoActual + 1 }}</span></a>
                </li>
            @elseif($mesActual == 1 or $mesActual == 2 and auth()->user()->roles->first()->id == 1)
                @if(auth()->user()->roles->first()->id == 1)
                    <li class="nav-item pillPri">
                        <a href="{{ url('/newPre/0',$añoActual-1) }}" class="nav-link"><span class="hide-menu"> Presupuesto de Egresos {{ $añoActual - 1 }}</span></a>
                    </li>
                @endif
            @endif
                <li class="nav-item principal">
                    <a class="nav-link"  href="#editar"> Presupuesto de Egresos {{ $añoActual }}</a>
                </li>
                @if($V != "Vacio" and auth()->user()->roles->first()->id == 1)
                    <li class="nav-item pillPri">
                        <a class="nav-link "href="{{ url('/presupuesto/level/create/'.$V) }}" class="btn btn-success">
                            <i class="fa fa-edit"></i><span class="hide-menu">&nbsp;Editar Presupuesto</span>
                        </a>
                    </li>
                @endif
                @if($V == "Vacio")
                    <li class="nav-item pillPri">
                        <a href="{{ url('/presupuesto/vigencia/create/0') }}" class="btn btn-drop">
                            <i class="fa fa-plus"></i>
                            <span class="hide-menu"> Nuevo Presupuesto de Egresos</span></a>
                    </li>
                @endif
        </ul>
        <div class="col-md-12 align-self-center" style="background-color:#fff;">
            @if($V != "Vacio")
                <div class="row" >
                    <div class="breadcrumb col-md-12 text-center" >
                        <strong>
                            <h4><b>Presupuesto de Egresos {{ $añoActual }}</b></h4>
                        </strong>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#tabHome" onclick="findPrep()"><i class="fa fa-home"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="@can('cdps-list') {{ url('administrativo/cdp/'.$V) }} @endcan">CDP's</a></li>
                    <li class="nav-item"><a class="nav-link" href="@can('registros-list') {{ url('administrativo/registros/'.$V) }} @endcan">Registros</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('administrativo/radCuentas/'.$V) }}">Radicación de Cuentas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('administrativo/ordenPagos/'.$V) }}">Orden de Pago</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('administrativo/pagos/'.$V) }}">Pagos</a></li>
                </ul>
                <hr>
                <!-- TABLA DE PRESUPUESTO -->
                <div class="tab-content" style="background-color: white">
                    <div id="tabHome" class="tab-pane active"><br>
                        <div class="table-responsive">
                            <div class="text-center" id="cargando" style="display: none">
                                <h4>Buscando informacion para cargar el presupuesto...</h4>
                            </div>
                            <div class="text-center" id="noFind" style="display: none">
                                <h4>Se esta realizando la carga del presupuesto, intenta nuevamente en unos minutos por favor.</h4>
                            </div>
                            <div class="text-center" id="refresPrep" style="display: none">
                                <h4>Se esta enviando la solicitud de actualización del presupuesto, un momento por favor....</h4>
                            </div>
                            <div class="text-center" id="refresPrepOK" style="display: none">
                                <h4>Presupuesto actualizado exitosamente, actualice la pagina para visualizar el estado actual del presupuesto.</h4>
                            </div>
                            <div class="text-center" id="infoPrep" style="display: none"></div>
                            <table id="tabla" class="table table-hover table-bordered table-striped " style="display: none">
                                <thead>
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
                                    @if(auth()->user()->roles->first()->id != 2)
                                        <th class="text-center">Cod Dependencia</th>
                                        <th class="text-center">Dependencia</th>
                                    @else
                                        <th class="text-center">Cod Dependencia</th>
                                        <th class="text-center">Dependencia</th>
                                    @endif
                                    <th class="text-center">Fuente</th>
                                    <th class="text-center">Código Producto</th>
                                    <th class="text-center">Código Indicador Producto</th>
                                    <th class="text-center">% Ejecución</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE PROYECTOS  -->

                    <div id="tab_proyectos" class=" tab-pane fade">
                        <div class="table-responsive mt-3" id="tabla_bpins">
                            <table class="table table-bordered" id="tabla_Proy">
                                <thead>
                                <tr>
                                    <th class="text-center">Codigo Proyecto</th>
                                    <th class="text-center">Nombre Proyecto</th>
                                    <th class="text-center">Secretaria</th>
                                    <th class="text-center">Ver</th>
                                    <th class="text-center"><i class="fa fa-file-pdf-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bpins->unique('cod_proyecto') as $item)
                                    <tr>
                                        <td>{{$item->cod_proyecto}}</td>
                                        <td>{{$item->nombre_proyecto}}</td>
                                        <td>{{$item->secretaria}}</td>
                                        <td><a class="btn btn-success" onclick="show_proyecto('{{$item->cod_proyecto}}')">Ver</a></td>
                                        <td><a class="btn btn-success" href="/presupuesto/proyecto/{{$item->cod_proyecto}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive" id="tabla_bpin_actividades">
                            <ul class="nav nav-tabs mt-3 mb-3">
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="modal" data-target="#myModal">Nueva Actividad</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#menu2">Decreto</a>
                                </li>
                            </ul>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">Codigo Actividad</th>
                                    <th class="text-center">Nombre Actividad</th>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center"><i class="fa fa-info-circle"></i></th>
                                </tr>
                                </thead>
                                <tbody id="tbody-actividades">
                                </tbody>
                            </table>

                            <div class="modal" id="myModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h3 class="modal-title">Nueva Actividad</h3>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="modal-body text-center">
                                            <form method="post" action="{{route('bpin.store')}}">
                                                <div class="row">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="cod_proyecto" id="input-cod-proyecto">
                                                    <input type="hidden" name="vigencia_id" id="vigencia_id" value="{{ $V }}">
                                                    <h4>Codigo de Actividad</h4>
                                                    <input type="text" name="cod_actividad" class="form-control">
                                                    <h4>Nombre de Actividad</h4>
                                                    <textarea name="nombre_actividad" class="form-control" row="3"></textarea>
                                                    <h4>Propios</h4>
                                                    <input type="text" name="propios" class="form-control">
                                                    <h4>SGP</h4>
                                                    <input type="text" name="sgp" class="form-control">
                                                    <h4>Rubro</h4>
                                                    <select name="fontRubEgr" id="fontRubEgr" style="width: 100%" class="selectRubroCC">
                                                        <option value="0">Seleccione el Rubro de Egresos</option>
                                                        @foreach($rubrosEgresosAll as $rubro)
                                                            <option value="{{ $rubro['id'] }}">{{ $rubro['code'] }}
                                                                {{ $rubro['nombre'] }} - {{ $rubro['fCode'] }} {{ $rubro['fName'] }}
                                                                - {{ $rubro['dep']['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <h4>Fuente</h4>
                                                    <select name="fuente" id="fuente" style="width: 100%" class="selectFuente">
                                                        <option value="0">Seleccione la Fuente</option>
                                                        @foreach($fuentes as $fuente)
                                                            <option value="{{ $fuente['id'] }}">{{ $fuente['code'] }} - {{ $fuente['description'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <h4>Dependencia</h4>
                                                    <select name="dependencia" id="dependencia" style="width: 100%" class="selectDep">
                                                        <option value="0">Seleccione la Dependencia</option>
                                                        @foreach($deps as $dep)
                                                            <option value="{{ $dep['id'] }}">{{ $dep['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <br>
                                                <button class="btn-sm btn-primary text-center" type="submit">Guardar</button>
                                            </form>
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="modal-footer text-center">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>Presupuesto de Egresos Año {{ $añoActual }}</b></h4>
                    </strong>
                </div>
                <br><br>
                <div class="alert alert-danger">
                    No se ha creado un presupuesto actual de egresos, para crearlo de click al siguiente link:
                    <a href="{{ url('presupuesto/vigencia/create/0') }}" class="alert-link">Crear Presupuesto de Egresos</a>
                </div>
            @endif
        </div>
    </div><br><br>
@stop
@section('js')
    <!-- Datatables personalizadas buttons-->
    <script src="{{ asset('/js/datatableCustom.js') }}"></script>


    <!-- tabla de proyectos -->
    <script>

        $('.selectRubroCC').select2();
        $('.selectFuente').select2();
        $('.selectDep').select2();
        $('.asignarRubroSelect').select2();

        var rol = @json(auth()->user()->roles->first()->id)

        const vigencia_id = @json($V);
        const prepSaved = @json($prepSaved);
        const añoPrep = @json(\Carbon\Carbon::parse($fechaData)->year);
        const mesPrep = @json(\Carbon\Carbon::parse($fechaData)->month);
        const diaPrep = @json(\Carbon\Carbon::parse($fechaData)->day);

        function getModalToMakeInforme() {
            $('#modalMakeInforme').modal('show');
        }

        function getModalToMakeCHIP(){
            $('#modalMakeCHIP').modal('show');
        }

        function JSONToCSVConvertor(JSONData, ReportTitle) {
            var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

            var CSV = '';

            for (var i = 0; i < arrData.length; i++) {
                var row = "";

                for (var index in arrData[i]) {
                    row += '"' + arrData[i][index] + '",';
                }

                row.slice(0, row.length - 1);

                CSV += row + '\r\n';
            }

            if (CSV == '') {
                alert("Invalid data");
                return;
            }

            //Generate a file name
            var fileName = "";
            fileName += ReportTitle.replace(/ /g,"_");

            //Initialize file format you want csv or xls
            var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
            var link = document.createElement("a");
            link.href = uri;

            link.style = "visibility:hidden";
            link.download = fileName + ".csv";

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function formCHIPSubmit(){
            $("#cargandoCHIP").show();
            var periodo = document.getElementById("periodo").value;
            var categoria = document.getElementById("categoria").value;
            if(periodo == 1) var mes = '03';
            else if(periodo == 2) var mes = '06'
            else if(periodo == 3) var mes = '09'
            else if(periodo == 4) var mes = '12'
            const fecha = new Date();
            const añoActual = fecha.getFullYear();

            $.ajax({
                method: "POST",
                url: "/presupuesto/generate/CHIP",
                data: { "periodo": periodo, "categoria": categoria,
                    "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(datos) {
                console.log(datos);
                if (categoria == 'ProgIng') JSONToCSVConvertor(datos, 'Programacion_Ingresos_'+mes+'/'+añoActual);
                if (categoria == 'EjecIng') JSONToCSVConvertor(datos, 'Ejecucion_Ingresos_'+mes+'/'+añoActual);
                if (categoria == 'ProgGasAdm') JSONToCSVConvertor(datos, 'Programacion_Gastos_Administracion_Central_'+mes+'/'+añoActual);
                else toastr.warning('SE RECIBIO LA SOLITUD PERO ESTAMOS TRABAJANDO EN HABILITAR ESE INFORME CHIP.');
                $("#cargandoCHIP").hide();
            }).fail(function() {
                $("#cargandoCHIP").hide();

                toastr.warning('OCURRIO UN ERROR AL GENERAR EL CHIP, INTENTE NUEVAMENTE MAS TARDE POR FAVOR.');
            });
        }

        function refreshPrep(){
            $("#noFind").hide();
            $("#cargando").hide();
            $("#infoPrep").hide();
            $("#refresPrep").show();
            $("#refresPrepOK").hide();

            $.ajax({
                method: "GET",
                url: "/presupuesto/refreshPrepSaved",
                data: { "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(datos) {
                $("#refresPrepOK").show();
                $("#refresPrep").hide();

                toastr.success('PRESUPUESTO ACTUALIZADO EXITOSAMENTE. ACTUALICE LA PAGINA.');
            }).fail(function() {
                $("#tabla").hide();
                $("#cargando").hide();
                $("#noFind").hide();
                $("#refresPrep").hide();
                $("#refresPrepOK").hide();

                toastr.warning('OCURRIO UN ERROR AL SOLICITAR LA ACTUALIZACIÓN DEL PRESUPUESTO.');
            });
        }


        function findPrep(){
            $("#cargando").show();
            $("#noFind").hide();
            $("#infoPrep").hide();
            $("#refresPrepOK").hide();

            var table = $('#tabla').DataTable();

            $.ajax({
                method: "POST",
                url: "/presupuesto/getPrepSaved",
                data: { "id": vigencia_id, "prepSaved": prepSaved,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                $("#tabla").show();
                table.destroy();
                $("#infoPrep").show();
                $("#cargando").hide();
                $("#noFind").hide();
                if(rol != 2){
                    table = $('#tabla').DataTable( {
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
                        "pageLength": 15,
                        responsive: true,
                        "searching": true,
                        ordering: false,
                        "lengthMenu": [ 10, 25, 50, 75, 100, "ALL" ],
                        dom: 'Bfrtip',
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
                                className: 'btn btn-primary',
                                title: 'Presupuesto Egresos '+añoPrep+'-'+mesPrep+'-'+diaPrep
                            },
                            {
                                extend:    'pdfHtml5',
                                text:      '<i class="fa fa-file-pdf-o"></i> ',
                                titleAttr: 'Exportar a PDF',
                                message : 'SIEX-Providencia',
                                header :true,
                                orientation : 'landscape',
                                pageSize: 'LEGAL',
                                className: 'btn btn-primary',
                            },
                            {
                                extend:    'print',
                                text:      '<i class="fa fa-print"></i> ',
                                titleAttr: 'Imprimir',
                                className: 'btn btn-primary'
                            },
                        ],
                        data: datos,
                        columns: [
                            { title: "Codigo BPIN", data: "code_bpin"},
                            { title: "Codigo Actividad", data: "code_act"},
                            { title: "Nombre Actividad", data: "name_act"},
                            { title: "Rubro", data: "rubroLink"},
                            { title: "Nombre", data: "nombre"},
                            { title: "P. Inicial", data: "p_inicial"},
                            { title: "Adición", data: "adicion"},
                            { title: "Reducción", data: "reduccion"},
                            { title: "Credito", data: "credito"},
                            { title: "CCredito", data: "ccredito"},
                            { title: "P.Definitivo", data: "p_def"},
                            { title: "CDP's", data: "cdps"},
                            { title: "Registros", data: "rps"},
                            { title: "Saldo Disponible", data: "saldo_disp"},
                            { title: "Saldo de CDP", data: "saldo_cdps"},
                            { title: "Ordenes de Pago", data: "ops"},
                            { title: "Pagos", data: "pagos"},
                            { title: "Cuentas Por Pagar", data: "cuentas_pagar"},
                            { title: "Reservas", data: "reservas"},
                            { title: "Cod Dependencia", data: "cod_dep"},
                            { title: "Dependencia", data: "name_dep"},
                            { title: "Fuente", data: "fuente"},
                            { title: "Código Producto", data: "cod_producto"},
                            { title: "Código Indicador Producto", data: "cod_indicador"},
                            { title: "% Ejecución", data: "ejec"},
                        ]
                    } );
                } else {
                    table = $('#tabla').DataTable( {
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
                        "pageLength": 15,
                        responsive: true,
                        "searching": true,
                        ordering: false,
                        "lengthMenu": [ 10, 25, 50, 75, 100, "ALL" ],
                        dom: 'Bfrtip',
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
                                message : 'SIEX-Providencia',
                                header :true,
                                orientation : 'landscape',
                                pageSize: 'LEGAL',
                                className: 'btn btn-primary',
                            },
                            {
                                extend:    'print',
                                text:      '<i class="fa fa-print"></i> ',
                                titleAttr: 'Imprimir',
                                className: 'btn btn-primary'
                            },
                            {
                                text: '<i class="fa fa-refresh"  onclick="refreshPrep()"></i>',
                                titleAttr: 'Actualizar Presupuesto',
                                className: 'btn btn-primary'
                            },
                        ],
                        data: datos,
                        columns: [
                            { title: "Codigo BPIN", data: "code_bpin"},
                            { title: "Codigo Actividad", data: "code_act"},
                            { title: "Nombre Actividad", data: "name_act"},
                            { title: "Rubro", data: "rubroLink"},
                            { title: "Nombre", data: "nombre"},
                            { title: "P. Inicial", data: "p_inicial"},
                            { title: "Adición", data: "adicion"},
                            { title: "Reducción", data: "reduccion"},
                            { title: "Credito", data: "credito"},
                            { title: "CCredito", data: "ccredito"},
                            { title: "P.Definitivo", data: "p_def"},
                            { title: "CDP's", data: "cdps"},
                            { title: "Registros", data: "rps"},
                            { title: "Saldo Disponible", data: "saldo_disp"},
                            { title: "Saldo de CDP", data: "saldo_cdps"},
                            { title: "Ordenes de Pago", data: "ops"},
                            { title: "Pagos", data: "pagos"},
                            { title: "Cuentas Por Pagar", data: "cuentas_pagar"},
                            { title: "Reservas", data: "reservas"},
                            { title: "Cod Dependencia", data: "cod_dep"},
                            { title: "Dependencia", data: "name_dep"},
                            { title: "Fuente", data: "fuente"},
                            { title: "Código Producto", data: "cod_producto"},
                            { title: "Código Indicador Producto", data: "cod_indicador"},
                        ]
                    } );
                }

            }).fail(function() {
                $("#tabla").hide();
                table.destroy();
                $("#cargando").hide();
                $("#noFind").show();
            });
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        window.onload = function () {
            findPrep();
        };

        const bpins = @json($bpins);
        const show_proyecto = cod_proyecto =>{
            $('#tabla_bpins').hide();
            $('#tabla_bpin_actividades').show();
            $('#tbody-actividades').empty();
            $('#input-cod-proyecto').val(cod_proyecto);
            bpins.filter(r => r.cod_proyecto == cod_proyecto).forEach(e =>{
                if (e.rubro != "No") var button = e.rubro+`<br> Dinero Asignado: `+e.rubro_find[0].propios.toLocaleString()+`<br><button onclick="getModalAsignaRubro(${e.cod_actividad})" class="btn btn-primary">Asignar Rubro a la Actividad</button>`;
                else {
                    //var button = `<b>No hay rubros de inversión disponibles para asignar.</b>`;
                    var button = `<button onclick="getModalAsignaRubro(${e.cod_actividad})" class="btn btn-primary">Asignar Rubro a la Actividad</button>`;
                }
                $('#tbody-actividades').append(`
                <tr>
                    <td>${e.cod_actividad}</td>
                    <td>${e.actividad}</td>
                    <td>${button}</td>
                    <td><a href="/presupuesto/actividad/${e.id}/${vigencia_id}" class="btn btn-primary"><i class="fa fa-info-circle"></i></a></td>
                </tr>
            `);
            });
        }

        function getModalAsignaRubro(code){
            bpins.filter(r => r.cod_actividad == code).forEach(e =>{
                document.getElementById("nameActividad").innerHTML = e.actividad;
                document.getElementById("codeActividad").innerHTML = code;
                document.getElementById("dispActividad").innerHTML = e.saldo;
                $('#actividadCode').val(code);
                $(document).on('keyup', '#valueAsignarRubro', function(event) {
                    let max= parseInt(e.saldo);
                    let valor = parseInt(this.value);
                    if(valor>max){
                        //alert("El Valor no está Permitido")
                        //this.value = max;
                    }
                });
            });
            $('#formAsignaRubro').modal('show');
        }

        const show_bpins = ()  =>{
            $('#tabla_bpins').show();
            $('#tabla_bpin_actividades').hide();
        }

    </script>
@stop