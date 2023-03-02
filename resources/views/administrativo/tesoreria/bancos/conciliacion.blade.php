@extends('layouts.dashboard')
@section('titulo')
    Conciliación Bancaria
@stop
@section('sidebar')@stop
@section('content')
    <form class="form-valide" action="{{url('/administrativo/tesoreria/bancos/makeConciliacion')}}" method="POST" enctype="multipart/form-data" id="prog">
        {{ csrf_field() }}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="col-md-12 align-self-center">
            <div class="breadcrumb text-center">
                <strong>
                    <h4><b>Conciliación Bancaria</b></h4>
                </strong>
            </div>
            <h5>Seleccione el mes.</h5>
            <select class="form-control" id="mes" name="mes">
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
            <select class="form-control" id="cuentaPUC" name="cuentaPUC" onchange="findRubroPUC(this)">
                <option value="0">Seleccione la cuenta para obtener la conciliación Bancaria</option>
                @foreach($result as $cuenta)
                    <option @if($cuenta['hijo'] == 0) disabled @endif value="{{$cuenta['id']}}">{{$cuenta['code']}} -
                        {{$cuenta['concepto']}} - SALDO INICIAL: $<?php echo number_format($cuenta['saldo_inicial'],0) ?></option>
                @endforeach
            </select>
            <div class="table-responsive text-center">
                <div class="text-center" id="cargando" style="display: none">
                    <br><br>
                    <h4>Buscando información para cargar conciliación bancaria...</h4>
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
                <button style="display: none" id="buttonMake" class="btn-sm btn-primary">ELABORAR CONCILIACIÓN</button>
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

        function findRubroPUC(option){
            $("#cargando").show();

            const mes = document.getElementById("mes").value;
            var table = $('#tabla').DataTable();

            $.ajax({
                method: "POST",
                url: "/administrativo/tesoreria/bancos/movAccount",
                data: { "id": option.value, "mes": mes,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                document.getElementById("cuentaBanco").innerHTML = datos[0]['cuenta']+' SALDO INICIAL:'+ formatter.format(datos[0]['inicial']);
                $("#tabla").show();
                $("#buttonMake").show();
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
                    "pageLength": 5,
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