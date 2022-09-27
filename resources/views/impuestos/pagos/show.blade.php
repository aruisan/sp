@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
        <div class="col-md-12 align-self-center">
            <div class="breadcrumb text-center">
                <strong>
                    <h4><b>Detalle del Pago</b></h4>
                </strong>
            </div>
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link" href="{{ url('/impuestos/Pagos') }}" ><i class="fa fa-arrow-left"></i></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" data-toggle="pill" href="#tabDetalle">Detalle</a>
                </li>
            </ul>
            <div class="tab-content" >
                <div id="tabDetalle" class="tab-pane fade in active"><br>
                    @if($pago->modulo == "ICA-Contribuyente")
                        <table id="TABLA1" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="3">FORMULARIO UNICO NACIONAL DE DECLARACION Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</th>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <th scope="row" >MUNICIPIO O DISTRITO </th>
                                <th scope="row">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                <th scope="row">AÑO GRAVABLE: {{$formulario->añoGravable}}</th>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td>Fecha Presentacion: {{ Carbon\Carbon::parse($formulario->presentacion)->Format('d-m-Y')}}</td>
                                <td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td colspan="3"><b>No. Formulario: {{$formulario->numReferencia}}</b></td>
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
                                            <td>6. Número de establecimientos Locales: <br>{{ $formulario->numEstableLoc }}</td>
                                            <td>Nal: <br> {{ $formulario->numEstableNal }}</td>
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
                                <th scope="row" colspan="2">B. BASE GRAVABLE</th>
                            </tr>
                            <tr>
                                <td>8. TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DEL PERIODO EN TODO EL PAIS:</td>
                                <td>$<?php echo number_format($formulario->totIngreOrd,0) ?></td>
                            </tr>
                            <tr>
                                <td>9. MENOS INGRESOS FUERA DE ESTE MUNICIPIO O DISTRITO</td>
                                <td>$<?php echo number_format($formulario->menosIngreFuera,0) ?></td>
                            </tr>
                            <tr>
                                <td>10. TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DE ESTE MUNICIPIO (REGLÓN 8 MENOS 9)</td>
                                <td>$<?php echo number_format($formulario->totIngreOrdin,0) ?></td>
                            </tr>
                            <tr>
                                <td>11. MENOS INGRESOS POR DEVOLUCIONES, REBAJAS, DESCUENTOS</td>
                                <td>$<?php echo number_format($formulario->menosIngreDevol,0) ?></td>
                            </tr>
                            <tr>
                                <td>12. MENOS INGRESOS POR EXPORTACIONES Y VENTAS DE ACTIVOS FIJOS</td>
                                <td>$<?php echo number_format($formulario->menosIngreExport,0) ?></td>
                            </tr>
                            <tr>
                                <td>13. MENOS INGRESOS POR OTRAS ACTIVIDADES EXCLUIDAS O NO SUJETAS Y OTROS INGRESOS NO GRAVADOS</td>
                                <td>$<?php echo number_format($formulario->menosIngreOtrasActiv,0) ?></td>
                            </tr>
                            <tr>
                                <td>14. MENOS INGRESOS POR ACTIVIDADES EXCENTES EN ESTE MUNICIPIO O DISTRITO (POR ACUERDO)</td>
                                <td>$<?php echo number_format($formulario->menosIngreActivExcentes,0) ?></td>
                            </tr>
                            <tr>
                                <td>15. TOTAL INGRESOS GRAVABLES (RENGLON 10 MENOS 11,12,13 Y 14)</td>
                                <td>$<?php echo number_format($formulario->totIngreGravables,0) ?></td>
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
                                <td>{{ $formulario->codClasiMuni }}</td>
                                <td>$<?php echo number_format($formulario->totIngreGravables,0) ?></td>
                                <td><?php echo number_format($formulario->tarifa,0) ?>%</td>
                                <td>$<?php echo number_format($formulario->impIndyCom,0) ?></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 2</td>
                                <td>{{ $formulario->codClasiMuni2 }}</td>
                                <td>$<?php echo number_format($formulario->ingreGravados2,0)?></td>
                                <td><?php echo number_format($formulario->tarifa2,0) ?>%</td>
                                <td>$<?php echo number_format($formulario->impIndyCom2,0) ?></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 3</td>
                                <td>{{ $formulario->codClasiMuni3 }}</td>
                                <td>$<?php echo number_format($formulario->ingreGravados3,0)?></td>
                                <td><?php echo number_format($formulario->tarifa3,0) ?>%</td>
                                <td>$<?php echo number_format($formulario->impIndyCom3,0) ?></td>
                            </tr>
                            <tr>
                                <td>ACTIVIDAD 4</td>
                                <td>{{ $formulario->codClasiMuni4 }}</td>
                                <td>$<?php echo number_format($formulario->ingreGravados4,0)?></td>
                                <td><?php echo number_format($formulario->tarifa4,0) ?>%</td>
                                <td>$<?php echo number_format($formulario->impIndyCom4,0) ?></td>
                            </tr>
                            <tr>
                                <td>OTRAS ACTIVIDADES</td>
                                <td>{{ $formulario->codClasiMuni5 }}</td>
                                <td>$<?php echo number_format($formulario->ingreGravados5,0) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">16. TOTAL INGRESOS GRAVADOS</td>
                                <td>$<?php echo number_format($formulario->totIngreGravado,0) ?></td>
                                <td>17. TOTAL IMPUESTO</td>
                                <td>$<?php echo number_format($formulario->totImpuesto,0) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">18. GENERACIÓN DE ENERGIA CAPACIDAD INSTALADA</td>
                                <td>$<?php echo number_format($formulario->genEnergiaCapacidad,0) ?></td>
                                <td>19. IMP LEY 56 DE 1981</td>
                                <td>$<?php echo number_format($formulario->impLey56,0) ?></td>
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
                                <td>$<?php echo number_format($formulario->totImpIndyCom,0)?></td>
                            </tr>
                            <tr>
                                <td>21. IMPUESTO DE AVISOS Y TABLEROS (15% DEL RENGLÓN 20)</td>
                                <td>$<?php echo number_format($formulario->impAviyTableros,0)?></td>
                            </tr>
                            <tr>
                                <td>22. PAGO POR UNIDADES COMERCIALES ADICIONALES DEL SECTOR FINANCIERO</td>
                                <td>$<?php echo number_format($formulario->pagoUndComer,0)?></td>
                            </tr>
                            <tr>
                                <td>23. SOBRETASA BOMBERIL (Ley 1575 de 2012) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
                                <td>$<?php echo number_format($formulario->sobretasaBomberil,0)?></td>
                            </tr>
                            <tr>
                                <td>24. SOBRETASA DE SEGURIDAD (LEY 1421 DE 2011) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
                                <td>$<?php echo number_format($formulario->sobretasaSeguridad,0)?></td>
                            </tr>
                            <tr>
                                <td><b>25. TOTAL IMPUESTO A CARGO (RENGLONES 20+21+22+23+24)</b></td>
                                <td>$<?php echo number_format($formulario->totImpCargo,0)?></td>
                            </tr>
                            <tr>
                                <td>26. MENOS VALOR DE EXENCIÓN O EXONERACIÓN SOBRE EL IMPUESTO Y NO SOBRE LOS INGRESOS</td>
                                <td>$<?php echo number_format($formulario->menosValorExencion,0)?></td>
                            </tr>
                            <tr>
                                <td>27. MENOS RETENCIONES que le practicaron a favor de este municipio o distrito en este periodo</td>
                                <td>$<?php echo number_format($formulario->menosRetenciones,0)?></td>
                            </tr>
                            <tr>
                                <td>28. MENOS AUTORRETENCIONES practicadas a favor de este municipio o distrito en este periodo</td>
                                <td>$<?php echo number_format($formulario->menosAutorretenciones,0)?></td>
                            </tr>
                            <tr>
                                <td>29. MENOS ANTICIPO LIQUIDADO EN EL AÑO ANTERIOR</td>
                                <td>$<?php echo number_format($formulario->menosAnticipoLiquidado,0)?></td>
                            </tr>
                            <tr>
                                <td>30. ANTICIPO DEL AÑO SIGUIENTE (Si existe, liquide porcentaje según Acuerdo Municipal o distrital)</td>
                                <td>$<?php echo number_format($formulario->anticipoAñoSiguiente,0)?></td>
                            </tr>
                            <tr>
                                <td>31. SANCION: {{ $formulario->SANCIONES }}</td>
                                <td>$<?php echo number_format($formulario->sancionesVal,0)?></td>
                            </tr>
                            <tr>
                                <td>32. MENOS SALDO A FAVOR DEL PERIODO ANTERIOR SIN SOLICITUD DE DEVOLUCIÓN O COMPENSACIÓN</td>
                                <td>$<?php echo number_format($formulario->menosSaldoaFavorPredio,0)?></td>
                            </tr>
                            <tr>
                                <td>33. TOTAL SALDO A CARGO (RENGLÓN 25-26-27-28-29+30+31-32)</td>
                                <td>$<?php echo number_format($formulario->totSaldoaCargo,0)?></td>
                            </tr>
                            @if($formulario->totSaldoaFavor != 0)
                                <tr>
                                    <td>34. TOTAL SALDO A FAVOR (RENGLÓN 25-26-27-28-29+30+31-32) SI EL RESULTADO ES MENOR A CERO</td>
                                    <td>$<?php echo number_format($formulario->totSaldoaFavor,0)?></td>
                                </tr>
                            @endif
                        </table>
                        {{-- TABLA D. PAGO --}}
                        <table id="TABLA5" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="2">D. PAGO	</th>
                            </tr>
                            <tr>
                            <tr>
                                <td>35. VALOR A PAGAR</td>
                                <td>$<?php echo number_format($formulario->valoraPagar,0)?></td>
                            </tr>
                            <tr>
                                <td>36. DESCUENTO POR PRONTO PAGO (Si existe, liquidelo según el Acuerdo Municipial o distrital)</td>
                                <td>$<?php echo number_format($formulario->valorDesc,0)?></td>
                            </tr>
                            <tr>
                                <td>37. INTERESES DE MORA</td>
                                <td>$<?php echo number_format($formulario->interesesMora,0)?></td>
                            </tr>
                            <tr>
                                <td><b>38. TOTAL A PAGAR (RENGLÓN 35-36+37)</b></td>
                                <td><b>$<?php echo number_format($formulario->totPagar,0)?></b></td>
                            </tr>
                            </tbody>
                        </table>
                        <table id="TABLA6" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="2">E. FINALIZAR</th>
                            </tr>
                            <tr>
                            <tr>
                                <td><a href="{{ url('impuestos/ICA/contri/update/'.$formulario->id) }}" class="btn btn-impuesto" style="font-size: 25px; color: white">Corregir</a></td>
                                <td>
                                    <form action="{{url('/impuestos/Pagos/Send')}}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <input type="hidden" value="{{$pago->id}}" name="pago_id">
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Firmar y Presentar</button>
                                    </form>
                                </td>
                            </tr>
                            </tbody>
                        </table>


                    @elseif($pago->modulo == "PREDIAL")
                        <table class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <td colspan="3">FORMULARIO UNICO DEL IMPUESTO PREDIAL</td>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td>MUNICIPIO O DISTRITO </td>
                                <td colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</td>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td>DEPARTAMENTO</td>
                                <td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- TABLA A. INFORMACIÓN DEL CONTRIBUYENTE --}}
                        <table id="TABLA2" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <td colspan="3">INFORMACIÓN DEL CONTRIBUYENTE</td>
                            </tr>
                            <tr>
                                <td>Naturaleza Juridica:{{$rit->natJuridiContri}}</td>
                                <td>{{ $rit->tipoDocContri }}{{ $rit->numDocContri }}</td>
                                <td>Clasificación Contribuyente:{{ $rit->claseContribuyente }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Nombre y apellidos o razón Social: {{ $rit->apeynomContri }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Dirección: {{ $rit->dirNotifContri }}</td>
                            </tr>
                            <tr>
                                <td>Teléfono Móvil: {{ $rit->movilContri }}</td>
                                <td colspan="2">Correo electrónico: {{ $rit->emailContri }}</td>
                            </tr>
                            </tbody>
                        </table>
                        {{-- TABLA B. BASE GRAVABLE --}}
                        <table class="table text-center table-bordered">
                            <tr style="background-color: #0e7224; color: white"><td colspan="2">INFORMACION PREDIO Y PAGO</td></tr>
                            <tbody>
                            <tr><td>Fecha de Creación:</td><td>{{ $formulario->presentacion }}</td></tr>
                            <tr><td>Número Catastral:</td><td>{{ $formulario->numCatas }}</td></tr>
                            <tr><td>Dirección:</td><td>{{ $formulario->direccion }}</td></tr>
                            <tr><td>Cédula:</td><td>{{$formulario->cedula}}</td></tr>
                            <tr><td>Propietario:</td><td>{{$formulario->propietario}}</td></tr>
                            <tr><td>Matricula Inmobiliaria:</td><td>{{$formulario->matricula}}</td></tr>
                            <tr><td>Área de Terreno:</td><td>{{$formulario->area}}m2</td></tr>
                            <tr><td>Tasa Interés:</td><td>{{$formulario->tasaInt}}</td></tr>
                            <tr><td>Tarifa por mil:</td><td>{{$formulario->tarifaMil}}</td></tr>
                            <tr><td>Tasa Bomberil:</td><td>{{$formulario->tarifaBomb}}</td></tr>
                            <tr><td>Fecha de pago:</td><td> {{ \Carbon\Carbon::parse($formulario->fechaPago)->format('d-m-Y') }}</td></tr>
                            <tr><td>Tasa de Descuento:</td><td>{{$formulario->tasaDesc}}%</td></tr>
                            <tr><td>Año de Inicio:</td><td>{{$formulario->año}}</td></tr>
                            </tbody>
                        </table>
                        <br>
                        <table class="table text-center table-bordered">
                            <thead>
                            <tr style="background-color: #0e7224; color: white">
                                <td>Años</td>
                                <td>Fecha de vencimiento</td>
                                <td>Avalúos</td>
                                <td>Imp Predial</td>
                                <td>Tasa Bomberil</td>
                                <td>Sub Total</td>
                                <td>Interes Mora</td>
                                <td>Tasa Ambiental</td>
                                <td>Interes Ambiental</td>
                                <td>TOTAL</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($formulario->liquid as $item)
                                <tr>
                                    <td>{{ $item->año }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->fecha_venc)->format('d-m-Y') }}</td>
                                    <td>$ <?php echo number_format($item->avaluo,0) ?></td>
                                    <td>$ <?php echo number_format($item->imp_predial,0) ?></td>
                                    <td>$ <?php echo number_format($item->tasa_bomberil,0) ?></td>
                                    <td>$ <?php echo number_format($item->sub_total,0) ?></td>
                                    <td>$ <?php echo number_format($item->int_mora,0) ?></td>
                                    <td>$ <?php echo number_format($item->tasa_ambiental,0) ?></td>
                                    <td>$ <?php echo number_format($item->int_ambiental,0) ?></td>
                                    <td>$ <?php echo number_format($item->tot_año,0) ?></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>TOTAL</td>
                                <td>$ <?php echo number_format($formulario->tot_imp,0) ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>DESCUENTO</td>
                                <td>$ <?php echo number_format($formulario->desc_imp,0) ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>TOTAL PAGO</td>
                                <td>$ <?php echo number_format($formulario->tot_pago,0) ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <table id="TABLA7" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <td colspan="2">Pago</td>
                            </tr>
                            <tr>
                                <td>
                                    Fecha de Creación
                                    <br>
                                    <h3>{{ $formulario->presentacion }}</h3>
                                </td>
                                <td>
                                    Valor a Pagar
                                    <br>
                                    <h3>$ <?php echo number_format($formulario->tot_pago,0) ?></h3>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    @elseif($pago->modulo == "ICA-AgenteRetenedor")
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
                                <td>1. Año: {{ $formulario->añoGravable }}</td>
                                <td>2. Periodo:
                                    @if($formulario->periodo == 1) Enero
                                    @elseif($formulario->periodo == 2) Febrero
                                    @elseif($formulario->periodo == 3) Marzo
                                    @elseif($formulario->periodo == 4) Abril
                                    @elseif($formulario->periodo == 5) Mayo
                                    @elseif($formulario->periodo == 6) Junio
                                    @elseif($formulario->periodo == 7) Julio
                                    @elseif($formulario->periodo == 8) Agosto
                                    @elseif($formulario->periodo == 9) Septiembre
                                    @elseif($formulario->periodo == 10) Octubre
                                    @elseif($formulario->periodo == 11) Noviembre
                                    @else Diciembre @endif
                                </td>
                                <td style="vertical-align: middle">
                                    @if($formulario->opciondeUso == "Declaración") DECLARACIÓN INICIAL
                                    @else CORRECCIÓN @endif
                                </td>
                            </tr>
                            <tr style="background-color: #bfc3bf; color: black">
                                <td colspan="3"><b>No. Formulario: {{$formulario->numReferencia}}</b></td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- TABLA A. INFORMACIÓN DEL CONTRIBUYENTE --}}
                        <table id="TABLA2" class="table text-center">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white">
                                <th scope="row" colspan="4">INFORMACIÓN DEL CONTRIBUYENTE</th>
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
                                            <td> Nombre y apellidos o razón Social: {{ $rit->apeynomContri }}</td>
                                            <td>{{ $rit->tipoDocContri }} No. {{ $rit->numDocContri }}</td>
                                            <td>Dirección de Notificación: {{ $rit->dirNotifContri }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Municipio o Distrito de la Dirección de Notificación: PROVIDENCIA Y SANTA CATALINA ISLAS </td>
                                            <td>Departamento: ARCHIPIELAGO DE SAN ANDRES </td>
                                        </tr>
                                        <tr>
                                            <td>Teléfono Móvil: {{ $rit->movilContri }}</td>
                                            <td colspan="2">Correo electrónico: {{ $rit->emailContri }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                11. Calidad de agente reteción <br> Código Agente:
                                                @if($formulario->codAgente == 1)
                                                    01 - Entidad pública
                                                @elseif($formulario->codAgente == 2)
                                                    02 Gran contribuyente
                                                @elseif($formulario->codAgente == 3)
                                                    03 consoricio uniones Temporales
                                                @elseif($formulario->codAgente == 4)
                                                    04 Autorretenedor
                                                @elseif($formulario->codAgente == 5)
                                                    05 Designado
                                                @else
                                                    06 Otro
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white"><th scope="row" colspan="3">Retenciones practicadas</th></tr>
                            <tr>
                                <td>12</td><td>Por contratos de obra o consultoría: </td>
                                <td>$<?php echo number_format($formulario->contratosObra,0) ?></td>
                            </tr>
                            <tr>
                                <td>13</td><td>Por Contratos de Prestación de servicios</td>
                                <td>$<?php echo number_format($formulario->contratosPrestServ,0) ?></td>
                            </tr>
                            <tr>
                                <td>14</td><td>Por Compras de bienes y servicios diferentes a los anteriors</td>
                                <td>$<?php echo number_format($formulario->compraBienes,0) ?></td>
                            </tr>
                            <tr>
                                <td>15</td><td>Por otras actividades gravadas</td>
                                <td>$<?php echo number_format($formulario->otrasActiv,0) ?></td>
                            </tr>
                            <tr>
                                <td>16</td><td>Practicadas en periodos anteriores dejadas de declarar</td>
                                <td>$<?php echo number_format($formulario->practicadasPeriodosAnt,0) ?></td>
                            </tr>
                            <tr>
                                <td>17</td><td>Total Retenciones practicadas</td>
                                <td>$<?php echo number_format($formulario->totRetenciones,0) ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <table class="table text-center table-bordered">
                            <tbody>
                            <tr style="background-color: #0e7224; color: white"><th scope="row" colspan="3">Pagos</th></tr>
                            <tr>
                                <td>18</td><td>Devolución por exceso de cobro</td>
                                <td>$<?php echo number_format($formulario->devolucionExceso,0) ?></td>
                            </tr>
                            <tr>
                                <td>19</td><td>Devolución retención practicada no aplicable</td>
                                <td>$<?php echo number_format($formulario->devolucionRetencion,0) ?></td>
                            </tr>
                            <tr>
                                <td>20</td><td>Total retención neta</td>
                                <td>$<?php echo number_format($formulario->totalRetencion,0) ?></td>
                            </tr>
                            <tr>
                                <td>21</td><td>Sanción por extemporaneidad o declarar</td>
                                <td>$<?php echo number_format($formulario->sancionExtemp,0) ?></td>
                            </tr>
                            <tr>
                                <td>22</td><td>Sanción por corrección o inexactitud</td>
                                <td>$<?php echo number_format($formulario->sancionCorreccion,0) ?></td>
                            </tr>
                            <tr>
                                <td>23</td><td>Intereses Moratorios</td>
                                <td>$<?php echo number_format($formulario->interesMoratorio,0) ?></td>
                            </tr>
                            <tr>
                                <td>24</td><td>Pago Total retenciones netas mas sanciones e intereses</td>
                                <td>$<?php echo number_format($formulario->pagoTotal,0) ?></td>
                            </tr>
                            </tbody>
                        </table>
                        {{-- TABLA E. FIRMAS --}}
                        <table id="TABLA7" class="table text-center table-bordered">
                            <tbody>
                            <tr><th style="background-color: #0e7224; color: white" scope="row" colspan="3">FIRMAS</th></tr>
                            <tr>
                                <td style="width: 300px">25. Identificación del signatario<br> {{ $formulario->compraBienes }}</td>
                                <td>26. Nombre del signatario <br> {{ $formulario->compraBienes }}</td>
                                <td style="vertical-align: middle">
                                    @if($formulario->signatario == "repLegal") Signatario Representante Legal
                                    @elseif($formulario->signatario == "delegado") Signatario Delegado o Con Poder
                                    @else Signatario Principal @endif
                                </td>
                            </tr>
                            <tr>
                                <td>30. T.P. Contador Revisor Fiscal signatario <br> {{ $formulario->tpRevFisc }}<br></td>
                                <td>31. Nombre del Contador o Revisor Fiscal <br> {{ $formulario->nameRevFisc }}<br></td>
                                <td>Fecha de presentación <br> <h3>{{ Carbon\Carbon::parse($formulario->presentacion)->Format('d-m-Y')}}</h3>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
