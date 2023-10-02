@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $vigencia->vigencia }}
@stop
@section('content')
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            <li class="nav-item principal">
                <a class="nav-link"  href=""> Presupuesto de Egresos {{ $vigencia->vigencia }}</a>
            </li>
            <li class="nav-item pillPri">
                <a class="nav-link"  href="{{ url('/presupuesto/') }}"> Regresar al Presupuesto</a>
            </li>
            <li class="dropdown">
                <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Historico &nbsp;<i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu ">
                    @foreach($years as $year)
                        <li>
                            <a href="{{ url('/presupuesto/historico/'.$year['id']) }}" class="btn btn-drop text-left">{{ $year['info'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="dropdown">
                <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Informe Mensual&nbsp;<i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu ">
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-01-31') }}" class="btn btn-drop text-left">Enero</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-02-29') }}" class="btn btn-drop text-left">Febrero</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-03-31') }}" class="btn btn-drop text-left">Marzo</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-04-30') }}" class="btn btn-drop text-left">Abril</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-05-31') }}" class="btn btn-drop text-left">Mayo</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-06-30') }}" class="btn btn-drop text-left">Junio</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-07-30') }}" class="btn btn-drop text-left">Julio</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-08-31') }}" class="btn btn-drop text-left">Agosto</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-09-30') }}" class="btn btn-drop text-left">Septiembre</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-10-31') }}" class="btn btn-drop text-left">Octubre</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastos/'.$V.'/'.$vigencia->vigencia.'-01-01/'.$vigencia->vigencia.'-11-30') }}" class="btn btn-drop text-left">Noviembre</a></li>
                    <li><a href="{{ url('/presupuesto/ejecucion/gastosTot/'.$V) }}"  class="btn btn-drop text-left">Diciembre</a></li>
                </ul>
            </li>
        </ul>
        <div class="col-md-12 align-self-center" style="background-color:#fff;">
            <div class="row" >
                <div class="breadcrumb col-md-12 text-center" >
                    <strong>
                        <h4><b>Presupuesto de Egresos {{ $vigencia->vigencia }}</b></h4>
                    </strong>
                </div>
            </div>

            <ul class="nav nav-pills">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" data-toggle="pill"  href="@can('fuentes-list') #tabFuente @endcan">Fuentes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="@can('rubros-list') #tabRubros @endcan">Rubros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link hidden" data-toggle="pill" href="@can('pac-list') #tabPAC @endcan">PAC</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="@can('cdps-list') #tabCert @endcan">CDP's</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="@can('registros-list') #tabReg @endcan">Registros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href=" @can('adiciones-list') #tabAddEgr @endcan">Adiciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href=" @can('reducciones-list') #tabRedEgr @endcan">Reducciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="@can('creditos-list') #tabCre @endcan">Creditos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled hidden" data-toggle="pill" href="#tabApl">Aplazamientos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#tabOP">Orden de Pago</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#tabP">Pagos</a>
                </li>
            </ul>

            <hr>
            <!-- TABLA DE PRESUPUESTO -->

            <div class="tab-content" style="background-color: white">
                <div id="tabHome" class="tab-pane active"><br>
                    <div class="table-responsive">

                        <table id="tabla_presupuesto1" class="table table-bordered table-striped ">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">P. Inicial</th>
                                <th class="text-center">Adición</th>
                                <th class="text-center">Reducción</th>
                                <th class="text-center">Credito</th>
                                <th class="text-center">CCredito</th>
                                <th class="text-center">P.Definitivo</th>
                                <th class="text-center">CDP's</th>
                                <th class="text-center">Registros</th>
                                <th class="text-center">Saldo Disponible</th>
                                <th class="text-center">Saldo de CDP</th>
                                <th class="text-center">Ordenes de Pago</th>
                                <th class="text-center">Pagos</th>
                                <th class="text-center">Cuentas Por Pagar</th>
                                <th class="text-center">Reservas</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($codigos as $codigo)
                                <tr>
                                    @if($codigo['valor'])
                                        <td class="text-dark" style="vertical-align:middle;"><a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}">{{ $codigo['codigo']}}</a></td>
                                    @else
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['codigo']}}</td>
                                    @endif
                                    <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                    <!-- PRESUPUESTO INICIAL-->
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @if($codigo['valor'])
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                    @elseif($codigo['valor'] == 0 and $codigo['id_rubro'] != "")
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                    @endif
                                <!-- ADICIÓN -->
                                    @foreach($valoresFinAdd as $valorFinAdd)
                                        @if($valorFinAdd['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinAdd['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresAdd as $valorAdd)
                                        @if($codigo['id_rubro'] == $valorAdd['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- REDUCCIÓN -->
                                    @foreach($valoresFinRed as $valorFinRed)
                                        @if($valorFinRed['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinRed['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresRed as $valorRed)
                                        @if($codigo['id_rubro'] == $valorRed['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorRed['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- CREDITO -->
                                    @foreach($valoresFinCred as $valorFinCred)
                                        @if($valorFinCred['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinCred['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresCred as $valorCred)
                                        @if($codigo['id_rubro'] == $valorCred['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorCred['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- CONTRACREDITO -->
                                    @foreach($valoresFinCCred as $valorFinCCred)
                                        @if($valorFinCCred['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinCCred['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresCcred as $valorCcred)
                                        @if($codigo['id_rubro'] == $valorCcred['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorCcred['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- PRESUPUESTO DEFINITIVO -->
                                    @foreach($valoresDisp as $valorDisponible)
                                        @if($valorDisponible['id'] == $codigo['id'])
                                            <td class="text-center" style="vertical-align:middle;">$ <?php echo number_format($valorDisponible['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($ArrayDispon as $valorPD)
                                        @if($codigo['id_rubro'] == $valorPD['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorPD['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- CDP'S -->
                                    @foreach($valoresFinCdp as $valorFinCdp)
                                        @if($valorFinCdp['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinCdp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresCdp as $valorCdp)
                                        @if($codigo['id_rubro'] == $valorCdp['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorCdp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- REGISTROS -->
                                    @foreach($valoresFinReg as $valorFinReg)
                                        @if($valorFinReg['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinReg['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresRubro as $valorRubro)
                                        @if($codigo['id_rubro'] == $valorRubro['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorRubro['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- SALDO DISPONIBLE -->
                                    @foreach($valorDisp as $vDisp)
                                        @if($vDisp['id'] == $codigo['id'])
                                            <td class="text-center" style="vertical-align:middle;">$ <?php echo number_format($vDisp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($saldoDisp as $salD)
                                        @if($codigo['id_rubro'] == $salD['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($salD['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- SALDO DE CDP -->
                                    @foreach($valorFcdp as $valFcdp)
                                        @if($valFcdp['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valFcdp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valorDcdp as $valorDCdp)
                                        @if($codigo['id_rubro'] == $valorDCdp['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorDCdp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- ORDENES DE PAGO -->
                                    @foreach($valoresFinOp as $valFinOp)
                                        @if($valFinOp['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valFinOp['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valOP as $valorOP)
                                        @if($codigo['id_rubro'] == $valorOP['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorOP['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- PAGOS -->
                                    @foreach($valoresFinP as $valFinP)
                                        @if($valFinP['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valFinP['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valP as $valorP)
                                        @if($codigo['id_rubro'] == $valorP['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorP['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- CUENTAS POR PAGAR -->
                                    @foreach($valoresFinC as $valFinC)
                                        @if($valFinC['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valFinC['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valCP as $valorCP)
                                        @if($codigo['id_rubro'] == $valorCP['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorCP['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                <!-- RESERVAS -->
                                    @foreach($valoresFinRes as $valFinRes)
                                        @if($valFinRes['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valFinRes['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @foreach($valR as $valorR)
                                        @if($codigo['id_rubro'] == $valorR['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorR['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">P. Inicial</th>
                                <th class="text-center">Adición</th>
                                <th class="text-center">Reducción</th>
                                <th class="text-center">Credito</th>
                                <th class="text-center">CCredito</th>
                                <th class="text-center">P.Definitivo</th>
                                <th class="text-center">CDP's</th>
                                <th class="text-center">Registros</th>
                                <th class="text-center">Saldo Disponible</th>
                                <th class="text-center">Saldo de CDP</th>
                                <th class="text-center">Ordenes de Pago</th>
                                <th class="text-center">Pagos</th>
                                <th class="text-center">Cuentas Por Pagar</th>
                                <th class="text-center">Reservas</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TABLA DE FUENTES -->

                <div id="tabFuente" class="tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" align="100%" id="tabla_fuente">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">P. Inicial</th>
                                @foreach($fuentes as $fuente)
                                    <th class="text-center">{{ $fuente['name'] }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($codigos as $codigo)
                                <tr>
                                    <td class="text-dark">{{ $codigo['codigo']}}</td>
                                    <td class="text-dark">{{ $codigo['name']}}</td>
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark">$ <?php echo number_format($valorInicial['valor'],0);?>.00</td>
                                        @endif
                                    @endforeach
                                    @if($codigo['valor']!=null)
                                        <td class="text-center text-dark">$ <?php echo number_format($codigo['valor'],0);?>.00</td>
                                    @elseif($codigo['valor']==null)
                                        <td class="text-center text-dark"></td>
                                    @endif
                                    @foreach($FRubros as $FRubro)
                                        @if($FRubro['rubro_id'] == $codigo['id_rubro'])
                                            <td class="text-center text-dark">$ <?php echo number_format($FRubro["valor"],0);?>.00</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABLA DE RUBROS -->

                <div id="tabRubros" class="tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_Rubros">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor Inicial</th>
                                <th class="text-center">Valor Final</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($Rubros as  $Rubro)
                                <tr>
                                    <td>{{ $Rubro['codigo'] }}</td>
                                    <td>{{ $Rubro['name'] }}</td>
                                    <td class="text-center">$ <?php echo number_format($Rubro['valor'],0);?>.00</td>
                                    <td class="text-center">$ <?php echo number_format($Rubro['valor_disp'],0);?>.00</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABLA DE PAC -->

                <div id="tabPAC" class="tab-pane fade"><br>
                    <h2 class="text-center">PAC</h2>
                </div>

                <!-- TABLA DE CDP's -->

                <div id="tabCert" class=" tab-pane fade"><br>
                    <div class="table-responsive">
                        @if(isset($cdps))
                            <table class="table table-bordered" id="tabla_CDP">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Objeto</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Estado Secretaria</th>
                                    <th class="text-center">Estado Jefe</th>
                                    <th class="text-center">Archivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cdps as $index => $cdp)
                                    <tr>
                                        <td class="text-center">{{ $cdp['code'] }}</td>
                                        <td class="text-center">{{ $cdp['name'] }}</td>
                                        <td class="text-center">$ <?php echo number_format($cdp['valor'],0);?>.00</td>
                                        <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($cdp['secretaria_e'] == "0")
                                                Pendiente
                                            @elseif($cdp['secretaria_e'] == "1")
                                                Rechazado
                                            @elseif($cdp['secretaria_e'] == "2")
                                                Anulado
                                            @else
                                                Enviado
                                            @endif
                                        </span>
                                        </td>
                                        <td class="text-center">
                                        <span class="badge badge-pill badge-danger">
                                            @if($cdp['jefe_e'] == "0")
                                                Pendiente
                                            @elseif($cdp['jefe_e'] == "1")
                                                Rechazado
                                            @elseif($cdp['jefe_e'] == "2")
                                                Anulado
                                            @elseif($cdp['jefe_e'] == "3")
                                                Aprobado
                                            @else
                                                En Espera
                                            @endif
                                        </span>
                                        </td>
                                        <td class="text-center">
                                            @if($cdp->secretaria_e == 3 and $cdp->jefe_e == 3)
                                                <a href="{{ url('administrativo/cdp/pdf/'.$cdp['id'].'/'.$V) }}" target="_blank" title="certificado" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <br><br>
                            <div class="alert alert-danger">
                                <center>
                                    No se crearón CDP's en esta vigencia.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TABLA DE REGISTROS -->

                <div id="tabReg" class=" tab-pane fade"><br>
                    <div class="table-responsive">
                        @if(count($registros) >= 1)
                            <table class="table table-bordered" id="tabla_Registros">
                                <thead>
                                <tr>
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Nombre Registro</th>
                                    <th class="text-center">Nombre Tercero</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Archivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($registros as $key => $data)
                                    <tr>
                                        <td class="text-center">{{ $data['code'] }}</td>
                                        <td class="text-center">{{ $data['objeto'] }}</td>
                                        <td class="text-center">{{ $data['nombre'] }}</td>
                                        <td class="text-center">$<?php echo number_format($data['valor'],0) ?></td>
                                        <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($data['estado'] == "0")
                                            Pendiente
                                        @elseif($data['estado'] == "1")
                                            Rechazado
                                        @elseif($data['estado'] == "2")
                                            Anulado
                                        @else
                                            Aprobado
                                        @endif
                                    </span>
                                        </td>
                                        <td class="text-center">
                                            @if($data['estado'] == 3)
                                                <a href="{{ url('administrativo/registro/pdf/'.$data['id'].'/'.$V) }}" target="_blank" title="certificado-registro" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <br>
                            <div class="alert alert-danger">
                                <center>
                                    No se crearón Registros en esta vigencia.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TABLAS DE ADICIONES -->
                <br>
                <div id="tabAddEgr" class=" tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_AddE">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor Adición</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($Rubros as  $Rubro)
                                @foreach($valoresAdd as $valAdd)
                                    @if($valAdd['id'] == $Rubro['id_rubro'] and $valAdd['valor'] > 0)
                                        <tr>
                                            <td>{{ $Rubro['codigo'] }}</td>
                                            <td>{{ $Rubro['name'] }}</td>
                                            <td class="text-center">$ <?php echo number_format($valAdd['valor'],0);?>.00</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABLAS DE REDUCCIONES -->

                <div id="tabRedEgr" class=" tab-pane fade "><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_RedE">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor Reducción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($Rubros as  $Rubro)
                                @foreach($valoresRed as $valRed)
                                    @if($valRed['id'] == $Rubro['id_rubro'] and $valRed['valor'] > 0)
                                        <tr>
                                            <td>{{ $Rubro['codigo'] }}</td>
                                            <td>{{ $Rubro['name'] }}</td>
                                            <td class="text-center">$ <?php echo number_format($valRed['valor'],0);?>.00</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABLA DE CREDITOS Y CONTRACREDITOS -->

                <div id="tabCre" class=" tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_Cyc">
                            <thead>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor Credito</th>
                                <th class="text-center">Valor Contracredito</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($Rubros as  $Rubro)
                                @foreach($valoresCyC as $valCyC)
                                    @if($valCyC['id'] == $Rubro['id_rubro'])
                                        @if($valCyC['valorC'] == 0 and $valCyC['valorCC'] == 0)
                                        @else
                                            <tr>
                                                <td>{{ $Rubro['codigo'] }}</td>
                                                <td>{{ $Rubro['name'] }}</td>
                                                <td class="text-center">$ <?php echo number_format($valCyC['valorC'],0);?>.00</td>
                                                <td class="text-center">$ <?php echo number_format($valCyC['valorCC'],0);?>.00</td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                <div id="tabApl" class=" tab-pane fade"><br>
                    <h2 class="text-center">Aplazamientos</h2>
                </div>

                <!-- TABLA DE ORDEN DE PAGOS  -->

                <div id="tabOP" class=" tab-pane fade">
                    <div class="table-responsive">
                        @if(count($ordenPagos) >= 1)
                            <table class="table table-bordered" id="tabla_OrdenPago">
                                <thead>
                                <tr>
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Concepto</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Tercero</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Archivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($ordenPagos as $key => $data)
                                    <tr>
                                        <td class="text-center">{{ $data['code'] }}</td>
                                        <td class="text-center">{{ $data['nombre'] }}</td>
                                        <td class="text-center">$<?php echo number_format($data['valor'],0) ?></td>
                                        <td class="text-center">{{ $data['persona'] }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-pill badge-danger">
                                                @if($data['estado'] == "0")
                                                    Pendiente
                                                @elseif($data['estado'] == "1")
                                                    Pagado
                                                @else
                                                    Anulado
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/ordenPagos/pdf',$data['id']) }}" target="_blank" title="Orden de Pago" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <br>
                            <div class="alert alert-danger">
                                <center>
                                    No se crearón ordenes de pagos en esta vigencia.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TABLA DE PAGOS  -->

                <div id="tabP" class=" tab-pane fade">
                    <div class="table-responsive">
                        @if(count($pagos) >= 1)
                            <table class="table table-bordered" id="tabla_Pagos">
                                <thead>
                                <tr>
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Orden de Pago</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Tercero</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Archivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($pagos as $key => $data)
                                    <tr>
                                        <td class="text-center">{{ $data['code'] }}</td>
                                        <td class="text-center">{{ $data['nombre'] }}</td>
                                        <td class="text-center">$<?php echo number_format($data['valor'],0) ?></td>
                                        <td class="text-center">{{ $data['persona'] }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-pill badge-danger">
                                                @if($data['estado'] == "0")
                                                    Pendiente
                                                @elseif($data['estado'] == "1")
                                                    Pagado
                                                @else
                                                    Anulado
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('administrativo/egresos/pdf',$data['id']) }}" target="_blank" title="Comprobante de Egresos" class="btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <br>
                            <div class="alert alert-danger">
                                <center>
                                    No se crearón pagos en esta vigencia.
                                </center>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
 </div>

  </div> 
  <br><br>
        @stop
    @section('js')


        <!-- Datatables personalizadas buttons-->
            <script src="{{ asset('/js/datatableCustom.js') }}"></script>


@stop
