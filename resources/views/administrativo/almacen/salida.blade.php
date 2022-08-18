@extends('layouts.dashboard')
@section('titulo')
    Nuevo Comprobante de Salida
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Nuevo Comprobante de Salida</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#tabHome">Comprobante de Salida</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <div class="table-responsive">
                    <div class="col-md-6 align-self-center">
                        <center>
                            <h3 class="box-title">Seleccione el producto correspondiente:</h3>
                        </center>

                        <br>
                        <table class="display" id="tabla_Productos">
                            <thead>
                            <tr>
                                <th class="text-center hidden"><i class="fa fa-hashtag"></i></th>
                                <th class="text-center"><i class="fa fa-hashtag"></i></th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Cantidad Actual</th>
                                <th class="text-center">Cantidad Minima</th>
                                <th class="text-center hidden">tipo</th>
                                <th class="text-center">Tipo</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($productos as $key => $data)
                                <tr onclick="ver('col{{$data->id}}','Obj{{$data->nombre}}','Actual{{$data->cant_actual}}','Minima{{$data->cant_minima}}','Tipo{{$data->tipo}}');" style="cursor:pointer">
                                    <td id="col{{$data->id}}" class="text-center hidden">{{ $data->id }}</td>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td id="Obj{{$data->nombre}}" class="text-center">{{ $data->nombre }}</td>
                                    <td id="Actual{{$data->cant_actual}}" class="text-center"><?php echo number_format($data->cant_actual,0) ?></td>
                                    <td id="Minima{{$data->cant_minima}}" class="text-center"><?php echo number_format($data->cant_minima,0) ?></td>
                                    <td id="Tipo{{$data->tipo}}" class="text-center hidden">{{$data->tipo}}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($data->tipo == "0")
                                                Consumo
                                            @else($data->tipo == "1")
                                                Devolutivo
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 align-self-center" style="display: none" id="consumo" name="consumo">
                        <form class="form-valide" action="{{url('/administrativo/salida')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="Objeto" id="Objeto" disabled>
                                        <input type="hidden"  class="form-control" name="IdProd" id="IdProd">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Actual</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="actual" id="actual" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Minima</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="minima" id="minima" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad a Retirar<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="salida" id="salida" value="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Valor Unidad<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="valUni" id="ValUni" value="0" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Descripción de Salida<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <textarea type="text" style="text-align: center" class="form-control" name="descripcion" id="descripcion" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto">
                                    <br>
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Realizar Salida</button>
                                    </center>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 align-self-center" style="display: none" id="devolutivo" name="devolutivo">
                        <form class="form-valide" action="{{url('/administrativo/salida')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="ObjetoD" id="ObjetoD" disabled>
                                        <input type="hidden"  class="form-control" name="IdProdD" id="IdProdD">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Actual</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="actual" id="actualD" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad Minima</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="minima" id="minimaD" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Cantidad a Retirar<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="salida" id="salidaD" value="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Valor Unidad<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="valUni" id="ValUniD" value="0" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 align-self-center">
                                <br>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right">Descripción de Salida<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <textarea type="text" style="text-align: center" class="form-control" name="descripcion" id="descripcionD" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto">
                                    <br>
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Realizar Salida</button>
                                    </center>
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

        function ver(col, Obj, Actual, Minima, Tipo){
            content = document.getElementById(col);
            var Obj = document.getElementById(Obj);
            var Tipo = document.getElementById(Tipo);
            var Actual = document.getElementById(Actual);
            var Min = document.getElementById(Minima);
            var data = content.innerHTML;

            if (data) {
                if(Tipo.innerHTML == 0){
                    $("#devolutivo").hide();
                    $("#consumo").show();
                    $("#Objeto").val(Obj.innerHTML);
                    $("#actual").val(Actual.innerHTML);
                    $("#minima").val(Min.innerHTML);
                    $("#IdProd").val(content.innerHTML);
                } else {
                    $("#consumo").hide();
                    $("#devolutivo").show();
                    $("#ObjetoD").val(Obj.innerHTML);
                    $("#actualD").val(Actual.innerHTML);
                    $("#minimaD").val(Min.innerHTML);
                    $("#IdProdD").val(content.innerHTML);
                }
            } else {
                $("#consumo").hide();
                $("#devolutivo").hide();
            }

        }
    </script>
@stop