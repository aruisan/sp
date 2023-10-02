@extends('layouts.dashboard')
@section('titulo')
    Realizar Conciliación Bancaria
@stop
@section('sidebar')@stop
@section('content')
@php $cheques_mano = 0 ; @endphp
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
    <div class="row" style="background-color: white">
        <br>
        <form class="form-valide" action="{{route('conciliacion.store', $conciliacion->id)}}" method="POST" enctype="multipart/form-data" id="prog">
            {{ csrf_field() }}
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <input type="hidden" name="cuenta" id="cuenta" value="{{ $rubroPUC->id }}">
            <input type="hidden" name="mes" id="mes" value="{{ $mesFind }}">
            <input type="hidden" name="año" id="año" value="{{ $añoActual }}">
            <input type="hidden" name="subTotBancoInicial" id="subTotBancoInicial" value="{{ $totBank }}">
            <input type="hidden" name="subTotBancoFinal" id="subTotBancoFinal" value="{{ $totBank }}">
            <input type="hidden" id="valor_final" name="saldo_final" value="{{$cheques_mano}}">
            <input type="hidden" name="finalizar" id="finalizar" value="0">
            <input type="hidden" name="data_cobro_select" id="input_data_cobro_select">
            <input type="hidden" name="data_cobro_no_select" id="input_data_cobro_no_select">
            @foreach([1] as $k => $v)
            <table class="table table-bordered table-hover {{$k ? 'my-fixed-item' : ''}}" id="tabla" >
                <thead>
                <tr>
                    <th class="text-center" colspan="4">RESUMEN DE LA INFORMACION</th>
                </tr>
                <tr>
                    <th class="text-center" colspan="4">Periodo ({{$periodo_inicial}} - {{$periodo_final}}) {{$rubroPUC->code}} -- Cuenta Bancaria {{$rubroPUC->concepto}}</th>
                </tr>
                <tr>
                    <th class="text-center">Saldo Libros</th>
                    <th class="text-center" id="td-s-libros-i"></th>
                    <th class="text-center">Saldo inicial bancos</th>
                    <th class="text-center">
                        @if(is_null($conciliacion_anterior))
                            <input type="number" min="0" class="form-control" required value="{{$conciliacion->saldo_inicial}}" name="saldo_inicial" id="valor_inicial" onkeyup="diferencia_siguiente_final()">
                        @else
                            <input type="hidden" value="{{$conciliacion->saldo_inicial}}" name="saldo_inicial" id="valor_inicial" onkeyup="diferencia_siguiente_final()">
                            {{$conciliacion->saldo_inicial}}
                        @endif
                    </th>
                </tr>
                </thead>
                <tbody id="bodyTabla">    
                <tr class="text-center">
                    <td>Saldo siguiente</td>
                    <td id="td-s-siguiente-i"></td>
                    <td> Saldo final</td>
                    <td id="td-saldo-final">
                    </td>
                </tr>
                <tr class="text-center">
                    <td colspan="2">Diferencia a Conciliar</td>
                    <td colspan="2" id="td-diferencia"></td>
                </tr>
                </tbody>
            </table>
            @endforeach

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
                    @foreach($conciliacion->cheques_mano as $index => $data)
                        <tr class="text-center">
                            <td>{{$data->fecha}}</td>
                            <td>{{$data->referencia}} - {{$data->CC}} - {{$data->tercero}} -- {{$data->numero}}</td>
                            <td>$<?php echo number_format($data->debito,0) ?></td>
                            <td>$<?php echo number_format($data->credito,0) ?></td>
                            <td>$<?php echo number_format($data->debito == 0 ? $data->credito : $data->debito,0) ?></td>
                            <td><input type="checkbox" name="check[]" value="{{ $data->id }}" 
                                onchange="checked_checke_mano(this, {{$data['debito'] == 0 ? 0 - $data['credito'] : $data['debito']}}, {{$index}}, {{$data->id}})" 
                                {{$data->aprobado == 'ON' ? 'checked' : ''}}>
                            </td>
                        </tr>
                        @php $cheques_mano = $cheques_mano + ($data['debito'] == 0 ? 0 - $data['credito'] : $data['debito']) @endphp
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <table class="table table-bordered table-hover" id="tablaOld">
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
                    @foreach($conciliacion->cuentas_temporales as $index => $item)
                        <tr class="text-center">
                            <td>{{$item->comprobante_ingreso_temporal->fecha}}</td>
                            <td>{{$item->comprobante_ingreso_temporal->referencia}} - {{$item->comprobante_ingreso_temporal->cc}} - {{$item->comprobante_ingreso_temporal->tercero}}</td>
                            <td>$<?php echo number_format(0,0) ?></td>
                            <td>$<?php echo number_format(0,0) ?></td>
                            <td>$<?php echo number_format($item->comprobante_ingreso_temporal->valor,0) ?></td>
                            <td><input type="checkbox" name="check_old[]" value="{{$item->comprobante_ingreso_temporal->id}}" 
                                    onchange="checked_checke_cobrados(this, {{$item->comprobante_ingreso_temporal->valor}}, {{$item->comprobante_ingreso_temporal->id}}, {{$item->id}})" 
                                    {{$item->check ? 'checked' : ''}} >
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <table class="table table-bordered table-hover">
                <hr>
                <thead>
                <tr>
                    <th colspan="4" class="text-center">CUADRO RESUMEN</th>
                </tr>
                </thead>
                <tbody id="bodyTabla">
                <tr class="text-center">
                    <td>Saldo siguiente</td>
                    <td id="td-s-siguiente-f"></td>
                    <td> Saldo Final</td>
                    <td id="td-s-inicial-f"></td>
                </tr>
                <tr class="text-center">
                    <td>Diferencia</td>
                    <td id="td-s-diferencia"></td>
                    <td></td>
                    <td></td>
                </tr>
               
                <tr class="text-center">
                    <td>SUMAS IGUALES</td>
                    <td id="td-sumas-iguales-libros"></td>
                    <td></td>
                    <td id="td-sumas-iguales-bancos">
                    </td>
                    <input type="hidden" name="sumaIgualBank" id="sumaIgualBank" value="{{ $totDeb - $totCred + $totCredAll - $totCred + $rubroPUC->saldo_inicial }}">
                </tr>
                </tbody>
            </table>
            <div class="text-center">
            {{--
                <button type="button" class="btn-sm btn-primary" onclick="guardar_ver(0)">Ver Pdf</button>
            --}}
                <button type="button" class="btn-sm btn-primary" onclick="guardar(1)">Finalizar</button>
            </div>
        </form>
    </div>
    {{--$totalLastMonth--}}
@stop
@section('js')
    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let saldos_libros_i = {{$conciliacion->saldo_libros}};
        const data_cheque_mano = @json($conciliacion->cheques_mano);
        const data_cheque_cobros = @json($conciliacion->cuentas_temporales->map(function($ct){ return $ct->comprobante_ingreso_temporal;}));
        let data_mano_select = @json($conciliacion->cheques_mano->filter(function($cm){ return $cm->aprobado == 'ON';})->values());
        let data_cobro_select = [];
        data_mano_select.forEach((e,i) => { data_mano_select[i]['id'] = i});
        let data_mano_no_select = @json($conciliacion->cheques_mano->filter(function($cm){ return $cm->aprobado == 'OFF';})->values());
        let data_cobro_no_select = @json($conciliacion->cuentas_temporales->map(function($ct){ return $ct->comprobante_ingreso_temporal;}));
        let cheques_mano = {{$cheques_mano}};
        let cheques_cobrados = {{$conciliacion->cuentas_temporales->sum('comprobante_ingreso_temporal.valor')}};
        let cheques_mano_libro = 0;
        let restar_cheques_mano = 0;
        let restar_cheques_cobrados = 0;
        let total_diferencia_siguiente_final = 0;
        console.log('data0', [data_mano_select, data_mano_no_select, data_cheque_mano]);


        
        $(document).ready(function(){
            diferencia_siguiente_final();
        });

        const guardar = finalizar =>{
            //primero se mandan los valores a los td
            let inicio = parseInt($('#valor_inicial').val());
            //alert(final)

            if(isNaN(inicio)){
                alert("Input Saldo Inicial debe ser numerico y obligatorio");
            }else{
                $('#input_data_cobro_select').val( JSON.stringify(data_cobro_select));
                $('#input_data_cobro_no_select').val( JSON.stringify(data_cobro_no_select));
                $('#finalizar').val(finalizar); 
                $('#prog').submit();
            }
        }

        const guardar_ver = async(dd) =>{
            data = {};
            data['libros'] = parseInt($('#input-iguales-libros').val());
            data['bancos'] = parseInt($('#input-iguales-bancos').val());
            data['inicio'] = parseInt($('#valor_inicial').val());
            data['final'] = parseInt($('#valor_final').val());
            data['cuenta'] = $('#cuenta').val();//
            data['mes'] = $('#mes').val();
            data['año'] = $('#año').val();
            data['cobros_select'] = data_cobro_select;
            data['cobros_no_select'] = data_cobro_no_select;
            data['mano_select'] = data_mano_select;
            data['mano_no_select'] = data_mano_no_select;
            if(isNaN(data['inicio'])){
                alert("Input Saldo Inicial debe ser numerico y obligatorio");
            }else if(isNaN(data['libros'])){
                alert("Input de partida sin conciliar libros debe ser numerico y obligatorio");
            }else if(isNaN(data['bancos'])){
                alert("Input de partida sin conciliar bancos debe ser numerico y obligatorio");
            }else{
                
                
                let response = await fetch('{{route('conciliacion.guardar-ver')}}', {
                    method:'POST',
                    body:JSON.stringify(data),
                    headers: new Headers({
                        'Content-Type': 'application/json',
                        "X-CSRF-TOKEN": token
                    })
                })
                .then(res=> res.json())
                .then(res => res)

                window.open(response.url, '_blank')
                //console.log('res', response);
            }
        }

        const diferencia_siguiente_final = () => {
            //inicvializar variables
           let libro_debito = 0;
           let libro_credito = 0;
           let banco_debito = 0;
           let banco_credito = 0;
           let banco_diferencia = 0;
           let banco_credito_anterior = 0;
           let banco_diferencia_anterior = 0

            //recoleccion de datos
           let saldo_inicial = parseInt($('#valor_inicial').val());
           /*
           let v_suma_iguales_libros = parseInt($('#input-iguales-libros').val());
           let v_suma_iguales_bancos = parseInt($('#input-iguales-bancos').val());
           */
           //procesos
           console.log('data', data_cheque_mano);
            if(data_cheque_mano.length > 0){
                data_cheque_mano.forEach(e => {
                    libro_credito += e.credito;
                    libro_debito += e.debito;
                });
            }
        
            if(data_mano_select.length > 0){
                data_mano_select.forEach(e => {
                    banco_debito += e.debito;
                    banco_credito += e.credito;
                });
            }
            if(data_mano_no_select.length > 0){
                data_mano_no_select.forEach(e => {
                    banco_diferencia += e.credito;
                    console.log('dfl', e)
                    console.log('bd', banco_diferencia)
                });
            }

                console.log('data_cobro_select0', data_cobro_select);
            if(data_cobro_select.length > 0){
                data_cobro_select.forEach(e => {
                    console.log('e.valor', e.valor);
                console.log('data_cobro_select', banco_credito_anterior);
                    banco_credito_anterior += e.valor;
                })
            }

            if(data_cobro_no_select.length > 0){
                data_cobro_no_select.forEach(e => {
                    banco_diferencia_anterior += e.valor;
                    console.log('df', e)
                })
            }

           let diferencia = banco_diferencia + banco_diferencia_anterior;
           console.log('bd2', diferencia)
           console.log('ss', [saldos_libros_i, libro_debito, libro_credito]);
           let saldo_siguiente = saldos_libros_i + libro_debito - libro_credito;
           console.log('final', [saldo_inicial, banco_debito, banco_credito, banco_credito_anterior]);
           let saldo_final = saldo_inicial + banco_debito - banco_credito - banco_credito_anterior;
           let sumas_iguales_libros = saldo_siguiente + diferencia;
           let sumas_iguales_bancos = saldo_final;

           //salidas input
           $('#valor_final').val(saldo_final);


           //salidas arriba
            $('#td-s-libros-i').html(saldos_libros_i);
            $('#td-s-siguiente-i').html(saldo_siguiente);
            $('#td-saldo-final, #td-s-inicial-f').html(saldo_final);
            $('#td-diferencia, #td-s-diferencia').html(diferencia);
            $('#td-sumas-iguales-libros').html(sumas_iguales_libros);
            $('#td-sumas-iguales-bancos').html(sumas_iguales_bancos);
            
            //salidas abajo libros
            $('#td-s-siguiente-f').html(saldo_siguiente);
            
            
            //console.log('rr', [ (cheques_cobrados - restar_cheques_cobrados)-final_bancos+total_b+(cheques_mano-restar_cheques_mano),  (cheques_cobrados - restar_cheques_cobrados),final_bancos,total_b,(cheques_mano-restar_cheques_mano)]);
        }

        const checked_checke_mano = (item, value, index, id) => {
            
            if(item.checked){
                restar_cheques_mano = restar_cheques_mano + value;
                let index_select = data_mano_no_select.findIndex(e => e.id == index );
                data_mano_no_select.splice(index_select, 1);
                let cm_select = data_cheque_mano[index];
                data_mano_select.push(cm_select)
            }else{
                restar_cheques_mano = restar_cheques_mano - value;
                let cm_select = data_cheque_mano[index];
                //cm_select['id'] = index;
                data_mano_no_select.push(cm_select)
                let index_select = data_mano_select.findIndex(e => e.id == id );
                data_mano_select.splice(index_select, 1);
            }
            console.log('cm_select', data_mano_select);
            console.log('cm_select2', data_mano_no_select);

            $.get(`/administrativo/tesoreria/bancos/conciliacion/cheques_mano/${id}`, function(data){
                console.log('data-mano', data);
            });


            diferencia_siguiente_final();
        }

        const checked_checke_cobrados = (item, value, id, relacion_id) => {
            if(item.checked){
                restar_cheques_cobrados = restar_cheques_cobrados - value;

                let index_select = data_cobro_no_select.findIndex(e => e.id == id );
                data_cobro_no_select.splice(index_select, 1);

                let cm_select_index = data_cheque_cobros.findIndex(e => e.id == id );
                data_cobro_select.push(data_cheque_cobros[cm_select_index])
            }else{
                restar_cheques_cobrados = restar_cheques_cobrados + value;
                
                let cm_select_id = data_cheque_cobros.findIndex(e => e.id == id);
                data_cobro_no_select.push(data_cheque_cobros[cm_select_id])

                let index_select = data_cobro_select.findIndex(e => e.id == id );
                data_cobro_select.splice(index_select, 1);
            }

            console.log('cm_select3', data_cobro_select);
            console.log('cm_select4', data_cobro_no_select);
            $.get(`/administrativo/tesoreria/bancos/conciliacion/cheques_temporales/${relacion_id}`, function(data){
                console.log('data-tempor', data);
            });

            diferencia_siguiente_final();
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

        $('#tablaOld').DataTable( {
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
    </script>
@stop
@section('css')
        <style>
        .my-fixed-item {
            position: fixed;
            z-index:2000;
            /*
            min-height: 120px;
            width: 252px;
            */
            text-align: center;
            word-wrap: break-word;
            background-color: white;
        }
        
        </style>
@stop