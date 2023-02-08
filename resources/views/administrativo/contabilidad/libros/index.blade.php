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
        <select class="form-control" id="cuentaPUC" name="cuentaPUC" onchange="findRubroPUC(this)">
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
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Nombre Documento</th>
                    <th class="text-center">Concepto</th>
                    <th class="text-center">Tercero</th>
                    <th class="text-center">NIT/CC</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                    <th class="text-center">Saldo</th>
                </thead>
                <tbody id="bodyTabla"></tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
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

            var table = $('#tabla').DataTable();

            $.ajax({
                method: "POST",
                url: "/administrativo/contabilidad/libros/rubros_puc",
                data: { "id": option.value,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
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
                    "pageLength": 5,
                    responsive: true,
                    "searching": true,
                    ordering: true,
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
            }).fail(function() {
                $("#tabla").hide();
                table.destroy();
                $("#cargando").hide();
                toastr.warning('NO SE OBTUVIERON DATOS DE ESA CUENTA');
            });
        }
    </script>
@stop