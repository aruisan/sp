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
                <input type="hidden" name="subTotBancoInicial" id="subTotBancoInicial" value="{{ $totBank }}">
                <input type="hidden" name="subTotBancoFinal" id="subTotBancoFinal" value="{{ $totBank }}">
                <table class="table table-bordered table-hover" id="tabla">
                    <thead>
                    <tr>
                        <th class="text-center" colspan="4">RESUMEN DE LA INFORMACION</th>
                    </tr>
                    <tr>
                        <th class="text-center">Saldo Libros</th>
                        <th class="text-center">$<?php echo number_format($totalLastMonth,0) ?></th>
                        <th class="text-center">Saldo inicial bancos</th>
                        <th class="text-center">
                            <input type="text" class="form-control" value="{{$rubroPUC->saldo_inicial}}">
                        </th>
                    </tr>
                    </thead>
                    <tbody id="bodyTabla">
                        {{--
                            <tr class="text-center">
                                <td>Ingresos</td>
                                <td>${{number_format($totDeb,0) }}</td>
                                <td>Abonos</td>
                                <td>${{number_format($totDeb,0)}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>Egresos</td>
                                <td>${{number_format($totCredAll,0)}}</td>
                                <td>Cargos</td>
                                <td>${{number_format($totCred,0)}}</td>
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
                            --}}
                            <tr class="text-center">
                        <td>Saldo siguiente</td>
                        <td>$<?php echo number_format($rubroPUC->saldo_inicial + $totDeb  - $totCredAll,0) ?></td>
                        <td> Saldo final</td>
                        <td><input type="text" class="form-control" value="{{$totDeb  - $totCred}}" id="valor_final" onkeyup="diferencia_siguiente_final()"></td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="2">Diferencia a Conciliar</td>
                        <td colspan="2" id="td-diferencia"></td>
                    </tr>
                    </tbody>
                </table>

                <div class="text-center">
                    <table class="table table-bordered table-hover" id="tablaBank">
                        <thead>
                        <tr>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">DEBITO</th>
                            <th class="text-center">CREDITO</th>
                            <th class="text-center">VALOR BANCO</th>
                            <th class="text-center">APROBADO</th>
                        </tr>
                        </thead>
                        <tbody id="bodyTabla">
                        @foreach($result as $index => $data)
                            <tr class="text-center">
                                <td>{{$data['fecha']}}</td>
                                <td>{{$data['referencia']}} - {{$data['CC']}} - {{$data['tercero']}}</td>
                                <td>$<?php echo number_format($data['debito'],0) ?></td>
                                <td>$<?php echo number_format($data['credito'],0) ?></td>
                                <td>$<?php echo number_format($data['debito'] - $data['credito'],0) ?></td>
                                <td><input type="checkbox" name="check[]" value="{{ $index }}" checked></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--
                        <button id="buttonMake" type="button" @click.prevent="nuevaFilaBanks" class="btn-sm btn-primary">AGREGAR CAMPO</button>
                        --}}
                </div>
                
                <div class="text-center">
                    <table class="table table-bordered table-hover" id="tablaBank">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="6">CHEQUES COBRADOS</th>
                        </tr>
                        <tr>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">DEBITO</th>
                            <th class="text-center">CREDITO</th>
                            <th class="text-center">VALOR BANCO</th>
                            <th class="text-center">APROBADO</th>
                        </tr>
                        </thead>
                        <tbody id="bodyTabla">
                        @foreach($comprobantes_old as $index => $item)
                            <tr class="text-center">
                                <td>{{$item->fecha}}</td>
                                <td>{{$item->referencia}} - {{$item->cc}} - {{$item->tercero}}</td>
                                <td>$<?php echo number_format(0,0) ?></td>
                                <td>$<?php echo number_format(0,0) ?></td>
                                <td>$<?php echo number_format(0 - $item->valor,0) ?></td>
                                <td><input type="checkbox" name="check_old[]" value="{{$item->id}}" checked></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--
                        <button id="buttonMake" type="button" @click.prevent="nuevaFilaBanks" class="btn-sm btn-primary">AGREGAR CAMPO</button>
                        --}}
                </div>
                <table class="table table-bordered table-hover">
                    <hr>
                    <thead>
                    <tr>
                        <th colspan="4" class="text-center">CUADRO RESUMEN</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="text-center">VALOR LIBROS</th>
                        <th></th>
                        <th class="text-center">VALOR BANCO</th>
                    </tr>
                    </thead>
                    <tbody id="bodyTabla">
                    <tr class="text-center">
                        <td>Saldo siguiente</td>
                        <td>$<?php echo number_format($rubroPUC->saldo_inicial + $totDeb  - $totCredAll,0) ?></td>
                        <td> Saldo final</td>
                        <td id="td_saldo_final"></td>
                    </tr>
                    <tr class="text-center">
                        <td>cheques en mano</td>{{--los deschuleados de deivith--}}
                        <td></td>{{--aqui--}}
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>cheques cobrados</td>{{--los chuleados de oscar y de otros meses--}}
                        <td></td>
                        <td></td>{{--aqui--}}
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>partidas sin conciliar</td>{{--input manual que digita el usuario--}}
                        <td><input type="text" class="form-control"></td>
                        <td></td>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                    <tr class="text-center">
                        <td>SUMAS IGUALES</td>
                        <td>$<?php echo number_format($totDeb - $totCredAll + $totalLastMonth,0) ?></td>{{-- se suma saldo siguiente ms cheques en mano mas el primer input--}}
                        <td></td>
                        <td>
                            <span id="sumaIgualBankSpan">$<?php echo number_format($totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial ,0) ?></span>
                            <input type="hidden" name="sumaIgualBank" id="sumaIgualBank" value="{{ $totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial }}">
                        </td>{{-- se suma saldo final mas cheques cobrados mas el segundo input--}}
                    </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="submit" class="btn-sm btn-primary">ENVIAR CONCILIACIÓN</button>
                    <button type="submit" class="btn-sm btn-primary">ENVIAR Y CIERRA LIBROS</button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script>
        const saldo_siguiente = {{$rubroPUC->saldo_inicial + $totDeb- $totCredAll}};
        
        $(document).ready(function(){
            diferencia_siguiente_final();
        });

        $('#valor_final').on('change', function(){
            diferencia_siguiente_final();
        })

        const diferencia_siguiente_final = () => {
            let final = parseInt($('#valor_final').val());
            $('#td_saldo_final').html(final);
            $('#td-diferencia').html(parseInt(saldo_siguiente)- final);
        }

        $('#tablaBank').DataTable( {
            responsive: true,
            "searching": false,
            dom: 'Bfrtip',
            order: false,
            "pageLength": 100000,
            buttons:[
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
                }
            ]
        } );

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