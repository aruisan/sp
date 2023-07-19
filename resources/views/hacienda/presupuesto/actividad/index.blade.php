@extends('layouts.dashboard')
@section('titulo') PROYECTOS {{ $vigencia->vigencia }} @stop
@section('content')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>PROYECTOS {{ $vigencia->vigencia }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item active">
                    <a class="tituloTabs" data-toggle="tab" onclick="show_bpins()"><i class="fa fa-home"></i></a>
                </li>
                <li class="dropdown">
                    <a class="nav-item dropdown-toggle" data-toggle="dropdown" href="#">Acciones &nbsp; <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#reporte" class="btn btn-drop text-left">Reporte</a>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#adicion" class="btn btn-drop text-left">Adición</a>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#credito" class="btn btn-drop  text-left">Traslado</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab_proyectos" class=" tab-pane active">
                    <div class="table-responsive mt-3" id="tabla_bpins">
                        <table class="table table-bordered" id="tabla_Proy">
                            <thead>
                            <tr>
                                <th class="text-center">Codigo Proyecto</th>
                                <th class="text-center">Nombre Proyecto</th>
                                <th class="text-center">Secretaria</th>
                                <th class="text-center">Ver</th>
                                <th class="text-center">Certificado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bpins->unique('cod_proyecto') as $item)
                                <tr>
                                    <td>{{$item->cod_proyecto}}</td>
                                    <td>{{$item->nombre_proyecto}}</td>
                                    <td>{{$item->secretaria}}</td>
                                    <td><a class="btn btn-success" onclick="show_proyecto('{{$item->cod_proyecto}}')">Ver</a></td>
                                    <td class="text-center"><a class="btn btn-success" href="/presupuesto/proyecto/{{$item->cod_proyecto}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive" id="tabla_bpin_actividades">
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
                                        <h4 class="modal-title">Nueva Actividad</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form method="post" action="{{route('bpin.store')}}">
                                            <div class="row">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="cod_proyecto" id="input-cod-proyecto">
                                                <div class="input-group my-2 col-md-12">
                                                    <label for="" class="col-sm-5">codigo de Actividad</label>
                                                    <input type="text" name="cod_actividad" class="form-control col-sm-7">
                                                </div>
                                                <div class="input-group my-2 col-md-12">
                                                    <label for="" class= col-sm-5">Nombre de Actividad</label>
                                                    <textarea name="nombre_actividad" class="form-control col-sm-7" row="3"></textarea>
                                                </div>
                                                <div class="input-group my-2 col-md-12">
                                                    <label for="" class="col-sm-5">Propios</label>
                                                    <input type="text" name="propios" class="form-control col-sm-7">
                                                </div>
                                                <div class="input-group my-2 col-md-12">
                                                    <label for="" class="col-sm-5">SGP</label>
                                                    <input type="text" name="sgp" class="form-control col-sm-7">
                                                </div>
                                                <div class="input-group my-2 col-md-12">
                                                    <button class="btn btn-primary" type="submit">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>

        window.onload = function () {
            $('#tabla_bpin_actividades').hide();
        };

        $('#tabla_Proy').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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

        });

        const bpins = @json($bpins);
        const vigencia_id = @json($vigencia->id);
        const show_proyecto = cod_proyecto =>{
            $('#tabla_bpins').hide();
            $('#tabla_bpin_actividades').show();
            $('#tbody-actividades').empty();
            $('#input-cod-proyecto').val(cod_proyecto);
            bpins.filter(r => r.cod_proyecto == cod_proyecto).forEach(e =>{
                if (e.rubro != "No") var button = e.rubro+`<br> `+e.fuente+`<br> Dinero Asignado: `+e.rubro_find[0].propios.toLocaleString();
                else {
                    var button = `<b>No hay rubros de inversión disponibles para asignar.</b>`;
                }
                $('#tbody-actividades').append(`
                <tr>
                    <td>${e.cod_actividad}</td>
                    <td>${e.actividad}</td>
                    <td>${button}</td>
                    <td class="text-center"><a href="/presupuesto/actividad/${e.id}/${vigencia_id}" class="btn btn-primary"><i class="fa fa-info-circle"></i></a></td>
                </tr>
            `);
            });

        }


        const show_bpins = ()  =>{
            $('#tabla_bpins').show();
            $('#tabla_bpin_actividades').hide();
        }

        function validarFormulario(id, rol, fecha, valor, control ) {
            console.log('vu', [id, rol, fecha, valor, control ]);

            if(valor != 0){
                if(valor > control){
                    var opcion = confirm("El valor asignado es superior al valor de control, esta seguro de enviar el CDP?");
                    if (opcion == true) {
                        window.location.href = "/administrativo/cdp/"+id+"/"+rol+"/"+fecha+"/"+valor+"/3";
                    }
                }else window.location.href = "/administrativo/cdp/"+id+"/"+rol+"/"+fecha+"/"+valor+"/3";
            }else{
                confirm("El Cdp esta en 0 no sirve continuar");
            }
        }


        $(document).ready(function() {
            $('#tabla_rubrosCdp').DataTable( {
                responsive: true,
                "searching": false,
                "ordering" : false
            } );

            $('#tablaRegistros').DataTable( {
                responsive: true
            } );

            $('#tablaCDPs').DataTable( {
                responsive: true
            } );

            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });
            
        } );
    </script>
@stop