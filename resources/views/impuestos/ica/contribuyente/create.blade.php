@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
        <div class="col-md-12 align-self-center">
            <div class="breadcrumb text-center">
                <strong>
                    <h4><b>FORMULARIO DECLARACION Y PAGO ICA</b></h4>
                    <h4><b>Municipio de Providencia y Santa Catalina</b></h4>
                    <h4><b>Secretaria de Hacienda Municipal</b></h4>
                    FORMATO SHI-WEB03-2022
                </strong>
            </div>
            <div class="col-lg-12">
                <div class="form-validation">
                    <form class="form-valide" action="{{url('/impuestos/ICA/contri')}}" method="POST" enctype="multipart/form-data" id="formulario">
                        {{ csrf_field() }}
                        {{-- ENCABEZADO--}}
                        <table id="TABLA1" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="3">FORMULARIO UNICO NACIONAL DE DECLARACION Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</th>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <th scope="row" >MUNICIPIO O DISTRITO </th>
                                <th scope="row" colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td>DEPARTAMENTO</td>
                                <td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td>AÑO GRAVABLE</td>
                                <td><select class="form-control" id="añoGravable" name="añoGravable" onchange="operation()">
                                        <option value="2016" @if($action == "Corrección" and $ica->añoGravable == "2016" ) selected @endif>2016</option>
                                        <option value="2017" @if($action == "Corrección" and $ica->añoGravable == "2017" ) selected @endif>2017</option>
                                        <option value="2018" @if($action == "Corrección" and $ica->añoGravable == "2018" ) selected @endif>2018</option>
                                        <option value="2019" @if($action == "Corrección" and $ica->añoGravable == "2019" ) selected @endif>2019</option>
                                        <option value="2020" @if($action == "Corrección" and $ica->añoGravable == "2020" ) selected @endif>2020</option>
                                        <option value="2021" @if($action == "Corrección" and $ica->añoGravable == "2021" ) selected @endif>2021</option>
                                        <option value="2022" @if($action == "Corrección" and $ica->añoGravable == "2022" ) selected @endif>2022</option>
                                        <option value="2023" @if($action == "Corrección" and $ica->añoGravable == "2023" ) selected @endif>2023</option>
                                    </select></td>
                                <td>
                                    @if($action == "Declaración")
                                        DECLARACIÓN INICIAL
                                        <input type="hidden" name="opciondeUso" value="Declaración">
                                    @else
                                        <b>CORRECCIÓN - NO. FORMULARIO <br>{{ $ica->numReferencia }}</b>
                                        <input type="hidden" name="opciondeUso" value="Corrección">
                                        <input type="hidden" name="ica_id" value="{{ $ica->id }}">
                                    @endif

                                </td>

                            </tr>
                            </tbody>
                        </table>

                        {{-- TABLA A. INFORMACIÓN DEL CONTRIBUYENTE --}}
                        <table id="TABLA2" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="4">A. INFORMACIÓN DEL CONTRIBUYENTE</th>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <table class="table text-center table-bordered">
                                        <tr>
                                            <td colspan="3">
                                                Naturaleza Juridica: <b>{{$rit->natJuridiContri}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> 1.Nombre y apellidos o razón Social: {{ $rit->apeynomContri }}</td>
                                            <td>2. {{ $rit->tipoDocContri }} No. {{ $rit->numDocContri }}</td>
                                            <td>3. Dirección de Notificación: {{ $rit->dirNotifContri }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">3. Municipio o Distrito de la Dirección de Notificación: PROVIDENCIA Y SANTA CATALINA ISLAS </td>
                                            <td>3. Departamento: ARCHIPIELAGO DE SAN ANDRES </td>
                                        </tr>
                                        <tr>
                                            <td>4. Teléfono Móvil: {{ $rit->movilContri }}</td>
                                            <td colspan="2">5. Correo electrónico: {{ $rit->emailContri }}</td>
                                        </tr>
                                        <tr>
                                            <td>6. Número de establecimientos Locales <input class="form-control" type="number" name="numEstableLoc" min="0" @if($action == "Corrección" ) value="{{ $ica->numEstableLoc }}" @else value="0" @endif required></td>
                                            <td>Nal <input class="form-control" type="number" name="numEstableNal" min="0" @if($action == "Corrección" ) value="{{ $ica->numEstableNal }}" @else value="0" @endif required></td>
                                            <td>7. Clasificación Contribuyente: <br> {{ $rit->claseContribuyente }} </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {{-- TABLA B. BASE GRAVABLE --}}
                        <table class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="3">B. BASE GRAVABLE</th>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DEL PERIODO EN TODO EL PAIS</td>
                                <td><input type="number" class="form-control" min="0" name="totIngreOrd" @if($action == "Corrección" ) value="{{ $ica->totIngreOrd }}" @else value="0" @endif id="totIngreOrd"
                                    onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>MENOS INGRESOS FUERA DE ESTE MUNICIPIO O DISTRITO</td>
                                <td><input type="number" class="form-control" min="0" name="menosIngreFuera" @if($action == "Corrección" ) value="{{ $ica->menosIngreFuera }}" @else value="0" @endif id="menosIngreFuera"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DE ESTE MUNICIPIO (REGLÓN 8 MENOS 9)</td>
                                <td><span id="totIngreOrdinSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totIngreOrdin,0) ?> @else $0 @endif</span>
                                    <input type="hidden" name="totIngreOrdin" id="totIngreOrdin">
                                </td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>MENOS INGRESOS POR DEVOLUCIONES, REBAJAS, DESCUENTOS</td>
                                <td><input type="number" class="form-control" min="0" name="menosIngreDevol" id="menosIngreDevol" @if($action == "Corrección" ) value="{{ $ica->menosIngreDevol }}" @else value="0" @endif
                                    onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>12</td>
                                <td>MENOS INGRESOS POR EXPORTACIONES Y VENTAS DE ACTIVOS FIJOS</td>
                                <td><input type="number" class="form-control" min="0" name="menosIngreExport" id="menosIngreExport" @if($action == "Corrección" ) value="{{ $ica->menosIngreExport }}" @else value="0" @endif
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>MENOS INGRESOS POR OTRAS ACTIVIDADES EXCLUIDAS O NO SUJETAS Y OTROS INGRESOS NO GRAVADOS</td>
                                <td><input type="number" class="form-control" min="0" name="menosIngreOtrasActiv" id="menosIngreOtrasActiv" @if($action == "Corrección" ) value="{{ $ica->menosIngreOtrasActiv }}" @else value="0" @endif
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td>MENOS INGRESOS POR ACTIVIDADES EXCENTES EN ESTE MUNICIPIO O DISTRITO (POR ACUERDO)</td>
                                <td><input type="number" class="form-control" min="0" name="menosIngreActivExcentes" id="menosIngreActivExcentes" @if($action == "Corrección" ) value="{{ $ica->menosIngreActivExcentes }}" @else value="0" @endif
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>15</td>
                                <td>TOTAL INGRESOS GRAVABLES (RENGLON 10 MENOS 11,12,13 Y 14)</td>
                                <td><span id="totIngreGravablesSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totIngreGravables,0) ?> @else $0 @endif</span>
                                    <input type="hidden" name="totIngreGravables" id="totIngreGravables"></td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- TABLA C. DISCRIMINACIÓN DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO --}}
                        <table id="TABLA3" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="5">C. DISCRIMINACIÓN DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO</th>
                            </tr>
                            <tr>
                                <th>ACTIVIDADES GRAVADAS</th>
                                <th>CODIGO CLASIFICACION MUNICIPAL</th>
                                <th>INGRESOS GRAVADOS</th>
                                <th>TARIFA</th>
                                <th>IMPUESTO INDUSTRIA Y COMERIO</th>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 1 (PRINCIPAL)</td>
                                <td><input class="form-control" type="text" name="codClasiMuni" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->codClasiMuni }}" @endif></td>
                                <td><span id="totIngreGravablesSpan2">@if($action == "Corrección" ) $<?php echo number_format($ica->totIngreGravables,0) ?> @else $0 @endif</span></td>
                                <td><input class="form-control" type="number" name="tarifa" id="tarifa" min="1" @if($action == "Corrección" ) value="{{ $ica->tarifa }}" @else value="1" @endif onchange="operation()"></td>
                                <td>
                                    <span id="impIndyComSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->impIndyCom,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->impIndyCom }}" @else value="0" @endif name="impIndyCom" id="impIndyCom"></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 2</td>
                                <td><input class="form-control" type="text" name="codClasiMuni2" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->codClasiMuni2 }}" @endif></td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->ingreGravados2 }}" @else value="0" @endif id="ingreGravados2" name="ingreGravados2" onchange="operation()"></td>
                                <td><input class="form-control" type="number" name="tarifa2" id="tarifa2" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->tarifa2 }}" @endif></td>
                                <td>
                                    <span id="impIndyCom2Span">@if($action == "Corrección" ) $<?php echo number_format($ica->impIndyCom2,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" id="impIndyCom2" @if($action == "Corrección" ) value="{{ $ica->impIndyCom2 }}" @else value="0" @endif name="impIndyCom2"></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 3</td>
                                <td><input class="form-control" type="text" name="codClasiMuni3" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->codClasiMuni3 }}" @endif></td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->ingreGravados3 }}" @else value="0" @endif id="ingreGravados3" name="ingreGravados3" onchange="operation()"></td>
                                <td><input class="form-control" type="number" name="tarifa3" id="tarifa3" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->tarifa3 }}" @endif></td>
                                <td>
                                    <span id="impIndyCom3Span">@if($action == "Corrección" ) $<?php echo number_format($ica->impIndyCom3,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->impIndyCom3 }}" @else value="0" @endif name="impIndyCom3" id="impIndyCom3"></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 4</td>
                                <td><input class="form-control" type="text" name="codClasiMuni4" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->codClasiMuni4 }}" @endif></td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->ingreGravados4 }}" @else value="0" @endif id="ingreGravados4" name="ingreGravados4" onchange="operation()"></td>
                                <td><input class="form-control" type="number" name="tarifa4" id="tarifa4" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->tarifa4 }}" @endif></td>
                                <td>
                                    <span id="impIndyCom4Span">@if($action == "Corrección" ) $<?php echo number_format($ica->impIndyCom4,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->ingreGravados4 }}" @else value="0" @endif name="impIndyCom4" id="impIndyCom4"></td>
                            </tr>
                            <tr>
                                <td>OTRAS ACTIVIDADES</td>
                                <td><input class="form-control" type="text" name="codClasiMuni5" onchange="operation()" @if($action == "Corrección" ) value="{{ $ica->codClasiMuni5 }}" @endif></td>
                                <td><input class="form-control" type="number" min="0" id="ingreGravados5" @if($action == "Corrección" ) value="{{ $ica->ingreGravados5 }}" @else value="0" @endif name="ingreGravado5" onchange="operation()"></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="2">16. TOTAL INGRESOS GRAVADOS</td>
                                <td>
                                    <span id="totIngreGravadoSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totIngreGravado,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totIngreGravado }}" @else value="0" @endif name="totIngreGravado" id="totIngreGravado"></td>
                                <td>17.TOTAL IMPUESTO</td>
                                <td>
                                    <span id="totImpuestoSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totImpuesto,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totImpuesto }}" @else value="0" @endif name="totImpuesto" id="totImpuesto"></td>
                            </tr>
                            <tr>
                                <td colspan="2">18.GENERACIÓN DE ENERGIA CAPACIDAD INSTALADA</td>
                                <td><input class="form-control" type="number" min="0" placeholder="KW" name="genEnergiaCapacidad" @if($action == "Corrección" ) value="{{ $ica->genEnergiaCapacidad }}"@endif
                                           id="genEnergiaCapacidad" onchange="operation()"></td>
                                <td>19.IMP LEY 56 DE 1981</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->impLey56 }}" @else value="0" @endif name="impLey56" id="impLey56"
                                           onchange="operation()"></td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- TABLA D. LIQUIDACIÓN IMPUESTO --}}
                        <table id="TABLA4" class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="2">D. LIQUIDACIÓN IMPUESTO</th>
                            </tr>
                            </tbody>
                            <tr>
                                <td>20. TOTAL IMPUESTO DE INDUSTRIA Y COMERCIO (RENGLÓN 17 + 19)</td>
                                <td>
                                    <span id="totImpIndyComSpan">@if($action == "Corrección") $<?php echo number_format($ica->totImpIndyCom,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totImpIndyCom }}" @else value="0" @endif name="totImpIndyCom" id="totImpIndyCom">
                                </td>
                            </tr>
                            <tr>
                                <td>21. IMPUESTO DE AVISOS Y TABLEROS (15% DEL RENGLÓN 20)</td>
                                <td>
                                    <span id="impAviyTablerosSpan">@if($action == "Corrección") $<?php echo number_format($ica->impAviyTableros,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->impAviyTableros }}" @else value="0" @endif name="impAviyTableros" id="impAviyTableros">
                            </tr>
                            <tr>
                                <td>22. PAGO POR UNIDADES COMERCIALES ADICIONALES DEL SECTOR FINANCIERO</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->pagoUndComer }}" @else value="0" @endif name="pagoUndComer" id="pagoUndComer"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>23. SOBRETASA BOMBERIL (Ley 1575 de 2012) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->sobretasaBomberil }}" @else value="0" @endif name="sobretasaBomberil"
                                           id="sobretasaBomberil" onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>24. SOBRETASA DE SEGURIDAD (LEY 1421 DE 2011) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->sobretasaSeguridad }}" @else value="0" @endif name="sobretasaSeguridad"
                                           id="sobretasaSeguridad" onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td><b>25. TOTAL IMPUESTO A CARGO (RENGLONES 20+21+22+23+24)</b></td>
                                <td>
                                    <span id="totImpCargoSpan">@if($action == "Corrección") $<?php echo number_format($ica->totImpCargo,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totImpCargo }}" @else value="0" @endif name="totImpCargo" id="totImpCargo">
                                </td>
                            </tr>
                            <tr>
                                <td>26. MENOS VALOR DE EXENCIÓN O EXONERACIÓN SOBRE EL IMPUESTO Y NO SOBRE LOS INGRESOS</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->menosValorExencion }}" @else value="0" @endif name="menosValorExencion" id="menosValorExencion"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>27. MENOS RETENCIONES que le practicaron a favor de este municipio o distrito en este periodo</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->menosRetenciones }}" @else value="0" @endif name="menosRetenciones" id="menosRetenciones"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>28. MENOS AUTORRETENCIONES practicadas a favor de este municipio o distrito en este periodo</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->menosAutorretenciones }}" @else value="0" @endif name="menosAutorretenciones" id="menosAutorretenciones"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>29. MENOS ANTICIPO LIQUIDADO EN EL AÑO ANTERIOR</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->menosAnticipoLiquidado }}" @else value="0" @endif name="menosAnticipoLiquidado" id="menosAnticipoLiquidado"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>30. ANTICIPO DEL AÑO SIGUIENTE (Si existe, liquide porcentaje según Acuerdo Municipal o distrital)</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->anticipoAñoSiguiente }}" @else value="0" @endif name="anticipoAñoSiguiente" id="anticipoAñoSiguiente"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>31. SANCIONES<br>
                                <table class="table table-bordered text-center">
                                    <tr>
                                        <td>
                                            <div class="form-check-inline">
                                                <input class="form-check-input" type="radio" name="SANCIONES" value="EXTEMPORANEIDAD" id="SANCIONES1" @if($action == "Corrección" and $ica->SANCIONES == "EXTEMPORANEIDAD" ) checked @else checked @endif>
                                                <label class="form-check-label" for="SANCIONES1">EXTEMPORANEIDAD</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="SANCIONES" value="CORRECCIÓN" id="SANCIONES2" @if($action == "Corrección" and $ica->SANCIONES == "CORRECCIÓN" ) checked @endif>
                                                <label class="form-check-label" for="SANCIONES2">CORRECCIÓN</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="SANCIONES" value="INEXACTITUD" id="SANCIONES3" @if($action == "Corrección" and $ica->SANCIONES == "INEXACTITUD" ) checked @endif>
                                                <label class="form-check-label" for="SANCIONES3">INEXACTITUD</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="SANCIONES" value="OTRA" id="SANCIONES4" @if($action == "Corrección" and $ica->SANCIONES == "OTRA" ) checked @endif>
                                                <label class="form-check-label" for="SANCIONES4">OTRA</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="SANCIONES" value="NINGUNA" id="SANCIONES5" @if($action == "Corrección" and $ica->SANCIONES == "NINGUNA" ) checked @endif>
                                                <label class="form-check-label" for="SANCIONES5">NINGUNA</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="display: none" id="cualOtraTR">
                                        <td colspan="4" style="vertical-align: middle"><input  class="form-control" type="text" name="cualOtra" id="cualOtra" @if($action == "Corrección" ) value="{{ $ica->cualOtra }}" @endif
                                             placeholder="Cual Otra?"></td>
                                    </tr>
                                </table>
                                </td>
                                <td style="vertical-align: middle"><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->sancionesVal }}" @else value="0" @endif name="sancionesVal" id="sancionesVal"
                                                                          onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>32. MENOS SALDO A FAVOR DEL PERIODO ANTERIOR SIN SOLICITUD DE DEVOLUCIÓN O COMPENSACIÓN</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->menosSaldoaFavorPredio }}" @else value="0" @endif name="menosSaldoaFavorPredio" id="menosSaldoaFavorPredio"
                                    onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>33. TOTAL SALDO A CARGO (RENGLÓN 25-26-27-28-29+30+31-32)</td>
                                <td>
                                    <span id="totSaldoaCargoSpan">@if($action == "Corrección") $<?php echo number_format($ica->totSaldoaCargo,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totSaldoaCargo }}" @else value="0" @endif name="totSaldoaCargo" id="totSaldoaCargo">
                            </tr>
                            <tr style="display: none" id="totalSaldoaFavorTR">
                                <td>34. TOTAL SALDO A FAVOR (RENGLÓN 25-26-27-28-29+30+31-32) SI EL RESULTADO ES MENOR A CERO</td>
                                <td>
                                    <span id="totSaldoaFavorSpan">@if($action == "Corrección") $<?php echo number_format($ica->totSaldoaFavor,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totSaldoaFavor }}" @else value="0" @endif name="totSaldoaFavor" id="totSaldoaFavor">
                            </tr>
                        </table>

                        {{-- TABLA D. PAGO --}}
                        <table id="TABLA5" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="2">D. PAGO	</th>
                            </tr>
                            <tr>
                                <td>35. VALOR A PAGAR</td>
                                <td>
                                    <span id="valoraPagarSpan">@if($action == "Corrección") $<?php echo number_format($ica->valoraPagar,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->valoraPagar }}" @else value="0" @endif name="valoraPagar" id="valoraPagar">
                            </tr>
                            <tr>
                                <td>36. DESCUENTO POR PRONTO PAGO (Si existe, liquidelo según el Acuerdo Municipial o distrital)</td>
                                <td>
                                    <span id="valorDescSpan">@if($action == "Corrección") $<?php echo number_format($ica->valorDesc,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->valorDesc }}" @else value="0" @endif name="valorDesc" id="valorDesc"
                                           onchange="operation()">
                                </td>
                            </tr>
                            <tr>
                                <td>37. INTERESES DE MORA</td>
                                <td><input class="form-control" type="number" min="0" @if($action == "Corrección" ) value="{{ $ica->interesesMora }}" @else value="0" @endif name="interesesMora" id="interesesMora"
                                           onchange="operation()"></td>
                            </tr>
                            <tr>
                                <td>38. TOTAL A PAGAR (RENGLÓN 35-36+37)</td>
                                <td>
                                    <span id="totPagarSpan">@if($action == "Corrección") $<?php echo number_format($ica->totPagar,0) ?> @else $0 @endif</span>
                                    <input class="form-control" type="hidden" min="0" @if($action == "Corrección" ) value="{{ $ica->totPagar }}" @else value="0" @endif name="totPagar" id="totPagar">
                            </tr>
                            </tbody>
                        </table>


                        {{-- TABLA E. FIRMAS --}}
                        <table id="TABLA7" class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="2">FINALIZAR FORMULARIO</th>
                            </tr>
                            <tr>
                                <td>
                                    37. Fecha de presentación
                                    <br>
                                    <h3>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</h3>
                                </td>
                                <td>
                                    @if($action == "Corrección")
                                        @if($pago->estado == "Borrador")
                                            <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Firmar y Presentar</button>
                                        @endif
                                    @else
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Generar Borrador</button>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row">ESTE FORMULARIO Y SU PRESENTACIÓN NO TIENE COSTO ALGUNO</th>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("formulario").addEventListener('submit', validarFormulario);
        });

        function validarFormulario(evento) {
            evento.preventDefault();
            var totIngreOrd = document.getElementById('totIngreOrd').value;
            //SE REMUEVE LA VALIDACION DEBIDO A QUE SE PUEDE GENERAR EL FORMULARIO POR VALOR A PAGAR EN 0$
            //if(totIngreOrd.length < 2) {
                //alert('Valor menor a dos digitos para el total de ingresos');
                //return;
            //}

            this.submit();
        }

        $("input[name=SANCIONES]").click(function(event){
            var valor = $(event.target).val();
            if(valor =="OTRA"){
                $("#cualOtraTR").show();
            } else {
                document.getElementById("cualOtraTR").style.display = "none";
            }
        });

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function operation(){
            var añoGravable = document.getElementById("añoGravable").value;

            var num1 = document.getElementById("totIngreOrd").value;
            var num2 = document.getElementById("menosIngreFuera").value;
            var tot = parseInt(num1) - parseInt(num2);
            if(tot < 0) tot = 0;

            document.getElementById('totIngreOrdinSpan').innerHTML = formatter.format(tot);
            document.getElementById('totIngreOrdin').value = tot;

            var num3 = document.getElementById("menosIngreDevol").value;
            var num4 = document.getElementById("menosIngreExport").value;
            var num5 = document.getElementById("menosIngreOtrasActiv").value;
            var num6 = document.getElementById("menosIngreActivExcentes").value;
            var value = parseInt(tot) - parseInt(num3) - parseInt(num4) - parseInt(num5) - parseInt(num6);
            if(value < 0) value = 0;

            document.getElementById('totIngreGravablesSpan').innerHTML = formatter.format(value);
            document.getElementById('totIngreGravablesSpan2').innerHTML = formatter.format(value);
            document.getElementById('totIngreGravables').value = value;

            var num7 = document.getElementById("tarifa").value;
            var impIyCo = (value * num7)/1000;
            if(impIyCo < 0) impIyCo = 0;
            document.getElementById('impIndyComSpan').innerHTML = formatter.format(impIyCo);
            document.getElementById('impIndyCom').value = impIyCo;

            var num8 = document.getElementById("ingreGravados2").value;
            var num9 = document.getElementById("tarifa2").value;
            if(num8 != 0){
                var impIyCo2 = (parseInt(num8) * parseInt(num9))/1000;
                if(impIyCo2 < 0) impIyCo2 = 0;
                document.getElementById('impIndyCom2Span').innerHTML = formatter.format(impIyCo2);
                document.getElementById('impIndyCom2').value = impIyCo2;
            }

            var num10 = document.getElementById("ingreGravados3").value;
            var num11 = document.getElementById("tarifa3").value;
            if(num10 != 0){
                var impIyCo3 = (parseInt(num10) * parseInt(num11))/1000;
                if(impIyCo3 < 0) impIyCo3 = 0;
                document.getElementById('impIndyCom3Span').innerHTML = formatter.format(impIyCo3);
                document.getElementById('impIndyCom3').value = impIyCo3;
            }

            var num12 = document.getElementById("ingreGravados4").value;
            var num13 = document.getElementById("tarifa4").value;
            if(num12 != 0){
                var impIyCo4 = (parseInt(num12) * parseInt(num13))/1000;
                if(impIyCo4 < 0) impIyCo4 = 0;
                document.getElementById('impIndyCom4Span').innerHTML = formatter.format(impIyCo4);
                document.getElementById('impIndyCom4').value = impIyCo4;
            }

            //16. TOTAL INGRESOS GRAVADOS
            var ingGrav2 = document.getElementById("ingreGravados2").value;
            var ingGrav3 = document.getElementById("ingreGravados3").value;
            var ingGrav4 = document.getElementById("ingreGravados4").value;
            var ingGrav5 = document.getElementById("ingreGravados5").value;
            var totIngrGrav = value + parseInt(ingGrav2) + parseInt(ingGrav3) + parseInt(ingGrav4) + parseInt(ingGrav5)
            if(totIngrGrav < 0) totIngrGrav = 0;

            document.getElementById('totIngreGravadoSpan').innerHTML = formatter.format(totIngrGrav);
            document.getElementById('totIngreGravado').value = totIngrGrav;

            //17. TOTAL IMPUESTO
            var impIndyCom2 = document.getElementById("impIndyCom2").value;
            var impIndyCom3 = document.getElementById("impIndyCom3").value;
            var impIndyCom4 = document.getElementById("impIndyCom4").value;
            var totImpuesto = parseInt(impIyCo) + parseInt(impIndyCom2) + parseInt(impIndyCom3) + parseInt(impIndyCom4);
            if(totImpuesto < 0) totImpuesto = 0;
            document.getElementById('totImpuestoSpan').innerHTML = formatter.format(totImpuesto);
            document.getElementById('totImpuesto').value = totImpuesto;

            //20. TOTAL IMPUESTO DE INDUSTRIA Y COMERCIO
            var ley56 = document.getElementById("impLey56").value;
            var totImpuestoIndyComer = parseInt(ley56) + parseInt(totImpuesto);
            if(totImpuestoIndyComer < 0) totImpuestoIndyComer = 0;
            document.getElementById('totImpIndyComSpan').innerHTML = formatter.format(totImpuestoIndyComer);
            document.getElementById('totImpIndyCom').value = totImpuestoIndyComer;

            //21. IMPUESTO DE AVISS Y TABLEROS
            var impAyT = (totImpuestoIndyComer * 15)/100;
            if(impAyT < 0) impAyT = 0;
            document.getElementById('impAviyTablerosSpan').innerHTML = formatter.format(impAyT);
            document.getElementById('impAviyTableros').value = impAyT;

            //25. TOTAL IMPUESTO A CARGO
            var pagoUndComer = document.getElementById("pagoUndComer").value;
            var sobretasaBomberil = document.getElementById("sobretasaBomberil").value;
            var sobretasaSeguridad = document.getElementById("sobretasaSeguridad").value;
            var totImpCargo = totImpuestoIndyComer + impAyT + parseInt(pagoUndComer) + parseInt(sobretasaBomberil) + parseInt(sobretasaSeguridad);
            if(totImpCargo < 0) totImpCargo = 0;
            document.getElementById('totImpCargoSpan').innerHTML = formatter.format(totImpCargo);
            document.getElementById('totImpCargo').value = totImpCargo;

            // 33. TOTAL SALDO A CARGO
            var menosValorExencion = document.getElementById("menosValorExencion").value;
            var menosRetenciones = document.getElementById("menosRetenciones").value;
            var menosAutorretenciones = document.getElementById("menosAutorretenciones").value;
            var menosAnticipoLiquidado = document.getElementById("menosAnticipoLiquidado").value;
            var anticipoAñoSiguiente = document.getElementById("anticipoAñoSiguiente").value;

            var radioSancion = document.getElementById("SANCIONES5").checked;
            var sanciones = document.getElementById("sancionesVal").value;
            if(radioSancion)sanciones = 0;
            var menosSaldoaFavorPredio = document.getElementById("menosSaldoaFavorPredio").value;

            var totSaldoCargo = totImpCargo - parseInt(menosValorExencion) - parseInt(menosRetenciones) - parseInt(menosAutorretenciones)
                - parseInt(menosAnticipoLiquidado) + parseInt(anticipoAñoSiguiente) + parseInt(sanciones) - parseInt(menosSaldoaFavorPredio);
            if(totSaldoCargo < 0) totSaldoCargo = 0;
            document.getElementById('totSaldoaCargoSpan').innerHTML = formatter.format(totSaldoCargo);
            document.getElementById('totSaldoaCargo').value = totSaldoCargo;

            //34. TOTAL SALDO A FAVOR
            if(totSaldoCargo < 0){
                document.getElementById('totSaldoaFavorSpan').innerHTML = formatter.format(totSaldoCargo * -1);
                document.getElementById('totSaldoaFavor').value = totSaldoCargo * -1;
                $("#totalSaldoaFavorTR").show();
            }

            //35. VALOR A PAGAR
            document.getElementById('valoraPagarSpan').innerHTML = formatter.format(totSaldoCargo);
            document.getElementById('valoraPagar').value = totSaldoCargo;

            //38. TOTAL A PAGAR

            //VALIDACION DEL DESCUENTO CORRESPONDIENTE
            if(añoGravable == 2023) tasaDesc = 0.3;
            else if(añoGravable == 2022)  tasaDesc = 0.0;
            else if(añoGravable == 2021)  tasaDesc = 0.0;
            else if(añoGravable == 2020)  tasaDesc = 0.8;
            else tasaDesc = 0.2;

            var valorDesc = totSaldoCargo * tasaDesc;

            document.getElementById("valorDesc").value = valorDesc;
            document.getElementById("valorDescSpan").innerHTML = formatter.format(valorDesc);
            var interesesMora = document.getElementById("interesesMora").value;
            var totPagar = totSaldoCargo - parseInt(valorDesc) + parseInt(interesesMora);
            if(totPagar < 0) totPagar = 0;
            document.getElementById('totPagarSpan').innerHTML = formatter.format(totPagar);
            document.getElementById('totPagar').value = totPagar;
        }
    </script>
@stop