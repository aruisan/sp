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
                @if(isset($radCuenta->pago))
                    <li class="nav-item">
                        <a class="nav-link"  href="{{ url('/administrativo/radCuentas/'.$radCuenta->id.'/3') }}"><i class="fa fa-arrow-right"></i> PASO 3</a>
                    </li>
                @endif
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
                                @if(count($radCuenta->adds) == 0)
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
                                @else
                                    @foreach($radCuenta->adds as $add)
                                        <tr>
                                            <td>ADICION AL CONTRATO: $<?php echo number_format( $add->valor,0) ?></td>
                                            <td>RP #{{ $add->registro->code }}</td>
                                            <td><a onclick="deleteAdd({{ $add->id }})" class="btn-sm btn-danger"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr id="addContrato" style="display: none; background-color: white">
                                    <td>ADICION AL CONTRATO: <span id="addCont"></span> </td>
                                    <td><span id="addCDP"></span></td>
                                    <td>RP #<span id="addRP"></span></td>
                                </tr>
                                <tr>
                                    <td>VALOR FINAL DEL CONTRATO
                                        <input type="hidden" name="valor_fin_cont" id="valor_fin_cont"  @if($radCuenta->valor_fin) value="{{$radCuenta->valor_fin}}" @endif>
                                        <span id="valorFinal">
                                            @if($radCuenta->valor_fin)
                                                $<?php echo number_format( $radCuenta->valor_fin,0) ?>
                                            @else
                                                $<?php echo number_format( $registro->saldo,0) ?>
                                            @endif
                                        </span>
                                    </td>
                                    <td>NUMERO DE PAGOS
                                        <input type="number" name="num_pagos" id="num_pagos" min="0" @if($radCuenta->num_pagos) value="{{$radCuenta->num_pagos}}" @else value="0" @endif class="form-control">
                                    </td>
                                    <td>
                                        VALOR PAGO MENSUAL
                                        <input type="number" name="val_pago_men" id="val_pago_men" min="0" @if($radCuenta->valor_mensual) value="{{$radCuenta->valor_mensual}}" @else value="0" @endif class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        INGRESO BASE RETENCIÓN
                                        <input type="number" name="ing_base" id="ing_base" min="0" @if($radCuenta->ing_retencion) value="{{$radCuenta->ing_retencion}}" @else value="0" @endif class="form-control">
                                    </td>
                                    <td>
                                        ANTICIPO
                                        <input type="number" name="anticipo" id="anticipo" min="0" @if($radCuenta->anticipo) value="{{$radCuenta->anticipo}}" @else value="0" @endif class="form-control">
                                    </td>
                                    <td>
                                        FECHA ANTICIPO
                                        <input type="date" name="fecha_anticipo" id="fecha_anticipo" @if($radCuenta->fecha_anticipo) value="{{$radCuenta->fecha_anticipo}}" @endif class="form-control">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">AMORTIZACIÓN ANTICIPO
                                        <input type="number" name="amortizacion" id="amortizacion" min="0" @if($radCuenta->amortizacion) value="{{$radCuenta->amortizacion}}" @else value="0" @endif class="form-control">
                                    </td>
                                    <td>
                                        FECHA
                                        <input type="date" name="fecha_amorth" id="fecha_amorth" @if($radCuenta->fecha_amort) value="{{$radCuenta->fecha_amort}}" @endif class="form-control">
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
                                                    <option @if($ordenPago->periodo_pago == "NO") selected @endif value="NO">NO APLICA</option>
                                                    <option @if($ordenPago->periodo_pago == "ENE") selected @endif value="ENE">ENERO</option>
                                                    <option @if($ordenPago->periodo_pago == "FEB") selected @endif value="FEB">FEBRERO</option>
                                                    <option @if($ordenPago->periodo_pago == "MAR") selected @endif value="MAR">MARZO</option>
                                                    <option @if($ordenPago->periodo_pago == "ABR") selected @endif value="ABR">ABRIL</option>
                                                    <option @if($ordenPago->periodo_pago == "MAY") selected @endif value="MAY">MAYO</option>
                                                    <option @if($ordenPago->periodo_pago == "JUN") selected @endif value="JUN">JUNIO</option>
                                                    <option @if($ordenPago->periodo_pago == "JUL") selected @endif value="JUL">JULIO</option>
                                                    <option @if($ordenPago->periodo_pago == "AGO") selected @endif value="AGO">AGOSTO</option>
                                                    <option @if($ordenPago->periodo_pago == "SEP") selected @endif value="SEP">SEPTIEMBRE</option>
                                                    <option @if($ordenPago->periodo_pago == "OCT") selected @endif value="OCT">OCTUBRE</option>
                                                    <option @if($ordenPago->periodo_pago == "NOV") selected @endif value="NOV">NOVIEMBRE</option>
                                                    <option @if($ordenPago->periodo_pago == "DIC") selected @endif value="DIC">DICIEMBRE</option>
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
                                    @if(count($radCuenta->ops) > 0)
                                        @foreach($radCuenta->ops as $op)
                                            <tr>
                                                <td># {{ $op->ordenPago->code }} - {{ $op->ordenPago->nombre }}</td>
                                                <td>Periodo: {{ $op->ordenPago->periodo_pago }}</td>
                                                <td>Factura: {{ $op->ordenPago->factura }}</td>
                                                <td>Planilla: {{ $op->ordenPago->planilla }}</td>
                                                <td><a onclick="deleteOP({{ $op->id }})" class="btn-sm btn-danger"><i class="fa fa-trash"></i></a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group row" id="buttonSend">
                            <div class="col-lg-12 ml-auto text-center">
                                @if(!isset($radCuenta->pago))
                                    <a onclick="deleteRad({{ $radCuenta->id }})" class="btn btn-primary">Eliminar Radicación</a>
                                @endif
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

        function deleteRad(id){
            var opcion = confirm("Esta seguro de querer eliminar la radicación de cuenta?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/RADICACION",
                    data: { "idRad": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('RADICACION ELIMINADA. REDIRIGIENDO USUARIO...');
                        window.location.href = "/administrativo/radCuentas/{{$vigencia_id}}";
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR LA RADICACION DE CUENTA. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        function deleteOP(id){
            var opcion = confirm("Esta seguro de querer eliminar la orden de pago?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/OP",
                    data: { "idOP": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('ORDEN DE PAGO ELIMINADA. RECARGANDO PAGINA...');
                        location.reload();
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR LA ORDEN DE PAGO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        function deleteAdd(id){
            var opcion = confirm("Esta seguro de querer eliminar el registro adicionado?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/ADD",
                    data: { "idADD": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('REGISTRO ADICIONADO HA SIDO ELIMINADO. RECARGANDO PAGINA...');
                        location.reload();
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR EL REGISTRO ADICIONADO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

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
