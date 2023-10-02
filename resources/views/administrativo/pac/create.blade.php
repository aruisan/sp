@extends('layouts.dashboard')
@section('titulo')
    Nuevo PAC
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Nuevo PAC</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/pac') }}"><i class="fa fa-backward"></i>&nbsp;Volver a PAC</a>
        </li>
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">Nuevo PAC</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <div class="table-responsive">
                    <div class="col-md-2 align-self-center">
                    </div>
                    <div class="col-md-8 align-self-center">
                        <center>
                            <h3 class="box-title">Seleccione el Rubro Presupuestal:</h3>
                        </center>
                        <br>
                        <table class="display" id="tabla_Productos">
                            <thead>
                            <tr>
                                <th class="text-center hidden"><i class="fa fa-hashtag"></i></th>
                                <th class="text-center"><i class="fa fa-hashtag"></i></th>
                                <th class="text-center hidden">Codigo</th>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center hidden">Valor Inicial</th>
                                <th class="text-center">Valor Inicial</th>
                                <th class="text-center">Valor Actual</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($Rubros as $key => $data)
                                <tr onclick="ver('col{{$data['id_rubro']}}','Code{{$data['codigo']}}','Obj{{$data['name']}}','Ini{{$data['valor']}}','Act{{$data['valor_disp']}}');" style="cursor:pointer">
                                    <td id="col{{$data['id_rubro']}}" class="text-center hidden">{{ $data['id_rubro'] }}</td>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td id="Code{{$data['codigo']}}" class="text-center hidden">{{$data['codigo']}}</td>
                                    <td class="text-center">{{$data['codigo']}}</td>
                                    <td id="Obj{{$data['name']}}" class="text-center">{{ $data['name']}}</td>
                                    <td class="text-center">$<?php echo number_format($data['valor'],0) ?></td>
                                    <td id="Ini{{$data['valor']}}" class="text-center hidden">{{$data['valor']}}</td>
                                    <td id="Act{{$data['valor_disp']}}" class="text-center">$<?php echo number_format($data['valor_disp'],0) ?></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <form class="form-valide" style="display: none" action="{{url('/administrativo/pac')}}" method="POST" enctype="multipart/form-data" id="form">
                            <center>
                                <h3 class="box-title">Proyección Nuevo Plan</h3>
                            </center>
                            <br>
                            {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="col-md-6 align-self-center">
                                    <div class="col-md-12 align-self-center">
                                        <div class="form-group">
                                            <br><br>
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Rubro</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="Objeto" id="Objeto" disabled>
                                                <input type="hidden"  class="form-control" name="IdRub" id="IdRub">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Nombre</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="actual" id="actual" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Apropiación Presupuestal</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="minima" id="minima" disabled>
                                                <input type="hidden" class="form-control" name="apropiacion" id="apropiacion">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Sin Situación de Fondos</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="fondos" id="fondos" onchange="fondo()" value="0" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Pac Aprobado</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="aprobado" id="aprobado" value="0" disabled>
                                                <input type="hidden" class="form-control" name="apro" id="apro" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">Rezago</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" onchange="rezago()" name="rez" id="rez" value="0" min="0" required >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right">A Distribuir</label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="distri" id="distri" value="0" disabled>
                                                <input type="hidden" class="form-control" name="distri2" id="distri2" value="0">
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
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="enero" id="enero" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>2</td>
                                            <td>Febrero</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="febrero" id="febrero" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>3</td>
                                            <td>Marzo</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="marzo" id="marzo" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>4</td>
                                            <td>Abril</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="abril" id="abril" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>5</td>
                                            <td>Mayo</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="mayo" id="mayo" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>6</td>
                                            <td>Junio</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="junio" id="junio" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>7</td>
                                            <td>Julio</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="julio" id="julio" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>8</td>
                                            <td>Agosto</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="agosto" id="agosto" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>9</td>
                                            <td>Septiembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="septiembre" id="septiembre" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>10</td>
                                            <td>Octubre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="octubre" id="octubre" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>11</td>
                                            <td>Noviembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="noviembre" id="noviembre" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>12</td>
                                            <td>Diciembre</td>
                                            <td><input type="number" step="any" style="text-align: center" class="form-control" onchange="distribucion()" name="diciembre" id="diciembre" value="0" min="0"></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td colspan="2">Total Distribuido</td>
                                            <td>
                                                <input type="number" style="text-align: center" class="form-control" onchange="distribucion()" name="total" id="total" value="0" disabled>
                                                <input type="hidden" class="form-control" name="tot" id="tot" value="0">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12 ml-auto">
                                        <br>
                                        <center>
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Guardar</button>
                                        </center>
                                    </div>
                                </div>
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

        function ver(col, Obj, Inicial, Actual){
            content = document.getElementById(col);
            var Obj = document.getElementById(Obj);
            var Inicial = document.getElementById(Inicial);
            var Actual = document.getElementById(Actual);
            var data = content.innerHTML;

            if (data) {
                $("#form").show();
                $("#Objeto").val(Obj.innerHTML);
                $("#actual").val(Inicial.innerHTML);
                $("#minima").val(Actual.innerHTML);
                $("#apropiacion").val(Actual.innerHTML);
                $("#aprobado").val(Actual.innerHTML);
                $("#distri").val(Actual.innerHTML);
                $("#distri2").val(Actual.innerHTML);
                $("#IdRub").val(content.innerHTML);
                var div = parseInt(Actual.innerHTML) / 12;
                $("#total").val(Actual.innerHTML);
                $("#tot").val(Actual.innerHTML);
                $("#enero").val(div);
                $("#febrero").val(div);
                $("#marzo").val(div);
                $("#abril").val(div);
                $("#mayo").val(div);
                $("#junio").val(div);
                $("#julio").val(div);
                $("#agosto").val(div);
                $("#septiembre").val(div);
                $("#octubre").val(div);
                $("#noviembre").val(div);
                $("#diciembre").val(div);
            } else {
                $("#form").hide();
            }
        }
        
        function fondo() {
            var dinero = document.getElementById("minima").value;
            var fondo = document.getElementById("fondos").value;
            var result = dinero - fondo;
            var div2 = result/12;
            $("#aprobado").val(result);
            $("#apro").val(result);
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