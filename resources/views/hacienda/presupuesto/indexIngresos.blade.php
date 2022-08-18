@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $añoActual }}
@stop
@section('content')
    @if($V != "Vacio")
        @include('modal.Informes.reporte')
    @endif
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            <li class="nav-item principal">
                <a class="nav-link"  href="#editar"> Presupuesto de Ingresos {{ $añoActual }}</a>
            </li>
            <li class="nav-item pillPri">
                <a class="nav-link "  href="{{ url('/presupuesto') }}">Presupuesto de Egresos {{ $añoActual }}</a>
            </li>
            @if($V != "Vacio")
                <li class="nav-item pillPri"> <a class="nav-link "href="{{ url('/presupuesto/level/create/'.$V) }}" class="btn btn-success"><i class="fa fa-edit"></i><span class="hide-menu">&nbsp;Editar Presupuesto</span></a></li>
            @endif
            @if($V != "Vacio")
                <li class="dropdown">
                    <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Informes&nbsp;<i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu ">
                        <li class="dropdown-submenu">
                            <a class="test btn btn-drop text-left" href="#">Contractual &nbsp;</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('/presupuesto/informes/contractual/homologar/'.$V) }}" class="btn btn-drop text-left">Homologar</a></li>
                                <li><a data-toggle="modal" data-target="#reporteHomologar" class="btn btn-drop text-left">Reporte</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="btn btn-drop text-left">FUT </a>
                        </li>
                        <li>
                            <a href="{{ url('/presupuesto/informes/lvl/1') }}" class="btn btn-drop text-left">Niveles</a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-drop text-left">Comparativo (Ingresos - Gastos)</a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-drop text-left">Fuentes</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="nav-item dropdown-toggle pillPri" href="" data-toggle="dropdown">Historico&nbsp;<i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu ">
                        @foreach($years as $year)
                            <li>
                                <a href="{{ url('/presupuesto/historico/'.$year['id']) }}" class="btn btn-drop text-left">{{ $year['info'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
            @if($V == "Vacio")
                <li class="nav-item pillPri">
                    <a href="{{ url('/presupuesto/vigencia/create/1') }}" class="btn btn-drop">
                        <i class="fa fa-plus"></i>
                        <span class="hide-menu"> Nuevo Presupuesto de Ingresos</span></a>
                </li>
            @endif
        </ul>
        <div class="col-md-12 align-self-center">
            @if($V != "Vacio")
                <div class="row" >
                    <div class="breadcrumb col-md-12 text-center" >
                        <strong>
                            <h4><b>Presupuesto de Ingresos {{ $añoActual }}</b></h4>
                        </strong>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li class="nav-item active">
                        <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill"  href="@can('fuentes-list') #tabFuente @endcan">Comprobantes de Ingresos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="@can('rubros-list') #tabRubros @endcan">Rubros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="@can('pac-list') #tabPAC @endcan">PAC</a>
                    </li>
                </ul>
                <div class="tab-content" style="background-color: white">
                    <div id="tabHome" class="tab-pane active"><br>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_presupuesto1" style="text-align: center">
                                <thead>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">PPTO. Inicial</th>
                                    <th class="text-center">Total Recaudado</th>
                                    <th class="text-center">Saldo Por Recaudar</th>
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
                                        @endif
                                    <!-- TOTAL RECAUDADO-->
                                        @foreach($valoresFinRec as $valorFinRec)
                                            @if($valorFinRec['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinRec['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @foreach($totalRecaud as $totalR)
                                            @if($codigo['id_rubro'] == $totalR['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($totalR['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                    <!-- SALDO POR RECAUDAR -->
                                        @foreach($valoresFinSald as $valorFinSald)
                                            @if($valorFinSald['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorFinSald['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @foreach($saldoRecaudo as $saldoR)
                                            @if($codigo['id_rubro'] == $saldoR['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$0</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE COMPROBANTES DE INGRESOS -->

                    <div id="tabFuente" class="tab-pane fade"><br>
                        <div class="table-responsive">
                            @if(count($comprobanteIng) >= 1)
                                <a href="{{ url('administrativo/CIngresos/'.$V) }}" class="btn btn-primary btn-block m-b-12">Comprobantes de Ingresos</a>
                                <br><br>
                                <table class="table table-bordered" id="tabla_CIng">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Concepto</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Ver</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($comprobanteIng as $key => $data)
                                        <tr>
                                            <td class="text-center">{{ $data->code }}</td>
                                            <td class="text-center">{{ $data->concepto }}</td>
                                            <td class="text-center">
                                    <span class="badge badge-pill badge-danger">
                                        @if($data->estado == "0")
                                            Pendiente
                                        @elseif($data->estado == "1")
                                            Rechazado
                                        @elseif($data->estado == "2")
                                            Anulado
                                        @elseif($data->estado == "3")
                                            Aprobado
                                        @else
                                            En Espera
                                        @endif
                                    </span>
                                            </td>
                                            <td class="text-center">$<?php echo number_format($data->valor,0) ?></td>
                                            <td class="text-center">
                                                <a href="{{ url('administrativo/CIngresos/show/'.$data->id) }}" title="Ver Comprobante de Ingreso" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <br>
                                <div class="alert alert-danger">
                                    <center>
                                        No hay comprobantes de ingresos.<br><br>
                                        <a href="{{ url('administrativo/CIngresos/create/'.$V) }}" class="btn btn-danger ">Crear Comprobante de Ingresos</a>
                                    </center>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- TABLA DE RUBROS -->

                    <div id="tabRubros" class="tab-pane fade"><br>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabla_Rubros_Ingresos">
                                <thead>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($Rubros as  $Rubro)
                                    <tr>
                                        <td>{{ $Rubro['codigo'] }}</td>
                                        <td>{{ $Rubro['name'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ url('presupuesto/rubro/'.$Rubro['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE PAC -->

                    <div id="tabPAC" class="tab-pane fade"><br>
                        <div class="table-responsive">
                            @if(count($PACdata) > 0)
                                <table class="table table-bordered" id="tabla_PAC">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Rubro</th>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Valor a Asignar</th>
                                        <th class="text-center">Total Asignado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($PACdata as $val)
                                        <tr class="text-center">
                                            <td>{{ $val['id'] }}</td>
                                            <td>{{ $val['rubro'] }}</td>
                                            <td>{{ $val['name'] }}</td>
                                            <td>$<?php echo number_format($val['valorD'],0) ?></td>
                                            <td>$<?php echo number_format($val['totalD'],0) ?></td>
                                            <td><a href="{{ url('administrativo/pac/'.$val['id'].'/edit') }}" title="Editar" class="btn btn-success"><i class="fa fa-edit"></i></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <br><br>
                                <div class="alert alert-danger">
                                    <center>
                                        No se encuentra ningun PAC almacenado en la plataforma, para crearlo de click al siguiente link:
                                        <a href="{{ url('administrativo/pac/create') }}" class="alert-link">Crear PAC</a>.
                                    </center>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            @else
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>Presupuesto de Ingresos Año {{ $añoActual }}</b></h4>
                    </strong>
                </div>
                <br><br>
                <div class="alert alert-danger">
                    No se ha creado un presupuesto actual de ingresos, para crearlo de click al siguiente link:
                    <a href="{{ url('presupuesto/vigencia/create/1') }}" class="alert-link">Crear Presupuesto de Ingresos</a>.
                </div>
            @endif
        </div>
    </div>
@stop
@section('js')
    <!-- Datatables personalizadas buttons-->
    <script src="{{ asset('/js/datatableCustom.js') }}"></script>
@stop