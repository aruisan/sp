@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
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
                            {{-- TABLA B. BASE GRAVABLE --}}
                            <table class="table text-center table-bordered">
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">INFORMACION PREDIO Y PAGO</th>
                                </tr>
                                <tbody>
                                <tr>
                                    <td style="vertical-align: middle">Número Catastral:</td>
                                    <td>{{$contribuyente->numCatastral}}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Dirección:</td>
                                    <td>{{$contribuyente->dir_predio}}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Cédula Catastral:</td>
                                    <td><input type="number" class="form-control" name="cedula" @if($action == "Corrección" ) value="{{ $ica->totIngreOrd }}" @endif id="cedula" required></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Propietario:</td>
                                    <td>{{$contribuyente->contribuyente}}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Matricula Inmobiliaria:</td>
                                    <td><input type="text" class="form-control" name="matricula" @if($action == "Corrección" ) value="{{ $ica->totIngreOrd }}" @endif id="matricula" required></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Área de Terreno:</td>
                                    <td>{{$contribuyente->areaTerreno}}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Tasa Interés:</td>
                                    <td><input type="text" class="form-control" name="tasaInt" value="25.45" id="tasaInt" required onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Tarifa por mil:</td>
                                    <td>
                                        <span id="tarifaMilSpan">5</span>
                                        <input type="hidden" name="tarifaMil" id="tarifaMil" value="5">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Tasa Bomberil:</td>
                                    <td><input type="text" class="form-control" name="tarifaBomb" value="0" id="tarifaBomb" required onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Fecha de pago:</td>
                                    <td><input type="date" class="form-control" name="fechaPago" id="fechaPago" required onchange="findTasa(this)"></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle">Tasa de Descuento:</td>
                                    <td>
                                        <span id="tasaDescSpan">0</span>
                                        <input type="hidden" name="tasaDesc" id="tasaDesc" value="-1">
                                    </td>
                                </tr>
                                <tr id="añoTR" style="display: none">
                                    <td style="vertical-align: middle">Año de Inicio:</td>
                                    @php($año = date('Y'))
                                    <td>
                                        <select id="año" style="width: 100px" class="form-control" name="año" onchange="listarAños(this.value)">
                                            @while($año >= 2005)
                                                <option value="{{$año}}">{{$año}}</option>
                                                @php($año = ($año-1))
                                            @endwhile
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

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
            //this.submit();
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
                    }
                }
            }
        }

        function findTasa(option){

            $.ajax({
                method: "POST",
                url: "/impuestos/PREDIAL/calendario",
                data: { "date": option.value,
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
            var tarifaXMil = document.getElementById("tarifaMil").value;
            var tot = parseInt(valor) * parseInt(tarifaXMil) / 1000;

            document.getElementById('impPredialSpan'+año).innerHTML = formatter.format(tot);
            document.getElementById('impPredial'+año).value = tot;

            var tasBomb = document.getElementById("tarifaBomb").value;
            var tasaBombTot = tot * parseFloat(tasBomb) / 100

            document.getElementById('tasaBomberilSpan'+año).innerHTML = formatter.format(tasaBombTot);
            document.getElementById('tasaBomberil'+año).value = tasaBombTot;

            document.getElementById('subTotalSpan'+año).innerHTML = formatter.format(tasaBombTot + tot);
            document.getElementById('subTotal'+año).value = tasaBombTot + tot;

            var fechaPago = document.getElementById('fechaPago').value;

            $.ajax({
                method: "POST",
                url: "/impuestos/PREDIAL/liquidar",
                data: { "fecha_pago": fechaPago, "añoVencimiento": año, "subTotal": tasaBombTot + tot ,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                // INTERES MORA
                document.getElementById('interesMoraSpan'+año).innerHTML = formatter.format(datos);
                document.getElementById('interesMora'+año).value = datos;

                //TOTAL POR AÑO
                var totalAño = tasaBombTot + tot + parseInt(datos);
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

            document.getElementById('total2Span').innerHTML = formatter.format(totalInicial);
            document.getElementById('total2').value = parseInt(totalInicial);

            var subTotalLastYear = document.getElementById('subTotal'+hoy.getFullYear()).value
            var tasaDesd = document.getElementById('tasaDesc').value;
            var desc = subTotalLastYear * tasaDesd / 100;

            document.getElementById('descuentoSpan').innerHTML = formatter.format(desc);
            document.getElementById('descuento').value = parseInt(desc);

            document.getElementById('totalPagoSpan').innerHTML = formatter.format(totalInicial - desc);
            document.getElementById('totalPago').value = totalInicial - desc;


        }

        function listarAños(año){
            $("#costeo").show();
            $("#TABLA7").show();
            $("#cuerpo tr").remove();
            const hoy = new Date();
            const numRows = hoy.getFullYear() - parseInt(año) +1;

            for (var i = 0; i < numRows; i++) {
                const year = parseInt(año) + i ;
                document.getElementById("cuerpo").insertRow(-1).innerHTML = '' +
                    '<td style="width: 100px">'+year+'</td>' +
                    '<input type="hidden" name="año'+year+'" id="año'+year+'" value="'+year+'">' +
                    '<td>01-Agosto-'+year+'' +
                    '<input type="hidden" name="fechaVen'+year+'" id="fechaVen'+year+'" value="01-Agosto-'+year+'">' +
                    '</td>' +
                    '<td><input required class="form-control" type="number" name="avaluo'+year+'" id="avaluo'+year+'" onchange="valores(this.value, '+year+')"></td>' +
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
                    '<td><span id="tasaAmbiental'+year+'">$0</span></td>' +
                    '<td><span id="interesAmbiental'+year+'">$0</span></td>' +
                    '<td>' +
                    '<input type="hidden" name="total'+year+'" id="total'+year+'" value="0" required min="0">' +
                    '<span id="totalSpan'+year+'">0</span>' +
                    '</td>';
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
                '<td>TOTAL</td>' +
                '<td>' +
                '<input type="hidden" name="total2" id="total2" value="0">' +
                '<span id="total2Span">0</span>' +
                '</td>';

            document.getElementById("cuerpo").insertRow(-1).innerHTML = '' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td>DESCUENTO</td>' +
                '<td>' +
                '<input type="hidden" name="descuento" id="descuento" value="0">' +
                '<span id="descuentoSpan">0</span>' +
                '</td>';

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