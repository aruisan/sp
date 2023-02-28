@extends('layouts.dashboard')
@section('titulo')  FORMULARIO DELINEACIÓN Y URBANISMO  @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center" translate="no">
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/impuestos/delineacion') }}"><i class="fa fa-arrow-circle-left"></i><i class="fa fa-home"></i> </a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" ><i class="fa fa-plus"></i><i class="fa fa-home"></i>NUEVO REGISTRO</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>FORMULARIO UNICO NACIONAL DELINEACION Y URBANISMO</b></h4>
                        <h4><b>MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</b></h4>
                        <h4><b>SECRETARIA DE GOBIERNO - HACIENDA</b></h4>
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" id="prog">
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">DATOS GENERALES</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td><b>Fecha: {{ Carbon\Carbon::parse($delinacion->fecha)->format('d-m-Y') }}</b></td>
                                    <td>Funcionario Responsable: {{ $responsable->name }} - {{ $responsable->email }}</td>
                                    <td> 1.2
                                        @if($delinacion->tramite == "INICIAL")
                                            <b>TRAMITE INICIAL</b>
                                            <input type="hidden" name="tramite" value="INICIAL">
                                        @elseif($delinacion->tramite == "MODIFICACIÓN")
                                            <b>MODIFICACIÓN DE LICENCIA VIGENTE</b>
                                            <input type="hidden" name="tramite" value="MODIFICACION">
                                        @elseif($delinacion->tramite == "REVALIDACIÓN")
                                            <b>REVALIDACIÓN</b>
                                            <input type="hidden" name="tramite" value="REVALIDACION">
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">IDENTIFICACIÓN DE LA SOLICITUD</th>
                                </tr>
                                <tr>
                                    <td>
                                        1.1 TIPO DE TRÁMITE
                                        <select class="form-control" id="tipoTramite" name="tipoTramite" onchange="cambioTipoTramite(this.value)" required>
                                            @if($tramite == "INICIAL") <option value="0">Seleccione el tipo de tramite</option> @endif
                                            <option value="LICENCIA DE URBANIZACIÓN" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "LICENCIA DE URBANIZACIÓN") selected @endif>LICENCIA DE URBANIZACIÓN</option>
                                            <option value="LICENCIA DE PARCELACIÓN" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "LICENCIA DE PARCELACIÓN") selected @endif>LICENCIA DE PARCELACIÓN</option>
                                            <option value="LICENCIA DE SUBDIVISIÓN" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "LICENCIA DE SUBDIVISIÓN") selected @endif>LICENCIA DE SUBDIVISIÓN</option>
                                            <option value="LICENCIA DE CONSTRUCCIÓN" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "LICENCIA DE CONSTRUCCIÓN") selected @endif>LICENCIA DE CONSTRUCCIÓN</option>
                                            <option value="INTERVENCIÓN Y OCUPACIÓN DEL ESPACIO PÚBLICO" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "INTERVENCIÓN Y OCUPACIÓN DEL ESPACIO PÚBLICO") selected @endif>INTERVENCIÓN Y OCUPACIÓN DEL ESPACIO PÚBLICO</option>
                                            <option value="RECONOCIMIENTO DE LA EXISTENCIA DE UNA EDIFICACIÓN" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "RECONOCIMIENTO DE LA EXISTENCIA DE UNA EDIFICACIÓN") selected @endif>RECONOCIMIENTO DE LA EXISTENCIA DE UNA EDIFICACIÓN</option>
                                            <option value="OTRAS ACTUACIONES" @if($tramite != "INICIAL" and $delinacion->tipoTramite == "OTRAS ACTUACIONES") selected @endif>OTRAS ACTUACIONES</option>
                                        </select>
                                        @if($tramite != "INICIAL" and $delinacion->tipoTramite == "OTRAS ACTUACIONES")
                                            <span id="otroTramite">
                                                <input  class="form-control" type="text" name="cualOtraActuacion" id="cualOtraActuacion" value="{{$delinacion->cualOtraActuacion}}" placeholder="Cual Otra?">
                                            </span>
                                        @else
                                            <span style="display: none" id="otroTramite">
                                                <input  class="form-control" type="text" name="cualOtraActuacion" id="cualOtraActuacion" @if($tramite != "INICIAL" ) value="" @endif
                                                placeholder="Cual Otra?">
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        1.3 MODALIDAD LICENCIA DE URBANIZACIÓN
                                        <select class="form-control" id="modalidadLicenciaUrbanizacion" name="modalidadLicenciaUrbanizacion">
                                            <option value="DESARROLLO" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaUrbanizacion == "DESARROLLO") selected @endif>DESARROLLO</option>
                                            <option value="SANEAMIENTO" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaUrbanizacion == "SANEAMIENTO") selected @endif>SANEAMIENTO</option>
                                            <option value="REURBANIZACIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaUrbanizacion == "REURBANIZACIÓN") selected @endif>REURBANIZACIÓN</option>
                                        </select>
                                    </td>
                                    <td>
                                        1.4 MODALIDAD LICENCIA DE SUBDIVISIÓN
                                        <select class="form-control" id="modalidadLicenciaSubdivision" name="modalidadLicenciaSubdivision">
                                            <option value="SUBDIVISIÓN RURAL" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaSubdivision == "SUBDIVISIÓN RURAL") selected @endif>SUBDIVISIÓN RURAL</option>
                                            <option value="SUBDIVISIÓN URBANA" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaSubdivision == "SUBDIVISIÓN URBANA") selected @endif>SUBDIVISIÓN URBANA</option>
                                            <option value="RELOTEO" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaSubdivision == "RELOTEO") selected @endif>RELOTEO</option>
                                        </select>
                                    </td>
                                    <td>
                                        1.5 MODALIDAD LICENCIA DE CONSTRUCCIÓN
                                        <select class="form-control" id="modalidadLicenciaConstruccion" name="modalidadLicenciaConstruccion" onchange="cambioTipoTramite(this.value)">
                                            @if($tramite == "INICIAL") <option value="0">Seleccione la modalidad</option>@endif
                                            <option value="OBRA NUEVA" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "OBRA NUEVA") selected @endif>OBRA NUEVA</option>
                                            <option value="AMPLIACIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "AMPLIACIÓN") selected @endif>AMPLIACIÓN</option>
                                            <option value="ADECUACIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "ADECUACIÓN") selected @endif>ADECUACIÓN</option>
                                            <option value="MODIFICACIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "MODIFICACIÓN") selected @endif>MODIFICACIÓN</option>
                                            <option value="RESTAURACIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "RESTAURACIÓN") selected @endif>RESTAURACIÓN</option>
                                            <option value="REFORZAMIENTO ESTRUCTURAL" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "REFORZAMIENTO ESTRUCTURAL") selected @endif>REFORZAMIENTO ESTRUCTURAL</option>
                                            <option value="DEMOLICIÓN TOTAL" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "DEMOLICIÓN TOTAL") selected @endif>DEMOLICIÓN TOTAL</option>
                                            <option value="DEMOLICIÓN PARCIAL" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "DEMOLICIÓN PARCIAL") selected @endif>DEMOLICIÓN PARCIAL</option>
                                            <option value="RECONSTRUCCIÓN" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "RECONSTRUCCIÓN") selected @endif>RECONSTRUCCIÓN</option>
                                            <option value="CERRAMIENTO" @if($tramite != "INICIAL" and $delinacion->modalidadLicenciaConstruccion == "CERRAMIENTO") selected @endif>CERRAMIENTO</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        1.6 USOS
                                        <select class="form-control" id="usos" name="usos" onchange="cambioUsos(this.value)">
                                            @if($tramite == "INICIAL") <option value="0">Seleccione el uso</option> @endif
                                            <option value="Vivienda" @if($tramite != "INICIAL" and $delinacion->usos == "Vivienda") selected @endif>Vivienda</option>
                                            <option value="Comercio y/o servicios" @if($tramite != "INICIAL" and $delinacion->usos == "Comercio y/o servicios") selected @endif>Comercio y/o servicios</option>
                                            <option value="Institucional/Dotacional" @if($tramite != "INICIAL" and $delinacion->usos == "Institucional/Dotacional") selected @endif>Institucional/Dotacional</option>
                                            <option value="Industrial" @if($tramite != "INICIAL" and $delinacion->usos == "Industrial") selected @endif>Industrial</option>
                                            <option value="Otro" @if($tramite != "INICIAL" and $delinacion->usos == "Otro") selected @endif>Otro</option>
                                        </select>
                                        @if($tramite != "INICIAL" and $delinacion->usos == "Otro")
                                            <span id="otroUso">
                                            <input  class="form-control" type="text" name="cualOtroUso" id="cualOtroUso" value="{{$delinacion->cualOtroUso}}" placeholder="Cual Otro?">
                                        @else
                                            <span style="display: none" id="otroUso">
                                            <input  class="form-control" type="text" name="cualOtroUso" id="cualOtroUso" @if($tramite != "INICIAL" ) value="" @endif
                                            placeholder="Cual Otro?">
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        1.7 ÁREA O UNIDADES CONSTRUIDA(S)
                                        <select class="form-control" id="area" name="area">
                                            <option value="Menor a 2.000 m2" @if($tramite != "INICIAL" and $delinacion->area == "Menor a 2.000 m2") selected @endif>Menor a 2.000 m2</option>
                                            <option value="Igual o mayor a 2.000 m2" @if($tramite != "INICIAL" and $delinacion->area == "Igual o mayor a 2.000 m2") selected @endif>Igual o mayor a 2.000 m2</option>
                                            <option value="Alcanza o supera mediante ampliaciones los 2.000 m2" @if($tramite != "INICIAL" and $delinacion->area == "Alcanza o supera mediante ampliaciones los 2.000 m2") selected @endif>Alcanza o supera mediante ampliaciones los 2.000 m2</option>
                                            <option value="Genera 5 o más unidades de vivienda para transferir a terceros" @if($tramite != "INICIAL" and $delinacion->area == "Genera 5 o más unidades de vivienda para transferir a terceros") selected @endif>Genera 5 o más unidades de vivienda para transferir a terceros</option>
                                        </select>
                                    </td>
                                    <td>
                                        1.8 TIPO DE VIVIENDA
                                        <select class="form-control" id="tipoVivienda" name="tipoVivienda">
                                            <option value="VIP" @if($tramite != "INICIAL" and $delinacion->tipoVivienda == "VIP") selected @endif>VIP</option>
                                            <option value="VIS" @if($tramite != "INICIAL" and $delinacion->tipoVivienda == "VIS") selected @endif>VIS</option>
                                            <option value="No VIS" @if($tramite != "INICIAL" and $delinacion->tipoVivienda == "No VIS") selected @endif>No VIS</option>
                                        </select>
                                    </td>
                                    <td>
                                        1.9 BIEN DE INTERÉS CULTURAL
                                        <select class="form-control" id="interesCultural" name="interesCultural">
                                            <option value="No" @if($tramite != "INICIAL" and $delinacion->interesCultural == "No") selected @endif>No</option>
                                            <option value="Si" @if($tramite != "INICIAL" and $delinacion->interesCultural == "Si") selected @endif>Si</option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">2. INFORMACIÓN SOBRE EL PREDIO</th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        2.1 DIRECCIÓN O NOMENCLATURA
                                        <br>
                                        ACTUAL
                                        <input type="text" class="form-control" @if($tramite != "INICIAL") value="{{ $delinacion->dirActual }}" @endif required name="dirActual" id="dirActual">
                                        <br>
                                        ANTERIOR(ES)
                                        <input type="text" class="form-control" @if($tramite != "INICIAL") value="{{ $delinacion->dirAnterior }}" @endif required name="dirAnterior" id="dirAnterior">
                                    </td>
                                    <td style="vertical-align: middle">
                                        2.2 No. MATRÍCULA INMOBILIARIA
                                        <input type="number" class="form-control" @if($tramite != "INICIAL") value="{{ $delinacion->matricula }}" @endif required name="matricula" id="matricula">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">
                                        2.3 No. IDENTIFICACIÓN CATASTRAL
                                        <input type="number" class="form-control" @if($tramite != "INICIAL") value="{{ $delinacion->idCatastral }}" @endif required name="idCatastral" id="idCatastral">
                                    </td>
                                    <td>
                                        2.4 CLASIFICACIÓN DEL SUELO
                                        <select class="form-control" id="clasificacionSuelo" name="clasificacionSuelo" required>
                                            <option value="URBANO" @if($tramite != "INICIAL" and $delinacion->clasificacionSuelo == "URBANO") selected @endif>URBANO</option>
                                            <option value="RURAL" @if($tramite != "INICIAL" and $delinacion->clasificacionSuelo == "RURAL") selected @endif>RURAL</option>
                                            <option value="DE EXPANSIÓN" @if($tramite != "INICIAL" and $delinacion->clasificacionSuelo == "DE EXPANSIÓN") selected @endif>DE EXPANSIÓN</option>
                                        </select>
                                    </td>
                                    <td>
                                        2.5 PLANIMETRÍA DEL LOTE
                                        <select class="form-control" id="planimetria" name="planimetria" required onchange="cambioPlanimetria(this.value)">
                                            <option value="Plano del Loteo" @if($tramite != "INICIAL" and $delinacion->planimetria == "Plano del Loteo") selected @endif>Plano del Loteo</option>
                                            <option value="Plano Topográfico" @if($tramite != "INICIAL" and $delinacion->planimetria == "Plano Topográfico") selected @endif>Plano Topográfico</option>
                                            <option value="Otro" @if($tramite != "INICIAL" and $delinacion->planimetria == "Otro") selected @endif>Otro</option>
                                        </select>
                                        @if($tramite != "INICIAL" and $delinacion->planimetria == "Otro")
                                            <span id="otraPlanimetria">
                                            <input  class="form-control" type="text" name="cualotraPlanimetria" value="{{$delinacion->cualotraPlanimetria}}" id="cualotraPlanimetria" placeholder="Cual Otro?">
                                        </span>
                                        @else
                                            <span style="display: none" id="otraPlanimetria">
                                            <input  class="form-control" type="text" name="cualotraPlanimetria" id="cualotraPlanimetria" @if($tramite != "INICIAL" ) value="" @endif
                                            placeholder="Cual Otro?">
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><td colspan="3">2.6 INFORMACIÓN GENERAL</td></tr>
                                <tr>
                                    <td colspan="2">BARRIO O URBANIZACIÓN
                                        <input type="text" class="form-control" @if($tramite != "INICIAL") value="{{ $delinacion->barrio }}" @endif required name="barrio" id="barrio">
                                    </td>
                                    <td>VEREDA<input type="text" class="form-control" name="vereda" id="vereda" @if($tramite != "INICIAL") value="{{ $delinacion->vereda }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>COMUNA<input type="text" class="form-control" name="comuna" id="comuna" @if($tramite != "INICIAL") value="{{ $delinacion->comuna }}" @endif></td>
                                    <td>SECTOR<input type="text" class="form-control" name="sector" id="sector" @if($tramite != "INICIAL") value="{{ $delinacion->sector }}" @endif></td>
                                    <td>ESTRATO<input type="number" class="form-control" required name="estrato" id="estrato" @if($tramite != "INICIAL") value="{{ $delinacion->estrato }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>CORREGIMIENTO<input type="text" class="form-control" name="corregimiento" id="corregimiento" @if($tramite != "INICIAL") value="{{ $delinacion->corregimiento }}" @endif></td>
                                    <td>MANZANA No.<input type="number" class="form-control" name="manzana" id="manzana" @if($tramite != "INICIAL") value="{{ $delinacion->manzana }}" @endif></td>
                                    <td>LOTE No.<input type="number" class="form-control" name="lote" id="lote" @if($tramite != "INICIAL") value="{{ $delinacion->lote }}" @endif></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center" id="infoVecinos">
                                <thead>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">3. INFORMACIÓN DE VECINOS COLINDANTES</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($tramite != "INICIAL")
                                    @foreach($vecinos as $vecino)
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: middle">DIRECCIÓN DEL PREDIO: {{$vecino->dirPredVecino}}</td>
                                            <td style="vertical-align: middle">DIRECCIÓN DE CORRESPONDENCIA: {{$vecino->dirCorrespVecino}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <thead>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">4. LINDEROS, DIMENSIONES Y ÁREAS</th>
                                </tr>
                                <tr>
                                    <td>LINDEROS</td>
                                    <td>LONGITUD (Metros lineales)</td>
                                    <td>COLINDA CON</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>NORTE</td>
                                    <td><input type="number" class="form-control" name="longNorte1" @if($tramite != "INICIAL") value="{{ $delinacion->longNorte1 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindNorte1" @if($tramite != "INICIAL") value="{{ $delinacion->colindNorte1 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosNorte2" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte2 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longNorte2" @if($tramite != "INICIAL") value="{{ $delinacion->longNorte2 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindNorte2" @if($tramite != "INICIAL") value="{{ $delinacion->colindNorte2 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosNorte3" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte3 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longNorte3" @if($tramite != "INICIAL") value="{{ $delinacion->longNorte3 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindNorte3" @if($tramite != "INICIAL") value="{{ $delinacion->colindNorte3 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosNorte4" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte4 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longNorte4" @if($tramite != "INICIAL") value="{{ $delinacion->longNorte4 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindNorte4" @if($tramite != "INICIAL") value="{{ $delinacion->colindNorte4 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>SUR</td>
                                    <td><input type="number" class="form-control" name="longSur1" @if($tramite != "INICIAL") value="{{ $delinacion->longSur1 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindSur1" @if($tramite != "INICIAL") value="{{ $delinacion->colindSur1 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosSur2" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte2 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longSur2" @if($tramite != "INICIAL") value="{{ $delinacion->longSur2 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindSur2" @if($tramite != "INICIAL") value="{{ $delinacion->colindSur2 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosSur3" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte3 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longSur3" @if($tramite != "INICIAL") value="{{ $delinacion->longSur3 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindSur3" @if($tramite != "INICIAL") value="{{ $delinacion->colindSur3 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosSur4" @if($tramite != "INICIAL") value="{{ $delinacion->linderosNorte4 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longSur4" @if($tramite != "INICIAL") value="{{ $delinacion->longSur4 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindSur4" @if($tramite != "INICIAL") value="{{ $delinacion->colindSur4 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>ORIENTE</td>
                                    <td><input type="number" class="form-control" name="longOriente1" @if($tramite != "INICIAL") value="{{ $delinacion->longOriente1 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOriente1" @if($tramite != "INICIAL") value="{{ $delinacion->colindOriente1 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOriente2" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOriente2 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOriente2" @if($tramite != "INICIAL") value="{{ $delinacion->longOriente2 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOriente2" @if($tramite != "INICIAL") value="{{ $delinacion->colindOriente2 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOriente3" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOriente3 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOriente3" @if($tramite != "INICIAL") value="{{ $delinacion->longOriente3 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOriente3" @if($tramite != "INICIAL") value="{{ $delinacion->colindOriente3 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOriente4" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOriente4 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOriente4" @if($tramite != "INICIAL") value="{{ $delinacion->longOriente4 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOriente4" @if($tramite != "INICIAL") value="{{ $delinacion->colindOriente4 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>OCCIDENTE</td>
                                    <td><input type="number" class="form-control" name="longOccidente1" @if($tramite != "INICIAL") value="{{ $delinacion->longOccidente1 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOccidente1" @if($tramite != "INICIAL") value="{{ $delinacion->colindOccidente1 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOccidente2" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOccidente2 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOccidente2" @if($tramite != "INICIAL") value="{{ $delinacion->longOccidente2 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOccidente2" @if($tramite != "INICIAL") value="{{ $delinacion->colindOccidente2 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOccidente3" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOccidente3 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOccidente3" @if($tramite != "INICIAL") value="{{ $delinacion->longOccidente3 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOccidente3" @if($tramite != "INICIAL") value="{{ $delinacion->colindOccidente3 }}" @endif></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="linderosOccidente4" @if($tramite != "INICIAL") value="{{ $delinacion->linderosOccidente4 }}" @endif></td>
                                    <td><input type="number" class="form-control" name="longOccidente4" @if($tramite != "INICIAL") value="{{ $delinacion->longOccidente4 }}" @else value="0" @endif></td>
                                    <td><input type="text" class="form-control" name="colindOccidente4" @if($tramite != "INICIAL") value="{{ $delinacion->colindOccidente4 }}" @endif></td>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td colspan="3">ÁREA TOTAL DEL PREDIO(S) <input type="number" class="form-control" name="areaTotalPredios" @if($tramite != "INICIAL") value="{{ $delinacion->areaTotalPredios }}" @endif required></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center" id="profesionales">
                                <thead>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="5">5. TITULARES Y PROFESIONALES RESPONSABLES</th>
                                </tr>
                                <tr>
                                    <td colspan="5">Los firmantes titulares y profesionales responsables declaramos bajo la gravedad del juramento que nos responsabilizamos totalmente por los estudios y
                                        documentos presentados con este formulario y por la veracidad de los datos aquí consignados. Así mismo, declaramos que conocemos las disposiciones
                                        vigentes que rigen la materia y las sanciones establecidas.</td>
                                </tr>
                                <tr>
                                    <td colspan="5">Acepta(n) ser notificado(s) de las actuaciones relacionadas con el trámite de licenciamiento a través del correo
                                        electrónico diligenciado y/o de los medios electrónicos fijados por la autoridad que adelanta el trámite:
                                        <select class="form-control" id="notificar" name="notificar" required>
                                            <option value="SI" @if($tramite != "INICIAL" and $delinacion->notificar == "SI") selected @endif>SI</option>
                                            <option value="NO" @if($tramite != "INICIAL" and $delinacion->notificar == "NO") selected @endif>NO</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><td colspan="5">5.1 TITULAR (ES) DE LA LICENCIA</td></tr>
                                </thead>
                                <tbody>
                                @if($tramite != "INICIAL")
                                    @foreach($titulares as $titular)
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: middle">NOMBRE: {{$titular->nameTit}}</td>
                                            <td style="vertical-align: middle">C.C. O NIT: {{$titular->ccTit}}</td>
                                            <td style="vertical-align: middle">TELÉFONO /CELULAR: {{$titular->telTit}}</td>
                                            <td style="vertical-align: middle">CORREO ELECTRÓNICO: {{$titular->emailTit}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <table class="table text-center" id="titulares">
                                <thead>
                                <tr><td colspan="4">5.2 PROFESIONALES RESPONSABLES</td></tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">URBANIZADOR/PARCELADOR (Sin requisitos de experiencia mínima)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameUrbanizador" id="nameUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->nameUrbanizador }}" @endif></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccUrbanizador" id="ccUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->ccUrbanizador }}" @endif></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatUrbanizador" id="numMatUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->numMatUrbanizador }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatUrbanizador" id="fechaExpMatUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->fechaExpMatUrbanizador }}" @endif></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailUrbanizador" id="emailUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->emailUrbanizador }}" @endif></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telUrbanizador" id="telUrbanizador" @if($tramite != "INICIAL") value="{{ $delinacion->telUrbanizador }}" @endif></td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">DIRECTOR DE LA CONSTRUCCIÓN (Experiencia mínima 3 años o posgrado)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameDir" id="nameDir" @if($tramite != "INICIAL") value="{{ $delinacion->nameDir }}" @endif></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccDir" id="ccDir" @if($tramite != "INICIAL") value="{{ $delinacion->ccDir }}" @endif></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatDir" id="numMatDir" @if($tramite != "INICIAL") value="{{ $delinacion->numMatDir }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatDir" id="fechaExpMatDir" @if($tramite != "INICIAL") value="{{ $delinacion->fechaExpMatDir }}" @endif></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailDir" id="emailDir" @if($tramite != "INICIAL") value="{{ $delinacion->emailDir }}" @endif></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telDir" id="telDir" @if($tramite != "INICIAL") value="{{ $delinacion->telDir }}" @endif></td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">ARQUITECTO PROYECTISTA (Sin requisitos de experiencia mínima)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameArq" id="nameArq" @if($tramite != "INICIAL") value="{{ $delinacion->nameArq }}" @endif></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccArq" id="ccArq" @if($tramite != "INICIAL") value="{{ $delinacion->ccArq }}" @endif></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatArq" id="numMatArq" @if($tramite != "INICIAL") value="{{ $delinacion->numMatArq }}" @endif></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatArq" id="fechaExpMatArq" @if($tramite != "INICIAL") value="{{ $delinacion->fechaExpMatArq }}" @endif></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailArq" id="emailArq" @if($tramite != "INICIAL") value="{{ $delinacion->emailArq }}" @endif></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telArq" id="telArq" @if($tramite != "INICIAL") value="{{ $delinacion->telArq }}" @endif></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" width="100px" style="vertical-align: middle">INGENIERO CIVIL DISEÑADOR ESTRUCTURAL (Experiencia mínima 5 años o posgrado)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameIngCivilDis" id="nameIngCivilDis"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccIngCivilDis" id="ccIngCivilDis"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatIngCivilDis" id="numMatIngCivilDis"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatIngCivilDis" id="fechaExpMatIngCivilDis"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailIngCivilDis" id="emailIngCivilDis"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telIngCivilDis" id="telIngCivilDis"></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Exige Supervisión Técnica
                                        <select class="form-control" id="supervTecnicaIngCivilDis" name="supervTecnicaIngCivilDis">
                                            <option value="No" @if($tramite != "INICIAL") selected @endif>No</option>
                                            <option value="Si" @if($tramite != "INICIAL") selected @endif>Si</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">DISEÑADOR DE ELEMENTOS NO ESTRUCTURALES (Experiencia mínima 3 años o posgrado)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameDiseñadorElem" id="nameDiseñadorElem"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccDiseñadorElem" id="ccDiseñadorElem"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatDiseñadorElem" id="numMatDiseñadorElem"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatDiseñadorElem" id="fechaExpMatDiseñadorElem"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailDiseñadorElem" id="emailDiseñadorElem"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telDiseñadorElem" id="telDiseñadorElem"></td>
                                </tr>
                                <tr>
                                    <td rowspan="3" width="100px" style="vertical-align: middle">INGENIERO CIVIL GEOTECNISTA (Experiencia mínima 5 años o posgrado)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameIngCivilGeo" id="nameIngCivilGeo"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccIngCivilGeo" id="ccIngCivilGeo"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatIngCivilGeo" id="numMatIngCivilGeo"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatIngCivilGeo" id="fechaExpMatIngCivilGeo"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailIngCivilGeo" id="emailIngCivilGeo"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telIngCivilGeo" id="telIngCivilGeo"></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Exige Supervisión Técnica
                                        <select class="form-control" id="supervTecnicaIngCivilGeo" name="supervTecnicaIngCivilGeo">
                                            <option value="No" @if($tramite != "INICIAL") selected @endif>No</option>
                                            <option value="Si" @if($tramite != "INICIAL") selected @endif>Si</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">INGENIERO TOPÓGRAFO Y/O TOPÓGRAFO</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameTopografo" id="nameTopografo"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccTopografo" id="ccTopografo"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatTopografo" id="numMatTopografo"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatTopografo" id="fechaExpMatTopografo"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailTopografo" id="emailTopografo"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telTopografo" id="telTopografo"></td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">REVISOR INDEPENDIENTE DE LOS DISEÑOS ESTRUCTURALES (Experiencia mínima 5 años o posgrado)</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameRevisor" id="nameRevisor"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccRevisor" id="ccRevisor"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatRevisor" id="numMatRevisor"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatRevisor" id="fechaExpMatRevisor"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailRevisor" id="emailRevisor"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telRevisor" id="telRevisor"></td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">OTROS PROFESIONALES ESPECIALISTAS</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameOtroProf1" id="nameOtroProf1"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccOtroProf1" id="ccOtroProf1"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatOtroProf1" id="numMatOtroProf1"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatOtroProf1" id="fechaExpMatOtroProf1"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailOtroProf1" id="emailOtroProf1"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telOtroProf1" id="telOtroProf1"></td>
                                </tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">OTROS PROFESIONALES ESPECIALISTAS</td>
                                    <td>NOMBRE<input type="text" class="form-control" name="nameOtroProf2" id="nameOtroProf2"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccOtroProf2" id="ccOtroProf2"></td>
                                    <td>N° MATRICULA PROFESIONAL<input type="number" class="form-control" name="numMatOtroProf2" id="numMatOtroProf2"></td>
                                </tr>
                                <tr>
                                    <td>FECHA EXP. MATRICULA<input type="date" class="form-control" name="fechaExpMatOtroProf2" id="fechaExpMatOtroProf2"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailOtroProf2" id="emailOtroProf2"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telOtroProf2" id="telOtroProf2"></td>
                                </tr>
                                <tr><td colspan="4">5.3 RESPONSABLE DE LA SOLICITUD</td></tr>
                                <tr>
                                    <td rowspan="2" width="100px" style="vertical-align: middle">RESPONSABLE DE LA SOLICITUD, APODERADO O MANDATARIO</td>
                                    <td colspan="2">NOMBRE<input type="text" class="form-control" name="nameResponsable" id="nameResponsable"></td>
                                    <td>CÉDULA<input type="number" class="form-control" name="ccResponsable" id="ccResponsable"></td>
                                </tr>
                                <tr>
                                    <td>DIRECCIÓN PARA CORRESPONDENCIA<input type="text" class="form-control" name="dirResponsable" id="dirResponsable"></td>
                                    <td>CORREO ELECTRÓNICO<input type="email" class="form-control" name="emailResponsable" id="emailResponsable"></td>
                                    <td>TELÉFONO /CELULAR<input type="number" class="form-control" name="telResponsable" id="telResponsable"></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="4">Acepta(n) ser notificado(s) de las actuaciones relacionadas con el trámite de licenciamiento a través del correo
                                        electrónico diligenciado y/o de los medios electrónicos fijados por la autoridad que adelanta el trámite:
                                        <select class="form-control" id="notificarResponsable" name="notificarResponsable">
                                            <option value="SI" @if($tramite != "INICIAL") selected @endif>SI</option>
                                            <option value="NO" @if($tramite != "INICIAL") selected @endif>NO</option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table style="display: none" class="table text-center" id="anexo">
                                <thead>
                                <tr style="background-color: #0e7224; color: white"><td colspan="4">ANEXO DE CONTRUCCIÓN SOSTENIBLE</td></tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="4">1. TIPO DE USO
                                        <select class="form-control" id="tipoUsos" name="tipoUsos" onchange="cambioTipoUsos(this.value)">
                                            @if($tramite == "INICIAL") <option value="0">Seleccione el tipo de tramite</option> @endif
                                            <option value="Vivienda" @if($tramite != "INICIAL") selected @endif>Vivienda</option>
                                            <option value="Comercio/Servicios" @if($tramite != "INICIAL") selected @endif>Comercio/Servicios</option>
                                            <option value="Institucional/Dotacional" @if($tramite != "INICIAL") selected @endif>Institucional/Dotacional</option>
                                            <option value="Industrial" @if($tramite != "INICIAL") selected @endif>Industrial</option>
                                            <option value="Educativo" @if($tramite != "INICIAL") selected @endif>Educativo</option>
                                            <option value="Salud" @if($tramite != "INICIAL") selected @endif>Salud</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroTipoUso">
                                        <input  class="form-control" type="text" name="cualotroTipoUso" id="cualotroTipoUso" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual Otro?"></span>
                                    </td>
                                </tr>
                                <tr style="background-color: #0e7224; color: white"><td colspan="4">2. REGLAMENTACIÓN DE CONSTRUCCIÓN SOSTENIBLE</td></tr>
                                <tr style="background-color: #bfc3bf; color: black"><td colspan="4">2.1 DECLARACIÓN SOBRE MEDIDAS DE AHORRO EN ENERGÍA</td></tr>
                                <tr>
                                    <td>
                                        2.1.1 MEDIDAS PASIVAS
                                        <select class="form-control" id="medidasPasivas" name="medidasPasivas" onchange="cambioMedidasPasivas(this.value)">
                                            <option value="Cubierta verde" @if($tramite != "INICIAL") selected @endif>Cubierta verde</option>
                                            <option value="Elementos de protección solar" @if($tramite != "INICIAL") selected @endif>Elementos de protección solar</option>
                                            <option value="Vidrios de protección solar" @if($tramite != "INICIAL") selected @endif>Vidrios de protección solar</option>
                                            <option value="Cubierta de protección solar" @if($tramite != "INICIAL") selected @endif>Cubierta de protección solar</option>
                                            <option value="Pared de protección solar" @if($tramite != "INICIAL") selected @endif>Pared de protección solar</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otraMedidaPasiva">
                                        <input  class="form-control" type="text" name="cualotraMedidaPasiva" id="cualotraMedidaPasiva" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                    <td>
                                        2.1.2 MEDIDAS ACTIVAS
                                        <select class="form-control" id="medidasActivas" name="medidasActivas" onchange="cambioMedidasActivas(this.value)">
                                            <option value="Iluminación eficiente" @if($tramite != "INICIAL") selected @endif>Iluminación eficiente</option>
                                            <option value="Equipos de aire acondicionado eficientes" @if($tramite != "INICIAL") selected @endif>Equipos de aire acondicionado eficientes</option>
                                            <option value="Agua caliente solar" @if($tramite != "INICIAL") selected @endif>Agua caliente solar</option>
                                            <option value="Controles de iluminación" @if($tramite != "INICIAL") selected @endif>Controles de iluminación</option>
                                            <option value="Variadores de velocidad para bombas" @if($tramite != "INICIAL") selected @endif>Variadores de velocidad para bombas</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otraMedidaActiva">
                                        <input  class="form-control" type="text" name="cualotraMedidaActiva" id="cualotraMedidaActiva" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                    <td>
                                        2.2 MATERIALIDAD MURO EXTERNO
                                        <select class="form-control" id="materialidadMuroExt" name="materialidadMuroExt" onchange="cambioMatExt(this.value)">
                                            <option value="Ladrillo portante" @if($tramite != "INICIAL") selected @endif>Ladrillo portante</option>
                                            <option value="Ladrillo común" @if($tramite != "INICIAL") selected @endif>Ladrillo común </option>
                                            <option value="Muro de concreto vaciado en obra" @if($tramite != "INICIAL") selected @endif>Muro de concreto vaciado en obra</option>
                                            <option value="Muro en superboard" @if($tramite != "INICIAL") selected @endif>Muro en superboard</option>
                                            <option value="Muro cortina en aluminio" @if($tramite != "INICIAL") selected @endif>Muro cortina en aluminio</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroMuroExt">
                                        <input  class="form-control" type="text" name="cualotroMuroExt" id="cualotroMuroExt" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                    <td>
                                        2.3 MATERIALIDAD MURO INTERNO
                                        <select class="form-control" id="materialidadMuroInt" name="materialidadMuroInt" onchange="cambioMatInt(this.value)">
                                            <option value="Ladrillo número 4 o similar" @if($tramite != "INICIAL") selected @endif>Ladrillo número 4 o similar</option>
                                            <option value="Drywall" @if($tramite != "INICIAL") selected @endif>Drywall</option>
                                            <option value="Ladrillo común" @if($tramite != "INICIAL") selected @endif>Ladrillo común</option>
                                            <option value="Muro de concreto vaciado en obra" @if($tramite != "INICIAL") selected @endif>Muro de concreto vaciado en obra</option>
                                            <option value="Mamposteria de bloque de concreto" @if($tramite != "INICIAL") selected @endif>Mamposteria de bloque de concreto</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroMuroInt">
                                        <input  class="form-control" type="text" name="cualotroMuroInt" id="cualotroMuroInt" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">
                                        2.4 MATERIALIDAD CUBIERTA
                                        <select class="form-control" id="materialidadCubierta" name="materialidadCubierta" onchange="cambioMatCub(this.value)">
                                            <option value="Cubierta de concreto vaciado en obra" @if($tramite != "INICIAL") selected @endif>Cubierta de concreto vaciado en obra</option>
                                            <option value="Panel tipo sándwich de aluminio" @if($tramite != "INICIAL") selected @endif>Panel tipo sándwich de aluminio</option>
                                            <option value="Tejas de arcilla" @if($tramite != "INICIAL") selected @endif>Tejas de arcilla</option>
                                            <option value="Metálica" @if($tramite != "INICIAL") selected @endif>Metálica</option>
                                            <option value="Fibrocemento" @if($tramite != "INICIAL") selected @endif>Fibrocemento</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroCub">
                                        <input class="form-control" type="text" name="cualotroCub" id="cualotroCub" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                    <td>
                                        2.5 RELACIÓN MURO VENTANA Y ALTURA PISO A TECHO
                                        <br>Rango (0% - 100%)
                                        <br>Norte <input class="form-control" name="relacionNorte" type="text">
                                        <br>Sur <input class="form-control" name="relacionSur" type="text">
                                        <br>Oriente <input class="form-control" name="relacionOriente" type="text">
                                        <br>Occidente <input class="form-control" name="relacionOccidente" type="text">
                                        <br>Altura piso a techo (m) <input class="form-control" name="relacionAltura" type="number">
                                    </td>
                                    <td style="vertical-align: middle">
                                        2.6 DECLARACIÓN SOBRE MEDIDAS DE AHORRO EN AGUA
                                        <select class="form-control" id="declaracionMedidasAhorroAgua" name="declaracionMedidasAhorroAgua" onchange="cambioAhorroAgua(this.value)">
                                            <option value="Sanitarios de bajo consumo" @if($tramite != "INICIAL") selected @endif>Sanitarios de bajo consumo</option>
                                            <option value="Lavamanos de bajo consumo" @if($tramite != "INICIAL") selected @endif>Lavamanos de bajo consumo</option>
                                            <option value="Duchas de bajo consumo" @if($tramite != "INICIAL") selected @endif>Duchas de bajo consumo</option>
                                            <option value="Orinales de bajo consumo" @if($tramite != "INICIAL") selected @endif>Orinales de bajo consumo</option>
                                            <option value="Recolección de agua lluvia" @if($tramite != "INICIAL") selected @endif>Recolección de agua lluvia</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroAhorroAgua">
                                        <input class="form-control" type="text" name="cualotroAhorroAgua" id="cualotroAhorroAgua" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                    <td style="vertical-align: middle">
                                        2.7 ZONIFICACIÓN CLIMÁTICA <br>
                                        Señale la zona Climática asignada de acuerdo al Anexo 2 de la Res. 549 de 2015
                                        <select class="form-control" id="zonificacionClimatica" name="zonificacionClimatica">
                                            <option value="Frío" @if($tramite != "INICIAL") selected @endif>Frío</option>
                                            <option value="Templado" @if($tramite != "INICIAL") selected @endif>Templado</option>
                                            <option value="Cálido seco" @if($tramite != "INICIAL") selected @endif>Cálido seco</option>
                                            <option value="Cálido húmedo" @if($tramite != "INICIAL") selected @endif>Cálido húmedo</option>
                                        </select>
                                        <br>
                                        ¿Su predio se encuentra en una zona climática distinta a la que le fue asignada?
                                        <select class="form-control" id="zonaClimatica" name="zonaClimatica" onchange="cambioZonaClimatica(this.value)">
                                            <option value="Sí" @if($tramite != "INICIAL") selected @endif>Sí</option>
                                            <option value="No" @if($tramite != "INICIAL") selected @endif>No</option>
                                            <option value="Otro" @if($tramite != "INICIAL") selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroZonificacionClimatica">
                                        <input class="form-control" type="text" name="cualotroZonificacionClimatica" id="cualotroZonificacionClimatica" @if($tramite != "INICIAL" ) value="" @endif
                                        placeholder="Cual?"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        2.8 AHORRO ESPERADO EN AGUA<br>
                                        Indique el ahorro que actualmente busca el proyecto en materia de agua
                                        <input type="text" class="form-control" name="ahorroEsperadoAgua">
                                    </td>
                                    <td colspan="2">
                                        2.9 AHORRO ESPERADO EN ENERGÍA<br>
                                        Indique el ahorro que actualmente busca el proyecto en materia de energía
                                        <input type="text" class="form-control" name="ahorroEsperadoEnergia">
                                    </td>
                                </tr>
                                <tr style="background-color: #0e7224; color: white"><td colspan="4">3. ÁREA DEL PROYECTO</td></tr>
                                <tr>
                                    <td colspan="2">ÁREA NETA DE URBANISMO Y PAISAJISMO (SI APLICA)</td>
                                    <td colspan="2"><input type="number" class="form-control" placeholder="m2" name="urbanismoPaisajismo"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">ÁREA NETA DE ZONAS COMUNES (SI APLICA)</td>
                                    <td colspan="2"><input type="number" class="form-control" placeholder="m2" name="zonasComunes"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">ÁREA NETA DE PARQUEADEROS (SI APLICA)</td>
                                    <td colspan="2"><input type="number" class="form-control" placeholder="m2" name="parqueaderos"></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr>
                                    <td colspan="4">Valor a pagar:
                                        <input type="number" class="form-control" name="valorPago" id="valorPago" min="0" value="{{ $delinacion->valorPago }}" required>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@stop
