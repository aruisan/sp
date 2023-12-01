@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
        <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="col-md-12 align-self-center">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>FORMULARIO IMPUESTO PREDIAL</b></h4>
                        <h4><b>Municipio de Providencia y Santa Catalina</b></h4>
                        <h4><b>Secretaria de Hacienda Municipal</b></h4>
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/impuestos/PREDIAL')}}" method="POST" enctype="multipart/form-data" id="formulario">
                            {{ csrf_field() }}
                            {{-- ENCABEZADO--}}
                            <input type="hidden" name="uvt" id="uvt" value="{{$uvt->valor}}">
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">FORMULARIO UNICO DEL IMPUESTO PREDIAL</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <th scope="row" >MUNICIPIO O DISTRITO </th>
                                    <th scope="row" colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
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
                                    <th scope="row" colspan="4">INFORMACIÓN DEL CONTRIBUYENTE</th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table class="table text-center table-bordered">
                                            <tr>
                                                <td>Nombre y apellidos o razón Social: {{$contribuyente->contribuyente}}</td>
                                                <td>No. {{$contribuyente->numIdent}}</td>
                                                <td>Dirección de Notificación: {{ $contribuyente->dir_notificacion }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Municipio: {{$contribuyente->municipio}} </td>
                                                <td>Departamento: ARCHIPIELAGO DE SAN ANDRES </td>
                                            </tr>
                                            <tr>
                                                <td>Teléfono Móvil: {{ $contribuyente->whatsapp }}</td>
                                                <td colspan="2">Correo electrónico: {{ $contribuyente->email }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            @if(count($predios) > 1)
                                <div class="text-center">
                                    Seleccione el numero catastral del predio a generar el formulario:
                                </div>
                                <select name="predio" id="predio" class="form-control" required onchange="ShowSelected()">
                                    <option>Seleccione el Predio</option>
                                    @foreach($predios as $predio)
                                        <option value="{{ $predio['id'] }}">{{ $predio['numCatastral'] }} - {{ $predio['dir_predio'] }}</option>
                                    @endforeach
                                </select>
                                <hr>
                                <input type="hidden" name="a2018" id="a2018" value="0">
                                <input type="hidden" name="a2019" id="a2019" value="0">
                                <input type="hidden" name="a2020" id="a2020" value="0">
                                <input type="hidden" name="a2021" id="a2021" value="0">
                                <input type="hidden" name="a2022" id="a2022" value="0">
                                <input type="hidden" name="a2023" id="a2023" value="0">
                                <table class="table text-center table-bordered" id="tablaMultiplePred" style="display: none">
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row" colspan="4">INFORMACION PREDIO Y PAGO</th>
                                    </tr>
                                    <tbody>
                                        <tr>
                                            <td>Num Catastral:<br><span id="numCatastral"></span></td>
                                            <td>Area Terreno:<br><span id="areaTerreno"></span></td>
                                            <td colspan="2">Dir Predio:<br><span id="dir_predio"></span></td>
                                        </tr>
                                    <tr>
                                        <td colspan="2">
                                            Matricula Inmobiliaria: <br><span id="matInmobiliaria"></span>
                                        </td>
                                        <td colspan="2">
                                            Cédula Catastral: <br><span id="cedCatastral"></span>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tasa Interés:</td>
                                            <td colspan="3">
                                                <span>25.45</span>
                                                <input type="hidden" class="form-control" name="tasaInt" value="25.45" id="tasaInt" required onchange="operation()">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tarifa por mil:</td>
                                            <td colspan="3">
                                                <span id="tarifaMilSpan">5</span>
                                                <input type="hidden" name="tarifaMil" id="tarifaMil" value="5">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Uso del Predio:</td>
                                            <td colspan="3">
                                                <span id="usoSpan">5</span>
                                                <input type="hidden" name="uso" id="uso" value="5">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Fecha de Pago:</td>
                                            <td colspan="3">
                                                <span id="fechaPagoSpan">0</span>
                                                <input type="hidden" class="form-control" name="tarifaBomb" value="8" id="tarifaBomb" required onchange="operation()">
                                                <input type="hidden" class="form-control" name="fechaPago" id="fechaPago" required onchange="findTasa(this.value)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tasa de Descuento:</td>
                                            <td colspan="3">
                                                <span id="tasaDescSpan">0</span>
                                                <input type="hidden" name="tasaDesc" id="tasaDesc" value="-1">
                                            </td>
                                        </tr>
                                        <tr id="ultimoAñoPagado" style="display: none">
                                            <td style="vertical-align: middle">Ultimo Año Pagado:</td>
                                            <td colspan="3">
                                                <span id="añoInicioSpan">0</span>
                                                <input type="hidden" name="añoInicio" id="añoInicio" value="-1">
                                            </td>
                                        </tr>
                                        <tr id="message">
                                            <td colspan="3">
                                                <div class="alert alert-danger">
                                                    POR DEFECTO SE SELECCIONA EL AÑO INICIAL PARA PAGO, EN EL CASO QUE SE
                                                    DEBE ÚNICAMENTE EL 2023, SE DEBE SELECCIONAR EN LA OPCIÓN DE AÑO INICIAL
                                                    PARA PAGO EL AÑO 2023 O DESDE EL AÑO QUE SE DEBE.
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="añoTR" class="text-center" style="display: none; vertical-align: middle">
                                            <td style="vertical-align: middle">Año Inicial para Pago:</td>
                                            @php($año = date('Y'))
                                            @php($año = $año - 1 - $contribuyente->años_deuda)
                                            @php($año2 = date('Y'))
                                            <td class="text-center" colspan="3">
                                                <select id="año" class="form-control text-center" name="año" onchange="listarAños(this.value)">
                                                    @while($año2 >= 2018)
                                                        <option value="{{$año2}}" @if($año + 1 == $año2) selected @endif>{{$año2}}</option>
                                                        @php($año2 = ($año2-1))
                                                    @endwhile
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                {{-- TABLA B. BASE GRAVABLE --}}
                                <input type="hidden" name="predio" id="predio" value="{{$contribuyente->id}}">
                                <input type="hidden" name="a2018" id="a2018" value="{{$contribuyente->a2018}}">
                                <input type="hidden" name="a2019" id="a2019" value="{{$contribuyente->a2019}}">
                                <input type="hidden" name="a2020" id="a2020" value="{{$contribuyente->a2020}}">
                                <input type="hidden" name="a2021" id="a2021" value="{{$contribuyente->a2021}}">
                                <input type="hidden" name="a2022" id="a2022" value="{{$contribuyente->a2022}}">
                                <input type="hidden" name="a2023" id="a2023" value="{{$contribuyente->a2023}}">
                                <table class="table text-center table-bordered">
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row" colspan="2">INFORMACION PREDIO Y PAGO</th>
                                    </tr>
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align: middle">Número Catastral:</td>
                                            <td>
                                                <span id="numCatastral"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Dirección:</td>
                                            <td><span id="dir_predio"></span></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Cédula Catastral:</td>
                                            <td><span id="cedCatastral"></span></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Matricula Inmobiliaria:</td>
                                            <td><span id="matInmobiliaria"></span></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Área de Terreno:</td>
                                            <td><span id="areaTerreno"></span></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tasa Interés:</td>
                                            <td>
                                                <span>25.45</span>
                                                <input type="hidden" class="form-control" name="tasaInt" value="25.45" id="tasaInt" required onchange="operation()">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tarifa por mil:</td>
                                            <td>
                                                <span id="tarifaMilSpan">5</span>
                                                <input type="hidden" name="tarifaMil" id="tarifaMil" value="5">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Uso del Predio:</td>
                                            <td>
                                                <span id="usoSpan">5</span>
                                                <input type="hidden" name="uso" id="uso" value="5">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Fecha de Pago:</td>
                                            <td>
                                                <span id="fechaPagoSpan">0</span>
                                                <input type="hidden" class="form-control" name="tarifaBomb" value="8" id="tarifaBomb" required onchange="operation()">
                                                <input type="hidden" class="form-control" name="fechaPago" id="fechaPago" required onchange="findTasa(this.value)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">Tasa de Descuento:</td>
                                            <td>
                                                <span id="tasaDescSpan">0</span>
                                                <input type="hidden" name="tasaDesc" id="tasaDesc" value="-1">
                                            </td>
                                        </tr>
                                        <tr id="ultimoAñoPagado" style="display: none">
                                            <td style="vertical-align: middle">Ultimo Año Pagado:</td>
                                            <td>
                                                <span id="añoInicioSpan">0</span>
                                                <input type="hidden" name="añoInicio" id="añoInicio" value="-1">
                                            </td>
                                        </tr>
                                        <tr id="message">
                                            <td colspan="2">
                                                <div class="alert alert-danger">
                                                    POR DEFECTO SE SELECCIONA EL AÑO INICIAL PARA PAGO, EN EL CASO QUE SE
                                                    DEBE ÚNICAMENTE EL 2023, SE DEBE SELECCIONAR EN LA OPCIÓN DE AÑO INICIAL
                                                    PARA PAGO EL AÑO 2023 O DESDE EL AÑO QUE SE DEBE.
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="añoTR" style="display: none">
                                            <td style="vertical-align: middle">Año Inicial para Pago:</td>
                                            @php($año = date('Y'))
                                            @php($año = $año - 1 - $contribuyente->años_deuda)
                                            @php($año2 = date('Y'))
                                            <td class="text-center" colspan="3">
                                                <select id="año" class="form-control text-center" name="año" onchange="listarAños(this.value)">
                                                    @while($año2 >= 2018)
                                                        <option value="{{$año2}}" @if($año + 1 == $año2) selected @endif>{{$año2}}</option>
                                                        @php($año2 = ($año2-1))
                                                    @endwhile
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif

                            {{-- TABLA C. DISCRIMINACIÓN DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO --}}
                            <div class="table-responsive">
                                <table style="display: none" id="costeo" class="table text-center table-bordered">
                                    <thead>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th>Años</th>
                                        <th>Fecha de vencimiento</th>
                                        <th>Avalúos</th>
                                        <th>Imp Predial</th>
                                        <th>Tasa Bomberil</th>
                                        <th>Sub_total</th>
                                        <th>Interes_mora</th>
                                        <th>Tasa_ambiental</th>
                                        <th>Interes_ambiental</th>
                                        <th>TOTAL</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cuerpo">
                                    </tbody>
                                </table>
                            </div>

                            {{-- TABLA E. FIRMAS --}}
                            <table id="TABLA7" style="display: none" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">Pagar</th>
                                </tr>
                                <tr>
                                    <td>
                                        Fecha de envio
                                        <br>
                                        <h3>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</h3>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Pagar</button>
                                    </td>
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

        var predios = @json(count($predios))

        window.onload = function() {
            if(predios == 1) ShowSelected();
        };

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("formulario").addEventListener('submit', validarFormulario);
        });

        function validarFormulario(evento) {
            evento.preventDefault();
            //AJUSTAR VALIDACION FORMULARIO
            var totPago = document.getElementById('totalPago').value;
            if(totPago < 0) {
                alert('El valor a pagar no puede ser menor a 0');
                return;
            }
            this.submit();
        }

        function ShowSelected(){
            var idPred = document.getElementById('predio').value;
            $.ajax({
                method: "POST",
                url: "/impuestos/PREDIAL/predio",
                data: { "id": idPred,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                document.getElementById('numCatastral').innerHTML = datos.numCatastral;
                document.getElementById('areaTerreno').innerHTML = datos.area;
                document.getElementById('dir_predio').innerHTML = datos.dir_predio;
                document.getElementById('matInmobiliaria').innerHTML = datos.matInmobiliaria;
                document.getElementById('cedCatastral').innerHTML = datos.cedCatastral;
                document.getElementById('a2018').value = datos.a2018;
                document.getElementById('a2019').value = datos.a2019;
                document.getElementById('a2020').value = datos.a2020;
                document.getElementById('a2021').value = datos.a2021;
                document.getElementById('a2022').value = datos.a2022;
                document.getElementById('a2023').value = datos.a2023;
                document.getElementById('fechaPago').value = datos.hoy;
                document.getElementById('fechaPagoSpan').innerHTML = datos.hoy;
                document.getElementById('año').value = datos.deudaYear;
                var uvtAño = document.getElementById('uvt').value;

                var yearStart = parseInt(datos.deudaYear) - 1;

                document.getElementById('añoInicioSpan').innerHTML = yearStart;
                document.getElementById('añoInicio').value = yearStart;

                //CALCULO DEL UVT DEL AVALUO
                var uvtPred = parseInt(datos.a2023) / parseInt(uvtAño);

                $.ajax({
                    method: "POST",
                    url: "/impuestos/PREDIAL/uvt",
                    data: { "_token": $("meta[name='csrf-token']").attr("content") }
                }).done(function(datos) {
                    //const predUVT = datos;
                    for (var i = 0; i < datos.length; i++) {
                        if(datos[i]['condicion'] != null){
                            if (datos[i]['uso'] == 1){
                                if(uvtPred <= datos[i]['condicion']){
                                    var uso = datos[i]['concepto'];
                                    var tarifaxMil = datos[i]['tarifa'];
                                    break;
                                }
                            } else if (datos[i]['uso'] == 2){
                                if(uvtPred <= datos[i]['condicion'] & uvtPred >= datos[i-1]['condicion']){
                                    var uso = datos[i]['concepto'];
                                    var tarifaxMil = datos[i]['tarifa'];
                                    break;
                                }
                            }else if(datos[i]['uso'] == 3){
                                if( uvtPred >= datos[i]['condicion']){
                                    var uso = datos[i]['concepto'];
                                    var tarifaxMil = datos[i]['tarifa'];
                                    break;
                                }
                            }
                        }
                    }

                    //SE COLOCA EL USO DEL PREDIO
                    document.getElementById('usoSpan').innerHTML = uso;
                    document.getElementById('uso').value = uso;

                    //SE ACTUALIZA LA TARIFA POR MIL
                    document.getElementById('tarifaMilSpan').innerHTML = tarifaxMil;
                    document.getElementById('tarifaMil').value = tarifaxMil;

                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL OBTENER LOS VALORES DEL UVT');
                });

                findTasa(datos.hoy);
                $("#tablaMultiplePred").show();

                operation();
                listarAños(datos.deudaYear);

            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL OBTENER EL PREDIO');
            });
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function operation(){
            var tasaInt = document.getElementById("tasaInt").value;
            var tarifaBomb = document.getElementById("tarifaBomb").value;
            var tasaDesc = document.getElementById("tasaDesc").value;

            if(parseInt(tasaInt) != 0){
                if(parseInt(tarifaBomb) != 0){
                    if(parseInt(tasaDesc) != -1){
                        $("#añoTR").show();
                        $("#ultimoAñoPagado").show();
                        //listarAños(document.getElementById("año").value);
                    }
                }
            }
        }

        function findTasa(option){

            $.ajax({
                method: "POST",
                url: "/impuestos/PREDIAL/calendario",
                data: { "date": option,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                document.getElementById('tasaDescSpan').innerHTML = datos+'%';
                document.getElementById('tasaDesc').value = datos;

                operation();
            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL OBTENER LA TASA DE DESCUENTO DEPENDIENDO LA FECHA');
            });
        }

        function valores(valor, año){

            var today = new Date();
            var year = today.getFullYear();

            var tarifaXMil = document.getElementById("tarifaMil").value;
            var tot = parseInt(valor) * parseInt(tarifaXMil) / 1000;

            document.getElementById('impPredialSpan'+año).innerHTML = formatter.format(tot);
            document.getElementById('impPredial'+año).value = tot;

            var tasBomb = document.getElementById("tarifaBomb").value;

            //SE APLICA EL DESCUENTO DE TASA BOMBERIL DE DEFECTO PARA TODOS LOS AÑOS ANTERIORES AL PAGO
            if(year != año) var tasaBombTot = tot * parseFloat(15) / 100
            else var tasaBombTot = tot * parseFloat(tasBomb) / 100

            document.getElementById('tasaBomberilSpan'+año).innerHTML = formatter.format(tasaBombTot);
            document.getElementById('tasaBomberil'+año).value = tasaBombTot;

            //DESCUENTO
            var descuento = false;
            if(year != año){
                var subTot =  tasaBombTot + tot;
            } else {
                if(descuento){
                    var suma = tasaBombTot + tot;
                    var subTot =  suma / 2;
                } else var subTot =  tasaBombTot + tot;
            }

            document.getElementById('subTotalSpan'+año).innerHTML = formatter.format(subTot);
            document.getElementById('subTotal'+año).value = subTot;

            var fechaPago = document.getElementById('fechaPago').value;

            $.ajax({
                method: "POST",
                url: "/impuestos/PREDIAL/liquidar",
                data: { "fecha_pago": fechaPago, "añoVencimiento": año, "subTotal": subTot ,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                // INTERES MORA
                document.getElementById('interesMoraSpan'+año).innerHTML = formatter.format(datos);
                document.getElementById('interesMora'+año).value = datos;

                //NUEVOS DESCUENTOS DE INTERESES DE MORA DE LA ALCALDIA
                if(año <= 2019){
                    var calenDescInt = 0.3;
                } else if(año == 2020){
                    var calenDescInt = 1;
                } else if(año == 2021){
                    var calenDescInt = 1;
                } else if(año == 2022){
                    var calenDescInt = 0.5;
                } else{
                    var calenDescInt = 0;
                }

                //DESCUENTOS DE INTERESES
                //var descIntereses = datos * calenDescInt;
                //SE PASA EL DESCUENTO A 0
                var descIntereses = datos * 0;

                //TASA AMBIENTAL
                var tasaAmb = tot * 0.01;
                console.log(tot, tasaAmb);

                document.getElementById('tasaAmbientalSpan'+año).innerHTML = formatter.format(tasaAmb);
                document.getElementById('tasaAmbiental'+año).value = parseInt(tasaAmb);

                //TOTAL POR AÑO
                var totalAño = subTot + parseInt(datos) - parseInt(descIntereses) + parseInt(tasaAmb) ;
                document.getElementById('totalSpan'+año).innerHTML = formatter.format(totalAño);
                document.getElementById('total'+año).value = parseInt(totalAño);

                totales();
            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL OBTENER LA TASA DE DESCUENTO DEPENDIENDO LA FECHA');
            });
        }

        function totales(){
            var año = document.getElementById("año").value;
            const hoy = new Date();
            const numRows = hoy.getFullYear() - parseInt(año) +1;

            let totales = [0];

            for (var i = 0; i < numRows; i++) {
                const year = parseInt(año) + i;
                var totalAño = document.getElementById('total'+year).value;
                totales.push(parseFloat(totalAño));
            }

            let totalInicial = 0;
            totales.forEach(function(a){totalInicial += a;});

            document.getElementById('totalPagoSpan').innerHTML = formatter.format(totalInicial);
            document.getElementById('totalPago').value = parseInt(totalInicial);

            //var subTotalLastYear = document.getElementById('subTotal'+hoy.getFullYear()).value
            //var tasaDesd = document.getElementById('tasaDesc').value;
            //var desc = subTotalLastYear * tasaDesd / 100;

            //document.getElementById('descuentoSpan').innerHTML = formatter.format(desc);
            //document.getElementById('descuento').value = parseInt(desc);

            //document.getElementById('totalPagoSpan').innerHTML = formatter.format(totalInicial - desc);
            //document.getElementById('totalPago').value = totalInicial - desc;


        }

        function listarAños(año){
            $("#costeo").show();
            $("#TABLA7").show();
            $("#cuerpo tr").remove();
            const hoy = new Date();
            //CAMBIO AÑO
            const numRows = hoy.getFullYear() - parseInt(año) +1;

            for (var i = 0; i < numRows; i++) {
                const year = parseInt(año) + i ;
                const avaluo = document.getElementById('a'+year).value;
                document.getElementById("cuerpo").insertRow(-1).innerHTML = '' +
                    '<td style="width: 100px">'+year+'</td>' +
                    '<input type="hidden" name="año'+year+'" id="año'+year+'" value="'+year+'">' +
                    '<td>01-Agosto-'+year+'' +
                    '<input type="hidden" name="fechaVen'+year+'" id="fechaVen'+year+'" value="01-Agosto-'+year+'">' +
                    '</td>' +
                    '<td>'+formatter.format(avaluo)+'</td>' +
                    '<td>' +
                    '<span id="impPredialSpan'+year+'">0</span>' +
                    '<input type="hidden" name="impPredial'+year+'" id="impPredial'+year+'" value="0">' +
                    '</td>' +
                    '<td>' +
                    '<span id="tasaBomberilSpan'+year+'">0</span>' +
                    '<input type="hidden" name="tasaBomberil'+year+'" id="tasaBomberil'+year+'" value="0">' +
                    '</td>' +
                    '<td>' +
                    '<span id="subTotalSpan'+year+'">0</span>' +
                    '<input type="hidden" name="subTotal'+year+'" id="subTotal'+year+'" value="0">' +
                    '</td>' +
                    '<td>' +
                    '<input type="hidden" name="interesMora'+year+'" id="interesMora'+year+'" value="0">' +
                    '<span id="interesMoraSpan'+year+'">0</span>' +
                    '</td>' +
                    '<td>' +
                    '<input type="hidden" name="tasaAmbiental'+year+'" id="tasaAmbiental'+year+'" value="0">' +
                    '<span id="tasaAmbientalSpan'+year+'">$0</span>' +
                    '</td>' +
                    '<td><span id="interesAmbiental'+year+'">$0</span></td>' +
                    '<td>' +
                    '<input type="hidden" name="total'+year+'" id="total'+year+'" value="0" required min="0">' +
                    '<span id="totalSpan'+year+'">0</span>' +
                    '</td>';

                valores(parseInt(avaluo), year);
            }

            document.getElementById("cuerpo").insertRow(-1).innerHTML = '' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td>TOTAL PAGO</td>' +
                '<td>' +
                '<input type="hidden" name="totalPago" id="totalPago" value="0" required min="0">' +
                '<span id="totalPagoSpan">0</span>' +
                '</td>';
        }

        $('#costeo').DataTable( {
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
    </script>
@stop