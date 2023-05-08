@extends('layouts.dashboard')
@section('titulo') Pagos Descuentos @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Pagos de Decuentos {{$vigencia->vigencia}} </b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">HISTORICO</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <div class="table-responsive">
                <form class="form-valide" action="{{url('/administrativo/tesoreria/descuentos/makePago')}}" method="POST" enctype="multipart/form-data" id="prog">
                    {{ csrf_field() }}
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <div class="col-md-12 align-self-center">
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
                        <select name="cuentaPUC" style="width: 100%" class="select-bank" onchange="findRubroPUC(this)" required>
                            <option value="0">SELECCIONE LA CUENTA BANCARIA</option>
                            @foreach($cuentas as $cuenta)
                                <option value="{{ $cuenta->id }}">{{ $cuenta->code }} - {{ $cuenta->concepto }}</option>
                            @endforeach
                        </select>
                        <div class="table-responsive text-center">
                            <div class="text-center" id="cargando" style="display: none">
                                <br><br>
                                <h4>Buscando información para cargar el pago de descuentos...</h4>
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
            </div>
        </div>
        <div id="tabHistorico" class="tab-pane fade">
            <div class="table-responsive">
                @if(count($pagos) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Mes</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagos as $index => $pago)
                            <tr>
                                <td class="text-center">{{ $pago->id }}</td>
                                <td class="text-center">{{ $pago->mes }}</td>
                                <td class="text-center">$ <?php echo number_format($pago->valor,0);?></td>
                                <td class="text-center"> {{ \Carbon\Carbon::parse($pago->created_at)->format('d-m-Y H:i:s') }}</td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/tesoreria/descuentos/viewpago/'.$pago->id.'/view') }}" title="Ver Pago" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/tesoreria/descuentos/PDFpago/'.$pago->id.'/PDF') }}" target="_blank" title="Comprobante Contable" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/pdf/'.$pago->orden_pago_id) }}" title="Orden de Pago" class="btn-sm btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                    @if($pago->egreso)
                                        @if($pago->egreso['estado'] == '1')
                                            <a href="{{ url('/administrativo/egresos/pdf/'.$pago->egreso['id']) }}" title="Comprobante de Egresos" class="btn-sm btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay pagos de descuentos registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')

    <script type="text/javascript" >

        $(document).ready(function(){

            $('.nav-tabs a[href="#tabTareas"]').tab('show')
        });

        const pucs = @json($cuentas);

        $('#mes').on('change', function(){
            validar_conciliacion();
        });

        const validar_conciliacion = () => {
            console.log('conciliaciones1', pucs.length);
            let mes = $('#mes').val();
            let conciliaciones_select_id = conciliaciones.filter(e => e.mes == mes && e.finalizar).map(e => e.puc_id);
            console.log('conciliaciones0', conciliaciones_select_id);
            //let pucs_select = pucs.filter(e => );
            $('#cuentaPUC').empty().append('<option value="0">Seleccione la cuenta para obtener la conciliación Bancaria</option>');
            //console.log('conciliaciones1', pucs_select.length);
            pucs.forEach(e => {
                let item = `<option ${e.hijo == 0 || conciliaciones_select_id.includes(e.id) ? 'disabled' : ''} value="${conciliaciones_select_id.includes(e.id) ? '' : e.id}">${conciliaciones_select_id.includes(e.id) ? 'FINALIZADO -': ''} ${e.code} - ${e.concepto} - SALDO INICIAL:  $${cash.format(e.saldo_inicial)}</option>`;
                $('#cuentaPUC').append(item);
            });
        }

        $('.select-bank').select2();

        function findRubroPUC(option){
            $("#cargando").show();

            const mes = document.getElementById("mes").value;
            var table = $('#tabla').DataTable();

            $.ajax({
                method: "POST",
                url: "/administrativo/tesoreria/descuentos/movimientos/pagos",
                data: { "id": option.value, "mes": mes,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                console.log(datos);
                if(datos.length > 0){
                    $("#buttonMake").show();
                    console.log('ddf', datos);
                    document.getElementById("cuentaBanco").innerHTML = datos[0]['cuenta']+' SALDO INICIAL:'+ datos[0]['total'];
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
                } else{
                    $("#tabla").hide();
                    table.destroy();
                    $("#cargando").hide();
                    $("#buttonMake").show();
                    toastr.warning('SE REALIZO LA BUSQUEDA EXITOSAMENTE PERO NO HAY MOVIMIENTOS EN ESE MES.');
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

    <script>

        function approve(value, num, cdps){
            if (value == true){
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = cdps[i]['id'];
                }
            } else{
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = null;
                }
            }
        }

        function approveUnidad(value, num, cdpId){
            var id = "checkInput"+num;
            if (value == true){
                document.getElementById(id).value = cdpId;
            } else {
                document.getElementById(id).value = null;
            }
        }

        $('#tabla_CDP').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
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
        ]
        } );

        $('#tabla_Historico').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_Process').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop
