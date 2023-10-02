@extends('layouts.dashboard')
@section('titulo')
    Vigencia: {{ $vigencia->vigencia }}
@stop
@section('content')
    <div class="row inputCenter">
        <ul class="nav nav-pills">
            <li class="nav-item principal">
                <a class="nav-link"  href=""> Presupuesto de Ingresos {{ $vigencia->vigencia }}</a>
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
        </ul>
        <div class="col-md-12 align-self-center">
            <div class="row" >
                <div class="breadcrumb col-md-12 text-center" >
                    <strong>
                        <h4><b>Presupuesto de Ingresos {{ $vigencia->vigencia }}</b></h4>
                    </strong>
                </div>
            </div>
                <ul class="nav nav-pills">
                    <li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#tabHome">ENERO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabFebrero">FEBRERO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabMarzo">MARZO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabAbril">ABRIL</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabMayo">MAYO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabJunio">JUNIO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabJulio">JULIO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabAgosto">AGOSTO</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabSeptiembre">SEPTIEMBRE</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabOctubre">OCTUBRE</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabNoviembre">NOVIEMBRE</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tabDiciembre">DICIEMBRE</a></li>
                    <li class="nav-item hidden" >
                        <a class="nav-link" data-toggle="pill"  href="@can('fuentes-list') #tabFuente @endcan">Fuentes</a>
                    </li>
                    <li class="nav-item hidden">
                        <a class="nav-link" data-toggle="pill" href="@can('rubros-list') #tabRubros @endcan">Rubros</a>
                    </li>
                    <li class="nav-item hidden">
                        <a class="nav-link" data-toggle="pill" href="@can('pac-list') #tabPAC @endcan">PAC</a>
                    </li>
                    <li class="nav-item hidden">
                        <a class="nav-link disabled" data-toggle="pill" href="#tabApl">Aplazamientos</a>
                    </li>
                </ul>
                <div class="tab-content" style="background-color: white">
                    <div id="tabHome" class="tab-pane active"><br>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_presupuesto" style="text-align: center">
                                <thead>
                                <tr><th colspan="10">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th></tr>
                                <tr><th colspan="10">CONCEJO MUNICIPAL</th></tr>
                                <tr>
                                    <th colspan="2">RESPONSABLE:</th>
                                    <th colspan="9">NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="2">CORTE</th>
                                    <th colspan="9">21 ENERO DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                    <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                    <!-- REDUCCIÓN -->
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                    <!-- PRESUPUESTO DEFINITIVO -->
                                            @foreach($valoresIniciales as $valorInicial)
                                                @if($valorInicial['id'] == $codigo['id'])
                                                    <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                                @endif
                                            @endforeach
                                            @if($codigo['valor'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                            @endif
                                            <!-- RECAUDADO MES-->
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 171.995.884</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 171.995.884</td>
                                    <!-- TOTAL RECAUDADO-->
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 171.995.884</td>
                                    <!-- SALDO POR RECAUDAR -->
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 694.238.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="tabFebrero" class="tab-pane fade"><br>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabla_febrero" style="width: 100%">
                                <thead>
                                <tr><th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th></tr>
                                <tr><th colspan="12">CONCEJO MUNICIPAL</th></tr>
                                <tr><th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th></tr>
                                <tr><th colspan="12">CORTE: 29 FEBRERO DE 2020</th></tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 47.000.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 171.995.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 218.995.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 647.238.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
                <div id="tabMarzo" class="tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" align="100%" id="tabla_marzo" style="width: 100%">
                            <thead>
                            <tr>
                                <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                            </tr>
                            <tr>
                                <th colspan="12">CONCEJO MUNICIPAL</th>
                            </tr>
                            <tr>
                                <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                            </tr>
                            <tr>
                                <th colspan="12">CORTE: 31 MARZO DE 2020</th>
                            </tr>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">P. Inicial</th>
                                <th class="text-center">Adición</th>
                                <th class="text-center">Reducción</th>
                                <th class="text-center">P.Definitivo</th>
                                <th class="text-center">Recaudo del Mes</th>
                                <th class="text-center">Recaudo Acumulado</th>
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
                                    <!-- ADICIÓN -->
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresAdd as $valorAdd)
                                        @if($codigo['id_rubro'] == $valorAdd['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    <!-- REDUCCIÓN -->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                    <!-- PRESUPUESTO DEFINITIVO -->
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @if($codigo['valor'])
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                    @endif
                                    <!-- RECAUDADO MES-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 82.000.000</td>
                                    <!-- RECAUDADO ACUMULADO-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 218.995.884</td>
                                    <!-- TOTAL RECAUDADO-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 300.995.884</td>
                                    <!-- SALDO POR RECAUDAR -->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 565.238.959</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tabAbril" class="tab-pane fade"><br>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" align="100%" id="tabla_abril" style="width: 100%">
                            <thead>
                            <tr>
                                <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                            </tr>
                            <tr>
                                <th colspan="12">CONCEJO MUNICIPAL</th>
                            </tr>
                            <tr>
                                <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                            </tr>
                            <tr>
                                <th colspan="12">CORTE: 30 ABRIL DE 2020</th>
                            </tr>
                            <tr>
                                <th class="text-center">Rubro</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">P. Inicial</th>
                                <th class="text-center">Adición</th>
                                <th class="text-center">Reducción</th>
                                <th class="text-center">P.Definitivo</th>
                                <th class="text-center">Recaudo del Mes</th>
                                <th class="text-center">Recaudo Acumulado</th>
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
                                    <!-- ADICIÓN -->
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        @endif
                                    @endforeach
                                    @foreach($valoresAdd as $valorAdd)
                                        @if($codigo['id_rubro'] == $valorAdd['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    <!-- REDUCCIÓN -->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                    <!-- PRESUPUESTO DEFINITIVO -->
                                    @foreach($valoresIniciales as $valorInicial)
                                        @if($valorInicial['id'] == $codigo['id'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                        @endif
                                    @endforeach
                                    @if($codigo['valor'])
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                    @endif
                                    <!-- RECAUDADO MES-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 33.000.000</td>
                                    <!-- RECAUDADO ACUMULADO-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 300.995.884</td>
                                    <!-- TOTAL RECAUDADO-->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 333.995.884</td>
                                    <!-- SALDO POR RECAUDAR -->
                                    <td class="text-center text-dark" style="vertical-align:middle;">$ 532.238.959</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tabMayo" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_mayo" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 31 MAYO DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 60.329.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 333.995.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 394.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 471.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabJunio" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_junio" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 30 JUNIO DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 46.000.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 394.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 440.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 425.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabJulio" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_juLio" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 30 JULIO DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 440.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 440.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 425.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabAgosto" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_agosto" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 31 AGOSTO DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 120.000.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 440.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 560.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 305.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabSeptiembre" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_septiembre" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 30 SEPTIEMBRE DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 560.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 560.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 305.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabOctubre" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_octubre" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 31 OCTUBRE DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 70.000.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 560.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 630.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 235.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabNoviembre" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_noviembre" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 30 NOVIEMBRE DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 630.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 630.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 235.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
                <div id="tabDiciembre" class="tab-pane fade"><br>
                    <div class="table-responsive">
                            <table class="table table-hover table-bordered" align="100%" id="tabla_diciembre" style="width: 100%">
                                <thead>
                                <tr>
                                    <th colspan="12">MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CONCEJO MUNICIPAL</th>
                                </tr>
                                <tr>
                                    <th colspan="12">RESPONSABLE: NICOLLE AMADOR HOOKER</th>
                                </tr>
                                <tr>
                                    <th colspan="12">CORTE: 31 DICIEMBRE DE 2020</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rubro</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">P. Inicial</th>
                                    <th class="text-center">Adición</th>
                                    <th class="text-center">Reducción</th>
                                    <th class="text-center">P.Definitivo</th>
                                    <th class="text-center">Recaudo del Mes</th>
                                    <th class="text-center">Recaudo Acumulado</th>
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
                                        <!-- ADICIÓN -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                            @endif
                                        @endforeach
                                        @foreach($valoresAdd as $valorAdd)
                                            @if($codigo['id_rubro'] == $valorAdd['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorAdd['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        <!-- REDUCCIÓN -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 0</td>
                                        <!-- PRESUPUESTO DEFINITIVO -->
                                        @foreach($valoresIniciales as $valorInicial)
                                            @if($valorInicial['id'] == $codigo['id'])
                                                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                                            @endif
                                        @endforeach
                                        @if($codigo['valor'])
                                            <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
                                        @endif
                                        <!-- RECAUDADO MES-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 137.000.000</td>
                                        <!-- RECAUDADO ACUMULADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 630.324.884</td>
                                        <!-- TOTAL RECAUDADO-->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 767.324.884</td>
                                        <!-- SALDO POR RECAUDAR -->
                                        <td class="text-center text-dark" style="vertical-align:middle;">$ 98.909.959</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>

                    <!-- TABLA DE FUENTES -->

                    <div id="tabFuente" class="tab-pane fade"><br>
                        <div class="table-responsive">
                            <br>
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
                                    <th class="text-center">Valor Por Recaudar</th>
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

                    <!-- TABLAS DE ADICIONES -->

                    <div id="tabAddIng" class=" tab-pane fade"><br>
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

                    <div id="tabRedIng" class=" tab-pane fade"><br>
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

                    <!-- TABLA DE APLAZAMIENTOS  -->

                    <div id="tabApl" class=" tab-pane fade"><br>
                        <h2 class="text-center">Aplazamientos</h2>
                    </div>

                </div>

        </div>
    </div>
@stop
@section('js')
    <script>

        $('#tabla_presupuesto').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE ENERO'
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

        });
        $('#tabla_febrero').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE FEBRERO'
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

        });
        $('#tabla_marzo').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE MARZO'
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

        });
        $('#tabla_abril').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE ABRIL'
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

        });
        $('#tabla_mayo').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE MAYO'
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

        });
        $('#tabla_junio').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE JUNIO'
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

        });
        $('#tabla_juLio').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE JULIO'
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

        });
        $('#tabla_agosto').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE AGOSTO'
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

        });
        $('#tabla_septiembre').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE SEPTIEMBRE'
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

        });
        $('#tabla_octubre').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE OCTUBRE'
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

        });
        $('#tabla_noviembre').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE NOVIEMBRE'
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

        });
        $('#tabla_diciembre').DataTable({
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

            responsive: "true",
            "ordering": false,
            dom: 'Bfrtilp',
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
                    className: 'btn btn-primary',
                    title: 'CORTE DE DICIEMBRE'
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

        });

        $('#tabla_Rubros').DataTable( {
            responsive: true,
            "searching": true,
            "pageLength": 5,
            dom: 'Bfrtip',
            buttons: [
                'pdf' ,'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_fuentes').DataTable( {
            responsive: true,
            "searching": false,
            dom: 'Bfrtip',
            buttons: [
                'pdf' ,'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_AddE').DataTable( {
            responsive: true,
            "searching": true,
            "pageLength": 5,
            dom: 'Bfrtip',
            buttons: [
                'pdf' ,'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_RedE').DataTable( {
            responsive: true,
            "searching": true,
            "pageLength": 5,
            dom: 'Bfrtip',
            buttons: [
                'pdf' ,'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_Cyc').DataTable( {
            responsive: true,
            "searching": true,
            "pageLength": 5,
            dom: 'Bfrtip',
            buttons: [
                'pdf' ,'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop