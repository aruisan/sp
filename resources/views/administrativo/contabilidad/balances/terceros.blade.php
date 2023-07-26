@extends('layouts.dashboard')
@section('titulo')
    Balance de Terceros
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Balance Terceros - {{ \Carbon\Carbon::now()->year }}
                        <br>
                        <select class="form-control text-center" name="mes" id="mes" onchange="change()">
                            <option value="0">Seleccione el mes</option>
                            <option value="1/1">Enero</option>
                            <option value="2/2">Febrero</option>
                            <option value="3/3">Marzo</option>
                            <option value="4/4">Abril</option>
                            <option value="5/5">Mayo</option>
                            <option value="6/6">Junio</option>
                            <option disabled value="7/7">Julio</option>
                            <option disabled value="8/8">Agosto</option>
                            <option disabled value="9/9">Septiembre</option>
                            <option disabled value="10/10">Octubre</option>
                            <option disabled value="11/11">Noviembre</option>
                            <option disabled value="12/12">Diciembre</option>
                            <option value="1/3">Enero - Marzo</option>
                            <option disabled value="1/6">Enero - Junio</option>
                            <option disabled value="1/9">Enero - Septiembre</option>
                            <option disabled value="1/12">Enero - Diciembre</option>
                        </select>
                    </b></h4>
            </strong>
        </div>
        <div class="table-responsive">
            <div class="text-center" id="cargando" style="display: none">
                <br><br>
                <h4>Cargando balance...</h4>
            </div>
            <table style="display: none" class="table table-bordered table-hover" id="tabla">
                <hr>
                <thead>
                <tr><th colspan="6" class="text-center"> <span id="cuentaBanco"></span></th></tr>
                <tr>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                </tr>
                </thead>
                <tbody id="bodyTabla"></tbody>
            </table>
        </div>
    </div>
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

        function change(){
            $("#cargando").show();

            const mes = document.getElementById("mes").value;
            var today = new Date();
            var year = today.getFullYear();
            var table = $('#tabla').DataTable();

            $.ajax({
                method: "GET",
                url: "/administrativo/contabilidad/balances/"+mes,
                data: { "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(datos) {
                if(mes === "1/1") var mesText = "Balance Enero - "+year
                else if(mes === '2/2') var mesText = "Balance Febrero - "+year
                else if(mes === '3/3') var mesText = "Balance Marzo - "+year
                else if(mes === '4/4') var mesText = "Balance Abril - "+year
                else if(mes === '5/5') var mesText = "Balance Mayo - "+year
                else if(mes === '6/6') var mesText = "Balance Junio - "+year
                else if(mes === '7/7') var mesText = "Balance Julio - "+year
                else if(mes === '8/8') var mesText = "Balance Agosto - "+year
                else if(mes === '9/9') var mesText = "Balance Septiembre - "+year
                else if(mes === '10/10') var mesText = "Balance Octubre - "+year
                else if(mes === '11/11') var mesText = "Balance Noviembre - "+year
                else if(mes === '12/12') var mesText = "Balance Diciembre - "+year
                else if(mes === '1/3') var mesText = "Balance Primer Semestre - "+year
                else if(mes === '1/6') var mesText = "Balance Segundo Semestre - "+year
                else if(mes === '1/9') var mesText = "Balance Tercer Semestre - "+year
                else if(mes === '1/12') var mesText = "Balance Cuarto Semestre - "+year
                document.getElementById("cuentaBanco").innerHTML = mesText;
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
                            title:     mesText,
                            className: 'btn btn-primary'
                        },
                        {
                            extend:    'excelHtml5',
                            text:      '<i class="fa fa-file-excel-o"></i> ',
                            titleAttr: 'Exportar a Excel',
                            title:     mesText,
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
                            title:     mesText,
                        },
                        {
                            extend:    'print',
                            text:      '<i class="fa fa-print"></i> ',
                            titleAttr: 'Imprimir',
                            className: 'btn btn-primary',
                            title:     mesText,
                        },
                    ],
                    data: datos,
                    columns: [
                        { title: "Fecha", data: "fecha"},
                        { title: "Cuenta", data: "code"},
                        { title: "Nombre Documento", data: "documento"},
                        { title: "Concepto", data: "concepto"},
                        { title: "Debito", data: "debito"},
                        { title: "Credito", data: "credito"},
                    ]
                } );
            }).fail(function() {
                $("#tabla").hide();
                table.destroy();
                $("#cargando").hide();
                toastr.warning('NO SE LOGRO GENERAR EL BALANCE.');
            });
        }
    </script>
@stop