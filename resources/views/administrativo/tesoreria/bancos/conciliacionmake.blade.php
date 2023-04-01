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
                <input type="hidden" name="finalizar" id="finalizar" value="0">
                <table class="table table-bordered table-hover" id="tabla">
                    <thead>
                    <tr>
                        <th class="text-center" colspan="4">RESUMEN DE LA INFORMACION</th>
                    </tr>
                    <tr>
                        <th class="text-center" colspan="4">Cuenta Bancaria {{$rubroPUC->concepto}}</th>
                    </tr>
                    <tr>
                        <th class="text-center">Saldo Libros</th>
                        <th class="text-center">$<?php echo number_format($totalLastMonth,0) ?></th>
                        <th class="text-center">Saldo inicial bancos</th>
                        <th class="text-center">
                            <input type="number" min="0" class="form-control" required value="{{$rubroPUC->saldo_inicial}}" name="saldo_inicial" id="valor_inicial">
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
                        <td><input type="number" min="0" class="form-control" required value="{{$totDeb  - $totCred}}" id="valor_final" name="saldo_final" onkeyup="diferencia_siguiente_final()"></td>
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
                            @php $cheques_mano = 0 ; @endphp
                        @foreach($result as $index => $data)
                            <tr class="text-center">
                                <td>{{$data['fecha']}}</td>
                                <td>{{$data['referencia']}} - {{$data['CC']}} - {{$data['tercero']}} -- {{$data['numero']}}</td>
                                <td>$<?php echo number_format($data['debito'],0) ?></td>
                                <td>$<?php echo number_format($data['credito'],0) ?></td>
                                <td>$<?php echo number_format($data['debito'] == 0 ? $data['credito'] : $data['debito'],0) ?></td>
                                <td><input type="checkbox" name="check[]" value="{{ $index }}" checked onchange="checked_checke_mano(this, {{$data['debito'] - $data['credito']}})"></td>
                            </tr>
                            @php $cheques_mano = $cheques_mano + ($data['debito'] == 0 ? $data['credito'] : $data['debito']) @endphp
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
                                <td>$<?php echo number_format($item->valor,0) ?></td>
                                <td><input type="checkbox" name="check_old[]" value="{{$item->id}}" checked onchange="checked_checke_cobrados(this, {{$item->valor}})"></td>
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
                        <td id="td-restar-checke-mano"></td>{{--aqui--}}
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="text-center">
                        <td>cheques cobrados</td>{{--los chuleados de oscar y de otros meses--}}
                        <td></td>
                        <td></td>
                        <td id="td-total-checke-cobrados"></td>{{--aqui--}}
                    </tr>
                    <tr class="text-center">
                        <td>partidas sin conciliar</td>{{--input manual que digita el usuario--}}
                        <td><input type="number" min="0" required name="partida_sin_conciliar_libros" class="form-control" id="input-iguales-libros" value="0" onkeyup="suma_iguales_libros()"></td>
                        <td></td>
                        <td><input type="number" min="0" required name="partida_sin_conciliar_bancos" class="form-control" id="input-iguales-bancos" value="0" onkeyup="suma_iguales_bancos()"></td>
                    </tr>
                    <tr class="text-center">
                        <td>SUMAS IGUALES</td>
                        <td id="td-dumas-iguales-libros"></td>{{-- se suma saldo siguiente ms cheques en mano mas el primer input--}}
                        <td></td>
                        <td id="td-dumas-iguales-bancos">
                        </td>{{-- se suma saldo final mas cheques cobrados mas el segundo input--}}
                        <input type="hidden" name="sumaIgualBank" id="sumaIgualBank" value="{{ $totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial }}">
                    </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="button" class="btn-sm btn-primary" onclick="guardar(0)">ENVIAR CONCILIACIÓN</button>
                    <button type="button" class="btn-sm btn-primary" onclick="guardar(1)">ENVIAR Y CIERRA LIBROS</button>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script>
        {{--
            --}}
        const data_mano = @json($result);
        const data_cobrados = @json($comprobantes_old);
        const cheques_mano = {{$cheques_mano}};
        const saldo_siguiente = {{$rubroPUC->saldo_inicial + $totDeb- $totCredAll}};
        const cheques_cobrados = {{$comprobantes_old->sum('valor')}};
        let restar_cheques_mano = 0;
        let restar_cheques_cobrados = 0;
        
        $(document).ready(function(){
            diferencia_siguiente_final();
            suma_iguales_bancos();
            suma_iguales_libros();
            $('#td-restar-checke-mano').html(0)
            $('#td-total-checke-cobrados').html(cheques_cobrados);
            {{----}}
            console.log('cheques_mano',cheques_mano);
            console.log('data_mano',data_mano);
            console.log('cheques_cobrados',cheques_cobrados);
            console.log('data_cobrados',data_cobrados);
        });

        const guardar = finalizar =>{
            let final = parseInt($('#valor_final').val());
            let libros = parseInt($('#input-iguales-libros').val());
            let bancos = parseInt($('#input-iguales-bancos').val());
            let inicio = parseInt($('#valor_inicial').val());
            //alert(final)

            if(isNaN(final)){
                alert("Input Saldo Final debe ser numerico y obligatorio");
            }else if(isNaN(inicio)){
                alert("Input Saldo Inicial debe ser numerico y obligatorio");
            }else if(isNaN(libros)){
                alert("Input de partida sin conciliar libros debe ser numerico y obligatorio");
            }else if(isNaN(bancos)){
                alert("Input de partida sin conciliar bancos debe ser numerico y obligatorio");
            }else{
                $('#finalizar').val(finalizar); 
                $('#prog').submit();
            }
        }

        $('#valor_final').on('change', function(){
            diferencia_siguiente_final();
        })

        const diferencia_siguiente_final = () => {
            let final = parseInt($('#valor_final').val());
            let valor = !isNaN(final) ? final : 0 ;
            $('#td_saldo_final').html(valor);
            $('#td-diferencia').html(parseInt(saldo_siguiente)- valor);
            suma_iguales_bancos();
        }

        const checked_checke_mano = (item, value) => {
            if(item.checked){
                restar_cheques_mano = restar_cheques_mano - value;
            }else{
                restar_cheques_mano = restar_cheques_mano + value;
            }
            $('#td-restar-checke-mano').html(0-restar_cheques_mano);
            suma_iguales_libros();
        }

        const checked_checke_cobrados = (item, value) => {
            if(item.checked){
                restar_cheques_cobrados = restar_cheques_cobrados - value;
            }else{
                restar_cheques_cobrados = restar_cheques_cobrados + value;
            }
            $('#td-total-checke-cobrados').html(cheques_cobrados - restar_cheques_cobrados);
            suma_iguales_bancos();
        }


        const suma_iguales_libros = () => {
            let input_value = parseInt($('#input-iguales-libros').val());
            let total = !isNaN(input_value) ? input_value : 0;
            console.log('rrr', [saldo_siguiente+restar_cheques_mano+total, saldo_siguiente,restar_cheques_mano,total]);
            $('#td-dumas-iguales-libros').html(saldo_siguiente+(0-restar_cheques_mano)+total);
        }

        const suma_iguales_bancos = () => {
            let final = parseInt($('#valor_final').val());
            let input_value = parseInt($('#input-iguales-bancos').val());
            let total = !isNaN(input_value) ? input_value : 0;
            $('#td-dumas-iguales-bancos').html((cheques_cobrados - restar_cheques_cobrados)+final+total);
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