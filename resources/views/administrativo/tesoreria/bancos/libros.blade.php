@extends('layouts.dashboard')
@section('titulo') Libros - Tesoreria @stop
@section('sidebar')@stop
@section('content')
    <form class="form-valide" action="{{url('/administrativo/tesoreria/bancos/makeConciliacion')}}" method="POST" enctype="multipart/form-data" id="prog">
        {{ csrf_field() }}
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

            <br>
            <select class="form-control" id="cuentaPUC" name="cuentaPUC" onchange="findRubroPUC()">
                <option value="0">Seleccione la cuenta para obtener el libro</option>
                @foreach($result as $cuenta)
                    <option @if($cuenta['hijo'] == 0) disabled @endif value="{{$cuenta['id']}}">{{$cuenta['code']}} -
                        {{$cuenta['concepto']}} - SALDO INICIAL: $<?php echo number_format($cuenta['saldo_inicial'],0) ?></option>
                @endforeach
            </select>
            <div class="table-responsive text-center">
                <div class="text-center" id="cargando" style="display: none">
                    <br><br>
                    <h4>Buscando información para cargar el libro...</h4>
                </div>
                <table style="display: none" class="table table-bordered table-hover" id="tabla">
                    <hr>
                    <thead>
                    <tr>
                        <th colspan="9" class="text-center"> <span id="cuentaBanco"></span></th>
                    </tr>
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
        </div>
    </form>
@stop

@section('js')

    <script>

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