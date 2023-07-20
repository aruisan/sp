@extends('layouts.dashboard')
@section('titulo')
    Registro {{ $registro->code }}
@stop
@section('sidebar')
    {{-- <li>
        <a href="{{route('registros.index')}}" class="btn btn-success">
            <span class="hide-menu"> Registros</span></a>
    </li> --}}

@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Detalles de Registro: {{ $registro->objeto }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link regresar"  href="{{url('administrativo/registros/'.$vigencia) }}">Volver a Registros</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#datos"> Registro {{ $registro->code }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#valor">Consultar Dinero CDP's</a>
        </li>
    </ul>

    <div class="col-lg-12 " style="background-color:white;">
        <div class="tab-content">
            <div id="datos" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2 tab-pane fade in active">
                <center>
                    <h3>Información del Registro</h3>
                </center>
                <hr>
                <div class="row col-md-12">
                    <div class="col-md-4">
                        <center>
                            <h4><b>Valor del Registro</b></h4>
                            <h5>Obtenido de los CDP</h5>
                        </center>
                        <div class="text-center">
                            $<?php echo number_format($registro->valor,0) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <h4><b>IVA del Registro</b></h4>
                        </center>
                        <div class="text-center">
                            $<?php echo number_format($registro->iva,0) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <h4><b>Valor Total del Registro</b></h4>
                            <h5>Valor Registro + Valor IVA</h5>
                        </center>
                        <div class="text-center">
                            $<?php echo number_format($registro->val_total,0) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <center>
                            <h4><b>Saldo del Registro</b></h4>
                            <h5>Dinero Disponible del Registro</h5>
                        </center>
                        <div class="text-center">
                            $<?php echo number_format($registro->saldo,0) ?>
                        </div>
                        <br>
                    </div>
                    @if($registro->ruta)
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <center>
                                <h4><b>Archivo Cargado</b></h4>
                                <h5>Archivo cargado al crear el registro</h5>
                            </center>
                            <div class="text-center">
                                <a href="/uploads/Registros/{{ $registro->ruta }}" target="_blank" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                            </div>
                            <br>
                        </div>
                    @endif
                </div>
                <div class="form-validation">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Tercero: </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                                {{ $registro->persona->nombre }}
                            </div>
                            <small class="form-text text-muted">Persona asignada al registro</small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Tipo de Documento: </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                                {{ $registro->tipo_doc }}
                            </div>
                            <small class="form-text text-muted">Tipo de Documento del registro</small>
                        </div>
                    </div>
                    @if( $registro->tipo_doc == "Contrato" or $registro->tipo_doc == "Factura" or $registro->tipo_doc == "Resolución")
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Número de Documento: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-hashtag" aria-hidden="true"></i></span>
                                    {{ $registro->num_doc }}
                                </div>
                                <small class="form-text text-muted">Número del Documento</small>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Fecha del Documento: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                    {{ $registro->ff_doc }}
                                </div>
                                <small class="form-text text-muted">Fecha del Documento</small>
                            </div>
                        </div>
                    @endif
                    @if($rol != 2 )
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Fecha Envio Secretaria: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                    {{ \Carbon\Carbon::parse($registro->ff_secretaria_e)->format('d-m-Y') }}
                                </div>
                                <small class="form-text text-muted">Fecha de Enviado Secretaria a Alcalde</small>
                            </div>
                            @if($rol == 3)
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label>Fecha Finalización: </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        {{ \Carbon\Carbon::parse($registro->ff_jefe_e)->format('d-m-Y') }}
                                    </div>
                                    <small class="form-text text-muted">Fecha de finalizado por el Jefe</small>
                                </div>

                            @endif
                        </div>
                    @endif
                    @if( $registro->tipo_doc == "Contrato")
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Tipo de Contrato: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                    @if($registro->tipo_contrato == '3')
                                        3 - DE OBRA PUBLICA
                                    @elseif($registro->tipo_contrato == '4')
                                        4 - DE CONSULTORIA
                                    @elseif($registro->tipo_contrato == '5')
                                        5 - DE INTERVENTORIA
                                    @elseif($registro->tipo_contrato == '6')
                                        6 - DE SUMINISTRO
                                    @elseif($registro->tipo_contrato == '10')
                                        10 - DE PRESTACION DE SERVICIOS
                                    @elseif($registro->tipo_contrato == '11')
                                        11 - DE ENCARGO FIDUCIARIO Y FIDUCIA PUBLICA
                                    @elseif($registro->tipo_contrato == '12')
                                        12 - ALQUILER O ARRENDAMIENTO
                                    @elseif($registro->tipo_contrato == '13')
                                        13 - DE CONCESION
                                    @elseif($registro->tipo_contrato == '20')
                                        20 - DEUDA PUBLICA
                                    @elseif($registro->tipo_contrato == '21')
                                        21 - CONVENIO INTERADMINISTRATIVO
                                    @elseif($registro->tipo_contrato == '22')
                                        22 - OTROS NO ESPECIFICADOS ANTERIORMENTE
                                    @endif
                                </div>
                                <small class="form-text text-muted">Tipo de Contrato</small>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Modalidad de Seleccion: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                    @if($registro->mod_seleccion == '0')
                                        NO APLICA
                                    @elseif($registro->mod_seleccion == '1')
                                        1 - LICITACION PUBLICA
                                    @elseif($registro->mod_seleccion == '2')
                                        2 - CONCURSO DE MERITOS
                                    @elseif($registro->mod_seleccion == '3')
                                        3 - SELECCION ABREVIADA
                                    @elseif($registro->mod_seleccion == '4')
                                        4 - CONTRATACION DIRECTA
                                    @elseif($registro->mod_seleccion == '8')
                                        8 - CUANTIA MINIMA
                                    @endif
                                </div>
                                <small class="form-text text-muted">Modalidad de Selección del contrato del registro</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Estado de Ejecución: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                    @if($registro->estado_ejec == '0')
                                        NO APLICA
                                    @elseif($registro->estado_ejec == '1')
                                        1 - EJECUCION
                                    @elseif($registro->estado_ejec == '2')
                                        2 - LIQUIDADO
                                    @elseif($registro->estado_ejec == '3')
                                        3 - SUSPENDIDO
                                    @endif
                                </div>
                                <small class="form-text text-muted">Estado de Ejecución del contrato del registro</small>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        @if($registro->jefe_e == 1)
                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <label>Observación del Rechazo </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                                    <textarea disabled class="form-control">{{ $registro->observacion }}</textarea>
                                </div>
                                <small class="form-text text-muted">Observación del rechazo del registro.</small>
                            </div>
                        @endif
                    </div>
                </div>
                <br>
                <hr>
                <center>
                    <h3>CDP's del Registro</h3>
                </center>
                <hr>
                <br>
                @if($rol == 3 and $registro->jefe_e != 3)
                    @if($registro->cdpsRegistro->first()->cdp->tipo == "Funcionamiento")
                        <table class="table table-bordered" >
                            <thead>
                            <tr>
                                <th class="text-center" colspan="6">DINERO TOMADO DE LOS RUBROS DE CDPs</th>
                            </tr>
                            <tr>
                                <th class="text-center"># CDP</th>
                                <th class="text-center">Nombre CDP</th>
                                <th class="text-center">Codigo</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Fuente</th>
                                <th class="text-center">Valor</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($infoRubro as $rubro)
                                <tr class="text-center">
                                    <td>{{$rubro['codCDP']}} </td>
                                    <td>{{$rubro['nameCDP']}} </td>
                                    <td>{{$rubro['codigo']}} </td>
                                    <td> {{$rubro['name']}}</td>
                                    <td> {{$rubro['font']}}</td>
                                    <td>${{number_format($rubro['value'])}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="table table-bordered">
                            <tbody>
                            @foreach($bpins as $bpin)
                                @if(isset($bpin->depRubroFont->fontRubro))
                                    <tr class="text-center">
                                        <td>Rubro: </td>
                                        <td>{{$bpin->depRubroFont->fontRubro->rubro->cod}} - {{$bpin->depRubroFont->fontRubro->rubro->name}}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <td>Fuente: </td>
                                        <td>{{$bpin->depRubroFont->fontRubro->sourceFunding->code}} - {{$bpin->depRubroFont->fontRubro->sourceFunding->description}}</td>
                                    </tr>
                                @endif
                                <tr class="text-center">
                                    <td>Proyecto: </td>
                                    <td>{{$bpin->actividad->cod_proyecto}} - {{$bpin->actividad->nombre_proyecto}}</td>
                                </tr>
                                <tr class="text-center">
                                    <td>Actividad: </td>
                                    <td>{{$bpin->actividad->cod_actividad}} - {{$bpin->actividad->actividad}}</td>
                                </tr>
                                <tr class="text-center">
                                    <td>Valor CDP: </td>
                                    <td style="vertical-align: middle">$ {{number_format($bpin->valor)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
                <br>
                <div class="table-responsive" id="prog">
                    @if($registro->cdpsRegistro->count() == 0 and $rol == 3)
                    @else
                        <form class="form" action="{{url('/administrativo/cdpsRegistro')}}" method="POST" class="form">
                            {{ csrf_field() }}
                            <table id="tabla_rubrosCdp" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col" class="text-center">Nombre CDP's</th>
                                    <th scope="col" class="text-center"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($registro->cdpsRegistro->count() == 0)
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td class="text-center">
                                            <input type="hidden" name="registro_id" value="{{ $registro->id }}">
                                            <select name="cdp_id[]" class="form-group-lg" required>
                                                @foreach($cdps as $cdp)
                                                    <option value="{{ $cdp['id'] }}">{{ $cdp['id'] }} - {{ $cdp['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center"><button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button></td>
                                    </tr>
                                @endif
                                @for($i = 0; $i < $registro->cdpsRegistro->count(); $i++)
                                    @php($cdpsRegistroData = $registro->cdpsRegistro[$i] )
                                    <tr>
                                        <td class="text-center">
                                            @if($registro->secretaria_e != 2)
                                                <button type="button" class="btn-sm btn-success" onclick="ver('fuente{{$i}}')" ><i class="fa fa-arrow-down"></i></button>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="col-lg-6">
                                                <h4>
                                                    <b>CDP :
                                                        <a href="{{ url('administrativo/cdp/'.$cdpsRegistroData->cdp->vigencia_id.'/'.$cdpsRegistroData->cdp->id) }}" title="Ver CDP">{{ $cdpsRegistroData->cdp->name }}</a>
                                                        </b>
                                                </h4>
                                            </div>
                                            <div class="col-lg-6">
                                                <h4>
                                                    Disponible:
                                                    <b>$<?php echo number_format($cdpsRegistroData->cdp->saldo,0) ?></b>
                                                </h4>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                        </td>
                                    </tr>
                                    <tr id="fuente{{$i}}" style="display: none">
                                        <td style="vertical-align: middle">
                                            @if($cdpsRegistroData->cdp->tipo == "Funcionamiento")
                                                <b>Rubros del CDP</b>
                                            @else
                                                <b>Actividades del CDP</b>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="col-lg-12">
                                                @if($cdpsRegistroData->cdp->tipo == "Funcionamiento")
                                                    @foreach($cdpsRegistroData->cdp->rubrosCdpValor as $RCV)
                                                        @if($RCV->valor_disp != 0)
                                                            <div class="col-lg-6">
                                                                <input type="hidden" name="registro_id" value="{{ $registro->id }}">
                                                                <input type="hidden" name="fuente_id[]" value="{{ $RCV->fontsRubro->id }}">
                                                                <input type="hidden" name="cdp_id_s[]" value="{{ $RCV->cdp_id }}">
                                                                <input type="hidden" name="rubro_id[]" value="{{ $RCV->fontsRubro->rubro_id }}">
                                                                <input type="hidden" name="rubros_cdp_id[]" value="{{ $cdpsRegistroData->id }}">
                                                                @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                                                <li style="list-style-type: none;">
                                                                    Dinero Disponible del Rubro {{ $RCV->fontsRubro->rubro->name }} :
                                                                    $<?php echo number_format( $RCV->valor_disp,0) ?>
                                                                </li>
                                                            </div>
                                                        @endif
                                                        <div class="col-lg-6">
                                                            @if($registro->secretaria_e == "3")
                                                                Valor Usado del Rubro {{ $RCV->fontsRubro->rubro->name }}:
                                                                @if($cdpsRegistroData->cdpRegistroValor->count() != 0)
                                                                    @foreach($RCV->fontsRubro->cdpRegistrosValor as  $valoresRV)
                                                                        @php($id_rubrosCdp = $cdpsRegistroData->id )
                                                                        @if($valoresRV->registro_id == $registro->id)
                                                                            @if($cdpsRegistroData->cdp->id == $valoresRV->cdp_id and $RCV->fontsRubro->rubro_id == $valoresRV->rubro_id)
                                                                                <input type="hidden" name="rubros_cdp_valor_id[]" value="{{ $valoresRV->id }}">
                                                                                @if($registro->secretaria_e == "0")
                                                                                    <input type="number" required  name="valorFuenteUsar[]" id="id{{$RCV->font_id}}" class="valor{{ $valoresRV->cdps_registro_id }}" value="{{ $RCV->valor_disp }}" max="{{ $RCV->valor_disp }}" style="text-align: center">
                                                                                @else
                                                                                    $<?php echo number_format( $valoresRV->valor,0) ?>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                    @if($registro->cdpRegistroValor->count() == 0)
                                                                        <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                        <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="{{$RCV->valor_disp}}" max="{{ $RCV->valor_disp }}" style="text-align: center">
                                                                    @endif
                                                                @else
                                                                    <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                    <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="{{$RCV->valor_disp}}" max="{{  $RCV->valor_disp }}" style="text-align: center">
                                                                @endif
                                                            @elseif($RCV->valor_disp > 0)
                                                                Valor Usado del Rubro {{ $RCV->fontsRubro->rubro->name }}:
                                                                @if($cdpsRegistroData->cdpRegistroValor->count() != 0 )
                                                                    @foreach($RCV->fontsRubro->cdpRegistrosValor as  $valoresRV)
                                                                        @php($id_rubrosCdp = $cdpsRegistroData->id )
                                                                        @if($valoresRV->registro_id == $registro->id)
                                                                            @if($cdpsRegistroData->cdp->id == $valoresRV->cdp_id and $RCV->fontsRubro->rubro_id == $valoresRV->rubro_id)
                                                                                <input type="hidden" name="rubros_cdp_valor_id[]" value="{{ $valoresRV->id }}">
                                                                                @if($registro->secretaria_e == "0")
                                                                                    <input type="number" required  name="valorFuenteUsar[]" id="id{{$RCV->font_id}}" class="valor{{ $valoresRV->cdps_registro_id }}" value="{{ $valoresRV->valor }}" max="{{ $cdpsRegistroData->cdp->saldo }}" style="text-align: center">
                                                                                @else
                                                                                    $<?php echo number_format( $valoresRV->valor,0) ?>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                    @if($registro->cdpRegistroValor->count() == 0)
                                                                        <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                        <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="{{$cdpsRegistroData->cdp->saldo}}" max="{{ $cdpsRegistroData->cdp->saldo }}" min="0" style="text-align: center">
                                                                    @endif
                                                                @else
                                                                    <input type="hidden" name="rubros_cdp_valor_id[]" value="">
                                                                    <input type="number" required  name="valorFuenteUsar[]" class="form-group-sm" value="{{$cdpsRegistroData->cdp->saldo}}" max="{{ $cdpsRegistroData->cdp->saldo }}" min="0" style="text-align: center">
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <!-- CPD DE INVERSION -->
                                                    @foreach($cdpsRegistroData->cdp->bpinsCdpValor as $item)
                                                        @if($item->valor_disp != 0)
                                                            <div class="col-lg-6">
                                                                <input type="hidden" name="registro_id" value="{{ $registro->id }}">
                                                                <input type="hidden" name="cdp_id_s[]" value="{{ $item->cdp_id }}">
                                                                @foreach($item->actividad->rubroFind as $data)
                                                                    @if($data->vigencia_id == $vigencia)
                                                                        <input type="hidden" name="rubro_id[]" value="{{ $data->rubro_id }}">
                                                                    @endif
                                                                @endforeach
                                                                <input type="hidden" name="rubros_cdp_id[]" value="{{ $cdpsRegistroData->id }}">
                                                                <input type="hidden" name="bpin_id[]" value="{{ $item->actividad->id }}">
                                                                <input type="hidden" name="bpin_cdp_valor_id[]" value="{{ $item->id }}">
                                                                @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                                                <table class="table table-bordered table-striped">
                                                                    <tr>
                                                                        <td>Dinero Disponible de la Actividad {{ $item->actividad->actividad }} :
                                                                            $<?php echo number_format($item->valor_disp,0) ?> - Fuente:
                                                                            {{$item->depRubroFont->fontRubro->sourceFunding->code}} -
                                                                            {{$item->depRubroFont->fontRubro->sourceFunding->description}}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        @endif
                                                        <div class="col-lg-6">
                                                            @if($registro->secretaria_e == "3")
                                                                Valor Usado de la Actividad {{ $item->actividad->actividad }}:
                                                                @if($cdpsRegistroData->cdpRegistroValor->count() != 0)
                                                                    @foreach($cdpsRegistroData->cdpRegistroValor as $valoresRV)
                                                                        @if(isset($valoresRV->bpin_cdp_valor_id))
                                                                            @if($item->id == $valoresRV->bpin_cdp_valor_id)
                                                                                $<?php echo number_format( $valoresRV->valor,0) ?>
                                                                            @endif
                                                                        @else
                                                                            $<?php echo number_format( $item->valor,0) ?>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <input type="hidden" name="bpin_cdp_valor_id[]" value="">
                                                                    <input type="number" required  name="valorActividadUsar[]" class="form-group-sm" value="{{$cdpsRegistroData->cdp->saldo}}" max="{{  $cdpsRegistroData->cdp->saldo }}" style="text-align: center">
                                                                @endif
                                                            @elseif($item->valor_disp > 0)
                                                                Valor Usado de la Actividad {{ $item->actividad->actividad }} - Fuente  {{$item->depRubroFont->fontRubro->sourceFunding->code}} -
                                                                {{$item->depRubroFont->fontRubro->sourceFunding->description}}:
                                                                <!-- ARREGLAR EL VALOR QUE SE ESTA MOSTRANDO -->
                                                                @if($cdpsRegistroData->cdpRegistroValor->count() != 0 )
                                                                    @foreach($cdpsRegistroData->cdpRegistroValor as  $valoresRV)
                                                                        @if(isset($valoresRV->bpin_cdp_valor_id))
                                                                            @if($item->id == $valoresRV->bpin_cdp_valor_id)
                                                                                <input type="hidden" name="cdp_registro_valor_id[]" value="{{ $valoresRV->id }}">
                                                                                $<?php echo number_format( $valoresRV->valor,0) ?>
                                                                            @endif
                                                                        @else
                                                                            @if($cdpsRegistroData->cdp->id == $valoresRV->cdp_id and $item->actividad->rubro_id == $valoresRV->rubro_id)
                                                                                <input type="hidden" name="cdp_registro_valor_id[]" value="{{ $valoresRV->id }}">
                                                                                $<?php echo number_format( $valoresRV->valor,0) ?>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                    @if($registro->cdpRegistroValor->count() == 0)
                                                                        <input type="number" required  name="valorActividadUsar[]" class="form-group-sm" value="{{$cdpsRegistroData->cdp->saldo}}" max="{{ $cdpsRegistroData->cdp->saldo }}" min="0" style="text-align: center">
                                                                    @endif
                                                                @else
                                                                    <input type="number" required  name="valorActividadUsar[]" class="form-group-sm" value="{{$cdpsRegistroData->cdp->saldo}}" max="{{ $cdpsRegistroData->cdp->saldo }}" min="0" style="text-align: center">
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <b>Valor Total</b>
                                            <br><b>
                                                @if($cdpsRegistroData->cdpRegistroValor->count() > 0)
                                                    $<?php echo number_format( $cdpsRegistroData->cdpRegistroValor->sum('valor') ,0) ?>
                                                @else
                                                    $0.00
                                                @endif
                                            </b><br>&nbsp;<br>
                                            @if($rol == 2 and $registro->secretaria_e != 3 and $registro->secretaria_e != 2)
                                                @if($cdpsRegistroData->cdpRegistroValor->count() > 0)
                                                    <b>Liberar Dinero</b>
                                                    <br>
                                                    <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarV({{ $cdpsRegistroData->cdpRegistroValor->first()->cdps_registro_id }})" ><i class="fa fa-money"></i></button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                @endfor
                                </tbody>
                            </table>
                            @if($registro->secretaria_e != 3 and $registro->secretaria_e != 2)
                                <center>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                            <label>IVA: </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                <input type="number" class="form-control" id="iva" name="iva" value="{{ $registro->iva }}" required min="0" style="text-align: center">
                                            </div>
                                            <small class="form-text text-muted">Valor del iva con el que se va a regir el registro</small>
                                        </div>
                                    </div>
                                </center>
                            @endif
                            <br>
                            <center>
                                @if($rol == 2 and $registro->secretaria_e != 3 and $registro->secretaria_e != 2)
                                    @php($fuenteAgua = false)
                                    @if($registro->cdpsRegistro->first()->cdp->tipo == "Funcionamiento")
                                        @foreach($infoRubro as $rubro)
                                            @if($rubro['codigo'] == "1.2.4.6.00" or $rubro['codigo'] == "1.3.2.2.13" or $rubro['codigo'] == "1.3.3.8.00" or $rubro['codigo'] == "1.3.3.11.13")
                                                @php($fuenteAgua = true)
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($cdpsRegistroData->cdp->bpinsCdpValor as $item)
                                            @if($item->depRubroFont->fontRubro->sourceFunding->code == "1.2.4.6.00" or
                                                $item->depRubroFont->fontRubro->sourceFunding->code == "1.3.2.2.13" or
                                                $item->depRubroFont->fontRubro->sourceFunding->code == "1.3.3.8.00" or
                                                $item->depRubroFont->fontRubro->sourceFunding->code == "1.3.3.11.13")
                                                @php($fuenteAgua = true)
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($fuenteAgua)
                                        <div class="row">
                                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <label>Valor con Cargo: </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                    <input type="number" class="form-control" id="value_water" name="value_water" required min="0" style="text-align: center">
                                                </div>
                                                <small class="form-text text-muted">Valor con cargo a la fuente</small>
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <label>Actividad </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                                    <select name="actividad" class="form-control">
                                                        <option value="0">NO APLICA</option>
                                                        <option value="1">A.3.10.1 ACUEDUCTO-CAPTACION</option>
                                                        <option value="2">A.3.10.10 ACUEDUCTO-PREINVERSIONES, ESTUDIOS</option>
                                                        <option value="3">A.3.10.11 ACUEDUCTO-INTERVENTORIA</option>
                                                        <option value="4">A.3.10.12 ACUEDUCTO-FORMULACION, IMPLEMENTACION Y ACCIONES DE FORTALECIMIENTO PARA LA ADMINISTRACION Y OPERACION DE LOS SERVICIOS</option>
                                                        <option value="5">A.3.10.13 ACUEDUCTO-SUBSIDIOS</option>
                                                        <option value="6">A.3.10.2 ACUEDUCTO-ADUCCION</option>
                                                        <option value="7">A.3.10.3 ACUEDUCTO-ALMACENAMIENTO</option>
                                                        <option value="8">A.3.10.4 ACUEDUCTO-TRATAMIENTO</option>
                                                        <option value="9">A.3.10.5 ACUEDUCTO-CONDUCCION</option>
                                                        <option value="10">A.3.10.6 ACUEDUCTO-MACROMEDICION</option>
                                                        <option value="11">A.3.10.7 ACUEDUCTO-DISTRIBUCION</option>
                                                        <option value="12">A.3.10.8 ACUEDUCTO-MICROMEDICION</option>
                                                        <option value="13">A.3.10.9 ACUEDUCTO-INDICE DE AGUA NO CONTABILIZADA</option>
                                                        <option value="14">A.3.11.1 ALCANTARILLADO-RECOLECCION</option>
                                                        <option value="15">A.3.11.2 ALCANTARILLADO-TRANSPORTE</option>
                                                        <option value="16">A.3.11.3 ALCANTARILLADO-TRATAMIENTO</option>
                                                        <option value="17">A.3.11.4 ALCANTARILLADO-DESCARGA</option>
                                                        <option value="18">A.3.11.5 ALCANTARILLADO-PREINVENSIONES, ESTUDIOS</option>
                                                        <option value="19">A.3.11.6 ALCANTARILLADO-INTERVENTORIA</option>
                                                        <option value="20">A.3.11.7 ALCANTARILLADO-FORTALECIMIENTO INSTITUCIONAL</option>
                                                        <option value="21">A.3.11.8 ALCANTARILLADO-SUBSIDIOS</option>
                                                        <option value="23">A.3.12.1 ASEO-PROYECTO DE TRATAMIENTO Y APROVECHAMIENTO DE RESIDUOS SOLIDOS</option>
                                                        <option value="24">A.3.12.2 ASEO-MAQUINARIA Y EQUIPOS</option>
                                                        <option value="25">A.3.12.3 ASEO-DISPOSICION FINAL</option>
                                                        <option value="26">A.3.12.4 ASEO-PREINVERSION Y ESTUDIOS</option>
                                                        <option value="27">A.3.12.5 ASEO-ASEO INTERVENTORIA</option>
                                                        <option value="28">A.3.12.6 ASEO-ASEO FORTALECIMIENTO INSTITUCIONAL</option>
                                                        <option value="29">A.3.12.7 ASEO-SUBSIDIOS</option>
                                                        <option value="30">A.3.13 TRANSFERENCIAS PDA INVERSION</option>
                                                        <option value="31">A.3.15 PAGOS PASIVOS LABORALES</option>
                                                        <option value="32">A.3.17 PAGO DE DEFICIT DE INVERSION EN AGUA POTABLE Y SANEAMIENTO BASICO</option>
                                                        <option value="33">A.3.18 PAGO SERVICIO A LA DEUDA</option>
                                                    </select>
                                                </div>
                                                <small class="form-text text-muted"> Seleccione la actividad.</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <label>Fuente: </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                                    <select name="font_water" class="form-control">
                                                        <option value="0">NO APLICA</option>
                                                        <option value="280">SGP APSB - SALDOS NO EJECUTADOS VIGENCIA</option>
                                                        <option value="290">SGP APSB - ONCE DOCEAVAS VIGENCIA</option>
                                                        <option value="300">SGP APSB - RENDIMIENTOS FINANCIEROS</option>
                                                        <option value="305">SGP MUNICIPIOS DESCERTIFICADOS</option>
                                                    </select>
                                                </div>
                                                <small class="form-text text-muted">Seleccione la fuente.</small>
                                            </div>
                                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <label>Localización: </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                                                    <select name="loc_water" class="form-control">
                                                        <option value="0">NO APLICA</option>
                                                        <option value="1">URBANO</option>
                                                        <option value="2">CENTRO POBLADO</option>
                                                        <option value="3">RURAL DISPERSO</option>
                                                    </select>
                                                </div>
                                                <small class="form-text text-muted">Seleccione la localización.</small>
                                            </div>
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-danger">Actualizar Registro</button>
                                    @if($registro->cdpRegistroValor->sum('valor') > 0 )
                                        @php($valTot = $registro->iva + $registro->cdpRegistroValor->sum('valor'))
                                        @if(auth()->user()->id == 39)
                                            <a href="{{url('/administrativo/registros/'.$registro->id.'/'.$fechaActual.'/'.$registro->cdpRegistroValor->sum('valor').'/3/'.$valTot.'/3')}}" type="submit" class="btn btn-success">
                                                Finalizar Registro
                                            </a>
                                        @else
                                            <a href="{{url('/administrativo/registros/'.$registro->id.'/'.$fechaActual.'/'.$registro->cdpRegistroValor->sum('valor').'/3/'.$valTot.'/2')}}" type="submit" class="btn btn-success">
                                                Enviar Registro al Jefe
                                            </a>
                                        @endif
                                    @endif
                                @elseif($rol == 3 and $registro->jefe_e != 3)
                                    @if($registro->secretaria_e == 3)
                                        <a data-toggle="modal" data-target="#observacion" class="btn btn-success">Rechazar Registro</a>
                                        @if($registro->cdpRegistroValor->sum('valor') > 0 )
                                            @php($valTot = $registro->iva + $registro->cdpRegistroValor->sum('valor'))
                                            <a href="{{url('/administrativo/registros/'.$registro->id.'/'.$fechaActual.'/'.$registro->cdpRegistroValor->sum('valor').'/3/'.$valTot.'/3')}}" type="submit" class="btn btn-success">
                                                Finalizar Registro
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            </center>
                        </form>
                        @if($cdpsRegistroData->cdpRegistroValor->count() == 0)
                            {!! Form::open(['method' => 'DELETE','route' => ['registros.destroy', $registro->id],'style'=>'display:inline']) !!}
                            <center>
                                <button type="submit" class="btn btn-primary">
                                    Borrar Registro
                                </button>
                            </center>
                            {!! Form::close() !!}
                        @endif
                    @endif
                </div>
                @if($ordenesPago->count() >= 1)
                    <hr><center><h3>Ordenes de Pago del Registro </h3></center><br>
                    <table class="table table-bordered" id="tabla_OrdenPago">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center"><i class="fa fa-eye"></i></th>
                            <th class="text-center">Archivo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($ordenesPago as $key => $data)
                            <tr>
                                <td class="text-center">{{ $data['code'] }}</td>
                                <td class="text-center">{{ $data['nombre'] }}</td>
                                <td class="text-center">$<?php echo number_format($data['valor'],0) ?></td>
                                <td class="text-center">{{ $data->registros->persona->nombre }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($data['estado'] == "0")
                                            Pendiente
                                        @elseif($data['estado'] == "1")
                                            Finalizada
                                        @else
                                            Anulada
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/ordenPagos/show',$data['id']) }}" title="Ver Orden de Pago" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    @if($data['estado'] == "1")
                                        <a href="{{ url('administrativo/ordenPagos/pdf',$data['id']) }}" target="_blank" title="Orden de Pago" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br>
                @elseif($registro->secretaria_e == 3)
                    <br><div class="alert alert-danger"><center>El Registro no tiene ordenes de pago asignadas</center></div><br>
                @elseif($registro->secretaria_e == 2)
                    <br><div class="alert alert-danger"><center>El Registro ha sido anulado</center></div><br>
                @endif
                @if($ordenesPago->count() == 0 and $rol == 3 and $registro->jefe_e == 3)
                    <form action="{{url('/administrativo/registros/'.$registro->id.'/anular')}}" method="POST" class="form">
                        {{method_field('POST')}}
                        {{ csrf_field() }}
                        <div class="row text-center">
                            <button class="btn btn-success text-center" type="submit" title="Al anular el Registro se retorna el dinero al CDP">Anular Registro</button>
                        </div>
                    </form>
                @endif
            </div>
            <div id="valor" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2 tab-pane">
                <br>
                <div class="card">
                    <center>
                        <h4><b>Dinero en los CDP's</b></h4>
                    </center>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            @foreach($cdps as $cdp)
                                <tr>
                                    <td>{{ $cdp['name'] }}</td>
                                    <td>$<?php echo number_format($cdp['saldo'],0) ?></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modal.observacion')
@stop
@section('js')

    <script type="text/javascript">

        var visto = null;

        function ver(num) {
            obj = document.getElementById(num);
            obj.style.display = (obj==visto) ? 'none' : '';
            if (visto != null)
                visto.style.display = 'none';
            visto = (obj==visto) ? null : obj;
        }

        $(document).ready(function() {

            $('#tabla_OrdenPago').DataTable( {
                responsive: true,
                "searching": false,
                "ordering" : false
            } );

            $('#tabla_rubrosCdp').DataTable( {
                responsive: true,
                "searching": false,
                "ordering" : false
            } );


            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });

            new Vue({
                el: '#prog',

                methods:{

                    eliminar: function(dato){
                        var urlcdpsRegistro = '/administrativo/cdpsRegistro/'+dato;
                        axios.delete(urlcdpsRegistro).then(response => {
                            location.reload();
                        });
                    },

                    eliminarV: function(dato){
                        var urlCdpRegistrosValor = '/administrativo/cdpsRegistro/valor/'+dato;
                        axios.delete(urlCdpRegistrosValor).then(response => {
                            location.reload();
                        });
                    },


                }
            });
        } );
    </script>
@stop