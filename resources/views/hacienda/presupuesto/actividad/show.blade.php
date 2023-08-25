@extends('layouts.dashboard')
@section('titulo') Información de la Actividad {{ $bpin->cod_actividad }} @stop
@section('content')
    @include('modal.adicionActividad')
    @include('modal.reduccionActividad')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>{{ $bpin->cod_actividad }} - {{ $bpin->actividad }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar"><a class="nav-link" href="{{ url('/presupuesto/actividades/'.$vigencia->id) }}">Volver a Proyectos</a></li>
                <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Actividad {{ $bpin->cod_actividad }}</a></li>
                <li class="dropdown">
                    <a class="nav-item dropdown-toggle" data-toggle="dropdown" href="#">Acciones<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a data-toggle="modal" data-target="#adicion" class="btn btn-drop text-left">Adición</a></li>
                        <!-- <li><a data-toggle="modal" data-target="#reduccion" class="btn btn-drop  text-left">Reducción</a></li> -->
                    </ul>
                </li>
            </ul>
        </div>
        <div class="col-lg-12" id="prog">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row ">
                        <br>
                        <div class="col-sm-9"><h4><b>Proyecto: {{ $bpin->nombre_proyecto }}</b></h4></div>
                        <div class="col-sm-3"><h4><b>Codigo del Proyecto:</b>&nbsp;{{ $bpin->cod_proyecto }}</h4></div>
                        <br><br>
                        <div class="form-validation">
                            <form class="form" action="">
                                <hr>
                                {{ csrf_field() }}
                                <div class="col-lg-6">
                                    <table class="table-responsive" style="width: 100%">
                                        <tbody class="text-center">
                                        <tr>
                                            <td><b>Actividad:</b></td>
                                            <td>{{ $bpin->actividad }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table-responsive" style="width: 100%">
                                        <tbody class="text-center">
                                        <tr>
                                            @if($bpin->rubroFind->count() > 0)
                                                <td><b>Saldo:</b></td>
                                                <td>$<?php echo number_format($bpin->rubroFind->sum('saldo'),0) ?></td>
                                            @endif
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($bpin->rubroFind->count() > 0)
                                    <br>
                                    <hr>
                                    <center>
                                        <h3>DEPENDENCIAS ASIGNADAS</h3>
                                    </center>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tablaRegistros">
                                            <thead>
                                            <tr>
                                                <th class="text-center">Id</th>
                                                <th class="text-center">Dependencia</th>
                                                <th class="text-center">Fuente</th>
                                                <th class="text-center">Rubro</th>
                                                <th class="text-center">Valor Inicial</th>
                                                <th class="text-center">Saldo</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bpin->rubroFind as $data)
                                                @if($data->vigencia_id == $vigencia->id)
                                                    <tr class="text-center">
                                                        <td>{{ $data->id }}</td>
                                                        <td>{{ $data->rubro->dependencias->name }}</td>
                                                        <td>{{ $data->rubro->fontRubro->sourceFunding->code }} - {{ $data->rubro->fontRubro->sourceFunding->description }}</td>
                                                        <td>{{ $data->rubro->fontRubro->rubro->cod }} - {{ $data->rubro->fontRubro->rubro->name }}</td>
                                                        <td>$ <?php echo number_format($data->propios,0);?>.00</td>
                                                        <td>$ <?php echo number_format($data->saldo,0);?>.00</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($cdps->count() > 0)
                                        <br>
                                        <hr>
                                        <center>
                                            <h3>CDPs ASIGNADOS</h3>
                                        </center>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="tablaCDPs">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Objeto</th>
                                                    <th class="text-center">Tipo</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Valor</th>
                                                    <th class="text-center">Saldo</th>
                                                    <th class="text-center">Dependencia</th>
                                                    <th class="text-center">Ver</th>
                                                    <th class="text-center">PDF</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($cdps as $data)
                                                    <tr class="text-center">
                                                        <td>{{ $data->cdp->code }}</td>
                                                        <td>{{ $data->cdp->name }}</td>
                                                        <td>{{ $data->cdp->tipo }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-pill badge-danger">
                                                                @if($data->cdp->jefe_e == "0")
                                                                    Pendiente
                                                                @elseif($data->cdp->jefe_e == "1")
                                                                    Rechazado
                                                                @elseif($data->cdp->jefe_e == "2")
                                                                    Anulado
                                                                @elseif($data->cdp->jefe_e == "3")
                                                                    Aprobado
                                                                @else
                                                                    En Espera
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>$ <?php echo number_format($data->cdp->valor,0);?>.00</td>
                                                        <td>$ <?php echo number_format($data->cdp->saldo,0);?>.00</td>
                                                        <td>{{ $data->cdp->dependencia->name }}</td>
                                                        <td class="text-center">
                                                            <a href="{{ url('administrativo/cdp/'.$vigencia->id.'/'.$data->cdp->id) }}" title="Ver CDP" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($data->cdp->jefe_e == "3")
                                                                <a href="{{ url('administrativo/cdp/pdf/'.$data->cdp->id.'/'.$vigencia->id) }}" target="_blank" title="File" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <br><div class="alert alert-danger"><center>La actividad no tiene CDPs asignados</center></div><br>
                                    @endif
                                @else
                                    <br><br>
                                    <div class="alert alert-danger text-center">La actividad no ha sido asignada a una dependencia</div>
                                @endif
                            </form>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>

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

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function findFontAdd(id, mov){
            $("#cargando").show();
            $.ajax({
                method: "POST",
                url: "/presupuesto/findFontDep/",
                data: { "id": id, "mov": mov, "tipo": '2', "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(datos) {
                document.getElementById("valueFont").innerHTML = formatter.format(datos["valor"]);
                if(datos["valor"] != 0) document.getElementById("movRubroID").value = datos["id"];
                else document.getElementById("movRubroID").value = 0;
                $("#divValues").show();
                $("#buttonEnviarAdd").show();
                $("#cargando").hide();
            }).fail(function() {
                $("#divValues").hide();
                $("#buttonEnviarAdd").hide();
                $("#cargando").hide();
                toastr.warning('OCURRIO UN ERROR AL REALIZAR LA BUSQUEDA DE LA FUENTE, INTENTE NUEVAMENTE POR FAVOR.');
            });
        }

        function findFontRed(id, mov){
            $("#cargandoRed").show();
            $.ajax({
                method: "POST",
                url: "/presupuesto/findFontDep/",
                data: { "id": id, "mov": mov, "tipo": '2', "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(datos) {
                console.log(datos["valor"], datos);
                document.getElementById("valueFontRed").innerHTML = formatter.format(datos["valor"]);
                if(datos["valor"] != 0) document.getElementById("movRubroIDRed").value = datos["id"];
                else document.getElementById("movRubroIDRed").value = 0;
                $("#divValuesRed").show();
                $("#buttonEnviarRed").show();
                $("#cargandoRed").hide();
            }).fail(function() {
                $("#divValuesRed").hide();
                $("#buttonEnviarRed").hide();
                $("#cargandoRed").hide();
                toastr.warning('OCURRIO UN ERROR AL REALIZAR LA BUSQUEDA DE LA FUENTE, INTENTE NUEVAMENTE POR FAVOR.');
            });
        }
    </script>
@stop