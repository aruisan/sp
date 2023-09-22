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
                    <form class="form-valide" action="{{url('/administrativo/radCuentas/paso/')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <div class="text-center" id="cargando" style="display: none">
                            <h4>Buscando informacion del tercero...</h4>
                        </div>
                        <div class="text-center" id="cargandoRP" style="display: none">
                            <h4>Buscando informacion del registro...</h4>
                        </div>
                        <div class="col-md-12 " style="background-color: white" id="formRP" name="formRP">
                            <table id="TABLA1" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">3. INFORMACIÓN FINANCIERA</th></tr>
                                <tr>
                                    <td>VALOR INICIAL DEL CONTRATO: $<?php echo number_format( $registro->val_total,0) ?></td>
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
                                <tr>
                                    <td>ADICION AL CONTRATO</td>
                                    <td>CDP</td>
                                    <td>RP</td>
                                </tr>
                                <tr>
                                    <td>VALOR FINAL DEL CONTRATO
                                        <input type="hidden" name="valor_fin_cont" id="valor_fin_cont">
                                        <span id="valorFinal">$<?php echo number_format( $registro->val_total,0) ?></span>
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
                        </div>
                        <div class="form-group row" id="buttonSend" style="display: none; background-color: white">
                            <div class="col-lg-12 ml-auto text-center">
                                <button type="submit" class="btn btn-primary">Generar Radicación</button>
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
        $('.select-rp').select2();
        $('.select-interventor').select2();

        function addRP(rp_id){
            console.log(rp_id);
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

    </script>
@stop
