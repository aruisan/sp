@extends('layouts.dashboard')
@section('titulo') Radicación de Cuentas - Paso 2 @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="breadcrumb text-center"><strong><h4><b>RADICACIÓN DE CUENTA - INFORMACIÓN FINANCIERA</b></h4></strong></div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$vigencia_id) }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >RADICACIÓN - PASO 2</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="form-validation">
                    <form class="form-valide" action="{{url('/administrativo/radCuentas/paso/2')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <div class="text-center" id="cargando" style="display: none">
                            <h4>Buscando informacion del registro...</h4>
                            <br>
                        </div>
                        <input type="hidden" name="radicacion_id" value="{{ $radCuenta->id }}">
                        <div class="col-md-12 " style="background-color: white" id="formRP" name="formRP">
                            <table id="TABLA1" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">3. INFORMACIÓN FINANCIERA</th></tr>
                                <tr>
                                    <td>VALOR INICIAL DEL CONTRATO: $<?php echo number_format( $registro->saldo,0) ?></td>
                                    <td>
                                        @foreach($cdps as $cdp)
                                            CDP: #{{ $cdp->code }}
                                        @endforeach
                                    </td>
                                    <td>RP: #{{$registro->code }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="form-group">
                                            <label class="col-lg-12 col-form-label text-center" for="persona_id">Adicion al Contrato. Seleccione el Registro: </label>
                                            <div class="col-lg-12 text-center">
                                                <select class="select-rp" style="width: 100%" name="adicion_rp_id" onchange="addRP(this.value)">
                                                    <option value="0">NO ADICIONAR</option>
                                                    @foreach($allRPs as $rp)
                                                        <option value="{{$rp->id}}">{{$rp->code}} - {{$rp->objeto}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="addContrato" style="display: none; background-color: white">
                                    <td>ADICION AL CONTRATO: <span id="addCont"></span> </td>
                                    <td><span id="addCDP"></span></td>
                                    <td>RP #<span id="addRP"></span></td>
                                </tr>
                                <tr>
                                    <td>VALOR FINAL DEL CONTRATO
                                        <input type="hidden" name="valor_fin_cont" id="valor_fin_cont">
                                        <span id="valorFinal">$<?php echo number_format( $registro->saldo,0) ?></span>
                                    </td>
                                    <td>NUMERO DE PAGOS
                                        <input type="number" name="num_pagos" id="num_pagos" min="0" value="0" class="form-control">
                                    </td>
                                    <td>
                                        VALOR PAGO MENSUAL
                                        <input type="number" name="val_pago_men" id="val_pago_men" min="0" value="0" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        INGRESO BASE RETENCIÓN
                                        <input type="number" name="ing_base" id="ing_base" min="0" value="0" class="form-control">
                                    </td>
                                    <td>
                                        ANTICIPO
                                        <input type="number" name="anticipo" id="anticipo" min="0" value="0" class="form-control">
                                    </td>
                                    <td>
                                        FECHA ANTICIPO
                                        <input type="date" name="fecha_anticipo" id="fecha_anticipo" class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">AMORTIZACIÓN ANTICIPO
                                        <input type="number" name="amortizacion" id="amortizacion" min="0" value="0" class="form-control">
                                    </td>
                                    <td>
                                        FECHA
                                        <input type="date" name="fecha_amorth" id="fecha_amorth" class="form-control">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            @if(count($ordenesPago) > 0)
                                <table id="tablaPagos" class="table text-center table-bordered table-responsive">
                                    <thead>
                                    <tr class="text-center">
                                        <th>Pago No.</th>
                                        <th>Valor</th>
                                        <th>Orden de Pago No.</th>
                                        <th>Fecha Pago</th>
                                        <th>Periodo de Pago</th>
                                        <th>Factura No.</th>
                                        <th>Planilla SSS.</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ordenesPago as $index => $ordenPago)
                                        <tr>
                                            <td style="vertical-align: middle">{{$index + 1}}</td>
                                            <td style="vertical-align: middle">$<?php echo number_format($ordenPago->valor,0) ?></td>
                                            <td style="vertical-align: middle">{{$ordenPago->code}}</td>
                                            <td style="vertical-align: middle">{{ \Carbon\Carbon::parse($ordenPago->created_at)->format('d-m-Y')}}</td>
                                            <td>
                                                <select name="periodoPago[]" class="form-control">
                                                    <option @if($ordenPago->periodo == "NO") selected @endif value="NO">NO APLICA</option>
                                                    <option @if($ordenPago->periodo == "ENE") selected @endif value="ENE">ENERO</option>
                                                    <option @if($ordenPago->periodo == "FEB") selected @endif value="FEB">FEBRERO</option>
                                                    <option @if($ordenPago->periodo == "MAR") selected @endif value="MAR">MARZO</option>
                                                    <option @if($ordenPago->periodo == "ABR") selected @endif value="ABR">ABRIL</option>
                                                    <option @if($ordenPago->periodo == "MAY") selected @endif value="MAY">MAYO</option>
                                                    <option @if($ordenPago->periodo == "JUN") selected @endif value="JUN">JUNIO</option>
                                                    <option @if($ordenPago->periodo == "JUL") selected @endif value="JUL">JULIO</option>
                                                    <option @if($ordenPago->periodo == "AGO") selected @endif value="AGO">AGOSTO</option>
                                                    <option @if($ordenPago->periodo == "SEP") selected @endif value="SEP">SEPTIEMBRE</option>
                                                    <option @if($ordenPago->periodo == "OCT") selected @endif value="OCT">OCTUBRE</option>
                                                    <option @if($ordenPago->periodo == "NOV") selected @endif value="NOV">NOVIEMBRE</option>
                                                    <option @if($ordenPago->periodo == "DIC") selected @endif value="DIC">DICIEMBRE</option>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="factura[]" value="{{ $ordenPago->factura }}"></td>
                                            <td>
                                                <input type="text" class="form-control" name="planilla[]" value="{{ $ordenPago->planilla }}">
                                                <input type="hidden" name="op_id[]" value="{{ $ordenPago->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                            <div id="crud">
                                <table id="tablaNewPagos" class="table table-bordered table-responsive">
                                    <tbody>
                                    <tr>
                                        <td colspan="5"><div class="text-center">
                                                <button type="button" @click.prevent="nuevaFila" class="btn btn-sm btn-primary">AGREGAR ORDEN DE PAGO</button>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group row" id="buttonSend">
                            <div class="col-lg-12 ml-auto text-center">
                                <button type="submit" class="btn btn-primary">Registrar Información Financiera</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        const vigencia_id = @json($vigencia_id);
        const rp_rad = @json($registro);
        $('.select-rp').select2();

        function addRP(rp_id){
            $("#cargando").show();
            $("#addContrato").hide();

            if (rp_id == 0){
                document.getElementById('valor_fin_cont').value = rp_rad.saldo;
                document.getElementById('valorFinal').innerHTML = formatter.format(rp_rad.saldo);
                $("#cargando").hide();
            } else {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/findRP",
                    data: { "idRP": rp_id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    $("#addContrato").show();
                    console.log(data);
                    document.getElementById('addCont').innerHTML = formatter.format(data.registro.saldo);
                    $("#addCDP").html("");
                    for(var y=0; y<data.cdps.length; y++){
                        var tr = `CDP #`+data.cdps[y].code+`<br>`;
                        $("#addCDP").append(tr)
                    }
                    document.getElementById('addRP').innerHTML = data.registro.code;

                    document.getElementById('valor_fin_cont').value = data.registro.saldo + rp_rad.saldo;
                    document.getElementById('valorFinal').innerHTML = formatter.format(data.registro.saldo + rp_rad.saldo);

                    $("#cargando").hide();
                }).fail(function() {
                    $("#cargando").hide();
                    $("#addContrato").hide();
                    toastr.warning('OCURRIO UN ERROR AL BUSCAR LA INFORMACION DEL TERCERO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        new Vue({
            el: '#crud',
            methods:{
                nuevaFila(){
                    $('#tablaNewPagos tbody tr:last').after('<tr>\n' +
                        '<td>Seleccione la orden de pago <br>' +
                        '<select class="form-control" name="otherOP[]">\n' +
                        '                                        @foreach($ordenesPagoAll as $ordenPagoAll)\n' +
                        '                                            <option value="{{$ordenPagoAll->id}}">{{$ordenPagoAll->code}} - {{$ordenPagoAll->nombre}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select></td>\n'+
                        '<td>Periodo Pago<br>' +
                        '<select class="form-control" name="periodoOtherOP[]">\n' +
                        '  <option value="NO">NO APLICA</option>\n' +
                        '  <option value="ENE">ENERO</option>\n' +
                        '  <option value="FEB">FEBRERO</option>\n' +
                        '  <option value="MAR">MARZO</option>\n' +
                        '  <option value="ABR">ABRIL</option>\n' +
                        '  <option value="MAY">MAYO</option>\n' +
                        '  <option value="JUN">JUNIO</option>\n' +
                        '  <option value="JUL">JULIO</option>\n' +
                        '  <option value="AGO">AGOSTO</option>\n' +
                        '  <option value="SEP">SEPTIEMBRE</option>\n' +
                        '  <option value="OCT">OCTUBRE</option>\n' +
                        '  <option value="NOV">NOVIEMBRE</option>\n' +
                        '  <option value="DIC">DICIEMBRE</option>\n' +
                        '  </select></td>\n'+
                        '<td>Factura No<input type="text" class="form-control" name="facturaOtherOP[]"></td>\n'+
                        '<td>Planilla SSS<input type="text" class="form-control" name="planillaOtherOP[]"></td>\n'+
                        '<td style="vertical-align: middle" class="text-center"><button type="button" class="borrar btn-sm btn-danger">&nbsp;-&nbsp; </button></td>\n'+
                        '</tr>\n');
                    $('.other-OP').select2();
                },
            }
        });

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

    </script>
@stop
