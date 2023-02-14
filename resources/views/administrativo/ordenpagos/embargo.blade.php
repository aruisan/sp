@extends('layouts.dashboard')
@section('titulo')
    Embargos {{ $vigencia->vigencia }}
@stop
@section('content')
    @include('modal.embargosOP')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Ordenes de Pago Vigencia {{ $vigencia->vigencia }} Disponibles para Embargos</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHistorico">Ordenes de Pago {{ $vigencia->vigencia }}</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white" id="crud">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div id="tabHistorico" class="tab-pane active"><br>
            <div class="table-responsive">
                @if(count($ordenPagos) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Valor Inicial</th>
                            <th class="text-center">Disponible</th>
                            <th class="text-center">Acciones</th>
                            <th class="text-center">Embargo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ordenPagos as $ordenPago)
                            <tr class="text-center">
                                <td>{{ $ordenPago['info']['code'] }}</td>
                                <td>{{ $ordenPago['info']['nombre'] }}</td>
                                <td>{{ $ordenPago['tercero'] }}</td>
                                <td>$<?php echo number_format($ordenPago['info']['valor'],0) ?></td>
                                <td>$<?php echo number_format($ordenPago['info']['saldo'],0) ?></td>
                                <td>
                                    <a href="{{ url('administrativo/ordenPagos/show/'.$ordenPago['info']['id']) }}" title="Ver Orden de Pago" class="btn-sm btn-success"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/pdf/'.$ordenPago['info']['id']) }}" title="Orden de Pago" class="btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                                <td>
                                    <button onclick="embargo({{ $ordenPago['info']['id'] }})" title="Generar Embargo" class="btn-sm btn-success">EMBARGO</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay ordenes de pago disponibles para realizar embargo.
                        </center>
                    </div>
                @endif
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

                function embargo(id){
                    $.ajax({
                        method: "POST",
                        url: "/administrativo/tesoreria/ordenPagos/embargos/getOP/find",
                        data: { "id": id,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        }
                    }).done(function(datos) {
                        //console.log(datos['descuentos']);

                        for (var i = 0; i < datos['descuentos'].length; i++) {
                            console.log(datos['descuentos'][i]);
                            document.getElementById("cuerpoDesc").insertRow(-1).innerHTML = '' +
                                '<td>'+datos['descuentos'][i]['nombre']+'</td>' +
                                '<td>'+formatter.format(datos['descuentos'][i]['valor'])+'</td>'
                        }

                        $('#formEmbargo').modal('show');

                    }).fail(function() {
                        toastr.warning('NO SE PUDO OBTENER LA ORDEN DE PAGO.');
                    });
                }

                $('#tabla_Historico').DataTable( {
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

                new Vue({
                    el: '#crud',

                    data:{
                        datos: []
                    },
                    methods:{

                        nuevaFilaDescMuni(){
                            $('#tabla_desc_muni tbody tr:last').after('<tr>\n' +
                                '<td></td>\n'+
                                '<td>Seleccione la cuenta del PUC <br>' +
                                '<select class="form-control" name="cuentaDesc[]">\n' +
                                '                                        @foreach($cuentas24 as $cuenta)\n' +
                                '                                            <option value="{{$cuenta->id}}">{{$cuenta->code}} - {{$cuenta->concepto}}</option>\n' +
                                '                                        @endforeach\n' +
                                '                                    </select></td>\n'+
                                '<td>Seleccione el tercero' +
                                '<select class="form-control" name="tercero[]">\n' +
                                '                                        @foreach($personas as $persona)\n' +
                                '                                            <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>\n' +
                                '                                        @endforeach\n' +
                                '                                    </select></td>\n'+
                                '<td>Valor a descontar<br><input type="number" class="form-control" name="valorDesc[]" min="1" value="1" required></td>\n'+
                                '<td style="vertical-align: middle" class="text-center" ><button type="button" class="borrar btn-sm btn-danger">&nbsp;-&nbsp; </button></td>\n'+
                                '</tr>\n');
                        },

                        eliminarDescM: function(dato){
                            var urlVigencia = '/administrativo/ordenPagos/descuento/m/'+dato;
                            axios.delete(urlVigencia).then(response => {
                                location.reload();
                            });
                        },
                    }
                });

            </script>
@stop