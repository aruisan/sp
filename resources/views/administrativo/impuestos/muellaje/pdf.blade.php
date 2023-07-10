@extends('layouts.predial_pdf')
@section('contenido')
    <div class="col-md-12 align-self-center" translate="no">
        <div class="col-lg-12">
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="breadcrumb text-center">
                        <strong>
                            <h4><b>REGISTRO DE ATRAQUE DE EMBARCACIONES</b></h4>
                            <h4><b>MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS</b></h4>
                            <h4><b>SECRETARIA DE GOBIERNO - HACIENDA</b></h4>
                        </strong>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-validation">
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th class="text-center" scope="row" colspan="3">REGISTRO DE INGRESO EMBARCACIÓN Y LIQUIDACION IMPUESTO MUELLAJE</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td colspan="3"><b>Registro de ingreso No. {{ $muellaje->numRegistroIngreso }}</b></td>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td><b>{{ Carbon\Carbon::parse($muellaje->fecha)->Format('d-m-Y')}}</b></td>
                                    <td colspan="2">Funcionario Responsable: {{ $responsable->name }} - {{ $responsable->email }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="6">DESCRIPCIÓN EMBARCACIÓN</th>
                                </tr>
                                <tr>
                                    <td>Nombre Embarcación</td>
                                    <td colspan="3">{{$muellaje->name}}</td>
                                    <td>Bandera</td>
                                    <td>{{$muellaje->bandera}}</td>
                                </tr>
                                <tr>
                                    <td>Tipo de Embarcación</td>
                                    <td colspan="3">{{$muellaje->tipo}}</td>
                                    <td>Pies de Eslora</td>
                                    <td>{{$muellaje->piesEslora}}</td>
                                </tr>
                                <tr>
                                    <td>Tipo de Carga</td>
                                    <td colspan="3">{{$muellaje->tipoCarga}}</td>
                                    <td>Tonelaje Carga</td>
                                    <td>{{$muellaje->tonelajeCarga}}</td>
                                </tr>
                                <tr>
                                    <td>Número de Tripulantes</td>
                                    <td>{{$muellaje->tripulantes}}</td>
                                    <td>Número de pasajeros</td>
                                    <td>{{$muellaje->pasajeros}}</td>
                                    <td>Transp Sustancias peligrosas</td>
                                    <td>@if($muellaje->sustanciasPeligrosas == 0) NO @else SI @endif</td>
                                </tr>
                                <tr>
                                    <td>NIT/CC</td>
                                    <td colspan="3">{{$muellaje->numIdent}}</td>
                                    <td>Fecha permiso</td>
                                    <td>{{ \Carbon\Carbon::parse($muellaje->fechaPermiso)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Titular del permiso</td>
                                    <td colspan="4">{{$muellaje->titularPermiso}}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center" id="vehiculosTable">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">VEHÍCULOS</th>
                                </tr>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row">Número de Vehículos</th>
                                    <th scope="row">Clase de Vehículos</th>
                                </tr>
                                @foreach($muellaje->vehiculosRelation as $vehiculo)
                                    <tr>
                                        <td>{{ $vehiculo->vehiculos }}</td>
                                        <td>{{ $vehiculo->claseVehiculo }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">PROPIETARIO EMBARCACIÓN</th>
                                </tr>
                                <tr>
                                    <td>Nombre Capitan o responsable embarcación</td>
                                    <td>{{$muellaje->nameCap}}</td>
                                    <td>Móvil</td>
                                    <td>{{$muellaje->movilCap}}</td>
                                </tr>
                                <tr>
                                    <td>Compañía o empresa propietario</td>
                                    <td>{{$muellaje->nameCompany}}</td>
                                    <td>Móvil</td>
                                    <td>{{$muellaje->movilCompany}}</td>
                                </tr>
                                <tr>
                                    <td>Correo electrónico</td>
                                    <td colspan="3">{{$muellaje->emailCap}}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">DESCRIPCIÓN NAVIERA</th>
                                </tr>
                                <tr>
                                    <td>Nombre Naviera</td>
                                    <td>{{$muellaje->nameNaviera}}</td>
                                    <td>NIT</td>
                                    <td>{{$muellaje->NITNaviera}}</td>
                                </tr>
                                <tr>
                                    <td>Nombre representante Legal</td>
                                    <td>{{$muellaje->nameRep}}</td>
                                    <td>CC/NIT/CE</td>
                                    <td>{{$muellaje->idRep}}</td>
                                </tr>
                                <tr>
                                    <td>Dirección Notificación</td>
                                    <td>{{$muellaje->dirNotificacion}}</td>
                                    <td>Municipio</td>
                                    <td>{{$muellaje->municipio}}</td>
                                </tr>
                                <tr>
                                    <td>Correo electrónico</td>
                                    <td colspan="3">{{$muellaje->emailNaviera}}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="6">LIQUIDACION IMPUESTO MUELLAJE</th>
                                </tr>
                                <tr>
                                    <td colspan="2">Nombre responsable del pago</td>
                                    <td colspan="4">{{$muellaje->nameRepPago}}</td>
                                </tr>
                                <tr>
                                    <td>Fecha de Atraque</td>
                                    <td>{{ \Carbon\Carbon::parse($muellaje->fechaAtraque)->format('d-m-Y') }}</td>
                                    <td>Fecha de Salida</td>
                                    <td>{{ \Carbon\Carbon::parse($muellaje->fechaSalida)->format('d-m-Y') }}</td>
                                    <td>Tarifa</td>
                                    <td>$<?php echo number_format($muellaje->tarifa,0) ?></td>
                                </tr>
                                <tr>
                                    <td>Hora de Ingreso</td>
                                    <td>{{$muellaje->horaIngreso}}</td>
                                    <td>Hora de salida</td>
                                    <td>{{$muellaje->horaSalida}}</td>
                                    <td>Valor diario</td>
                                    <td>{{$muellaje->valorDiario}}</td>
                                </tr>
                                <tr>
                                    <td>Número total de días</td>
                                    <td>{{$muellaje->numTotalDias}}</td>
                                    <td>Valor a pagar</td>
                                    <td colspan="3">$<?php echo number_format($muellaje->valorPago,0) ?></td>
                                </tr>
                                <tr>
                                    <td>Observaciones</td>
                                    <td colspan="5">{{$muellaje->observaciones}}</td>
                                </tr>
                                </tbody>
                            </table>
                            @if($pago->estado == "Pagado")
                                <table class="table text-center">
                                    <tbody>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row">DETALLE DEL PAGO</th>
                                    </tr>
                                    <tr style="background-color: #bfc3bf; color: black">
                                        <td style="vertical-align: middle">FECHA DE PAGO: {{ \Carbon\Carbon::parse($pago->fechaPago)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr style="background-color: #bfc3bf; color: black">
                                        <td>CONSTANCIA DE PAGO SUBIDA POR: {{$pago->user_pago->name}} - {{$pago->user_pago->email}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop