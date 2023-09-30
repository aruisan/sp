@extends('layouts.dashboard')
@section('titulo')  REGISTRO DE ATRAQUE DE EMBARCACIONES @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center" translate="no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <form class="form-valide" action="{{url('/administrativo/impuestos/muellaje')}}" method="POST" enctype="multipart/form-data" id="prog">
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
                            @if(count($atraques) >= 1)
                                <div class="text-center">
                                    Seleccione el nombre de la embarcación:
                                    <select  class="form-control" required onchange="ShowSelected(this.value)">
                                        <option>Seleccione Embarcación</option>
                                        @foreach($atraques as $atraque)
                                            <option value="{{ $atraque->id }}">{{ $atraque->name }} - bandera: {{ $atraque->bandera }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>
                            @endif
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="6">DESCRIPCIÓN EMBARCACIÓN</th>
                                </tr>
                                <tr>
                                    <td>Nombre Embarcación</td>
                                    <td colspan="3"><input class="form-control" type="text" name="name" id="name" required></td>
                                    <td>Bandera</td>
                                    <td><select class="form-control" id="bandera" name="bandera" onchange="changeFlag(this.value)">
                                            <option value="NACIONAL">NACIONAL</option>
                                            <option value="INTERNACIONAL">INTERNACIONAL</option>
                                            <option value="EXCLUIDOS FUERZAS MILITARES">EXCLUIDOS FUERZAS MILITARES</option>
                                            <option value="EXCLUIDOS GOBIERNOS EXTRANJEROS">EXCLUIDOS GOBIERNOS EXTRANJEROS</option>
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td>Tipo de Embarcación</td>
                                    <td>
                                        <select class="form-control" id="tipo" name="tipo" onchange="changeTipo(this.value)" required>
                                            <option>Seleccione el tipo de embarcación</option>
                                            <option value="0">Motonave, planchones, barcos de carga, pasajeros y similares.</option>
                                            <option value="1">Pesqueros, yates y similares</option>
                                        </select>
                                    </td>
                                    <td>Pies de Eslora</td>
                                    <td>
                                        <select class="form-control" id="piesEsloraTipo0INT" onchange="changeTarifa(this.value)" name="piesEslora" style="display: none">
                                            <option>Seleccione una opción</option>
                                            <option value="Hasta 37 mts - 130 USD">Hasta 37 mts - 130 USD</option>
                                            <option value="Hasta 38 mts - 50 mts - 223.85 USD">Hasta 38 mts - 50 mts - 223.85 USD</option>
                                            <option value="Hasta 51 mts - 57 mts - 313.3 USD">Hasta 51 mts - 57 mts - 313.3 USD</option>
                                            <option value="Hasta 58 mts - 75 mts - 533 USD">Hasta 58 mts - 75 mts - 533 USD</option>
                                            <option value="Hasta 76 mts - 89 mts - 730.6 USD">Hasta 76 mts - 89 mts - 730.6 USD</option>
                                            <option value="Hasta 90 mts - 101 mts - 930.8 USD">Hasta 90 mts - 101 mts - 930.8 USD</option>
                                            <option value="Hasta 102 mts y más - 1667.9 USD">Hasta 102 mts y más - 1667.9 USD</option>
                                        </select>
                                        <select class="form-control" id="piesEsloraTipo1" onchange="changeTarifa(this.value)" name="piesEslora"  style="display: none">
                                            <option>Seleccione una opción</option>
                                            <option value="Hasta 37 mts - 30.096 USD">Hasta 37 mts - 30.096 USD</option>
                                            <option value="38 mts en adelante - 45.096 USD">38 mts en adelante - 45.096 USD</option>
                                        </select>
                                        <select class="form-control" id="piesEsloraTipo0NAC" onchange="changeTarifa(this.value)" name="piesEslora" style="display: none">
                                            <option>Seleccione una opción</option>
                                            <option value="Hasta 37 mts - 30.096 USD">Hasta 37 mts - 30.096 USD</option>
                                            <option value="Hasta 38 mts - 50 mts - 45.096 USD">Hasta 38 mts - 50 mts - 45.096 USD</option>
                                            <option value="Hasta 51 mts - 57 mts - 54.12 USD">Hasta 51 mts - 57 mts - 54.12 USD</option>
                                            <option value="Hasta 58 mts - 75 mts - 66.144 USD">Hasta 58 mts - 75 mts - 66.144 USD</option>
                                            <option value="Hasta 76 mts - 89 mts - 78.168 USD">Hasta 76 mts - 89 mts - 78.168 USD</option>
                                            <option value="Hasta 90 mts - 101 mts - 90.252 USD">Hasta 90 mts - 101 mts - 90.252 USD</option>
                                            <option value="Hasta 102 mts y más - 180.396 USD">Hasta 102 mts y más - 180.396 USD</option>
                                        </select>
                                        <select class="form-control" id="piesEsloraTipo2" onchange="changeTarifa(this.value)" name="piesEslora"  style="display: none">
                                            <option>Seleccione una opción</option>
                                            <option value="Hasta 37 mts - 130 USD">Hasta 37 mts - 130 USD</option>
                                            <option value="38 mts en adelante - 223.85 USD">38 mts en adelante - 223.85 USD</option>
                                        </select>
                                    </td>
                                    <td>Ruta exclusiva SAI-PVA-SAI</td>
                                    <td><select class="form-control" id="exclusive" name="exclusive" onchange="changeExclusive(this.value)">
                                            <option value="NO">NO</option>
                                            <option value="SI">SI</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tipo de Carga</td>
                                    <td colspan="3"><input class="form-control" type="text" name="tipoCarga" id="tipoCarga" required></td>
                                    <td>Tonelaje Carga</td>
                                    <td><input class="form-control" type="number" min="0" value="0" name="tonelajeCarga" id="tonelajeCarga" required></td>
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
                                    <td>NIT/CC</td>
                                    <td colspan="3"><input class="form-control" type="number" name="numIdent" id="numIdent" required></td>
                                    <td>Fecha permiso</td>
                                    <td><input type="date" class="form-control" name="fechaPermiso" id="fechaPermiso" required></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Titular del permiso</td>
                                    <td colspan="4"><input class="form-control" type="text" name="titularPermiso" id="titularPermiso" required></td>

                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center" id="vehiculosTable">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="5">VEHÍCULOS</th>
                                </tr>
                                <tr>
                                    <td><button type="button" @click.prevent="nuevaFila" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-plus"></i></button></td>
                                    <td>Número de vehículos</td>
                                    <td><input class="form-control" type="number" value="0" min="0" name="vehiculos[]" id="vehiculos"></td>
                                    <td>Clase vehiculo</td>
                                    <td><input class="form-control" type="text" name="claseVehiculo[]" id="claseVehiculo"></td>
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
                                    <th scope="row" colspan="4">DESCRIPCIÓN AGENTE MARÍTIMO</th>
                                </tr>
                                <tr>
                                    <td>Nombre Agente Marítimo</td>
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
                            <table class="table text-center" id="tablePay">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">LIQUIDACION IMPUESTO MUELLAJE</th>
                                </tr>
                                <tr>
                                    <td colspan="2">Nombre responsable del pago</td>
                                    <td colspan="2"><input class="form-control" type="text" name="nameRepPago" id="nameRepPago" required></td>
                                </tr>
                                <tr>
                                    <td>Fecha de Atraque</td>
                                    <td><input class="form-control" type="date" name="fechaAtraque" id="fechaAtraque" required></td>
                                    <td>Fecha de Salida</td>
                                    <td><input class="form-control" type="date" name="fechaSalida" id="fechaSalida" min="{{ Carbon\Carbon::today()}}" required></td>
                                </tr>
                                <tr>
                                    <td>Hora de Ingreso</td>
                                    <td><input class="form-control" type="time" name="horaIngreso" id="horaIngreso" required></td>
                                    <td>Hora de salida</td>
                                    <td><input class="form-control" type="time" name="horaSalida" id="horaSalida" required></td>
                                </tr>
                                <tr>
                                    <td>Días Permanencia</td>
                                    <td><input class="form-control" type="number" value="0" onchange="changeDays(this.value)" name="numTotalDias" id="numTotalDias" min="0" required></td>
                                    <td>Horas Permanencia</td>
                                    <td><input class="form-control" onchange="changeHours(this.value)" type="number" value="0" name="numHoras" id="numHoras" min="0" max="7" required></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Tarifa</td>
                                    <td colspan="2">
                                        <input class="form-control" type="hidden" name="tarifa" id="tarifa" min="0" value="0" step=".001">
                                        <span id="tarifaSpan">0 USD</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Valor del Dolar Diario</td>
                                    <td colspan="2">
                                        <input class="form-control" type="text" name="valorUSD" id="valorUSD" value="{{ $valorUSDToday }}">
                                    </td>
                                </tr>
                                <tr id="descuento" style="display: none">
                                    <td colspan="4">
                                        <div class="col-md-12 align-self-center">
                                            <div class="alert alert-info text-center">
                                                Cuenta con el 25% de descuento en la tarifa plena.
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><h3>Valor a pagar</h3></td>
                                    <td colspan="2">
                                        <input class="form-control" type="hidden" name="valorPago" id="valorPago" min="1" value="0" step=".001" required>
                                        <h3><span id="valorPagoSpan">0 $</span></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Observaciones</td>
                                    <td colspan="3"><textarea class="form-control" type="text" name="observaciones" id="observaciones"></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="4">
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
@section('js')
    <script>

        function changeDays(dias){
            var exclusive = document.getElementById('exclusive').value;
            var tarifa = document.getElementById('tarifa').value;
            var horas = document.getElementById('numHoras').value;
            if(horas <= 2 && horas > 0){
                var div = tarifa / 2;
                var div2 = div / 24;
                var valHours = div2 * horas;

                var valDays = dias * tarifa;

                var totPay = parseFloat(valHours) + parseFloat(valDays);

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    totPay = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(totPay);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(totPay).toFixed(10));

            } else if(horas <= 6 && horas > 2){
                var div = tarifa / 24;
                var valHours = div * horas;

                var valDays = dias * tarifa;

                var totPay = parseFloat(valHours) + parseFloat(valDays);

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    totPay = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(totPay);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(totPay).toFixed(10));

            } else if(horas == 0){

                var valDays = dias * tarifa;

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    valDays = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(valDays);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(valDays).toFixed(10));
            }
        }

        function changeHours(horas){
            var exclusive = document.getElementById('exclusive').value;
            var tarifa = document.getElementById('tarifa').value;
            var dias = document.getElementById('numTotalDias').value;
            if(horas <= 2 && horas > 0){
                var div = tarifa / 2;
                var div2 = div / 24;
                var valHours = div2 * horas;

                var valDays = dias * tarifa;

                var totPay = parseFloat(valHours) + parseFloat(valDays);

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    totPay = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(totPay);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(totPay));

            } else if(horas <= 6 && horas > 2) {
                var div = tarifa / 24;
                var valHours = div * horas;

                var valDays = dias * tarifa;

                var totPay = parseFloat(valHours) + parseFloat(valDays);

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    totPay = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(totPay);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(totPay));
            }else if(horas == 0 ){
                var valDays = dias * tarifa;

                if(exclusive == 'SI') {
                    $('#descuento').show();
                    var multi = parseFloat(tarifa) * 75;
                    valDays = parseFloat(multi) / 100;
                } else $('#descuento').hide();

                document.getElementById('valorPago').value = parseFloat(valDays);
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(parseFloat(valDays));
            }else if(horas > 6){
                toastr.warning('MAYOR A 6 HORAS, INGRESAR COMO DIA COMPLETO');
                document.getElementById('numHoras').value = 0;
                document.getElementById('valorPago').value = 0;
                document.getElementById('valorPagoSpan').innerHTML = formatter.format(0);
                document.getElementById('numTotalDias').value = 0;
            }
        }

        function changeTarifa(tarifa){
            if(tarifa == "Hasta 37 mts - 130 USD"){
                document.getElementById('tarifa').value = 130;
                document.getElementById('tarifaSpan').innerHTML = '130 USD';
            } else if(tarifa == "Hasta 38 mts - 50 mts - 223.85 USD") {
                document.getElementById('tarifa').value = 223.85;
                document.getElementById('tarifaSpan').innerHTML = '223.85 USD';
            } else if(tarifa == "Hasta 51 mts - 57 mts - 313.3 USD"){
                document.getElementById('tarifa').value = 313.3;
                document.getElementById('tarifaSpan').innerHTML = '313.3 USD';
            } else if(tarifa == "Hasta 58 mts - 75 mts - 533 USD"){
                document.getElementById('tarifa').value = 533;
                document.getElementById('tarifaSpan').innerHTML = '533 USD';
            } else if(tarifa == "Hasta 76 mts - 89 mts - 730.6 USD"){
                document.getElementById('tarifa').value = 730.6;
                document.getElementById('tarifaSpan').innerHTML = '730.6 USD';
            } else if(tarifa == "Hasta 90 mts - 101 mts - 930.8 USD"){
                document.getElementById('tarifa').value = 930.8;
                document.getElementById('tarifaSpan').innerHTML = '930.8 USD';
            } else if(tarifa == "Hasta 102 mts y más - 1667.9 USD"){
                document.getElementById('tarifa').value = 1667.9;
                document.getElementById('tarifaSpan').innerHTML = '1667.9 USD';
            } else if(tarifa == "Hasta 37 mts - 130 USD"){
                document.getElementById('tarifa').value = 130;
                document.getElementById('tarifaSpan').innerHTML = '130 USD';
            } else if(tarifa == "38 mts en adelante - 223.85 USD"){
                document.getElementById('tarifa').value = 223.85;
                document.getElementById('tarifaSpan').innerHTML = '223.85 USD';
            } else if(tarifa == "Hasta 51 mts - 57 mts - 313.3 USD"){
                document.getElementById('tarifa').value = 313.3;
                document.getElementById('tarifaSpan').innerHTML = '313.3 USD';
            } else if(tarifa == "Hasta 37 mts - 30.096 USD"){
                document.getElementById('tarifa').value = 30.096;
                document.getElementById('tarifaSpan').innerHTML = '30.096 USD';
            } else if(tarifa == "Hasta 38 mts - 50 mts - 45.096 USD"){
                document.getElementById('tarifa').value = 45.096;
                document.getElementById('tarifaSpan').innerHTML = '45.096 USD';
            } else if(tarifa == "Hasta 51 mts - 57 mts - 54.12 USD"){
                document.getElementById('tarifa').value = 54.12;
                document.getElementById('tarifaSpan').innerHTML = '54.12 USD';
            } else if(tarifa == "Hasta 58 mts - 75 mts - 66.144 USD"){
                document.getElementById('tarifa').value = 66.144;
                document.getElementById('tarifaSpan').innerHTML = '66.144 USD';
            } else if(tarifa == "Hasta 76 mts - 89 mts - 78.168 USD"){
                document.getElementById('tarifa').value = 78.168;
                document.getElementById('tarifaSpan').innerHTML = '78.168 USD';
            } else if(tarifa == "Hasta 90 mts - 101 mts - 90.252 USD"){
                document.getElementById('tarifa').value = 90.252;
                document.getElementById('tarifaSpan').innerHTML = '90.252 USD';
            } else if(tarifa == "Hasta 102 mts y más - 180.396 USD"){
                document.getElementById('tarifa').value = 180.396;
                document.getElementById('tarifaSpan').innerHTML = '180.396 USD';
            }
        }

        function changeTipo(tipo){
            var bandera = document.getElementById('bandera').value;

            if(tipo == 0){
                if(bandera == "NACIONAL") {
                    $('#piesEsloraTipo0NAC').show();
                    $('#piesEsloraTipo1').hide();
                    $('#piesEsloraTipo2').hide();
                    $('#piesEsloraTipo0INT').hide();
                } else if(bandera == "INTERNACIONAL"){
                    $('#piesEsloraTipo0NAC').hide();
                    $('#piesEsloraTipo1').hide();
                    $('#piesEsloraTipo2').hide();
                    $('#piesEsloraTipo0INT').show();
                }
            } else{
                $('#piesEsloraTipo0NAC').hide();
                $('#piesEsloraTipo0INT').hide();
                $('#piesEsloraTipo1').show();
            }
        }

        function changeExclusive(validate){
            if(validate == 'SI'){
                $('#descuento').show();
            } else{
                $('#descuento').hide();
            }
        }

        function changeFlag(flag){
            $('#piesEsloraTipo0NAC').hide();
            $('#piesEsloraTipo0INT').hide();
            $('#piesEsloraTipo1').hide();

            document.getElementById('valorPago').value = 0;
            document.getElementById('valorPagoSpan').innerHTML = '0$';

            document.getElementById('tarifa').value = 0;
            document.getElementById('tarifaSpan').innerHTML = '0$';

            document.getElementById('numTotalDias').value = 0;
            document.getElementById('numHoras').value = 0;

            if(flag == "NACIONAL"){
                $('#tipo').show();
                $('#descuento').hide();
            } else if(flag == "INTERNACIONAL"){
                $('#tipo').show();
                $('#descuento').hide();
            } else{
                $('#tipo').hide();
                $('#descuento').hide();
            }
        }

        function ShowSelected(id) {
            $.ajax({
                method: "POST",
                url: "/administrativo/impuestos/muellaje/"+id+"/find",
                data: { "id": id, "_token": $("meta[name='csrf-token']").attr("content")}
            }).done(function(data) {
                document.getElementById('name').value = data.name;
                document.getElementById('bandera').value = data.bandera;
                document.getElementById('claseVehiculo').value = data.claseVehiculo;
                document.getElementById('dirNotificacion').value = data.dirNotificacion;
                document.getElementById('emailCap').value = data.emailCap;
                document.getElementById('emailNaviera').value = data.emailNaviera;
                document.getElementById('idRep').value = data.idRep;
                document.getElementById('movilCap').value = data.movilCap;
                document.getElementById('movilCompany').value = data.movilCompany;
                document.getElementById('municipio').value = data.municipio;
                document.getElementById('nameCap').value = data.nameCap;
                document.getElementById('nameCompany').value = data.nameCompany;
                document.getElementById('nameNaviera').value = data.nameNaviera;
                document.getElementById('nameRep').value = data.nameRep;
                document.getElementById('nameRepPago').value = data.nameRepPago;
                document.getElementById('numIdent').value = data.numIdent;
                document.getElementById('piesEslora').value = data.piesEslora;
                document.getElementById('sustanciasPeligrosas').value = data.sustanciasPeligrosas;
                document.getElementById('NITNaviera').value = data.NITNaviera;
                document.getElementById('titularPermiso').value = data.titularPermiso;
                document.getElementById('tipoCarga').value = data.tipoCarga;
                document.getElementById('tripulantes').value = data.tripulantes;
                document.getElementById('pasajeros').value = data.pasajeros;
                document.getElementById('vehiculos').value = data.vehiculos;
                document.getElementById('tonelajeCarga').value = data.tonelajeCarga;
                document.getElementById('tipo').value = data.tipo;
            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL BUSCAR LA EMBARCACIÓN PARA EL CORRESPONDIENTE LLENADO AUTOMATICO. LLENE LOS DATOS MANUALMENTE POR FAVOR');
            });

        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })


        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        new Vue({
            el: '#prog',

            methods:{

                eliminarVehiculo: function(dato){
                    var opcion = confirm("Esta seguro de eliminar el vehículo?");
                    if (opcion == true) {
                        var urlexogena = '/administrativo/impuestos/muellaje/vehiculo/delete/'+dato;
                        axios.delete(urlexogena).then(response => {
                            location.reload();
                        });
                    }
                },

                nuevaFila(){
                    $('#vehiculosTable tbody tr:last').after('<tr>\n' +
                        '<td style="vertical-align: middle"><button type="button" class="btn-primary-impuestos btn-sm borrar">&nbsp;-&nbsp; </button></td>\n'+
                        '<td>Número de vehículos</td>\n'+
                        '<td><input class="form-control" type="number" value="0" min="0" name="vehiculos[]" id="vehiculos" required></td>\n'+
                        '<td>Clase vehiculo</td>\n'+
                        '<td><input class="form-control" type="text" name="claseVehiculo[]" id="claseVehiculo" required></td>\n'+
                        '</tr>\n');
                },

            }
        });
    </script>
@stop