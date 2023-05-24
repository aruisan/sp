@extends('layouts.dashboard')
@section('titulo') Información de la Actividad {{ $bpin->cod_actividad }} @stop
@section('content')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>{{ $bpin->cod_actividad }} - {{ $bpin->actividad }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar"><a class="nav-link" href="{{ url('/presupuesto/actividades/'.$vigencia->id) }}">Volver a Actividades</a></li>
                <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Actividad {{ $bpin->cod_actividad }}</a></li>
                @if(auth()->user()->roles->first()->id != 2)
                    <li class="hidden">
                        <a class="nav-item dropdown-toggle" data-toggle="dropdown" href="#">Acciones<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a data-toggle="modal" data-target="#adicion" class="btn btn-drop text-left">Adición</a>
                            </li>
                            <li>
                                <a data-toggle="modal" data-target="#reduccion" class="btn btn-drop  text-left">Reducción</a>
                            </li>
                            <li>
                                <a data-toggle="modal" data-target="#credito" class="btn btn-drop  text-left">Credito</a>
                            </li>
                        </ul>
                    </li>
                @endif
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
                                @else
                                    <br><div class="alert alert-danger"><center>La actividad no ha sido asignada a una dependencia</center></div><br>
                                @endif
                                @if($cdps->count() > 0)
                                    <br>
                                    <hr>
                                    <center>
                                        <h3>CDPs ASIGNADOS</h3>
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
                                            @foreach($cdps as $data)
                                                    <tr class="text-center">
                                                        <td>{{ $data }}</td>
                                                        <td>{{ $data }}</td>
                                                        <td>{{ $data }}</td>
                                                        <td>{{ $data }}</td>
                                                        <td>$ <?php echo number_format($data->valor,0);?>.00</td>
                                                        <td>$ <?php echo number_format($data->valor,0);?>.00</td>
                                                    </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <br><div class="alert alert-danger"><center>La actividad no ha sido asignada a una dependencia</center></div><br>
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

            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });


        } );
    </script>
@stop