@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $añoActual }}
@stop
@section('content')
    @if($V != "Vacio")
        @include('modal.Informes.reporte')
        @include('modal.Informes.ejecucionPresupuestal')
        @include('modal.Proyectos.asignarubro')
        @include('modal.Informes.makeInforme')
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            @if($mesActual == 12)
                <li class="nav-item pillPri">
                    <a href="{{ url('/newPre/0',$añoActual+1) }}" class="nav-link"><span class="hide-menu"> Presupuesto de Egresos {{ $añoActual + 1 }}</span></a>
                </li>
            @elseif($mesActual == 1 or $mesActual == 2 and auth()->user()->roles->first()->id == 1)
                @if(auth()->user()->roles->first()->id == 1)
                    <li class="nav-item pillPri">
                        <a href="{{ url('/newPre/0',$añoActual-1) }}" class="nav-link"><span class="hide-menu"> Presupuesto de Egresos {{ $añoActual - 1 }}</span></a>
                    </li>
                @endif
            @endif
                <li class="nav-item principal">
                    <a class="nav-link"  href="#editar"> Presupuesto de Egresos {{ $añoActual }}</a>
                </li>
                <li class="nav-item pillPri">
                    <a class="nav-link "  href="{{ url('/presupuestoIng') }}">Presupuesto de Ingresos {{ $añoActual }}</a>
                </li>
                @if($V != "Vacio" and auth()->user()->roles->first()->id == 1)
                    <li class="nav-item pillPri">
                        <a class="nav-link "href="{{ url('/presupuesto/level/create/'.$V) }}" class="btn btn-success">
                            <i class="fa fa-edit"></i><span class="hide-menu">&nbsp;Editar Presupuesto</span>
                        </a>
                    </li>
                @endif
                <!-- SE DEBE ACTUALIZAR LA EJECUCIÓN PRESUPUESTAL.
                <li class="nav-item pillPri">
                    <a data-toggle="modal" data-target="#ejecucionPresupuestal" class="nav-link" style="cursor: pointer">Ejecución Presupuestal</a>
                </li>
                -->
                @if($V != "Vacio" and auth()->user()->roles->first()->id != 2)
                    <li class="dropdown">
                        <a class="nav-item dropdown-toggle pillPri" style="cursor: pointer" onclick="getModalToMakeInforme()">Generar Informe de Presupuestos</a>
                        <!-- SE COMENTAN LOS REPORTES QUE NO TIENEN ACCESO FUNCIONAL.
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
                        -->
                    </li>
                    <!-- SE COMENTAN LOS AÑOS DEBIDO A QUE REALMENTE NO HAY HISTORICO PARA LA ALCALDIA
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
                    -->
                @endif
                @if($V == "Vacio")
                    <li class="nav-item pillPri">
                        <a href="{{ url('/presupuesto/vigencia/create/0') }}" class="btn btn-drop">
                            <i class="fa fa-plus"></i>
                            <span class="hide-menu"> Nuevo Presupuesto de Egresos</span></a>
                    </li>
                @endif
        </ul>
        <div class="col-md-12 align-self-center" style="background-color:#fff;">
            @if($V != "Vacio")
                <div class="row" >
                    <div class="breadcrumb col-md-12 text-center" >
                        <strong>
                            <h4><b>Presupuesto de Egresos {{ $añoActual }}</b></h4>
                        </strong>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li class="nav-item active">
                        <a class="nav-link" data-toggle="pill" href="#tabHome"><i class="fa fa-home"></i></a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" data-toggle="pill"  href="@can('fuentes-list') #tabFuente @endcan">Fuentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="@can('rubros-list') #tabRubros @endcan">Rubros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="@can('pac-list') #tabPAC @endcan">PAC</a>
                    </li>
                    @if(count($cdps) >= 1)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="@can('cdps-list') {{ url('administrativo/cdp/'.$V) }} @endcan">CDP's</a>
                        </li>
                    @endif
                    @if(count($registros) >= 1)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="@can('registros-list') {{ url('administrativo/registros/'.$V) }} @endcan">Registros</a>
                        </li>
                    @endif
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
                    @if(count($ordenPagos) >= 1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('administrativo/ordenPagos/'.$V) }}">Orden de Pago</a>
                        </li>
                    @endif
                    @if(count($pagos) >= 1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('administrativo/pagos/'.$V) }}">Pagos</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab_proyectos" onclick="show_bpins()">Proyectos</a>
                    </li>
                </ul>
                <hr>

                <!-- TABLA DE PRESUPUESTO -->

                <div class="tab-content" style="background-color: white">
                    <div id="tabHome" class="tab-pane active"><br>
                        <div class="table-responsive">
                            <table id="tabla_presupuesto1" class="table table-hover table-bordered table-striped ">
                                <thead>
                                <tr>
                                    <th class="text-center">Codigo BPIN</th>
                                    <th class="text-center">Codigo Actividad</th>
                                    <th class="text-center">Nombre Actividad</th>
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
                                    @if(auth()->user()->roles->first()->id != 2)
                                        <th class="text-center">Cod Dependencia</th>
                                        <th class="text-center">Dependencia</th>
                                    @endif
                                    <th class="text-center">Fuente</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    <tr>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['codBpin']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['codActiv']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['nameActiv']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">@if($codigo['id_rubro'] != 0) <a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}"> @endif{{ $codigo['cod'] }}</a></td>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_inicial'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['adicion'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['reduccion'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['credito'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['ccredito'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_def'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['cdps'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['registros'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['saldo_disp'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['saldo_cdp'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['ordenes_pago'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['pagos'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['cuentas_pagar'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['reservas'],0);?></td>
                                        @if(auth()->user()->roles->first()->id != 2)
                                            <td class="text-dark" style="vertical-align:middle;">{{ $codigo['codDep']}}</td>
                                            <td class="text-dark" style="vertical-align:middle;">{{ $codigo['dep']}}</td>
                                        @endif
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['fuente']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-center">Codigo BPIN</th>
                                    <th class="text-center">Codigo Actividad</th>
                                    <th class="text-center">Nombre Actividad</th>
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
                                    @if(auth()->user()->roles->first()->id != 2)
                                        <th class="text-center">Cod Dependencia</th>
                                        <th class="text-center">Dependencia</th>
                                    @endif
                                    <th class="text-center">Fuente</th>
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
                                    <th class="text-center">Valores de Fuentes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    <tr>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                        <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_inicial'],0);?></td>
                                        <td class="text-dark" style="vertical-align:middle;">
                                            @foreach($fonts as $font)
                                                @if($font['id'] == $codigo['cod'])
                                                    {{$font['code']}} {{$font['description']}} = $ <?php echo number_format($font['value'],0);?>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE RUBROS -->

                    <div id="tabRubros" class="tab-pane fade"><br>
                        <div class="text-center">
                            <select id="tipoRubros" class="form-control" name="tipoRubros" onchange="findRubros(this.value)">
                                <option value="0">Seleccione el tipo a filtrar</option>
                                <option value="FUNCIONAMIENTO">FUNCIONAMIENTO</option>
                                <option value="INVERSION">INVERSIÓN</option>
                            </select>
                        </div>
                        <br>
                        <div class="table-responsive" style="display: none" id="tablaList">
                            <table class="table table-bordered" id="tabla_Rubros">
                                <thead>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Valor Inicial</th>
                                    <th class="text-center">Valor Disponible</th>
                                    @if(auth()->user()->roles->first()->id != 2)
                                        <th class="text-center">Valor Por Asignar</th>
                                    @endif
                                    <th class="text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    @if($codigo['id_rubro'] > 0 and $codigo['tipo'] == 'Funcionamiento')
                                        <tr>
                                            <td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>
                                            <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                            <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_inicial'],0);?></td>
                                            <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_disp'],0);?></td>
                                            @if(auth()->user()->roles->first()->id != 2)<td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_asign'],0);?></td>@endif
                                            <td class="text-center">
                                                <a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
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
                                    <th class="text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    @if($codigo['id_rubro'] > 0)
                                        @if($codigo['adicion'] > 0)
                                            <tr>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['adicion'],0);?></td>
                                                <td class="text-center">
                                                    <a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
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
                                    <th class="text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    @if($codigo['id_rubro'] > 0)
                                        @if($codigo['reduccion'] > 0)
                                            <tr>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['reduccion'],0);?></td>
                                                <td class="text-center">
                                                    <a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
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
                                    <th class="text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($presupuesto as $codigo)
                                    @if($codigo['id_rubro'] > 0)
                                        @if($codigo['credito'] > 0 or $codigo['ccredito'] > 0)
                                            <tr>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td>
                                                <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['credito'],0);?></td>
                                                <td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['ccredito'],0);?></td>
                                                <td class="text-center">
                                                    <a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE PROYECTOS  -->

                    <div id="tab_proyectos" class=" tab-pane fade">
                        <div class="table-responsive mt-3" id="tabla_bpins">
                            <table class="table table-bordered" id="tabla_Proy">
                                <thead>
                                <tr>
                                    <th class="text-center">Codigo Proyecto</th>
                                    <th class="text-center">Nombre Proyecto</th>
                                    <th class="text-center">Secretaria</th>
                                    <th class="text-center">Ver</th>
                                    <th class="text-center"><i class="fa fa-file-pdf-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bpins->unique('cod_proyecto') as $item)
                                    <tr>
                                        <td>{{$item->cod_proyecto}}</td>
                                        <td>{{$item->nombre_proyecto}}</td>
                                        <td>{{$item->secretaria}}</td>
                                        <td><a class="btn btn-success" onclick="show_proyecto('{{$item->cod_proyecto}}')">Ver</a></td>
                                        <td><a class="btn btn-success" href="/presupuesto/proyecto/{{$item->cod_proyecto}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive" id="tabla_bpin_actividades">
                            <ul class="nav nav-tabs mt-3 mb-3">
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="modal" data-target="#myModal">Nueva Actividad</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#menu1">Adición</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#menu2">Reducción</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#menu2">Decreto</a>
                                </li>
                            </ul>
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
            @else
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>Presupuesto de Egresos Año {{ $añoActual }}</b></h4>
                    </strong>
                </div>
                <br><br>
                <div class="alert alert-danger">
                    No se ha creado un presupuesto actual de egresos, para crearlo de click al siguiente link:
                    <a href="{{ url('presupuesto/vigencia/create/0') }}" class="alert-link">Crear Presupuesto de Egresos</a>
                </div>
            @endif
        </div>
    </div><br><br>
@stop
@section('js')
    <!-- Datatables personalizadas buttons-->
    <script src="{{ asset('/js/datatableCustom.js') }}"></script>

    <!-- tabla de proyectos -->
    <script>

        function getModalToMakeInforme(){
            $('#modalMakeInforme').modal('show');

        }

        const bpins = @json($bpins);
        const rubInvDisp = @json($rubBPIN);
        const vigencia_id = @json($V);
        //console.log('bpins', bpins)

        $('.asignarRubroSelect').select2();

        const show_proyecto = cod_proyecto =>{
            $('#tabla_bpins').hide();
            $('#tabla_bpin_actividades').show();
            $('#tbody-actividades').empty();
            $('#input-cod-proyecto').val(cod_proyecto);
            bpins.filter(r => r.cod_proyecto == cod_proyecto).forEach(e =>{
                if (e.rubro != "No") var button = e.rubro+`<br> Dinero Asignado: `+e.rubro_find[0].propios.toLocaleString();
                else {
                    if(rubInvDisp.length == 0) var button = `<b>No hay rubros de inversión disponibles para asignar.</b>`;
                    else var button = `<button onclick="getModalAsignaRubro(${e.cod_actividad})" class="btn btn-primary">Asignar Rubro a la Actividad</button>`;
                }
                $('#tbody-actividades').append(`
                <tr>
                    <td>${e.cod_actividad}</td>
                    <td>${e.actividad}</td>
                    <td>${button}</td>
                    <td><a href="/presupuesto/actividad/${e.id}/${vigencia_id}" class="btn btn-primary"><i class="fa fa-info-circle"></i></a></td>
                </tr>
            `);
            });

        }

        function getModalAsignaRubro(code){
            bpins.filter(r => r.cod_actividad == code).forEach(e =>{
                document.getElementById("nameActividad").innerHTML = e.actividad;
                document.getElementById("codeActividad").innerHTML = code;
                document.getElementById("dispActividad").innerHTML = e.saldo;
                $('#actividadCode').val(code);
                $('#vigencia_id').val(e.vigencia_id);
                $('#valueAsignarRubro').val(e.saldo);

                $(document).on('keyup', '#valueAsignarRubro', function(event) {
                    let max= parseInt(e.saldo);
                    let valor = parseInt(this.value);
                    if(valor>max){
                        alert("El Valor no está Permitido")
                        this.value = max;
                    }

                });
            });
            $('#formAsignaRubro').modal('show');
        }

        function findRubros(tipo){
            var table = $('#tabla_Rubros').DataTable();
            table.destroy();
            document.getElementById('tablaList').innerHTML = '';
            if (tipo == "FUNCIONAMIENTO"){
                document.getElementById('tablaList').innerHTML ='<table class="table table-bordered" id="tabla_Rubros">'+
                    '<thead><tr><th class="text-center">Rubro</th><th class="text-center">Nombre</th><th class="text-center">Valor Inicial</th><th class="text-center">Valor Disponible</th>@if(auth()->user()->roles->first()->id != 2)<th class="text-center">Valor Por Asignar</th>@endif<th class="text-center">Ver</th></tr></thead>'+
                    '<tbody>@foreach($presupuesto as $codigo)@if($codigo['id_rubro'] > 0 and $codigo['tipo'] == 'Funcionamiento')<tr><td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>'+
                    '<td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td><td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_inicial'],0);?></td>'+
                    '<td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_disp'],0);?></td>@if(auth()->user()->roles->first()->id != 2)<td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_asign'],0);?></td>@endif<td class="text-center"><a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a></td></tr>'+
                    '@endif @endforeach</tbody></table>';
            } else if(tipo  == "INVERSION"){
                document.getElementById('tablaList').innerHTML ='<table class="table table-bordered" id="tabla_Rubros">'+
                    '<thead><tr><th class="text-center">Rubro</th><th class="text-center">Nombre</th><th class="text-center">Valor Inicial</th><th class="text-center">Valor Disponible</th>@if(auth()->user()->roles->first()->id != 2)<th class="text-center">Valor Por Asignar</th>@endif<th class="text-center">Ver</th></tr></thead>'+
                    '<tbody>@foreach($presupuesto as $codigo)@if($codigo['id_rubro'] > 0 and $codigo['tipo'] == 'Inversion')<tr><td class="text-dark" style="vertical-align:middle;">{{ $codigo['cod']}}</td>'+
                    '<td class="text-dark" style="vertical-align:middle;">{{ $codigo['name']}}</td><td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['presupuesto_inicial'],0);?></td>'+
                    '<td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_disp'],0);?></td>@if(auth()->user()->roles->first()->id != 2)<td class="text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['rubros_asign'],0);?></td>@endif<td class="text-center"><a href="{{ url('presupuesto/rubro/'.$codigo['id_rubro']) }}" class="btn-sm btn-success"><i class="fa fa-info"></i></a></td></tr>'+
                    '@endif @endforeach</tbody></table>';
            } else document.getElementById('tablaList').innerHTML = '';
            $("#tablaList").show();
            table = $('#tabla_Rubros').DataTable( {
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
                responsive: "true",
                "ordering": false,
                dom: 'lrtip',
                paging: false,
                info: false,
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
            } );
        }

        const show_bpins = ()  =>{
            $('#tabla_bpins').show();
            $('#tabla_bpin_actividades').hide();
        }

    </script>
@stop