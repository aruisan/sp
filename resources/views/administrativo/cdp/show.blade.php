@extends('layouts.dashboard')
@section('titulo') Información del CDP {{ $cdp->code }} @stop
@section('content')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Información del CDP</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/cdp/'.$cdp->vigencia_id) }}">Volver a CDP'S</a></li>
                <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">CDP {{ $cdp->code }}</a></li>
                <li class="nav-item "><a class="tituloTabs" data-toggle="tab" href="#rubros">Dinero en Rubros</a></li>
                @if($cdp->secretaria_e == "3" and $cdp->alcalde_e == "3" and $cdp->jefe_e == "3")
                    <li class="nav-item"><a class="nav-link" href="{{ url('administrativo/cdp/pdf/'.$cdp->id.'/'.$cdp->vigencia_id) }}" target="_blank" title="PDF"><i class="fa fa-file-pdf-o"></i></a></li>
                @elseif($cdp->secretaria_e == "3")
                    <li class="nav-item"><a class="nav-link" href="{{ url('administrativo/cdp/pdfBorrador/'.$cdp->id.'/'.$cdp->vigencia_id) }}" target="_blank" title="BORRADOR DE PDF"><i class="fa fa-file-pdf-o"></i></a></li>
                @endif
            </ul>
        </div>
        <div class="col-lg-12" id="prog">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row ">
                        @if($cdp->jefe_e == "1" and $cdp->motivo != null)
                            <div class="col-md-12 align-self-center">
                                <div class="alert alert-danger text-center">
                                    <center>
                                        <h4><b>Motivo del Rechazo del Jefe</b></h4>
                                    </center>
                                    <div class="text-center">
                                        {{ $cdp->motivo }}
                                    </div>
                                </div>
                            </div>
                        @elseif($cdp->alcalde_e == "1" and $cdp->motivo != null)
                            <div class="col-md-12 align-self-center">
                                <div class="alert alert-danger text-center">
                                    <center><h4><b>Motivo del Rechazo del Alcalde</b></h4></center>
                                    <div class="text-center">{{ $cdp->motivo }}</div>
                                </div>
                            </div>
                        @endif
                        <br>
                        <div class="col-sm-9"><h3>Objeto del CDP: {{ $cdp->name }}</h3></div>
                        <div class="col-sm-3"><h4><b>Número del CDP:</b>&nbsp;{{ $cdp->code }}</h4></div>
                        <br><br>
                        <div class="form-validation">
                            <form class="form" action="">
                                <hr>
                                {{ csrf_field() }}
                                <div class="col-lg-6">
                                    <table class="table-responsive" width="100%">
                                        <tbody class="text-center">
                                        <tr>
                                            <td><b>Dependencia:</b></td>
                                            <td><textarea class="text-center" style="border: none; resize: none;" disabled>{{ $cdp->dependencia->name }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td><b>Fecha de Creación:</b></td>
                                            <td>{{ $cdp->fecha }}</td>
                                        </tr>
                                        @if($cdp->secretaria_e == "3" and $cdp->jefe_e == "3")
                                            <tr>
                                                <td><b>Fecha de Envio por Secretaria:</b></td>
                                                <td>{{ $cdp->ff_secretaria_e }}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table-responsive" style="width: 100%">
                                        <tbody class="text-center">
                                        <tr>
                                            <td><b>Observación:</b></td>
                                            @if($cdp->observacion == null)
                                                <td><textarea class="text-center" style="border: none; resize: none;" disabled>No Aplica</textarea></td>
                                            @else
                                                <td><textarea class="text-center" style="border: none; resize: none;" disabled>{{ $cdp->observacion }}</textarea></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td><b>Saldo:</b></td>
                                            <td>$<?php echo number_format($cdp->saldo,0) ?></td>
                                        </tr>
                                        @if($cdp->secretaria_e == "3" and $cdp->jefe_e == "3")
                                            <tr>
                                                <td><b>Fecha de Finalización:</b></td>
                                                <td>{{ $cdp->ff_jefe_e }}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-12 text-center">
                                    @if($cdp->secretaria_e == "3" and $cdp->jefe_e == "0")
                                        <br><b>Fecha de Envio por Secretaria: {{ \Carbon\Carbon::parse($cdp->ff_secretaria_e)->format('d-m-Y') }}</b>
                                    @endif
                                    @if($cdp->alcalde_e == "3" and $cdp->jefe_e == "0")
                                        <br><b>Fecha de Envio por Alcalde: {{ \Carbon\Carbon::parse($cdp->ff_alcalde_e)->format('d-m-Y') }}</b>
                                    @endif
                                </div>
                            </form>
                            @if($cdp->secretaria_e == "0")
                                <div class="col-lg-12 text-center">
                                    <br><b><h4><b>VALOR DE CONTROL DEL CDP: $<?php echo number_format( $cdp->valueControl,0) ?></b></h4></b>
                                </div>
                            @endif
                            <div class="col-lg-12 text-center">
                                <br>
                                <b><h4><b>Valor del CDP</b></h4>
                                    @if($cdp->valor == 0)
                                        <h4><b>$<?php echo number_format( $cdp->rubrosCdpValor->sum('valor_disp'),0) ?></b></h4>
                                    @else
                                        <h4><b>$<?php echo number_format( $cdp->valor,0) ?></b></h4>
                                    @endif
                                </b>
                            </div>
                            <br>
                        </div>
                            @if($cdp->tipo == "Funcionamiento")
                            <!-- PROCESO DE SELECCION DEL RUBRO PARA EL CDP --->
                            <div class="col-md-12 align-self-center">
                                <br><br>
                                <hr>
                                <center>
                                    <h3>Rubros del CDP</h3>
                                </center>
                                <hr>
                                <div class="table-responsive">
                                    @if($cdp->rubrosCdp->count() == 0 )
                                        <div class="col-md-12 align-self-center">
                                            <div class="alert alert-danger text-center">
                                                El CDP no tiene rubros asignados. Desea borrar el CDP? &nbsp;
                                                <form action="{{ url('/administrativo/cdp/'.$cdp->vigencia_id.'/'.$cdp->id.'/delete') }}" method="post" class="form">
                                                    {!! method_field('DELETE') !!}
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Borrar CDP
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    <form action="{{url('/administrativo/rubrosCdp')}}" method="POST" class="form">
                                        {{ csrf_field() }}
                                        <table id="tabla_rubrosCdp" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th scope="col" class="text-center">Rubros</th>
                                                <th scope="col" class="text-center">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($cdp->jefe_e != "3")
                                                @if($cdp->rubrosCdp->count() == 0)
                                                    @if($rol == 2)
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td class="text-center">
                                                                <input type="hidden" name="cdp_id" value="{{ $cdp->id }}">
                                                                <select name="rubro_id[]" class="form-control" onchange="selectedRubro(this.value)" required>
                                                                    @foreach($infoRubro as $rubro)
                                                                        <option value="{{ $rubro['id_rubro'] }}">{{ $rubro['codigo'] }} - {{ $rubro['name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="text-center"></td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endif
                                            @for($i = 0; $i < $cdp->rubrosCdp->count(); $i++)
                                                @php($rubrosCdpData = $cdp->rubrosCdp[$i] )
                                                <tr>
                                                    <td class="text-center">

                                                    </td>
                                                    <td class="text-center">
                                                        <div class="col-lg-4">
                                                            <h4>
                                                                <b>{{ $rubrosCdpData->rubros->name }}</b>
                                                            </h4>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <h4>
                                                                @foreach($infoRubro as $infoR)
                                                                    @if($infoR['id_rubro'] == $rubrosCdpData->rubros->id)
                                                                        <b>Rubro: {{ $infoR['codigo'] }}</b>
                                                                    @endif
                                                                @endforeach
                                                            </h4>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            @php( $valorT = $rubrosCdpData->rubrosCdpValor->sum('valor') )
                                                            <h4>
                                                                <b>
                                                                    Valor:
                                                                    @if($rubrosCdpData->rubrosCdpValor->count() > 0)
                                                                        $<?php echo number_format( $rubrosCdpData->rubrosCdpValor->sum('valor') ,0) ?>
                                                                    @else
                                                                        $0.00
                                                                    @endif
                                                                </b>
                                                            </h4>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn-sm btn-success" onclick="ver('fuente{{$i}}')" ><i class="fa fa-arrow-down"></i></button>
                                                        @if($rol == 2)
                                                            @if($rubrosCdpData->rubrosCdpValor->count() == 0)
                                                                <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminar({{ $rubrosCdpData->id }})" ><i class="fa fa-trash-o"></i></button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr id="fuente{{$i}}" style="display: none">
                                                    <td style="vertical-align: middle">
                                                        <b>Fuentes del rubro {{ $rubrosCdpData->rubros->name }}</b>
                                                    </td>
                                                    <td>
                                                        <div class="col-lg-12">
                                                            @foreach($rubrosCdpData->rubros->fontsRubro as $fuentesRubro)
                                                                @if($cdp->jefe_e == "3")
                                                                    <div class="col-lg-6">
                                                                        <input type="hidden" name="fuente_id[]" value="{{ $fuentesRubro->id }}">
                                                                        <input type="hidden" name="cdp_id" value="{{ $cdp->id }}">
                                                                        <input type="hidden" name="rubros_cdp_id[]" value="{{ $rubrosCdpData->id }}">
                                                                        <li style="list-style-type: none;">
                                                                            {{ $fuentesRubro->sourceFunding->description }} :
                                                                            @foreach($fuentesRubro->dependenciaFont as $dep)
                                                                                @if($dep->dependencia_id == $user->dependencia_id)
                                                                                    $<?php echo number_format( $dep->saldo,0) ?>
                                                                                @endif
                                                                            @endforeach
                                                                        </li>
                                                                    </div>
                                                                @elseif($fuentesRubro->valor_disp != 0)
                                                                    <div class="col-lg-6">
                                                                        <input type="hidden" name="fuente_id[]" value="{{ $fuentesRubro->id }}">
                                                                        <input type="hidden" name="cdp_id" value="{{ $cdp->id }}">
                                                                        <input type="hidden" name="rubros_cdp_id[]" value="{{ $rubrosCdpData->id }}">
                                                                        <li style="list-style-type: none;">
                                                                            {{ $fuentesRubro->sourceFunding->description }} :
                                                                            @foreach($fuentesRubro->dependenciaFont as $dep)
                                                                                @if($dep->dependencia_id == $user->dependencia_id)
                                                                                    $<?php echo number_format( $dep->saldo,0) ?>
                                                                                @endif
                                                                            @endforeach
                                                                        </li>
                                                                    </div>
                                                                @endif
                                                                <div class="col-lg-6">
                                                                    @if($cdp->jefe_e == "3")
                                                                        Valor usado de {{ $fuentesRubro->sourceFunding->description}}
                                                                        @if($fuentesRubro->rubrosCdpValor->count() != 0)
                                                                            @foreach($fuentesRubro->rubrosCdpValor as  $valoresFR)
                                                                                @php($id_rubrosCdp = $rubrosCdpData->id )
                                                                                @if($valoresFR->cdp_id == $cdp->id)
                                                                                    <input type="hidden" name="rubros_cdp_valor_id[]" value="{{ $valoresFR->id }}">
                                                                                    @if($cdp->secretaria_e == "0")
                                                                                        <input type="number" required  name="valorFuenteUsar[]" id="id{{$fuentesRubro->font_id}}" class="valor{{ $valoresFR->rubrosCdp_id }}" value="{{ $valoresFR->valor }}" max="{{ $fuentesRubro->valor_disp }}" style="text-align: center">
                                                                                    @else
                                                                                        $<?php echo number_format( $valoresFR->valor,0) ?>
                                                                                    @endif
                                                                                @endif
                                                                            @endforeach
                                                                            @if($cdp->rubrosCdpValor->count() == 0)
                                                                                <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                                    @foreach($fuentesRubro->dependenciaFont as $dep)
                                                                                        @if($dep->dependencia_id == $user->dependencia_id)
                                                                                            <input type="hidden" name="fuenteDep_id[]" value="{{ $dep->id }}">
                                                                                            <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="0" max="{{ $fuentesRubro->saldo }}" style="text-align: center">
                                                                                        @endif
                                                                                    @endforeach
                                                                            @endif
                                                                        @else
                                                                            <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                            @foreach($fuentesRubro->dependenciaFont as $dep)
                                                                                @if($dep->dependencia_id == $user->dependencia_id)
                                                                                    <input type="hidden" name="fuenteDep_id[]" value="{{ $dep->id }}">
                                                                                    <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm valorFuenteUsar" value="0" max="{{ $fuentesRubro->saldo }}" style="text-align: center">
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    @elseif($fuentesRubro->valor_disp != 0)
                                                                        Valor usado de {{ $fuentesRubro->sourceFunding->description}}
                                                                        @foreach($fuentesRubro->dependenciaFont as $dep)
                                                                            @if($dep->dependencia_id == $user->dependencia_id)
                                                                                @if($dep->rubroCdpValor->count() != 0)
                                                                                    <!-- VALIDACION HERE -->
                                                                                    @foreach($fuentesRubro->rubrosCdpValor as  $valoresFR)
                                                                                        @php($id_rubrosCdp = $rubrosCdpData->id )
                                                                                        @if($valoresFR->cdp_id == $cdp->id)
                                                                                            <input type="hidden" name="rubros_cdp_valor_id[]" value="{{ $valoresFR->id }}">
                                                                                            @if($cdp->secretaria_e == "0")
                                                                                                <input type="number" required  name="valorFuenteUsar[]" id="id{{$fuentesRubro->font_id}}" class="valor{{ $valoresFR->rubrosCdp_id }}" value="{{ $valoresFR->valor }}" max="{{ $fuentesRubro->valor_disp }}" style="text-align: center">
                                                                                            @else
                                                                                                $<?php echo number_format( $valoresFR->valor,0) ?>
                                                                                            @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                    @if($cdp->rubrosCdpValor->count() == 0)
                                                                                        <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                                        <input type="hidden" name="fuenteDep_id[]" value="{{ $dep->id }}">
                                                                                        <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="0" max="{{ $fuentesRubro->saldo }}" style="text-align: center">
                                                                                    @endif
                                                                                @else
                                                                                    <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                                    <input type="hidden" name="fuenteDep_id[]" value="{{ $dep->id }}">
                                                                                    <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="0" max="{{ $fuentesRubro->saldo }}" style="text-align: center">
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <b>Valor Total</b>
                                                        <br>
                                                        <b>
                                                            @if($rubrosCdpData->rubrosCdpValor->count() > 0)
                                                                $<?php echo number_format( $rubrosCdpData->rubrosCdpValor->sum('valor') ,0) ?>
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </b>
                                                        <br>&nbsp;<br>
                                                        @if($cdp->jefe_e != "3" and $cdp->jefe_e != "2" and $cdp->secretaria_e != "3")
                                                            @if($rol == 2)
                                                                @if($rubrosCdpData->rubrosCdpValor->count() > 0)
                                                                    <b>Liberar Dinero</b>
                                                                    <br>
                                                                    <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarV({{ $rubrosCdpData->rubrosCdpValor->first()->rubrosCdp_id }})" ><i class="fa fa-money"></i></button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                            </tbody>
                                        </table><br>
                                        <center>
                                            @if($cdp->jefe_e == "2" and $cdp->secretaria_e == "3")
                                                <div class="col-md-12 align-self-center">
                                                    <div class="alert alert-danger text-center">
                                                        El CDP ha sido anulado.
                                                    </div>
                                                </div>
                                            @else
                                                @if($cdp->jefe_e != "3")
                                                    @if($rol == 2 and $cdp->secretaria_e != "3")
                                                        <button type="button" v-on:click.prevent="nuevaFilaPrograma" class="btn btn-success">Agregar Fila</button>
                                                        <button type="submit" class="btn btn-primary">Guardar Rubros</button>
                                                        @if($cdp->rubrosCdpValor->sum('valor_disp') > 0 )
                                                            <a class="btn btn-success" onclick="validarFormulario({{$cdp->id}}, {{$rol}}, '{{$fechaActual}}', {{$cdp->rubrosCdpValor->sum('valor_disp')}}, {{$cdp->valueControl}})">Enviar CDP</a>
                                                        @endif
                                                    @elseif($rol == 5)
                                                        @if($cdp->jefe_e == "2" and $cdp->secretaria_e == "3")
                                                            <div class="col-md-12 align-self-center">
                                                                <div class="alert alert-danger text-center">
                                                                    El CDP ha sido anulado.
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <a href="{{url('/administrativo/cdp/'.$cdp->id.'/'.$rol.'/'.$fechaActual.'/'.$cdp->valor.'/3')}}" class="btn btn-danger">
                                                                    Enviar al Jefe
                                                                </a>
                                                                <a data-toggle="modal" data-target="#observacionCDP" class="btn btn-success">
                                                                    Rechazar
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @elseif($rol == 3)
                                                        @if($cdp->rubrosCdp->count() > 0 )
                                                            <a href="{{url('/administrativo/cdp/'.$cdp->id.'/'.$rol.'/'.$fechaActual.'/'.$cdp->rubrosCdpValor->sum('valor_disp').'/3')}}" class="btn btn-danger">
                                                                Finalizar CDP
                                                            </a>
                                                            <a data-toggle="modal" data-target="#observacionCDP" class="btn btn-success">
                                                                Rechazar
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        </center>
                                    </form>
                                </div>
                                @if($cdp->jefe_e == 3 and $cdp->cdpsRegistro->count() >= 1)
                                    <br><br>
                                    <hr>
                                    <center>
                                        <h3>Registros Asignados al CDP</h3>
                                    </center>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tablaRegistros">
                                            <thead>
                                            <tr>
                                                <th class="text-center">Id</th>
                                                <th class="text-center">Nombre</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Valor Inicial</th>
                                                <th class="text-center">Valor Disponible</th>
                                                <th class="text-center">Ver</th>
                                                <th class="text-center">PDF</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($cdp->cdpsRegistro as $data)
                                                <tr class="text-center">
                                                    <td>{{ $data->registro->code }}</td>
                                                    <td>{{ $data->registro->objeto }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-pill badge-danger">
                                                            @if($data->registro->secretaria_e == "0")
                                                                Pendiente
                                                            @elseif($data->registro->secretaria_e == "1")
                                                                Rechazado
                                                            @elseif($data->registro->secretaria_e == "2")
                                                                Anulado
                                                            @else
                                                                Aprobado
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>$ <?php echo number_format($data->registro->valor,0);?>.00</td>
                                                    <td>$ <?php echo number_format( $data->registro->saldo,0);?>.00</td>
                                                    <td class="text-center">
                                                        <a href="{{ url('administrativo/registros/show',$data->registro_id) }}" title="Ver Registro" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($data->registro->secretaria_e == "3")
                                                            <a href="{{ url('administrativo/registro/pdf/'.$data->registro_id.'/'.$cdp->vigencia_id) }}" title="Ver Archivo" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @elseif($cdp->jefe_e == 3)
                                    <br><div class="alert alert-danger"><center>El CDP no tiene registros asignados</center></div><br>
                                @endif
                                @if($cdp->jefe_e == "3" and $cdp->secretaria_e == "3" and $cdp->saldo == $cdp->valor and $cdp->cdpsRegistro->count() == 0)

                                    <form action="{{url('/administrativo/cdp/'.$cdp->id.'/anular/'.$cdp->vigencia_id)}}" method="POST" class="form">
                                        {{method_field('POST')}}
                                        {{ csrf_field() }}
                                        <div class="row text-center">
                                            <button class="btn btn-success text-center" type="submit" title="Al anular el CDP se retorna el dinero al rubro">Anular CDP</button>
                                        </div>
                                    </form>

                                @endif
                                </div>
                            @else
                                <!-- CDP DE INVERSION -->
                                <div class="col-md-12 align-self-center">
                                    @if($cdp->bpinsCdpValor->count() == 0 )
                                        <div class="col-md-12 align-self-center">
                                            <div class="alert alert-danger text-center">
                                                El CDP no tiene una actividad asiganda. Desea borrar el CDP? &nbsp;
                                                <form action="{{ url('/administrativo/cdp/'.$cdp->vigencia_id.'/'.$cdp->id.'/delete') }}" method="post" class="form">
                                                    {!! method_field('DELETE') !!}
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Borrar CDP
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                        @if($cdp->jefe_e != "3")
                                            @if($cdp->bpinsCdpValor->count() == 0)
                                                @if($rol == 2)
                                                    <form action="{{url('/administrativo/cdp/'.$cdp->id.'/'.$cdp->vigencia_id.'/asignActividad')}}" method="POST" class="form text-center">
                                                        {{ csrf_field() }}
                                                        <br><br><hr><center><h3>SELECCIONE EL PROYECTO</h3></center><hr>
                                                        <div class="table-light">
                                                            <input type="hidden" name="cdp_id" value="{{ $cdp->id }}">
                                                            <table class="table table-borderless table-hover">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center">Cod.</th>
                                                                    <th class="text-center">Nombre</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                @foreach($unicoBpins as $item)
                                                                    <tr onclick="showActividades({{$item->cod_proyecto}})" style="cursor: pointer">
                                                                        <td>{{ $item->cod_proyecto }}</td>
                                                                        <td>{{ $item->nombre_proyecto }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                        <div id="actividades" style="display: none">
                                                            <hr><center><h3>ACTIVIDADES</h3></center><hr>
                                                            <table class="table table-borderless">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center">Cod.</th>
                                                                    <th class="text-center">Nombre</th>
                                                                    <th class="text-center">Dinero Disp</th>
                                                                    <th class="text-center">Dinero a Usar</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="tbody_actividades">
                                                                </tbody>
                                                            </table>

                                                            <button type="submit" class="btn btn-success">ENVIAR CDP</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            @else
                                                <br><br><hr><center><h3>PROYECTO SELECCIONADO: <br> <br> {{$cdp->bpinsCdpValor[0]->actividad->cod_proyecto}} - {{$cdp->bpinsCdpValor[0]->actividad->nombre_proyecto}} </h3></center><hr>
                                                <hr><center><h3>ACTIVIDADES:</h3></center><hr>
                                                <table class="table table-borderless">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">Cod.</th>
                                                        <th class="text-center">Nombre</th>
                                                        <th class="text-center">Dinero Usado</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($cdp->bpinsCdpValor as $item)
                                                            <tr>
                                                                <td>{{$item->actividad->cod_actividad}}</td>
                                                                <td>{{$item->actividad->actividad}}</td>
                                                                <td>$<?php echo number_format( $item->valor ,0) ?></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @if($cdp->jefe_e == "1" || $cdp->alcalde_e == "1")
                                                    <div class="text-center">
                                                        <form action="{{url('/administrativo/cdp/'.$cdp->id.'/RestartInv/')}}" method="POST" class="form">
                                                            {{method_field('POST')}}
                                                            {{ csrf_field() }}
                                                            <div class="row text-center">
                                                                <button class="btn btn-danger text-center" type="submit" title="Reiniciar CDP">Reiniciar</button>
                                                            </div>
                                                        </form>
                                                        <br>
                                                        <form action="{{url('/administrativo/cdp/'.$cdp->id.'/DeleteInv/')}}" method="POST" class="form">
                                                            {{method_field('POST')}}
                                                            {{ csrf_field() }}
                                                            <div class="row text-center">
                                                                <button class="btn btn-danger text-center" type="submit" title="Reiniciar CDP">Eliminar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                                @if($rol == 3)
                                                    @if($cdp->bpinsCdpValor->count() > 0 )
                                                        @if($cdp->jefe_e == "2" and $cdp->secretaria_e == "3")
                                                            <div class="col-md-12 align-self-center">
                                                                <div class="alert alert-danger text-center">
                                                                    El CDP ha sido anulado.
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <a href="{{url('/administrativo/cdp/'.$cdp->id.'/'.$rol.'/'.$fechaActual.'/'.$cdp->valor.'/3')}}" class="btn btn-danger">
                                                                    Finalizar CDP
                                                                </a>
                                                                <a data-toggle="modal" data-target="#observacionCDP" class="btn btn-success">
                                                                    Rechazar
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @elseif($rol == 5)
                                                    @if($cdp->jefe_e == "2" and $cdp->secretaria_e == "3")
                                                        <div class="col-md-12 align-self-center">
                                                            <div class="alert alert-danger text-center">
                                                                El CDP ha sido anulado.
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <a href="{{url('/administrativo/cdp/'.$cdp->id.'/'.$rol.'/'.$fechaActual.'/'.$cdp->valor.'/3')}}" class="btn btn-danger">
                                                                Enviar al Jefe
                                                            </a>
                                                            <a data-toggle="modal" data-target="#observacionCDP" class="btn btn-success">
                                                                Rechazar
                                                            </a>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if($cdp->jefe_e == "2" and $cdp->secretaria_e == "3")
                                                        <div class="col-md-12 align-self-center">
                                                            <div class="alert alert-danger text-center">
                                                                El CDP ha sido anulado.
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                            @endif
                                        @else
                                            <br><br><hr><center><h3>PROYECTO SELECCIONADO: <br> <br> {{$cdp->bpinsCdpValor[0]->actividad->cod_proyecto}} - {{$cdp->bpinsCdpValor[0]->actividad->nombre_proyecto}} </h3></center><hr>
                                            <hr><center><h3>ACTIVIDADES:</h3></center><hr>
                                            <table class="table table-borderless">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">Cod.</th>
                                                    <th class="text-center">Nombre</th>
                                                    <th class="text-center">Rubro</th>
                                                    <th class="text-center">Dinero Usado</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($cdp->bpinsCdpValor as $item)
                                                    <tr>
                                                        <td>{{$item->actividad->cod_actividad}}</td>
                                                        <td>{{$item->actividad->actividad}}</td>
                                                        <td>{{$item->actividad->rubro->cod}} - {{$item->actividad->rubro->name}}</td>
                                                        <td>$<?php echo number_format( $item->valor ,0) ?></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            @if($cdp->jefe_e == "3" and $cdp->secretaria_e == "3" and $cdp->saldo == $cdp->valor and $cdp->cdpsRegistro->count() == 0)
                                                <form action="{{url('/administrativo/cdp/'.$cdp->id.'/anular/'.$cdp->vigencia_id)}}" method="POST" class="form">
                                                    {{method_field('POST')}}
                                                    {{ csrf_field() }}
                                                    <div class="row text-center">
                                                        <button class="btn btn-success text-center" type="submit" title="Al anular el CDP se retorna el dinero al rubro">Anular CDP</button>
                                                    </div>
                                                </form>
                                            @endif
                                            @if($cdp->jefe_e == 3 and $cdp->cdpsRegistro->count() >= 1)
                                                <br><br>
                                                <hr>
                                                <center>
                                                    <h3>Registros Asignados al CDP</h3>
                                                </center>
                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="tablaRegistros">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center">Id</th>
                                                            <th class="text-center">Nombre</th>
                                                            <th class="text-center">Estado</th>
                                                            <th class="text-center">Valor Inicial</th>
                                                            <th class="text-center">Valor Disponible</th>
                                                            <th class="text-center">Ver</th>
                                                            <th class="text-center">PDF</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($cdp->cdpsRegistro as $data)
                                                            <tr class="text-center">
                                                                <td>{{ $data->registro->code }}</td>
                                                                <td>{{ $data->registro->objeto }}</td>
                                                                <td class="text-center">
                                                        <span class="badge badge-pill badge-danger">
                                                            @if($data->registro->secretaria_e == "0")
                                                                Pendiente
                                                            @elseif($data->registro->secretaria_e == "1")
                                                                Rechazado
                                                            @elseif($data->registro->secretaria_e == "2")
                                                                Anulado
                                                            @else
                                                                Aprobado
                                                            @endif
                                                        </span>
                                                                </td>
                                                                <td>$ <?php echo number_format($data->registro->valor,0);?>.00</td>
                                                                <td>$ <?php echo number_format( $data->registro->saldo,0);?>.00</td>
                                                                <td class="text-center">
                                                                    <a href="{{ url('administrativo/registros/show',$data->registro_id) }}" title="Ver Registro" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($data->registro->secretaria_e == "3")
                                                                        <a href="{{ url('administrativo/registro/pdf/'.$data->registro_id.'/'.$cdp->vigencia_id) }}" title="Ver Archivo" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @elseif($cdp->jefe_e != "2")
                                                <br><div class="alert alert-danger"><center>El CDP no tiene registros asignados</center></div><br>
                                            @endif
                                        @endif
                                </div>
                            @endif
                    </div>
                </div>
                <div id="rubros" class="tab-pane ">
                    <div class="card">
                        <br>
                        <center>
                            <h4><b>Dinero Disponible en Rubros</b></h4>
                        </center>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" class="text-center">Rubro</th>
                                    <th scope="col" class="text-center">Concepto</th>
                                    <th scope="col" class="text-center">Dinero Disponible</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($valores as $valor)
                                    @foreach($infoRubro as $info)
                                        @if($valor['id_rubro'] == $info['id_rubro'])
                                            <tr>
                                                <td class="text-center">{{ $info['codigo'] }}</td>
                                                <td class="text-center">{{ $valor['name'] }}</td>
                                                <td class="text-center">$<?php echo number_format($valor['dinero'],0) ?></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modal.observacionCDP')
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

        new Vue({
            el: '#prog',

            methods:{

                eliminar: function(dato){
                    var urlrubrosCdp = '/administrativo/rubrosCdp/'+dato;
                    axios.delete(urlrubrosCdp).then(response => {
                        location.reload();
                    });
                },

                eliminarV: function(dato){
                    var urlrubrosCdpValor = '/administrativo/rubrosCdp/valor/'+dato;
                    axios.delete(urlrubrosCdpValor).then(response => {
                        location.reload();
                    });
                },

                nuevaFilaPrograma: function(){
                    var nivel=parseInt($("#tabla_rubrosCdp tr").length);
                    $('#tabla_rubrosCdp tbody tr:first').before('<tr>\n' +
                        '                                <td>&nbsp;</td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <input type="hidden" name="cdp_id" value="{{ $cdp->id }}">\n' +
                        '                                    <select name="rubro_id[]" class="form-control" onchange="selectedRubro(this.value)" required>\n' +
                        '                                       @foreach($infoRubro as $rubro)\n' +
                        '                                           <option value="{{ $rubro['id_rubro'] }}">{{ $rubro['codigo'] }} - {{ $rubro['name'] }}</option>\n' +
                        '                                       @endforeach\n' +
                        '                                   </select>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center"><button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button></td>\n' +
                        '                            </tr>');

                }
            }
        });

        const bpins = @json($bpins);
        var valueControl = '<?php echo $cdp->valueControl; ?>';

        function selectedRubro(rubro){
            console.log(rubro);
        }

        function showActividades(codProy){
            document.getElementById("tbody_actividades").innerHTML = "";
            document.getElementById("actividades").style.display = "";
            bpins.filter(r => r.cod_proyecto == codProy).forEach(e => {
                $('#tbody_actividades').append(`
                <tr>
                    <td>${e.cod_actividad} <input type="hidden" name="codActividad[]" value="${e.cod_actividad}"></td>
                    <td>${e.actividad}</td>
                    <td>${ parseInt(e.rubro_find[0].saldo).toLocaleString('de-DE')} $</td>
                    <td><input type="number" class="form-control" min="0" value="0" max="${valueControl}" name="valUsedActividad[]"></td>
                </tr>
            `);
            });

            window.scrollTo(0,document.body.scrollHeight);
        }

        var count1 = '<?php echo $cdp->rubrosCdp->count(); ?>';
        var ciclo1 = JSON.parse('<?php echo json_encode($cdp->rubrosCdp); ?>');

        for (i = 0; i < count1; i++) {
            var r1 = ciclo1[i];
            var fontsR = r1.rubros.fonts_rubro;
            for (j = 0; j < fontsR.length; j++){
                var fuenteR = fontsR[j].rubros_cdp_valor;
                for (k = 0; k < fuenteR.length; k++){
                    var idClass = '#valor'+fuenteR[k].rubrosCdp_id;
                    var idId = '.id'+fontsR[j].font_id;
                    var i = i;
                }

            }
        };

        var visto = null;
        function ver(num) {
            obj = document.getElementById(num);
            obj.style.display = (obj==visto) ? 'none' : '';
            if (visto != null)
                visto.style.display = 'none';
            visto = (obj==visto) ? null : obj;
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