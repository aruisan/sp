@extends('layouts.dashboard')
@section('titulo')
    Libros
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Libros</b></h4>
            </strong>
        </div>
        <h5>Seleccione la fecha</h5>
        <input type="text" name="fecha" id="fecha" class="form-control" required>
        <input type="hidden" name="fechaInicial" id="fechaInicial" class="form-control" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
        <input type="hidden" name="fechaFinal" id="fechaFinal" class="form-control" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">

        <select style="display: none" class="form-control" id="mes" name="mes">
            <option value="0">AÑO</option>
            <option value="1">ENERO</option>
            <option value="2">FEBRERO</option>
            <option value="3">MARZO</option>
            <option value="4">ABRIL</option>
            <option value="5">MAYO</option>
            <option value="6">JUNIO</option>
            <option value="7">JULIO</option>
            <option value="8">AGOSTO</option>
            <option value="9">SEPTIEMBRE</option>
            <option value="10">OCTUBRE</option>
            <option value="11">NOVIEMBRE</option>
            <option value="12">DICIEMBRE</option>
        </select>
        <br>
        <select style="width: 100%" class="select-cuenta" id="cuentaPUC" name="cuentaPUC" onchange="findRubroPUC()">
            <option value="0">Seleccione la cuenta para obtener el libro</option>
            @foreach($result as $cuenta)
                <option value="{{$cuenta['id']}}">{{$cuenta['code']}} - {{$cuenta['concepto']}}</option>
            @endforeach
        </select>
        <div class="table-responsive">
            <div class="text-center" id="cargando" style="display: none">
                <br><br>
                <h4>Buscando informacion para cargar libro...</h4>
            </div>
            <table style="display: none" class="table table-bordered table-hover" id="tabla">
                <hr>
                <thead>
                <tr><th colspan="9" class="text-center"> <span id="cuentaBanco"></span></th></tr>
                <tr>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Tercero</th>
                    <th class="text-center">NIT/CC</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                    <th class="text-center">Saldo</th>
                </tr>
                </thead>
                <tbody id="bodyTabla"></tbody>
            </table>
        </div>
        <hr>
        <h3 class="text-center">ENERO</h3>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr><th class="text-center" colspan="7">ENERO</th></tr>
                <tr>
                    <th class="text-center" colspan="2">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                </tr>
                </thead>
                <tbody>
                @php($deb = 0)
                @php($cred = 0)
                @foreach($result2 as $cuenta2)
                    @foreach($resultEne as $padEnero)
                        @if($cuenta2['id'] == $padEnero['cuenta_id'])
                            <tr>
                                <td colspan="2">Enero</td>
                                <td>{{$padEnero['code']}} - {{$padEnero['concepto']}}</td>
                                <td colspan="2">{{$padEnero['concepto']}}</td>
                                <td>{{$padEnero['debito']}}</td>
                                <td>{{$padEnero['credito']}}</td>
                            </tr>
                            @if(strlen($padEnero['code']) == 2)
                                @php($deb = $deb + $padEnero['debito'])
                                @php($cred = $cred + $padEnero['credito'])
                            @endif
                            @foreach($enero as $value)
                                @if($padEnero['cuenta_id'] == $value['padre_id'])
                                    <tr>
                                        <td>Enero</td>
                                        <td>{{$value['fecha']}}</td>
                                        <td>{{$value['cuenta']}}</td>
                                        <td>{{$value['modulo']}}</td>
                                        <td>{{$value['concepto']}}</td>
                                        <td>{{$value['debito']}}</td>
                                        <td>{{$value['credito']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
                <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                    <td colspan="2">ENERO</td>
                    <td colspan="3"><b>TOTALES</b></td>
                    <td><b>{{$deb}}</b></td>
                    <td><b>{{$cred}}</b></td>
                </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <h3 class="text-center">FEBRERO</h3>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr><th class="text-center" colspan="7">FEBRERO</th></tr>
                <tr>
                    <th class="text-center" colspan="2">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                </tr>
                </thead>
                <tbody>
                @php($deb = 0)
                @php($cred = 0)
                @foreach($result2 as $cuenta2)
                    @foreach($resultFeb as $padFeb)
                        @if($cuenta2['id'] == $padFeb['cuenta_id'])
                            <tr>
                                <td colspan="2">Febrero</td>
                                <td>{{$padFeb['code']}} - {{$padFeb['concepto']}}</td>
                                <td colspan="2">{{$padFeb['concepto']}}</td>
                                <td>{{$padFeb['debito']}}</td>
                                <td>{{$padFeb['credito']}}</td>
                            </tr>
                            @if(strlen($padFeb['code']) == 2)
                                @php($deb = $deb + $padFeb['debito'])
                                @php($cred = $cred + $padFeb['credito'])
                            @endif
                            @foreach($febrero as $value)
                                @if($padFeb['cuenta_id'] == $value['padre_id'])
                                    <tr>
                                        <td>Febrero</td>
                                        <td>{{$value['fecha']}}</td>
                                        <td>{{$value['cuenta']}}</td>
                                        <td>{{$value['modulo']}}</td>
                                        <td>{{$value['concepto']}}</td>
                                        <td>{{$value['debito']}}</td>
                                        <td>{{$value['credito']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
                <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                    <td colspan="2">FEBRERO</td>
                    <td colspan="3"><b>TOTALES</b></td>
                    <td><b>{{$deb}}</b></td>
                    <td><b>{{$cred}}</b></td>
                </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <h3 class="text-center">MARZO</h3>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr><th class="text-center" colspan="7">MARZO</th></tr>
                <tr>
                    <th class="text-center" colspan="2">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                </tr>
                </thead>
                <tbody>
                @php($deb = 0)
                @php($cred = 0)
                @foreach($result2 as $cuenta2)
                    @foreach($resultMar as $pad)
                        @if($cuenta2['id'] == $pad['cuenta_id'])
                            <tr>
                                <td colspan="2">Marzo</td>
                                <td>{{$pad['code']}} - {{$pad['concepto']}}</td>
                                <td colspan="2">{{$pad['concepto']}}</td>
                                <td>{{$pad['debito']}}</td>
                                <td>{{$pad['credito']}}</td>
                            </tr>
                            @if(strlen($pad['code']) == 2)
                                @php($deb = $deb + $pad['debito'])
                                @php($cred = $cred + $pad['credito'])
                            @endif
                            @foreach($marzo as $value)
                                @if($pad['cuenta_id'] == $value['padre_id'])
                                    <tr>
                                        <td>Marzo</td>
                                        <td>{{$value['fecha']}}</td>
                                        <td>{{$value['cuenta']}}</td>
                                        <td>{{$value['modulo']}}</td>
                                        <td>{{$value['concepto']}}</td>
                                        <td>{{$value['debito']}}</td>
                                        <td>{{$value['credito']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
                <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                    <td colspan="2">MARZO</td>
                    <td colspan="3"><b>TOTALES</b></td>
                    <td><b>{{$deb}}</b></td>
                    <td><b>{{$cred}}</b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>

        $('.select-cuenta').select2();

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        $('input[name="fecha"]').daterangepicker({
            opens: 'center'
        }, function(start, end) {
            document.getElementById("fechaInicial").value = start.format('YYYY-MM-DD');
            document.getElementById("fechaFinal").value = end.format('YYYY-MM-DD');

            const cuenta = document.getElementById("cuentaPUC").value;
            if(parseInt(cuenta) != 0){
                findRubroPUC();
            }
        });

        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": 3000,
                "extendedTimeOut": 0,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "tapToDismiss": true
            }

        });

        function findRubroPUC(){
            $("#cargando").show();

            const cuenta = document.getElementById("cuentaPUC").value;

            const fechaInicial = document.getElementById("fechaInicial").value;
            const fechaFinal =document.getElementById("fechaFinal").value;
            var table = $('#tabla').DataTable();

            $.ajax({
                method: "POST",
                url: "/administrativo/tesoreria/bancos/movAccountLibros",
                data: { "id": cuenta, "fechaInicial": fechaInicial, "fechaFinal": fechaFinal,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                document.getElementById("cuentaBanco").innerHTML = datos[0]['cuenta']+' SALDO INICIAL:'+ formatter.format(datos[0]['inicial']);
                $("#tabla").show();
                table.destroy();
                $("#cargando").hide();
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
                    ],
                    data: datos,
                    columns: [
                        { title: "Fecha", data: "fecha"},
                        { title: "Cuenta", data: "cuenta"},
                        { title: "Nombre Documento", data: "modulo"},
                        { title: "Concepto", data: "concepto"},
                        { title: "Tercero", data: "tercero"},
                        { title: "NIT/CC", data: "CC"},
                        { title: "Debito", data: "debito"},
                        { title: "Credito", data: "credito"},
                        { title: "Saldo", data: "total"}
                    ]
                } );


                //$("#tabla").hide();
                //table.destroy();
                //$("#cargando").hide();
                //toastr.warning('SE ESTAN REALIZANDO AJUSTES. INTENTE NUEVAMENTE MAS TARDE.');
            }).fail(function() {
                $("#tabla").hide();
                $("#buttonMake").hide();
                table.destroy();
                $("#cargando").hide();
                toastr.warning('NO SE OBTUVIERON DATOS DE ESA CUENTA');
            });
        }
    </script>
@stop