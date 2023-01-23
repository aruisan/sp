@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
            <div class="col-md-12 align-self-center">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>Registro de Información Tributaria</b></h4>
                        <h4><b>Alcaldia Municipal de Providencia</b></h4>
                        <h4><b>Secretaria de Hacienda</b></h4>
                        FORMATO SHI-WEB02-2022
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/impuestos/RIT')}}"  method="POST" enctype="multipart/form-data" id="formulario">
                            {{ csrf_field() }}
                            {{-- TABLA 1.ENCABEZADO--}}
                            <table id="TABLA1" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">I. ENCABEZADO (Sólo puede marcar una casilla para 1 y 2, e ingrese la identificación del contribuyente)</th>
                                </tr>
                                <tr>
                                    <td>
                                        1. Escoja opción de uso
                                        @if($action == "Inscripción")
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" value="Inscripción" id="opciondeUso1" checked>
                                                <label class="form-check-label" for="opciondeUso1">Inscripción</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" disabled>
                                                <label class="form-check-label" for="opciondeUso2">Actualización</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" disabled>
                                                <label class="form-check-label" for="opciondeUso3">Cancelación</label>
                                            </div>
                                        @elseif($action == "Actualización")
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" disabled>
                                                <label class="form-check-label" for="opciondeUso1">Inscripción</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" value="Actualización" id="opciondeUso2" checked>
                                                <label class="form-check-label" for="opciondeUso2">Actualización</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opciondeUso" value="Cancelación" id="opciondeUso3">
                                                <label class="form-check-label" for="opciondeUso3">Cancelación</label>
                                            </div>
                                        @else
                                            <input class="form-check-input" type="hidden" name="opciondeUso" value="Actualización">
                                            <br><b>Restaurar RIT</b>
                                        @endif
                                    </td>
                                    <td>
                                        2. Clase de Contribuyente
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="claseContribuyente" value="Retenedor" id="claseContribuyente"
                                                   @if($action != "Inscripción" and $rit->claseContribuyente == "Retenedor") checked @else checked @endif>
                                            <label class="form-check-label" for="claseContribuyente">Retenedor</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="claseContribuyente" value="Contribuyente" id="claseContribuyente2"
                                                   @if($action != "Inscripción" and $rit->claseContribuyente == "Contribuyente") checked @endif>
                                            <label class="form-check-label" for="claseContribuyente2">Contribuyente</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="claseContribuyente" value="Mixto" id="claseContribuyente3"
                                                   @if($action != "Inscripción" and $rit->claseContribuyente == "Mixto") checked @endif>
                                            <label class="form-check-label" for="claseContribuyente3">Mixto</label>
                                        </div>
                                    </td>
                                    <td>
                                        2.1. Otras Clases de Contribuyente
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="otrasClasesContribuyente" value="Gran Contribuyente" id="otrasClasesContribuyente"
                                                   @if($action != "Inscripción" and $rit->otrasClasesContribuyente == "Gran Contribuyente") checked @else checked @endif>
                                            <label class="form-check-label" for="otrasClasesContribuyente">Gran Contribuyente</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="otrasClasesContribuyente" value="Régimen Simple de Tributación" id="otrasClasesContribuyente2"
                                                   @if($action != "Inscripción" and $rit->otrasClasesContribuyente == "Régimen Simple de Tributación") checked @endif>
                                            <label class="form-check-label" for="otrasClasesContribuyente2">Régimen Simple de Tributación</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="otrasClasesContribuyente" value="Autorretenedor ICA" id="otrasClasesContribuyente3"
                                                   @if($action != "Inscripción" and $rit->otrasClasesContribuyente == "Autorretenedor ICA") checked @endif>
                                            <label class="form-check-label" for="otrasClasesContribuyente3">Autorretenedor ICA</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="otrasClasesContribuyente" value="Ninguna" id="otrasClasesContribuyente4"
                                                   @if($action != "Inscripción" and $rit->otrasClasesContribuyente == "Ninguna") checked @endif>
                                            <label class="form-check-label" for="otrasClasesContribuyente4">Ninguna</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="RevFiscalRow">
                                    <td colspan="3">
                                        3. Datos del Revisor fiscal y/o contador
                                        <table class="table text-center">
                                            <tr>
                                                <td><input type="text" class="form-control" placeholder="NOMBRE Y APELLIDOS" name="nameRevFisc"
                                                           @if($action != "Inscripción") value="{{ $rit->nameRevFisc }}" @endif></td>
                                                <td><input type="text" class="form-control" placeholder="IDENTIFICACIÓN" name="idRevFisc"
                                                           @if($action != "Inscripción") value="{{ $rit->idRevFisc }}" @endif></td>
                                                <td><input type="text" class="form-control" placeholder="T.P" name="TPRevFisc"
                                                           @if($action != "Inscripción") value="{{ $rit->TPRevFisc }}" @endif></td>
                                                <td><input type="email" class="form-control" placeholder="EMAIL" name="emailRevFisc"
                                                           @if($action != "Inscripción") value="{{ $rit->emailRevFisc }}" @endif></td>
                                                <td><input type="number" class="form-control" placeholder="MÓVIL" name="movilRevFisc"
                                                           @if($action != "Inscripción") value="{{ $rit->movilRevFisc }}" @endif></td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" class="form-control" placeholder="NOMBRE Y APELLIDOS" name="nameCont"
                                                           @if($action != "Inscripción") value="{{ $rit->nameCont }}" @endif></td>
                                                <td><input type="text" class="form-control" placeholder="IDENTIFICACIÓN" name="idCont"
                                                           @if($action != "Inscripción") value="{{ $rit->idCont }}" @endif></td>
                                                <td style="width: 10%"><input type="text" class="form-control" placeholder="T.P" name="TPCont"
                                                                              @if($action != "Inscripción") value="{{ $rit->TPCont }}" @endif></td>
                                                <td><input type="email" class="form-control" placeholder="EMAIL" name="emailCont"
                                                           @if($action != "Inscripción") value="{{ $rit->emailCont }}" @endif></td>
                                                <td style="width: 20%"><input type="number" class="form-control" placeholder="MÓVIL" name="movilCont"
                                                                              @if($action != "Inscripción") value="{{ $rit->movilCont }}" @endif></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR --}}
                            <table id="TABLA2" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR</th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        4. Tipo y Número de Documento
                                        <table class="table text-center">
                                            <tr>
                                                <td>
                                                    <div class="form-check-inline">
                                                        <input class="form-check-input" type="radio" name="tipoDocContri" value="C.C." id="tipoDoc1"
                                                               @if($action != "Inscripción" and $rit->tipoDocContri == "C.C.") checked @else checked @endif>
                                                        <label class="form-check-label" for="tipoDoc1">C.C</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="tipoDocContri" value="NIT" id="tipoDoc2"
                                                               @if($action != "Inscripción" and $rit->tipoDocContri == "NIT") checked @endif>
                                                        <label class="form-check-label" for="tipoDoc2">NIT</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="tipoDocContri" value="T.I." id="tipoDoc3"
                                                               @if($action != "Inscripción" and $rit->tipoDocContri == "T.I.") checked @endif>
                                                        <label class="form-check-label" for="tipoDoc3">T.I</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="tipoDocContri" value="C.E." id="tipoDoc4"
                                                               @if($action != "Inscripción" and $rit->tipoDocContri == "C.E.") checked @endif>
                                                        <label class="form-check-label" for="tipoDoc4">C.E</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" >No.</label>
                                                    </div>
                                                </td>
                                                <td><input type="text" class="form-control" name="numDocContri" required maxlength="14"
                                                           @if($action != "Inscripción") value="{{ $rit->numDocContri }}" @endif></td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" > - DV </label>
                                                    </div>
                                                </td>
                                                <td style="width: 10%"><input type="text" class="form-control" name="DVDocContri" maxlength="1"
                                                                              @if($action != "Inscripción") value="{{ $rit->DVDocContri }}" @endif></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        5. Naturaleza Jurídica
                                        <select class="form-control" id="natJuridiContri" name="natJuridiContri">
                                            @if($action == "Inscripción") <option value="0">Seleccione el código</option> @endif
                                            <option value="PJ" @if($action != "Inscripción" and $rit->natJuridiContri == "PJ") selected @endif>PJ - Jurídica</option>
                                            <option value="PN" @if($action != "Inscripción" and $rit->natJuridiContri == "PN") selected @endif>PN - Natural</option>
                                            <option value="SH" @if($action != "Inscripción" and $rit->natJuridiContri == "SH") selected @endif>SH - Sociedad de Hecho</option>
                                            <option value="PA" @if($action != "Inscripción" and $rit->natJuridiContri == "PA") selected @endif>PA - Patrimonio Autónomo</option>
                                            <option value="CO" @if($action != "Inscripción" and $rit->natJuridiContri == "CO") selected @endif>CO - Consorcios</option>
                                            <option value="UT" @if($action != "Inscripción" and $rit->natJuridiContri == "UT") selected @endif>UT - Unidad Temporal</option>
                                            <option value="CR" @if($action != "Inscripción" and $rit->natJuridiContri == "CR") selected @endif>CR - Comunidad organizada</option>
                                            <option value="SI" @if($action != "Inscripción" and $rit->natJuridiContri == "SI") selected @endif>SI - Sucesión Iliquida</option>
                                            <option value="SM" @if($action != "Inscripción" and $rit->natJuridiContri == "SM") selected @endif>SM - Sociedad de Economía mixta de todo orden</option>
                                            <option value="UA" @if($action != "Inscripción" and $rit->natJuridiContri == "UA") selected @endif>UA - Unidad Administrativa con régimen especial</option>
                                            <option value="DC" @if($action != "Inscripción" and $rit->natJuridiContri == "DC") selected @endif>DC - Departamento de Cundinamarca</option>
                                            <option value="LN" @if($action != "Inscripción" and $rit->natJuridiContri == "LN") selected @endif>LN - La Nación</option>
                                            <option value="EE" @if($action != "Inscripción" and $rit->natJuridiContri == "EE") selected @endif>EE - Entidad del Estado</option>
                                            <option value="EM" @if($action != "Inscripción" and $rit->natJuridiContri == "EM") selected @endif>EM - Establecimiento Público y Empresa Industrial, Comercial de Orden Municipal</option>
                                            <option value="EC" @if($action != "Inscripción" and $rit->natJuridiContri == "EC") selected @endif>EC - Entidad del Estado de cualquier naturaleza</option>
                                            <option value="EN" @if($action != "Inscripción" and $rit->natJuridiContri == "EN") selected @endif>EN - Establecimiento Público y Empresa Industrial, Comercial de Orden Nacional</option>
                                            <option value="ED" @if($action != "Inscripción" and $rit->natJuridiContri == "ED") selected @endif>ED - Establecimiento Público y Empresa Industrial, Comercial de Orden Departamental</option>
                                        </select>
                                    </td>
                                    <td>
                                        6. Tipo de Sociedad
                                        <select class="form-control" id="tipSociedadContri" name="tipSociedadContri">
                                            @if($action == "Inscripción") <option value="0">Seleccione el código</option> @endif
                                            <option value="1" @if($action != "Inscripción" and $rit->tipSociedadContri == "1") selected @endif>1 - Colectiva</option>
                                            <option value="2" @if($action != "Inscripción" and $rit->tipSociedadContri == "2") selected @endif>2 - Limitada</option>
                                            <option value="3" @if($action != "Inscripción" and $rit->tipSociedadContri == "3") selected @endif>3 - Anónima</option>
                                            <option value="4" @if($action != "Inscripción" and $rit->tipSociedadContri == "4") selected @endif>4 - En comandita por acciones</option>
                                            <option value="5" @if($action != "Inscripción" and $rit->tipSociedadContri == "5") selected @endif>5 - En comandita simple</option>
                                            <option value="6" @if($action != "Inscripción" and $rit->tipSociedadContri == "6") selected @endif>6 - Unipersonal</option>
                                            <option value="7" @if($action != "Inscripción" and $rit->tipSociedadContri == "7") selected @endif>7 - Anónima Simplificada</option>
                                            <option value="8" @if($action != "Inscripción" and $rit->tipSociedadContri == "8") selected @endif>8 - De Economía mixta</option>
                                            <option value="9" @if($action != "Inscripción" and $rit->tipSociedadContri == "9") selected @endif>9 - Extranjera</option>
                                            <option value="10" @if($action != "Inscripción" and $rit->tipSociedadContri == "10") selected @endif>10 - Civil</option>
                                            <option value="11" @if($action != "Inscripción" and $rit->tipSociedadContri == "11") selected @endif>11 - Asociativa de Trabajo</option>
                                            <option value="11" @if($action != "Inscripción" and $rit->tipSociedadContri == "12") selected @endif>12 - Responsables de IVA</option>
                                            <option value="11" @if($action != "Inscripción" and $rit->tipSociedadContri == "13") selected @endif>13 - NO Responsables de IVA</option>
                                            <option value="12" @if($action != "Inscripción" and $rit->tipSociedadContri == "14") selected @endif>14 - Otras</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        7. Tipo de Entidad
                                        <select class="form-control" id="tipEntidadContri" name="tipEntidadContri">
                                            @if($action == "Inscripción") <option value="0">Seleccione el código</option> @endif
                                            <option value="20" @if($action != "Inscripción" and $rit->tipEntidadContri == "20") selected @endif>20 - Financiera</option>
                                            <option value="21" @if($action != "Inscripción" and $rit->tipEntidadContri == "21") selected @endif>21 - Oficial</option>
                                            <option value="22" @if($action != "Inscripción" and $rit->tipEntidadContri == "22") selected @endif>22 - Privada</option>
                                            <option value="23" @if($action != "Inscripción" and $rit->tipEntidadContri == "23") selected @endif>23 - Patrimonios Autónomos</option>
                                        </select>
                                    </td>
                                    <td colspan="3">
                                        8. Clase de Entidad
                                        <select class="form-control" id="claEntidadContri" name="claEntidadContri">
                                            @if($action == "Inscripción") <option value="0">Seleccione el código</option> @endif
                                            <option value="30" @if($action != "Inscripción" and $rit->claEntidadContri == "30") selected @endif>30 - BANCOS</option>
                                            <option value="31" @if($action != "Inscripción" and $rit->claEntidadContri == "31") selected @endif>31 - CORPORCION FINANCIERA</option>
                                            <option value="32" @if($action != "Inscripción" and $rit->claEntidadContri == "32") selected @endif>32 - COMPAÑÍA DE SEGUROS</option>
                                            <option value="33" @if($action != "Inscripción" and $rit->claEntidadContri == "33") selected @endif>33 - CIAS DE FINANCIAMIENTO COMERCIAL</option>
                                            <option value="34" @if($action != "Inscripción" and $rit->claEntidadContri == "34") selected @endif>34 - ALMACEN GENERAL DE DEPOSITO</option>
                                            <option value="35" @if($action != "Inscripción" and $rit->claEntidadContri == "35") selected @endif>35 - SOCIEDAD DE CAPITALIZACIÓN</option>
                                            <option value="36" @if($action != "Inscripción" and $rit->claEntidadContri == "36") selected @endif>36 - LEASING</option>
                                            <option value="37" @if($action != "Inscripción" and $rit->claEntidadContri == "37") selected @endif>37 - FIDUCIARIAS</option>
                                            <option value="38" @if($action != "Inscripción" and $rit->claEntidadContri == "38") selected @endif>38 - DEMÁS ENTE DE CRÉDITO Y FINANCIACIÓN</option>
                                            <option value="39" @if($action != "Inscripción" and $rit->claEntidadContri == "39") selected @endif>39 - BANCO DE LA REPÚBLICA</option>
                                            <option value="40" @if($action != "Inscripción" and $rit->claEntidadContri == "40") selected @endif>40 - DEL ORDEN NACIONAL</option>
                                            <option value="41" @if($action != "Inscripción" and $rit->claEntidadContri == "41") selected @endif>41 - DEL ORDEN DEPARTAMENTAL</option>
                                            <option value="42" @if($action != "Inscripción" and $rit->claEntidadContri == "42") selected @endif>42 - DEL ORDEN MUNICIPAL</option>
                                            <option value="43" @if($action != "Inscripción" and $rit->claEntidadContri == "43") selected @endif>43 - COOPERATIVA</option>
                                            <option value="44" @if($action != "Inscripción" and $rit->claEntidadContri == "44") selected @endif>44 - PRECOOPERATIVA</option>
                                            <option value="45" @if($action != "Inscripción" and $rit->claEntidadContri == "45") selected @endif>45 - ASOCIACIÓN MUTUAL</option>
                                            <option value="46" @if($action != "Inscripción" and $rit->claEntidadContri == "46") selected @endif>46 - FONDO DE EMPLEADOS</option>
                                            <option value="47" @if($action != "Inscripción" and $rit->claEntidadContri == "47") selected @endif>47 - MICROEMPRESAS Y FAMIEMPRESAS</option>
                                            <option value="48" @if($action != "Inscripción" and $rit->claEntidadContri == "48") selected @endif>48 - EDUCACIÓN PRIVADA</option>
                                            <option value="49" @if($action != "Inscripción" and $rit->claEntidadContri == "49") selected @endif>49 - RECICLAJE</option>
                                            <option value="50" @if($action != "Inscripción" and $rit->claEntidadContri == "50") selected @endif>50 - SERVICIOS DE SALUD</option>
                                            <option value="51" @if($action != "Inscripción" and $rit->claEntidadContri == "51") selected @endif>51 - ASISTENCIA SOCIAL</option>
                                            <option value="52" @if($action != "Inscripción" and $rit->claEntidadContri == "52") selected @endif>52 - ECOLOGIA Y ROTECCION DEL AMBIENTE</option>
                                            <option value="53" @if($action != "Inscripción" and $rit->claEntidadContri == "53") selected @endif>53 - ATENCIÓN A LOS DAMNIFICADOS</option>
                                            <option value="54" @if($action != "Inscripción" and $rit->claEntidadContri == "54") selected @endif>54 - VOLUNTARIADO SOCIAL DESARROLLO COMUNITARIO</option>
                                            <option value="55" @if($action != "Inscripción" and $rit->claEntidadContri == "55") selected @endif>55 - INVESTIGACIÓN DIVULGACIÓN CIENCIA TECNOLOGÍA</option>
                                            <option value="56" @if($action != "Inscripción" and $rit->claEntidadContri == "56") selected @endif>56 - PROMOCIÓN DEPORTE Y RECREACIÓN POPULAR</option>
                                            <option value="57" @if($action != "Inscripción" and $rit->claEntidadContri == "57") selected @endif>57 - PROMOCIÓN VALORES PARTICIPACIÓN CIUDADANA</option>
                                            <option value="58" @if($action != "Inscripción" and $rit->claEntidadContri == "58") selected @endif>58 - PROMOCIÓN DE MICRO Y FAMIEMPRESAS</option>
                                            <option value="59" @if($action != "Inscripción" and $rit->claEntidadContri == "59") selected @endif>59 - PROMOCIÓN DE ANTIVIDADES CULTURALES</option>
                                            <option value="60" @if($action != "Inscripción" and $rit->claEntidadContri == "60") selected @endif>60 - PROMOCIÓN ENTIDADES SIN ÁNIMO DE LUCRO</option>
                                            <option value="61" @if($action != "Inscripción" and $rit->claEntidadContri == "61") selected @endif>61 - ORGANISMOS DE SOCORRO</option>
                                            <option value="62" @if($action != "Inscripción" and $rit->claEntidadContri == "62") selected @endif>62 - PRIVADA</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        9. Apellidos y Nombres ó Razón Social <input type="text" class="form-control" name="apeynomContri" required
                                                                                     @if($action != "Inscripción") value="{{$rit->apeynomContri}}" @endif >
                                    </td>
                                    <td>
                                        10. Avisos
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="avisos" id="avisos"
                                                   @if($action != "Inscripción" and $rit->avisos == true) checked @endif>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        11. Dirección de Notificación<input type="text" class="form-control" name="dirNotifContri" required
                                                                            @if($action != "Inscripción") value="{{$rit->dirNotifContri}}" @endif>
                                    </td>
                                    <td>
                                        12. Barrio / Vereda<input type="text" class="form-control" name="barrioContri" required
                                                                  @if($action != "Inscripción") value="{{$rit->barrioContri}}" @endif>
                                    </td>
                                    <td>
                                        13. Ciudad<input type="text" class="form-control" name="ciudadContri" required
                                                         @if($action != "Inscripción") value="{{$rit->ciudadContri}}" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        14. Teléfono (*)<input type="number" class="form-control" name="telContri" maxlength="7" required
                                                               @if($action != "Inscripción") value="{{$rit->telContri}}" @endif>
                                    </td>
                                    <td>
                                        15. Sitio web<input type="text" class="form-control" name="webPageContri"
                                                            @if($action != "Inscripción") value="{{$rit->webPageContri}}" @endif>
                                    </td>
                                    <td>
                                        16. Teléfono Móvil (*)<input type="number" class="form-control" name="movilContri" maxlength="10" required
                                                                     @if($action != "Inscripción") value="{{$rit->movilContri}}" @endif>
                                    </td>
                                    <td>
                                        17. Correo Electrónico (*)<input type="email" class="form-control" name="emailContri" required
                                                                         @if($action != "Inscripción") value="{{$rit->emailContri}}" @endif>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA III. REPRESENTACIÓN LEGAL --}}
                            <table id="TABLA3" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="6">III. REPRESENTACIÓN LEGAL </th>
                                </tr>
                                <tr>
                                    <td>
                                        18. Nombres y Apellidos (*)
                                        <input type="text" class="form-control" name="nombreRepLegal"
                                               @if($action != "Inscripción") value="{{$rit->nombreRepLegal}}" @endif>
                                        <br>
                                        <input type="text" class="form-control" name="nombreRepLegal2"
                                               @if($action != "Inscripción") value="{{$rit->nombreRepLegal2}}" @endif>
                                    </td>
                                    <td style="width: 10%">
                                        TD (*)
                                        <select class="form-control" id="TDRepLegal" name="TDRepLegal">
                                            <option value="C.C" @if($action != "Inscripción" and $rit->TDRepLegal == "C.C") selected @endif>C.C</option>
                                            <option value="NIT" @if($action != "Inscripción" and $rit->TDRepLegal == "NIT") selected @endif>NIT</option>
                                            <option value="T.I" @if($action != "Inscripción" and $rit->TDRepLegal == "T.I") selected @endif>T.I</option>
                                            <option value="C.E" @if($action != "Inscripción" and $rit->TDRepLegal == "C.E") selected @endif>C.E</option>
                                        </select>
                                        <br>
                                        <select class="form-control" id="TDRepLegal2" name="TDRepLegal2">
                                            <option value="C.C" @if($action != "Inscripción" and $rit->TDRepLegal2 == "C.C") selected @endif>C.C</option>
                                            <option value="NIT" @if($action != "Inscripción" and $rit->TDRepLegal2 == "NIT") selected @endif>NIT</option>
                                            <option value="T.I" @if($action != "Inscripción" and $rit->TDRepLegal2 == "T.I") selected @endif>T.I</option>
                                            <option value="C.E" @if($action != "Inscripción" and $rit->TDRepLegal2 == "C.E") selected @endif>C.E</option>
                                        </select>
                                    </td>
                                    <td>
                                        19. Identificación número (*)
                                        <input type="text" class="form-control" name="IDNumRepLegal"
                                               @if($action != "Inscripción") value="{{$rit->IDNumRepLegal}}" @endif>
                                        <br>
                                        <input type="text" class="form-control" name="IDNumRepLegal2"
                                               @if($action != "Inscripción") value="{{$rit->IDNumRepLegal2}}" @endif>
                                    </td>
                                    <td>
                                        20. CR (*)
                                        <select class="form-control" id="CRRepLegal" name="CRRepLegal">
                                            @if($action == "Inscripción") <option value="0">Seleccione la calidad</option> @endif
                                            <option value="4" @if($action != "Inscripción" and $rit->CRRepLegal == "4") selected @endif>4 - Padres por sus hijos menores</option>
                                            <option value="5" @if($action != "Inscripción" and $rit->CRRepLegal == "5") selected @endif>5 - Tutores y curadores por los incapaces</option>
                                            <option value="6" @if($action != "Inscripción" and $rit->CRRepLegal == "6") selected @endif>6 - Representnte Legal Titular de persona Jurídica y sociedades de hecho</option>
                                            <option value="7" @if($action != "Inscripción" and $rit->CRRepLegal == "7") selected @endif>7 - Patrimonio Autónomo</option>
                                            <option value="8" @if($action != "Inscripción" and $rit->CRRepLegal == "8") selected @endif>8 - Albaceas</option>
                                            <option value="9" @if($action != "Inscripción" and $rit->CRRepLegal == "9") selected @endif>9 - Donatorios o Asignatorios</option>
                                            <option value="10" @if($action != "Inscripción" and $rit->CRRepLegal == "10") selected @endif>10 - Liquidaciones de sociedades</option>
                                            <option value="11" @if($action != "Inscripción" and $rit->CRRepLegal == "11") selected @endif>11 - Mandatarios o apoderados generales</option>
                                            <option value="12" @if($action != "Inscripción" and $rit->CRRepLegal == "12") selected @endif>12 - Delegado para firma de declaraciones tributarias</option>
                                            <option value="13" @if($action != "Inscripción" and $rit->CRRepLegal == "13") selected @endif>13 - Otro</option>
                                            <option value="14" @if($action != "Inscripción" and $rit->CRRepLegal == "14") selected @endif>14 - Socio Solidario</option>
                                        </select>
                                        <br>
                                        <select class="form-control" id="CRRepLegal2" name="CRRepLegal2">
                                            <option value="0">Seleccione la calidad</option>
                                            <option value="4" @if($action != "Inscripción" and $rit->CRRepLegal2 == "4") selected @endif>4 - Padres por sus hijos menores</option>
                                            <option value="5" @if($action != "Inscripción" and $rit->CRRepLegal2 == "5") selected @endif>5 - Tutores y curadores por los incapaces</option>
                                            <option value="6" @if($action != "Inscripción" and $rit->CRRepLegal2 == "6") selected @endif>6 - Representnte Legal Titular de persona Jurídica y sociedades de hecho</option>
                                            <option value="7" @if($action != "Inscripción" and $rit->CRRepLegal2 == "7") selected @endif>7 - Patrimonio Autónomo</option>
                                            <option value="8" @if($action != "Inscripción" and $rit->CRRepLegal2 == "8") selected @endif>8 - Albaceas</option>
                                            <option value="9" @if($action != "Inscripción" and $rit->CRRepLegal2 == "9") selected @endif>9 - Donatorios o Asignatorios</option>
                                            <option value="10" @if($action != "Inscripción" and $rit->CRRepLegal2 == "10") selected @endif>10 - Liquidaciones de sociedades</option>
                                            <option value="11" @if($action != "Inscripción" and $rit->CRRepLegal2 == "11") selected @endif>11 - Mandatarios o apoderados generales</option>
                                            <option value="12" @if($action != "Inscripción" and $rit->CRRepLegal2 == "12") selected @endif>12 - Delegado para firma de declaraciones tributarias</option>
                                            <option value="13" @if($action != "Inscripción" and $rit->CRRepLegal2 == "13") selected @endif>13 - Otro</option>
                                            <option value="14" @if($action != "Inscripción" and $rit->CRRepLegal2 == "14") selected @endif>14 - Socio Solidario</option>
                                        </select>
                                    </td>
                                    <td>
                                        21. Correo Electrónico (*)
                                        <input type="email" class="form-control" name="emailRepLegal"
                                               @if($action != "Inscripción") value="{{$rit->emailRepLegal}}" @endif>
                                        <br>
                                        <input type="email" class="form-control" name="emailRepLegal2"
                                               @if($action != "Inscripción") value="{{$rit->emailRepLegal2}}" @endif>
                                    </td>
                                    <td>
                                        Telefono Movil o WhatsApp (*)
                                        <input type="number" class="form-control" name="telRepLegal"
                                               @if($action != "Inscripción") value="{{$rit->telRepLegal}}" @endif>
                                        <br>
                                        <input type="number" class="form-control" name="telRepLegal2"
                                               @if($action != "Inscripción") value="{{$rit->telRepLegal2}}" @endif>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA IV. DATOS DE ESTABLECIMIENTOS DE COMERCIO UBICADOS EN PROVIDENCIA --}}
                            <div class="table-responsive">
                                <table id="TABLA4" class="table text-center">
                                    <thead>
                                        <tr style="background-color: #0e7224; color: white">
                                            <th></th>
                                            <th colspan="7">IV. DATOS DE ESTABLECIMIENTOS DE COMERCIO UBICADOS EN PROVIDENCIA</th>
                                            <th></th>
                                        </tr>
                                    <tr>
                                        <th style="vertical-align: middle"><i class="fa fa-plus"></i>/<i class="fa fa-trash"></i></th>
                                        <th style="vertical-align: middle">22. Nombre comercial del establecimiento</th>
                                        <th style="vertical-align: middle">23. Matricula Mercantil</th>
                                        <th style="vertical-align: middle">24. Teléfono</th>
                                        <th style="vertical-align: middle">25. Fecha de inicio de actividades</th>
                                        <th style="vertical-align: middle">26. Dirección del establecimiento</th>
                                        <th style="vertical-align: middle">27. Barrio</th>
                                        <th style="vertical-align: middle">28. Fecha solicitada de cancelación</th>
                                        <th style="vertical-align: middle">Clasificación</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($action != "Inscripción")
                                        @foreach($establecimientos as $establecimiento)
                                            <tr>
                                                <td><button type="button" @click.prevent="eliminarEstablecimiento({{ $establecimiento->id }})" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-trash"></i></button></td>
                                                <td style="vertical-align: middle">{{ $establecimiento->nombre }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->matMercantil }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->telefono }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->fechaInicio }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->direccion }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->barrio }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->fechaCancel }}</td>
                                                <td style="vertical-align: middle">{{ $establecimiento->clasificacion }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td style="vertical-align: middle"></td>
                                            <td style="vertical-align: middle"><input type="text" class="form-control" name="nombre[]" required></td>
                                            <td style="vertical-align: middle"><input type="text" class="form-control" name="matMercantil[]" required></td>
                                            <td style="vertical-align: middle"><input type="number" class="form-control" name="telefono[]" required></td>
                                            <td style="vertical-align: middle"><input type="date" class="form-control" name="fechaInicio[]" required></td>
                                            <td style="vertical-align: middle"><input type="text" class="form-control" name="direccion[]" required></td>
                                            <td style="vertical-align: middle"><input type="text" class="form-control" name="barrio[]" required></td>
                                            <td style="vertical-align: middle"><input type="date" class="form-control" name="fechaCancel[]"></td>
                                            <td style="vertical-align: middle">
                                                <select style="width: 100%" class="form-control" name="clasificacion[]" required>
                                                        <option value="Servicio Comercial">Servicio Comercial</option>
                                                        <option value="Servicio Industrial">Servicio Industrial</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endif

                                    </tbody>
                                </table>
                                <div class="text-center" id="buttonAddEstable">
                                    <button type="button" @click.prevent="nuevaFilaRIT" class="btn btn-sm btn-primary-impuestos">AGREGAR ESTABLECIMIENTO</button>
                                </div>
                            </div>

                            {{-- TABLA V. DATOS DE ACTIVIDADES ECONÓMICAS --}}
                            <div class="table-responsive">
                                <table id="TABLA5" class="table text-center table-bordered">
                                    <thead>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row" colspan="5">V. DATOS DE ACTIVIDADES ECONÓMICAS </th>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align: middle"><i class="fa fa-plus"></i>/<i class="fa fa-trash"></i></th>
                                        <th style="vertical-align: middle">31. Seleccione CIIU</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($action != "Inscripción")
                                        @foreach($actividades as $actividad)
                                            <tr>
                                                <td><button type="button" @click.prevent="eliminarActividad({{ $actividad->id }})" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-trash"></i></button></td>
                                                <td style="vertical-align: middle">{{ $actividad->code }} - {{ $actividad->description }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td style="vertical-align: middle"></td>
                                            <td style="vertical-align: middle">
                                                <select style="width: 100%" class="select-ciuu" name="codCIIU[]" required>
                                                    @foreach($ciius as $ciiu)
                                                        <option value="{{$ciiu->id}}">{{$ciiu->code_ciuu}} - {{$ciiu->description}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div class="text-center" id="buttonAddActividad">
                                    <button type="button" @click.prevent="nuevaFilaActividades" class="btn btn-sm btn-primary-impuestos">AGREGAR ACTIVIDAD</button>
                                </div>
                            </div>

                            {{-- TABLA VI. CANCELACIÓN --}}
                            <table id="TABLA6" style="display: none" class="table text-center">
                                    <tbody>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row" colspan="2">VI. CANCELACIÓN</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            34. Tipo de Cancelación
                                            <br>
                                            <b>Cancelación Total de Contribuyente</b>
                                            <br>
                                            <b>(Queda sin establecimientos activos)</b>
                                        </td>
                                        <td>
                                            35. Motivo de Cancelación
                                            <table class="table text-center">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label" for="traspasoMotCancel">Traspaso (Ventas, Fusión, Herencia)</label>
                                                            <input class="form-check-input" type="radio" name="motivCancelacion" value="Traspaso" id="traspasoMotCancel" checked>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label" for="traspasoMotCancel2">Terminación del negocio</label>
                                                            <input class="form-check-input" type="radio" name="motivCancelacion" value="Terminación" id="traspasoMotCancel2">
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            <table id="TABLA8" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">ANEXOS</th>
                                </tr>
                                <tr>
                                    <td><b>CARGAR RUT</b><br>
                                        @if($action != "Inscripción" and $rit->rutaFileRUT != null)
                                            <a href="{{Storage::url($rit->rutaFileRUT)}}" target="_blank" title="Ver" class="btn btn-success">RUT ALMACENADO</a>
                                            <br>
                                            Si desea cambiar el RUT almacenado seleccione un nuevo archivo.
                                        @endif
                                        <input type="file" class="form-check-input" accept=".pdf" name="fileRUT">
                                    </td>
                                    <td><b>CARGAR CAMARA DE COMERCIO</b><br>
                                        @if($action != "Inscripción" and $rit->rutaFileCC != null)
                                            <a href="{{Storage::url($rit->rutaFileCC)}}" target="_blank" title="Ver" class="btn btn-success">CAMARA DE COMERCIO ALMACENADO</a>
                                            <br>
                                            Si desea cambiar el documento camara de comercio almacenado seleccione un nuevo archivo.
                                        @endif
                                        <input type="file" class="form-check-input" accept=".pdf" name="fileCC">
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA VII. FIRMAS Y FECHA DE RECEPCIÓN --}}
                            <table id="TABLA7" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">VII. FIRMAS Y FECHA DE RECEPCIÓN</th>
                                </tr>
                                <tr>
                                    <td>
                                        37. Fecha de radicación
                                        <br>
                                        <h3>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</h3>
                                        @if($action != "Inscripción")
                                            <input type="hidden" name="rit_id" value="{{ $rit->id }}">
                                            <input type="hidden" name="tipoCancelacion" value="Total">
                                        @endif
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Enviar</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row">ESTE FORMULARIO Y SU RADICACIÓN NO TIENEN COSTO ALGUNO</th>
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
            var natJuridiContri = document.getElementById('natJuridiContri').value;
            if(natJuridiContri == 0) {
                alert('Debe seleccionar la naturaleza juridica');
                return;
            }
            var tipSociedadContri = document.getElementById('tipSociedadContri').value;
            if(tipSociedadContri == 0) {
                alert('Debe seleccionar el tipo de sociedad');
                return;
            }
            var tipEntidadContri = document.getElementById('tipEntidadContri').value;
            if(tipEntidadContri == 0) {
                alert('Debe seleccionar el tipo de entidad');
                return;
            }
            var claEntidadContri = document.getElementById('claEntidadContri').value;
            if(claEntidadContri == 0) {
                alert('Debe seleccionar la clase de entidad');
                return;
            }
            this.submit();
        }

        $(document).ready(function(){

            $('.select-ciuu').select2();
            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });

            $("input[name=opciondeUso]").click(function(event){
                var valor = $(event.target).val();
                if(valor =="Actualización"){
                    $("#TABLA2").show();
                    $("#TABLA3").show();
                    $("#TABLA4").show();
                    $("#TABLA5").show();
                    $("#TABLA6").hide();
                    $("#RevFiscalRow").show();
                    $("#buttonAddEstable").show();
                    $("#buttonAddActividad").show();
                    $("#TABLA8").show();
                } else if (valor == "Cancelación") {
                    document.getElementById("TABLA2").style.display = "none";
                    document.getElementById("TABLA3").style.display = "none";
                    document.getElementById("TABLA4").style.display = "none";
                    document.getElementById("TABLA5").style.display = "none";
                    document.getElementById("TABLA6").style.display = "";
                    document.getElementById("RevFiscalRow").style.display = "none";
                    document.getElementById("buttonAddEstable").style.display = "none";
                    document.getElementById("buttonAddActividad").style.display = "none";
                    document.getElementById("TABLA8").style.display = "none";
                }
            });

            $('#TABLA4').DataTable( {
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

            $('#TABLA5').DataTable( {
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
        });

        let app = new Vue({
            el: '#app',
            methods:{

                eliminarActividad: function(dato){
                    var opcion = confirm("Esta seguro de eliminar la actividad del RIT?");
                    if (opcion == true) {
                        var urlexogena = '/impuestos/RIT/actividad/delete/'+dato;
                        axios.delete(urlexogena).then(response => {
                            location.reload();
                        });
                    }
                },

                eliminarEstablecimiento: function(dato){
                    var opcion = confirm("Esta seguro de eliminar el establecimiento del RIT?");
                    if (opcion == true) {
                        var urlexogena = '/impuestos/RIT/establecimiento/delete/'+dato;
                        axios.delete(urlexogena).then(response => {
                            location.reload();
                        });
                    }
                },

                nuevaFilaRIT(){
                    $('#TABLA4 tbody tr:first').after('<tr>\n' +
                        '<td style="vertical-align: middle"><button type="button" class="btn-primary-impuestos btn-sm borrar">&nbsp;-&nbsp; </button></td>\n'+
                        '<td><input type="text" class="form-control" name="nombre[]" required></td>\n'+
                        '<td><input type="text" class="form-control" name="matMercantil[]"></td>\n'+
                        '<td><input type="number" class="form-control" name="telefono[]" required></td>\n'+
                        '<td><input type="date" class="form-control" name="fechaInicio[]" required></td>\n'+
                        '<td><input type="text" class="form-control" name="direccion[]" required></td>\n'+
                        '<td><input type="text" class="form-control" name="barrio[]" required></td>\n'+
                        '<td><input type="date" class="form-control" name="fechaCancel[]"></td>\n' +
                        '<td style="vertical-align: middle"><select style="width: 100%" class="form-control" name="clasificacion[]"><option value="Servicio Comercial">Servicio Comercial</option><option value="Servicio Industrial">Servicio Industrial</option></select></td>\n' +
                        '</tr>\n');
                },

                nuevaFilaActividades(){
                    $('#TABLA5 tbody tr:first').after('<tr>\n' +
                        '<td style="vertical-align: middle"><button type="button" class="btn-primary-impuestos btn-sm borrar">&nbsp;-&nbsp; </button></td>\n'+
                        '<td style="vertical-align: middle"><select style="width: 100%" class="select-ciuu" name="codCIIU[]" required>@foreach($ciius as $ciiu)<option value="{{$ciiu->id}}">{{$ciiu->code_ciuu}} - {{$ciiu->description}}</option>@endforeach</select></td>\n'+
                        '</tr>\n');

                    $('.select-ciuu').select2();
                }
            }
        });
    </script>
@stop