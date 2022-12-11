@extends('layouts.dashboard')
@section('titulo')  REGISTRO DE ATRAQUE DE EMBARCACIONES @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center" translate="no">
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/impuestos/muellaje') }}"><i class="fa fa-arrow-circle-left"></i><i class="fa fa-ship"></i> </a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" ><i class="fa fa-plus"></i><i class="fa fa-ship"></i>NUEVO REGISTRO</a>
                </li>
            </ul>
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
                        <form class="form-valide" action="{{url('/administrativo/impuestos/muellaje')}}" method="POST" enctype="multipart/form-data" id="formulario">
                            {{ csrf_field() }}
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">REGISTRO DE INGRESO EMBARCACIÓN Y LIQUIDACION IMPUESTO MUELLAJE</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td><b>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</b></td>
                                    <td colspan="2">Funcionario Responsable: {{ $responsable }}</td>
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
                                    <td colspan="3"><input class="form-control" type="text" name="name" id="name" required></td>
                                    <td>Bandera</td>
                                    <td><select class="form-control" id="bandera" name="bandera">
                                            <option value="NACIONAL">NACIONAL</option>
                                            <option value="INTERNACIONAL">INTERNACIONAL</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tipo de Embarcación</td>
                                    <td colspan="3">
                                        <select class="form-control" id="tipo" name="tipo">
                                            <option value="0">TIPO 0</option>
                                            <option value="1">TIPO 1</option>
                                        </select>
                                    </td>
                                    <td>Pies de Eslora</td>
                                    <td><input class="form-control" type="number" min="1" value="1" name="piesEslora" id="piesEslora" required></td>
                                </tr>
                                <tr>
                                    <td>Tipo de Carga</td>
                                    <td colspan="3"><input class="form-control" type="text" name="tipoCarga" id="tipoCarga" required></td>
                                    <td>Tonelaje Carga</td>
                                    <td><input class="form-control" type="number" min="1" value="1" name="tonelajeCarga" id="tonelajeCarga" required></td>
                                </tr>
                                <tr>
                                    <td>Número de Tripulantes</td>
                                    <td><input class="form-control" type="number" value="1" min="1" name="tripulantes" id="tripulantes" required></td>
                                    <td>Número de pasajeros</td>
                                    <td><input class="form-control" type="number" value="0" min="0" name="pasajeros" id="pasajeros" required></td>
                                    <td>Transp Sustancias peligrosas</td>
                                    <td><select class="form-control" id="sustanciasPeligrosas" name="sustanciasPeligrosas">
                                            <option value="0">NO</option>
                                            <option value="1">SI</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Número de vehículos</td>
                                    <td><input class="form-control" type="number" value="0" min="0" name="vehiculos" id="vehiculos" required></td>
                                    <td>Clase vehiculo</td>
                                    <td><input class="form-control" type="text" name="claseVehiculo" id="claseVehiculo" required></td>
                                    <td>Fecha permiso</td>
                                    <td><input type="date" class="form-control" name="fechaPermiso" id="fechaPermiso" required></td>
                                </tr>
                                <tr>
                                    <td>Titular del permiso</td>
                                    <td colspan="3"><input class="form-control" type="text" name="titularPermiso" id="titularPermiso" required></td>
                                    <td>NIT/CC</td>
                                    <td><input class="form-control" type="number" name="numIdent" id="numIdent" required></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">PROPIETARIO EMBARCACIÓN</th>
                                </tr>
                                <tr>
                                    <td>Nombre Capitan o responsable embarcación</td>
                                    <td><input class="form-control" type="text" name="nameCap" id="nameCap" required></td>
                                    <td>Móvil</td>
                                    <td><input class="form-control" type="number" name="movilCap" id="movilCap" required></td>
                                </tr>
                                <tr>
                                    <td>Compañía o empresa propietario</td>
                                    <td><input class="form-control" type="text" name="nameCompany" id="nameCompany" required></td>
                                    <td>Móvil</td>
                                    <td><input class="form-control" type="number" name="movilCompany" id="movilCompany" required></td>
                                </tr>
                                <tr>
                                    <td>Correo electrónico</td>
                                    <td colspan="3"><input class="form-control" type="email" name="emailCap" id="emailCap" required></td>
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
                                    <td><input class="form-control" type="text" name="nameNaviera" id="nameNaviera" required></td>
                                    <td>NIT</td>
                                    <td><input class="form-control" type="number" name="NITNaviera" id="NITNaviera" required></td>
                                </tr>
                                <tr>
                                    <td>Nombre representante Legal</td>
                                    <td><input class="form-control" type="text" name="nameRep" id="nameRep" required></td>
                                    <td>CC/NIT/CE</td>
                                    <td><input class="form-control" type="number" name="idRep" id="idRep" required></td>
                                </tr>
                                <tr>
                                    <td>Dirección Notificación</td>
                                    <td><input class="form-control" type="text" name="dirNotificacion" id="dirNotificacion" required></td>
                                    <td>Municipio</td>
                                    <td><input class="form-control" type="text" name="municipio" id="municipio" required></td>
                                </tr>
                                <tr>
                                    <td>Correo electrónico</td>
                                    <td colspan="3"><input class="form-control" type="email" name="emailNaviera" id="emailNaviera" required></td>
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
                                    <td colspan="4"><input class="form-control" type="text" name="nameRepPago" id="nameRepPago" required></td>
                                </tr>
                                <tr>
                                    <td>Fecha de Atraque</td>
                                    <td><input class="form-control" type="date" name="fechaAtraque" id="fechaAtraque" required></td>
                                    <td>Fecha de Salida</td>
                                    <td><input class="form-control" type="date" name="fechaSalida" id="fechaSalida" min="{{ Carbon\Carbon::today()}}" required></td>
                                    <td>Tarifa</td>
                                    <td><input class="form-control" type="number" name="tarifa" id="tarifa" min="0" required></td>
                                </tr>
                                <tr>
                                    <td>Hora de Ingreso</td>
                                    <td><input class="form-control" type="time" name="horaIngreso" id="horaIngreso" required></td>
                                    <td>Hora de salida</td>
                                    <td><input class="form-control" type="time" name="horaSalida" id="horaSalida" required></td>
                                    <td>Valor diario</td>
                                    <td><input class="form-control" type="number" name="valorDiario" id="valorDiario" min="0" value="0" required></td>
                                </tr>
                                <tr>
                                    <td>Número total de días</td>
                                    <td><input class="form-control" type="number" value="1" name="numTotalDias" id="numTotalDias" min="1" required></td>
                                    <td>Valor a pagar</td>
                                    <td colspan="3"><input class="form-control" type="number" name="valorPago" id="valorPago" min="1" value="0" required></td>
                                </tr>
                                <tr>
                                    <td>Observaciones</td>
                                    <td colspan="5"><textarea class="form-control" type="text" name="observaciones" id="observaciones"></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">ENVIAR</button>
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
