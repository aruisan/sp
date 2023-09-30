@extends('layouts.dashboard')
@section('titulo')
    PAC - {{ $data[0]['rubro']['codigo'] }} - {{ $data[0]['rubro']['name'] }}
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>{{ $data[0]['rubro']['codigo'] }} - {{ $data[0]['rubro']['name'] }}</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/pac') }}"><i class="fa fa-backward"></i>&nbsp;Volver a PAC</a>
        </li>
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">PAC</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <div class="table-responsive">
                    <div class="col-md-2 align-self-center">
                    </div>
                    <div class="col-md-8 align-self-center">
                        <form class="form-valide"  action="{{url('/administrativo/pac/'.$data[0]['pac']->id)}}" method="POST" enctype="multipart/form-data" id="form">
                            {!! method_field('PUT') !!}
                            {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="col-md-6 align-self-center">
                                    <div class="col-md-12 align-self-center">
                                        <br><br><br><br><br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Apropiación Presupuestal</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" value="{{ $data[0]['rubro']['valor'] }}" name="minima" id="minima" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Sin Situación de Fondos</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="fondos" id="fondos" onchange="fondo()" value="{{ $data[0]['pac']->situacion_fondos }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Pac Aprobado</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="aprobado" id="aprobado" value="{{ $data[0]['pac']->aprobado }}" disabled>
                                                <input type="hidden" class="form-control" name="apro" id="apro" value="{{ $data[0]['pac']->aprobado }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Rezago</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" onchange="rezago()" name="rez" id="rez" value="{{ $data[0]['pac']->rezago }}" min="0" required >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">A Distribuir</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="distri" id="distri" value="{{ $data[0]['pac']->distribuir }}" disabled>
                                                <input type="hidden" class="form-control" name="distri2" id="distri2" value="{{ $data[0]['pac']->distribuir }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <table class="table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th class="text-center"><i class="fa fa-hashtag"></i></th>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Valor</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="text-center">
                                            <td>1</td>
                                            <td>Enero</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Enero" id="enero" value="{{ $meses[0]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>2</td>
                                            <td>Febrero</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Febrero" id="febrero" value="{{ $meses[1]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>3</td>
                                            <td>Marzo</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Marzo" id="marzo" value="{{ $meses[2]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>4</td>
                                            <td>Abril</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Abril" id="abril" value="{{ $meses[3]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>5</td>
                                            <td>Mayo</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Mayo" id="mayo" value="{{ $meses[4]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>6</td>
                                            <td>Junio</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Junio" id="junio" value="{{ $meses[5]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>7</td>
                                            <td>Julio</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Julio" id="julio" value="{{ $meses[6]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>8</td>
                                            <td>Agosto</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Agosto" id="agosto" value="{{ $meses[7]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>9</td>
                                            <td>Septiembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Septiembre" id="septiembre" value="{{ $meses[8]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>10</td>
                                            <td>Octubre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Octubre" id="octubre" value="{{ $meses[9]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>11</td>
                                            <td>Noviembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Noviembre" id="noviembre" value="{{ $meses[10]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>12</td>
                                            <td>Diciembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="Diciembre" id="diciembre" value="{{ $meses[11]->valor }}" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td colspan="2">Total Distribuido</td>
                                            <td>
                                                <input type="number" style="text-align: center" class="form-control" onchange="distribucion()" name="total" id="total" value="{{ $data[0]['pac']->total_distri }}" disabled>
                                                <input type="hidden" class="form-control" name="tot" id="tot" value="{{ $data[0]['pac']->total_distri }}">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <br><br><br>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Actualizar</button>
                            </div>
                        </form>
                        <form action="{{ asset('/administrativo/pac/'.$data[0]['pac']->id) }}" method="POST" class="form-valide">
                            <div class="col-md-6 align-self-center">
                                {!! method_field('DELETE') !!}
                                {{ csrf_field() }}
                                <br><br><br>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i>&nbsp;&nbsp;Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>

        $('#tabla_Productos').DataTable( {
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
            "pageLength": 5,
            responsive: "true"
        });
        $(document).ready(function() {
            var table = $('#tabla_Productos').DataTable();

            $('#tabla_Productos tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            } );

            $('#button').click( function () {
                table.row('.selected').remove().draw( false );
            } );
        } );

        function fondo() {
            var dinero = document.getElementById("minima").value;
            var fondo = document.getElementById("fondos").value;
            var res = dinero - fondo;
            var rezago = document.getElementById("rez").value;
            var result = parseInt(res) - parseInt(rezago);
            var div2 = result/12;
            $("#aprobado").val(res);
            $("#apro").val(res);
            $("#distri").val(result);
            $("#distri2").val(result);
            $("#total").val(result);
            $("#tot").val(result);
            $("#enero").val(div2);
            $("#febrero").val(div2);
            $("#marzo").val(div2);
            $("#abril").val(div2);
            $("#mayo").val(div2);
            $("#junio").val(div2);
            $("#julio").val(div2);
            $("#agosto").val(div2);
            $("#septiembre").val(div2);
            $("#octubre").val(div2);
            $("#noviembre").val(div2);
            $("#diciembre").val(div2);
        }

        function rezago() {
            var dinero = document.getElementById("aprobado").value;
            var rez = document.getElementById("rez").value;
            var result = dinero - rez;
            var div2 = result/12;
            $("#distri").val(result);
            $("#distri2").val(result);
            $("#total").val(result);
            $("#tot").val(result);
            $("#enero").val(div2);
            $("#febrero").val(div2);
            $("#marzo").val(div2);
            $("#abril").val(div2);
            $("#mayo").val(div2);
            $("#junio").val(div2);
            $("#julio").val(div2);
            $("#agosto").val(div2);
            $("#septiembre").val(div2);
            $("#octubre").val(div2);
            $("#noviembre").val(div2);
            $("#diciembre").val(div2);
        }

        function distribucion() {
            var distribuir = document.getElementById("distri2").value;
            var enero = document.getElementById("enero").value;
            var febrero = document.getElementById("febrero").value;
            var marzo = document.getElementById("marzo").value;
            var abril = document.getElementById("abril").value;
            var mayo = document.getElementById("mayo").value;
            var junio = document.getElementById("junio").value;
            var julio = document.getElementById("julio").value;
            var agosto = document.getElementById("agosto").value;
            var septiembre = document.getElementById("septiembre").value;
            var octubre = document.getElementById("octubre").value;
            var noviembre = document.getElementById("noviembre").value;
            var diciembre = document.getElementById("diciembre").value;

            var total = parseInt(enero) + parseInt(febrero) + parseInt(marzo) + parseInt(abril) + parseInt(mayo) + parseInt(junio) + parseInt(julio) + parseInt(agosto) + parseInt(septiembre) + parseInt(octubre) + parseInt(noviembre) + parseInt(diciembre);
            var distribuido = parseInt(distribuir) - total;
            $("#total").val(total);
            $("#tot").val(total);
            $("#distri").val(distribuido);
        }
    </script>
@stop