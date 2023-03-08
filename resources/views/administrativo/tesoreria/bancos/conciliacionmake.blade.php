@extends('layouts.dashboard')
@section('titulo')
    Realizar Conciliación Bancaria
@stop
@section('sidebar')@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Realizar Conciliación Bancaria {{ $mesFind }} - {{ $añoActual }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/administrativo/tesoreria/bancos/conciliacion') }}" ><i class="fa fa-arrow-left"></i></a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Realizar Conciliación Bancaria {{ $mesFind }} - {{ $añoActual }}</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white" id="app">
        <div id="tabTareas" class="tab-pane active"><br>
            <form class="form-valide" action="{{url('/administrativo/tesoreria/bancos/conciliacion')}}" method="POST" enctype="multipart/form-data" id="prog">
                {{ csrf_field() }}
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="cuenta" value="{{ $rubroPUC->id }}">
                <input type="hidden" name="mes" value="{{ $mesFind }}">
                <input type="hidden" name="año" value="{{ $añoActual }}">
                <table class="table table-bordered table-hover" id="tabla">
                    <thead>
                    <tr>
                        <th class="text-center" colspan="4">RESUMEN DE LA INFORMACION</th>
                    </tr>
                    <tr>
                        <th class="text-center">Saldo Libros</th>
                        <th class="text-center">$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></th>
                        <th class="text-center">Saldo inicial bancos</th>
                        <th class="text-center">$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></th>
                    </tr>
                    </thead>
                    <tbody id="bodyTabla">
                    <tr class="text-center">
                        <td>Ingresos</td>
                        <td>$<?php echo number_format($totDeb,0) ?></td>
                        <td>Abonos</td>
                        <td>$<?php echo number_format($totDeb,0) ?></td>
                    </tr>
                    <tr class="text-center">
                        <td>Egresos</td>
                        <td>$<?php echo number_format($totCredAll,0) ?></td>
                        <td>Cargos</td>
                        <td>$<?php echo number_format($totCred,0) ?></td>
                    </tr>
                    <tr class="text-center">
                        <td>Comisiones</td>
                        <td></td>
                        <td>Total IVA:</td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Impuestos</td>
                        <td></td>
                        <td>Total Retención:</td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Chequeras</td>
                        <td></td>
                        <td>Total Intereses:</td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Saldo siguiente</td>
                        <td>$<?php echo number_format($rubroPUC->saldo_inicial + $totDeb  - $totCredAll,0) ?></td>
                        <td> Saldo final</td>
                        <td>$<?php echo number_format($totDeb  - $totCred,0) ?></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-hover" id="tablaBank">
                    <hr>
                    <thead>
                        <th class="text-center">FECHA</th>
                        <th class="text-center">REFERENCIA</th>
                        <th class="text-center">DEBITO</th>
                        <th class="text-center">CREDITO</th>
                        <th class="text-center">VALOR BANCO</th>
                        <th class="text-center">ESTADO</th>
                    </thead>
                    <tbody id="bodyTabla">
                    @foreach($result as $data)
                        <tr class="text-center">
                            <td>{{$data['fecha']}}</td>
                            <td>{{$data['referencia']}}</td>
                            <td>$<?php echo number_format($data['debito'],0) ?></td>
                            <td>$<?php echo number_format($data['credito'],0) ?></td>
                            <td>$<?php echo number_format($data['debito'] - $data['credito'],0) ?></td>
                            <td>
                                @if($data['pago_estado'] == 1)
                                    APROBADO
                                @else
                                    NO APROBADO
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="text-center">
                        <td colspan="6"><button id="buttonMake" type="button" @click.prevent="nuevaFilaBanks" class="btn-sm btn-primary">AGREGAR CAMPO</button></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-hover">
                    <hr>
                    <thead>
                    <th></th>
                    <th class="text-center">DEBITO</th>
                    <th class="text-center">CREDITO</th>
                    <th class="text-center">VALOR BANCO</th>
                    </thead>
                    <tbody id="bodyTabla">
                    <tr class="text-center">
                        <td>SUBTOTAL</td>
                        <td>$<?php echo number_format($totDeb,0) ?></td>
                        <td>$<?php echo number_format($totCredAll,0) ?></td>
                        <td>
                            <span id="subTotBancoSpan">$<?php echo number_format($totBank,0) ?></span>
                            <input type="hidden" name="subTotBancoInicial" id="subTotBancoInicial" value="{{ $totBank }}">
                            <input type="hidden" name="subTotBancoFinal" id="subTotBancoFinal" value="{{ $totBank }}">
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>Egresos</td>
                        <td>$<?php echo number_format($totCredAll,0) ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Cobros pendientes</td>
                        <td></td>
                        <td></td>
                        <td>$<?php echo number_format($totCredAll - $totCred,0) ?></td>
                    </tr>
                    <tr class="text-center">
                        <td>Valor sin conciliar</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Abonos en curso</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Cargos en curso</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>Saldo inicial</td>
                        <td>$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></td>
                        <td></td>
                        <td>$<?php echo number_format($rubroPUC->saldo_inicial,0) ?></td>
                    </tr>
                    <tr class="text-center">
                        <td>SUMAS IGUALES</td>
                        <td>$<?php echo number_format($totDeb - $totCredAll + $rubroPUC->saldo_inicial,0) ?></td>
                        <td></td>
                        <td>
                            <span id="sumaIgualBankSpan">$<?php echo number_format($totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial ,0) ?></span>
                            <input type="hidden" name="sumaIgualBank" id="sumaIgualBank" value="{{ $totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial }}">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-hover" id="tablePayPend">
                    <hr>
                    <thead>
                    <tr>
                        <th class="text-center" colspan="5">Relación de pagos pendientes</th>
                    </tr>
                    <tr>
                        <th class="text-center">FECHA</th>
                        <th class="text-center">CED/NIT</th>
                        <th class="text-center">BENEFICIARIO</th>
                        <th class="text-center">DEBITO</th>
                        <th class="text-center">CREDITO</th>
                    </tr>
                    </thead>
                    <tbody id="bodyTabla">
                    @foreach($result as $data)
                        @if($data['pago_estado'] != 1)
                            <tr class="text-center">
                                <td>{{ $data['fecha'] }}</td>
                                <td>{{ $data['CC'] }}</td>
                                <td>{{ $data['tercero'] }}</td>
                                <td>$<?php echo number_format($data['debito'],0) ?></td>
                                <td>$<?php echo number_format($data['credito'],0) ?></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="submit" class="btn-sm btn-primary">ENVIAR CONCILIACIÓN</button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script>

        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
            valores();
        });

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function valores(){
            //const sumaBank = document.getElementById('sumaIgualBank').value;
            var subTotBancoInicial = document.getElementById('subTotBancoInicial').value;
            const cantidadBanco = document.querySelectorAll('input[name="banco[]"]');
            var total = 0;
            cantidadBanco.forEach((elemento) => {
                total = parseInt(total) + parseInt(elemento.value);
            });

            document.getElementById('subTotBancoFinal').value = parseInt(subTotBancoInicial) + parseInt(total);
            document.getElementById('sumaIgualBankSpan').innerHTML = formatter.format(parseInt(subTotBancoInicial) + parseInt(total));
            document.getElementById('subTotBancoSpan').innerHTML = formatter.format(parseInt(subTotBancoInicial) + parseInt(total));
        }


        let app = new Vue({
            el: '#app',
            methods:{

                nuevaFilaBanks(){
                    $('#tablaBank tbody tr:last').after('<tr>\n' +
                        '<td class="text-center" style="vertical-align: middle"><button type="button" class="btn-sm btn-primary borrar">&nbsp;-&nbsp; </button></td>\n'+
                        '<td><input type="text" class="form-control" name="ref[]" required></td>\n'+
                        '<td><input type="hidden" class="form-control" name="deb[]"></td>\n'+
                        '<td><input type="hidden" class="form-control" name="cred[]" required></td>\n'+
                        '<td><input onchange="valores()" type="number" value="0" class="form-control" name="banco[]" required></td>\n'+
                        '<td></td>\n'+
                        '</tr>\n');
                },
            }
        });
    </script>
@stop