@extends('layouts.dashboard')
@section('titulo') Radicación de Cuentas - Paso 3 @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="breadcrumb text-center"><strong><h4><b>RADICACIÓN DE CUENTA - DATOS PARA EL PAGO</b></h4></strong></div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$vigencia_id) }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$radCuenta->id.'/2') }}"><i class="fa fa-arrow-left"></i> PASO 2</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >RADICACIÓN - PASO 3</a>
                </li>
                @if(count($radCuenta->anexos) > 0)
                    <li class="nav-item">
                        <a class="nav-link"  href="{{ url('/administrativo/radCuentas/'.$radCuenta->id.'/4') }}"><i class="fa fa-arrow-right"></i> PASO 4</a>
                    </li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="form-validation" id="crud">
                    <form class="form-valide" action="{{url('/administrativo/radCuentas/paso/3')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <input type="hidden" name="radicacion_id" value="{{ $radCuenta->id }}">
                        <div class="col-md-12 " style="background-color: white" id="formRP" name="formRP">
                            <table id="TABLA1" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">3. DATOS PARA EL PAGO</th></tr>
                                <tr>
                                    <td>Número trabajadores asociados actividad contractual
                                        <input type="number" name="num_trabajadores" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->num_trabajadores}}" @else value="0" @endif>
                                    </td>
                                    <td>Número Planilla
                                        <input type="number" name="num_planilla" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->num_planilla}}" @else value="0" @endif>
                                    </td>
                                    <td>Número de contratos con el municipio u otras entidades PoP
                                        <input type="number" name="num_contratos" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->num_contratos}}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Periodo Salud
                                        <select name="periodo_salud" class="form-control">
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "NO") selected @endif value="NO">NO APLICA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "ENE") selected @endif value="ENE">ENERO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "FEB") selected @endif value="FEB">FEBRERO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "MAR") selected @endif value="MAR">MARZO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "ABR") selected @endif value="ABR">ABRIL</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "MAY") selected @endif value="MAY">MAYO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "JUN") selected @endif value="JUN">JUNIO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "JUL") selected @endif value="JUL">JULIO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "AGO") selected @endif value="AGO">AGOSTO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "SEP") selected @endif value="SEP">SEPTIEMBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "OCT") selected @endif value="OCT">OCTUBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "NOV") selected @endif value="NOV">NOVIEMBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_salud == "DIC") selected @endif value="DIC">DICIEMBRE</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        Valor Salud
                                        <input type="number" name="valor_salud" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->valor_salud}}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Periodo Pensión
                                        <select name="periodo_pension" class="form-control">
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "NO") selected @endif value="NO">NO APLICA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "ENE") selected @endif value="ENE">ENERO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "FEB") selected @endif value="FEB">FEBRERO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "MAR") selected @endif value="MAR">MARZO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "ABR") selected @endif value="ABR">ABRIL</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "MAY") selected @endif value="MAY">MAYO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "JUN") selected @endif value="JUN">JUNIO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "JUL") selected @endif value="JUL">JULIO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "AGO") selected @endif value="AGO">AGOSTO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "SEP") selected @endif value="SEP">SEPTIEMBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "OCT") selected @endif value="OCT">OCTUBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "NOV") selected @endif value="NOV">NOVIEMBRE</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->periodo_pension == "DIC") selected @endif value="DIC">DICIEMBRE</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        Valor Pensión
                                        <input type="number" name="valor_pension" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->valor_pension}}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ARL
                                        <select name="arl" class="form-control">
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->arl == "NO") selected @endif value="NO">NO APLICA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->arl == "ARL POSITIVA") selected @endif value="ARL POSITIVA">ARL POSITIVA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->arl == "ARL SURA") selected @endif value="ARL SURA">ARL SURA</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        Valor ARL
                                        <input type="number" name="valor_arl" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->valor_arl}}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Caja Compensación
                                        <select name="caja" class="form-control">
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->caja == "NO") selected @endif value="NO">NO APLICA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->caja == "CAJASAI") selected @endif value="CAJASAI">CAJASAI</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->caja == "COLSUBSIDIO") selected @endif value="COLSUBSIDIO">COLSUBSIDIO</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->caja == "COMFAMA") selected @endif value="COMFAMA">COMFAMA</option>
                                            <option @if(isset($radCuenta->pago->id) and $radCuenta->pago->caja == "CAFAM") selected @endif value="CAFAM">CAFAM</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        Valor Caja
                                        <input type="number" name="valor_caja" class="form-control" min="0" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->valor_caja}}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Valor a Pagar
                                        <input type="number" class="form-control" name="valor_pago" @if(isset($radCuenta->pago->id)) value="{{$radCuenta->pago->valor_pago}}" @else value="{{$radCuenta->valor_ini}}" @endif>
                                    </td>
                                </tr>
                                @if(isset($radCuenta->pago->id))
                                    @php($hasPago = 1)
                                    <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">DESCUENTOS</th></tr>
                                    @php($showOP = false)
                                    @if($radCuenta->persona->regimen != "ordinario")
                                        @php($valueDIAN = 0)
                                        @if($radCuenta->persona->regimen == "simple tributacion")
                                            @if($radCuenta->registro->tipo_contrato == 3 or $radCuenta->registro->tipo_contrato == 10
                                                or $radCuenta->registro->tipo_contrato == 4 or $radCuenta->registro->tipo_contrato == 5
                                                or $radCuenta->registro->tipo_contrato == 6 or $radCuenta->registro->tipo_contrato == 12)
                                                @php($valueDIAN = 0)
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 0)
                                                @if($radCuenta->registro->tipo_contrato == 3)
                                                    @php($obraPub = 5)
                                                    @php($showOP = true)
                                                @endif
                                            @endif
                                        @else
                                            @if($radCuenta->registro->tipo_contrato != 20 and $radCuenta->registro->tipo_contrato != 22)
                                                @php($valueDIAN = 0)
                                                @php($adultoMayor = 0)
                                                @php($sobretasa = 0)
                                                @php($estampilla = 0)
                                                @php($ica = 0)
                                                @if($radCuenta->registro->tipo_contrato == 3)
                                                    @php($obraPub = 5)
                                                    @php($showOP = true)
                                                @endif
                                            @endif
                                        @endif
                                    @else
                                        @if($radCuenta->persona->responsabilidad_renta == "Gran Contribuyente")
                                            @if($radCuenta->registro->tipo_contrato == 3 or $radCuenta->registro->tipo_contrato == 10
                                                or $radCuenta->registro->tipo_contrato == 4 or $radCuenta->registro->tipo_contrato == 6
                                                or $radCuenta->registro->tipo_contrato == 12)
                                                @php($valueDIAN = 0)
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 10)
                                                @if($radCuenta->registro->tipo_contrato == 3)
                                                    @php($obraPub = 5)
                                                    @php($showOP = true)
                                                @endif
                                            @endif
                                        @elseif($radCuenta->persona->responsabilidad_renta == "Responsable Impuesto Renta")
                                            @if($radCuenta->registro->tipo_contrato == 3)
                                                @php($valueDIAN = 2)
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 10)
                                                @php($obraPub = 5)
                                                @php($showOP = true)
                                            @elseif($radCuenta->registro->tipo_contrato == 10 or $radCuenta->registro->tipo_contrato == 4
                                                      or $radCuenta->registro->tipo_contrato == 5)
                                                @if($radCuenta->persona->tipo == "NATURAL")
                                                    @php($valueDIAN = 10)
                                                @else
                                                    @php($valueDIAN = 11)
                                                @endif
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 10)
                                                @php($obraPub = 0)
                                            @elseif($radCuenta->registro->tipo_contrato == 6)
                                                @if($radCuenta->registro->val_total >= 1145000)
                                                    @php($valueDIAN = 2.5)
                                                    @php($adultoMayor = 2)
                                                    @php($sobretasa = 2)
                                                    @php($estampilla = 0)
                                                    @php($ica = 10)
                                                    @php($obraPub = 0)
                                                @endif
                                            @elseif($radCuenta->registro->tipo_contrato == 12 or $radCuenta->registro->tipo_contrato == 7)
                                                @php($valueDIAN = 3.5)
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 10)
                                                @php($obraPub = 0)
                                            @elseif($radCuenta->registro->tipo_contrato == 8)
                                                @php($valueDIAN = 1)
                                                @php($adultoMayor = 2)
                                                @php($sobretasa = 2)
                                                @php($estampilla = 0)
                                                @php($ica = 10)
                                                @php($obraPub = 0)
                                            @endif
                                        @elseif($radCuenta->persona->responsabilidad_renta == "No declarante impuesto de Renta")
                                            @if($radCuenta->persona->tipo == "NATURAL")
                                                @if($radCuenta->registro->tipo_contrato == 10)
                                                    @if($radCuenta->valor_mensual < 4029140)
                                                        @if($radCuenta->num_trabajadores <= 1)
                                                            @if($radCuenta->num_entidades <= 1)
                                                                @php($valueDIAN = 0)
                                                            @endif
                                                        @elseif($radCuenta->num_entidades > 1)
                                                            @php($valueDIAN = 10)
                                                        @endif
                                                        @php($adultoMayor = 2)
                                                        @php($sobretasa = 2)
                                                        @php($estampilla = 0)
                                                        @php($ica = 10)
                                                        @php($obraPub = 0)
                                                    @elseif($radCuenta->valor_mensual >= 4029140)
                                                        @php($valueDIAN = 10)
                                                        @php($adultoMayor = 2)
                                                        @php($sobretasa = 2)
                                                        @php($estampilla = 0)
                                                        @php($ica = 10)
                                                        @php($obraPub = 0)
                                                    @endif
                                                @elseif($radCuenta->registro->tipo_contrato == 4 or $radCuenta->registro->tipo_contrato == 5)
                                                    @php($valueDIAN = 10)
                                                    @php($adultoMayor = 2)
                                                    @php($sobretasa = 2)
                                                    @php($estampilla = 0)
                                                    @php($ica = 10)
                                                    @php($obraPub = 0)
                                                @elseif($radCuenta->registro->tipo_contrato == 6 or $radCuenta->registro->tipo_contrato == 12
                                                            or $radCuenta->registro->tipo_contrato == 7)
                                                    @php($valueDIAN = 3.5)
                                                    @php($adultoMayor = 2)
                                                    @php($sobretasa = 2)
                                                    @php($estampilla = 0)
                                                    @php($ica = 10)
                                                    @php($obraPub = 0)
                                                @endif
                                            @else
                                        @endif
                                       @endif
                                    @endif
                                    <tr>
                                        <td>Retencion en la Fuente DIAN: {{ $valueDIAN }}%
                                            <input type="hidden" value="{{ $valueDIAN }}" name="reteDIAN">
                                        </td>
                                        <td colspan="2">
                                            Valor: $<?php echo number_format( ($valueDIAN * $radCuenta->pago->valor_pago) / 100 ,0) ?>
                                            <input type="hidden" value="{{ ($valueDIAN * $radCuenta->pago->valor_pago) / 100 }}" name="reteDIANValue">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Estampilla Adulto Mayor: {{ $adultoMayor }}%
                                            <input type="hidden" value="{{ $adultoMayor }}" name="adulto">
                                        </td>
                                        <td colspan="2">
                                            Valor: $<?php echo number_format( ($adultoMayor * $radCuenta->pago->valor_pago) / 100 ,0) ?>
                                            <input type="hidden" value="{{ ($adultoMayor * $radCuenta->pago->valor_pago) / 100 }}" name="adultoValue">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sobretasa Deportiva: {{ $sobretasa }}%
                                            <input type="hidden" value="{{ $sobretasa }}" name="sobretasa">
                                        </td>
                                        <td colspan="2">
                                            Valor: $<?php echo number_format( ($sobretasa * $radCuenta->pago->valor_pago) / 100 ,0) ?>
                                            <input type="hidden" value="{{ ($sobretasa * $radCuenta->pago->valor_pago) / 100 }}" name="sobretasaValue">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Estampilla Justicia: {{ $estampilla }}%
                                            <input type="hidden" value="{{ $estampilla }}" name="estampilla">
                                        </td>
                                        <td colspan="2">
                                            Valor: $<?php echo number_format( ($estampilla * $radCuenta->pago->valor_pago) / 100 ,0) ?>
                                            <input type="hidden" value="{{ ($estampilla * $radCuenta->pago->valor_pago) / 100 }}" name="estampillaValue">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Industria y comercio ICA: {{ $ica }}x1000
                                            <input type="hidden" value="{{ $ica }}" name="ica">
                                        </td>
                                        <td colspan="2">
                                            Valor: $<?php echo number_format( ($ica * $radCuenta->pago->valor_pago) / 1000 ,0) ?>
                                            <input type="hidden" value="{{ ($ica * $radCuenta->pago->valor_pago) / 1000 }}" name="icaValue">
                                        </td>
                                    </tr>
                                    @if($showOP)
                                        <tr>
                                            <td>Contribución contrato de Obra pública: {{ $obraPub }}%
                                                <input type="hidden" value="{{ $obraPub }}" name="obraPub">
                                            </td>
                                            <td colspan="2">
                                                Valor: $<?php echo number_format( ($obraPub * $radCuenta->pago->valor_pago) / 100 ,0) ?>
                                                <input type="hidden" value="{{ ($obraPub * $radCuenta->pago->valor_pago) / 100 }}" name="obra_pubValue">
                                            </td>
                                        </tr>
                                    @endif
                                    @if(count($radCuenta->pago->descuentos) > 0)
                                        @foreach($radCuenta->pago->descuentos as $descuento)
                                            <tr>
                                                <td>{{ $descuento->type }}</td>
                                                <td>Valor: $<?php echo number_format( $descuento->valor,0) ?></td>
                                                <td><a onclick="deleteDesc({{ $descuento->id }})" class="btn-sm btn-danger"><i class="fa fa-trash"></i></a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @else
                                    @php($hasPago = 0)
                                    @php($valueDIAN = 0)
                                    @php($adultoMayor = 0)
                                    @php($sobretasa = 0)
                                    @php($estampilla = 0)
                                    @php($ica = 0)
                                    @php($obraPub = 0)
                                @endif
                                </tbody>
                            </table>
                            @if(isset($radCuenta->pago->id))
                                <table id="tablaTotales" class="table text-center table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>TOTAL DESCUENTOS: <b><span id="totalDescSpan">
                                                    @if($radCuenta->pago->totalDesc > 0)
                                                        $<?php echo number_format( $radCuenta->pago->totalDesc,0) ?>
                                                    @endif
                                                </span></b>
                                            <input type="hidden" name="totalDesc" id="totalDesc"
                                                   @if($radCuenta->pago->totalDesc > 0) value="{{$radCuenta->pago->totalDesc}}" @endif>
                                        </td>
                                        <td>NETO A PAGAR: <b><span id="netoPagoSpan">
                                                    @if($radCuenta->pago->netoPago > 0)
                                                        $<?php echo number_format( $radCuenta->pago->netoPago,0) ?>
                                                    @endif
                                                </span></b>
                                            <input type="hidden" name="netoPago" id="netoPago"
                                                   @if($radCuenta->pago->netoPago > 0) value="{{$radCuenta->pago->netoPago}}" @endif>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        @if(isset($radCuenta->pago->id))
                            <input type="hidden" name="rad_cuenta_pago_id" value="{{$radCuenta->pago->id}}">
                            <div class="text-center">
                                <button type="button" @click.prevent="nuevaFilaEmbargo" class="btn btn-sm btn-primary">AGREGAR EMBARGO</button>
                                <button type="button" @click.prevent="nuevaFilaLibranza" class="btn btn-sm btn-primary">AGREGAR LIBRANZA</button>
                            </div>
                        @endif
                        <br>
                        <div class="form-group row" id="buttonSend">
                            <div class="col-lg-12 ml-auto text-center">
                                @if(isset($radCuenta->pago->id) and count($radCuenta->anexos) == 0)
                                    <a onclick="deletePago({{ $radCuenta->id }})" class="btn btn-primary">Eliminar Datos del Pago</a>
                                @endif
                                <button type="submit" class="btn btn-primary">Registrar Datos Para el Pago</button>
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
        const radCuentaPago = @json($hasPago);
        const variable = @json($radCuenta->pago['valor_pago']);

        function deletePago(id){
            var opcion = confirm("Esta seguro de querer eliminar los datos para el pago?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/PAGO",
                    data: { "idRad": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('DATOS DEL PAGO ELIMINADO. RECARGANDO PAGINA...');
                        location.reload();
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR LOS DATOS DEL PAGO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        function deleteDesc(id){
            var opcion = confirm("Esta seguro de querer eliminar el descuento?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/DESCUENTO",
                    data: { "idDesc": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('DESCUENTO ELIMINADO. RECARGANDO PAGINA...');
                        location.reload();
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR EL DESCUENTO. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }

        if(radCuentaPago === 1){
            const reteDian = @json(($valueDIAN * $radCuenta->pago['valor_pago']) / 100);
            const adulto = @json(($adultoMayor * $radCuenta->pago['valor_pago']) / 100);
            const sobretasa = @json(($sobretasa * $radCuenta->pago['valor_pago']) / 100);
            const estampilla = @json(($estampilla * $radCuenta->pago['valor_pago']) / 100);
            const ica = @json(($ica * $radCuenta->pago['valor_pago']) / 1000);
            const obraPub = @json(($obraPub * $radCuenta->pago['valor_pago']) / 100);
            const valuePago = @json($radCuenta->pago['valor_pago']);

            window.onload = function (){
                calculeValues();
            }

            function calculeValues(){
                const totDesc = reteDian + adulto + sobretasa + estampilla + ica + obraPub;
                document.getElementById('totalDesc').value = totDesc;
                document.getElementById('totalDescSpan').innerHTML = formatter.format(totDesc);

                const totalPago = valuePago - totDesc;
                document.getElementById('netoPago').value = totalPago;
                document.getElementById('netoPagoSpan').innerHTML = formatter.format(totalPago);
            }

            function valueDesc(value){
                var actualDesc = document.getElementById('totalDesc').value;
                var actualTotal = document.getElementById('netoPago').value;

                document.getElementById('totalDesc').value = Number(value) + Number(actualDesc);
                document.getElementById('totalDescSpan').innerHTML = formatter.format(Number(value) + Number(actualDesc));

                document.getElementById('netoPago').value =  Number(actualTotal) - Number(value);
                document.getElementById('netoPagoSpan').innerHTML = formatter.format(Number(actualTotal) - Number(value));
            }

            const formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0
            })

            $(document).on('click', '.borrar', function (event) {
                location.reload();
            });

            new Vue({
                el: '#crud',
                methods:{
                    nuevaFilaEmbargo(){
                        $('#TABLA1 tbody tr:last').after('<tr>\n' +
                            '<td>Embargo</td>\n'+
                            '<td>Valor: <input type="number" class="form-control" name="embargo[]" min="1" value="0" onchange="valueDesc(this.value)"></td>\n'+
                            '<td style="vertical-align: middle; width: 8%" class="text-center"><button type="button" class="borrar btn-sm btn-danger"><i class="fa fa-refresh"></i></button></td>\n'+
                            '</tr>\n');
                    },
                    nuevaFilaLibranza(){
                        $('#TABLA1 tbody tr:last').after('<tr>\n' +
                            '<td>Libranza</td>\n'+
                            '<td>Valor: <input type="number" class="form-control" name="libranza[]" min="1" value="0" onchange="valueDesc(this.value)"></td>\n'+
                            '<td style="vertical-align: middle; width: 8%" class="text-center"><button type="button" class="borrar btn-sm btn-danger"><i class="fa fa-refresh"></i></button></td>\n'+
                            '</tr>\n');
                    },
                }
            });
        }
    </script>
@stop
