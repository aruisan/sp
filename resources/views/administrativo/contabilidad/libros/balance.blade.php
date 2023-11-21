@extends('layouts.dashboard')
@section('titulo')
    BALANCE - MAKE
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>BALANCE {{ $balance->tipo }} {{ $balance->año }}</b></h4>
            </strong>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th class="text-center" colspan="6">BALANCE {{ $balance->tipo }} MESES: {{ $balance->mes }}</th>
                </tr>
                <tr>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datSavedBalance as $data)
                    <tr>
                        <td>{{$data['fecha']}}</td>
                        <td>{{$data['code']}} - {{$data['cuentaConcept']}}</td>
                        @if($data['concepto'] == "")
                            <td colspan="2">{{$data['documento']}}</td>
                        @else
                            <td>{{$data['documento']}}</td>
                            <td>{{$data['concepto']}}</td>
                        @endif
                        <td>{{$data['debito']}}</td>
                        <td>{{$data['credito']}}</td>
                    </tr>
                @endforeach
                <tr class="text-center" style="background-color: rgba(19,165,255,0.14)">
                    <td colspan="4"><b>TOTALES</b></td>
                    <td><b>{{$balance->data[count($balance->data) - 1]['debito']}}</b></td>
                    <td><b>{{$balance->data[count($balance->data) - 1]['credito']}}</b></td>
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
                if (datos.length > 0) {
                    document.getElementById("cuentaBanco").innerHTML = datos[0]['cuenta'] + ' SALDO INICIAL:' + formatter.format(datos[0]['inicial']);
                    $("#tabla").show();
                    table.destroy();
                    $("#cargando").hide();
                    table = $('#tabla').DataTable({
                        language: {
                            "lengthMenu": "Mostrar _MENU_ registros",
                            "zeroRecords": "No se encontraron resultados",
                            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "sSearch": "Buscar:",
                            "oPaginate": {
                                "sFirst": "Primero",
                                "sLast": "Último",
                                "sNext": "Siguiente",
                                "sPrevious": "Anterior"
                            },
                            "sProcessing": "Procesando...",
                        },
                        "pageLength": 15,
                        responsive: true,
                        "searching": true,
                        ordering: false,
                        "lengthMenu": [10, 25, 50, 75, 100, "ALL"],
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'copyHtml5',
                                text: '<i class="fa fa-clone"></i> ',
                                titleAttr: 'Copiar',
                                className: 'btn btn-primary'
                            },
                            {
                                extend: 'excelHtml5',
                                text: '<i class="fa fa-file-excel-o"></i> ',
                                titleAttr: 'Exportar a Excel',
                                className: 'btn btn-primary'
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="fa fa-file-pdf-o"></i> ',
                                titleAttr: 'Exportar a PDF',
                                message: 'SIEX-Providencia',
                                header: true,
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                className: 'btn btn-primary',
                            },
                            {
                                extend: 'print',
                                text: '<i class="fa fa-print"></i> ',
                                titleAttr: 'Imprimir',
                                className: 'btn btn-primary'
                            },
                        ],
                        data: datos,
                        columns: [
                            {title: "Fecha", data: "fecha"},
                            {title: "Cuenta", data: "cuenta"},
                            {title: "Nombre Documento", data: "modulo"},
                            {title: "Concepto", data: "concepto"},
                            {title: "Tercero", data: "tercero"},
                            {title: "NIT/CC", data: "CC"},
                            {title: "Debito", data: "debito"},
                            {title: "Credito", data: "credito"},
                            {title: "Saldo", data: "total"}
                        ]
                    });


                    //$("#tabla").hide();
                    //table.destroy();
                    //$("#cargando").hide();
                    //toastr.warning('SE ESTAN REALIZANDO AJUSTES. INTENTE NUEVAMENTE MAS TARDE.');
                } else {
                    $("#tabla").hide();
                    $("#buttonMake").hide();
                    table.destroy();
                    $("#cargando").hide();
                    toastr.warning('NO SE OBTUVIERON DATOS DE ESA CUENTA PARA MOSTRAR');
                }
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