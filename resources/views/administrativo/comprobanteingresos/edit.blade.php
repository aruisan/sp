@extends('layouts.dashboard')
@section('titulo')
    C.C. {{ $comprobante->code }} - {{ $vigencia->vigencia }}
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de Contabilidad {{ $comprobante->code }} - {{ $vigencia->vigencia }}</b></h4>
        </strong>
    </div>
    <div class="col-md-12 align-self-center">

        <ul class="nav nav-pills">
            <li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/CIngresos/'.$comprobante->vigencia_id) }}">Volver a Comprobante de Contabilidad</a></li>
            <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Comprobante de Contabilidad {{ $comprobante->code }}</a></li>
        </ul>
        <div class="col-lg-12 ">
            <br><br>
            <div class="form-validation">
                <form class="form-valide"  enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Concepto <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <textarea name="concepto" class="form-control" required>{{ $comprobante->concepto }}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($comprobante->ruta)
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="file">Archivo: </label>
                                    <div class="col-lg-6">
                                        <a target="_blank" class="btn btn-sm btn-danger" href="/uploads/CertificadoIngresos/{{ $comprobante->ruta }}"><i class="fa fa-file-pdf-o"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="tipo">Tipo <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="tipoCI" id="tipoCI" onchange="cambioTipo(this.value)">
                                        <option value="Nota Debito" @if($comprobante->tipoCI == 'Nota Debito') selected @endif>Nota Debito</option>
                                        <option value="Nota Credito" @if($comprobante->tipoCI == 'Nota Credito') selected @endif>Nota Credito</option>
                                        <option value="Consignacion" @if($comprobante->tipoCI == 'Consignacion') selected @endif>Consignacion</option>
                                        <option value="Transferencia" @if($comprobante->tipoCI == 'Transferencia') selected @endif>Transferencia</option>
                                        <option value="Ingreso sin indetificar" @if($comprobante->tipoCI == 'Ingreso sin identificar') selected @endif>Ingreso sin identificar</option>
                                        <option value="Impuestos" @if($comprobante->tipoCI == 'Impuestos') selected @endif>Impuestos</option>
                                        <option value="SGP Salud" @if($comprobante->tipoCI == 'SGP Salud') selected @endif>SGP Salud</option>
                                        <option value="SGP Educacion" @if($comprobante->tipoCI == 'SGP Educacion') selected @endif>SGP Educacion</option>
                                        <option value="SGP Otros sectores" @if($comprobante->tipoCI == 'SGP Otros sectores') selected @endif>SGP Otros sectores</option>
                                        <option value="Otro" @if($comprobante->tipoCI == 'Otro') selected @endif>Otro</option>
                                    </select>
                                    <span style="display: none" id="otroTipo">
                                            <input class="form-control" type="text" name="cualOtroTipo" id="cualOtroTipo" placeholder="Cual otro?">
                                        </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="file">Fecha: </label>
                                <div class="col-lg-6">
                                    {{ \Carbon\Carbon::parse($comprobante->ff)->format('d-m-Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Valor <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="number" name="valor" min="0" value="{{ $comprobante->valor}}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="observacion">Valor Iva <span class="text-danger">*</span></label>
                                <div class="col-lg-6">
                                    <input type="number" name="valorIva" value="0" min="{{ $comprobante->iva}}" max="99999999" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Tercero</label>
                                <div class="col-lg-6">
                                    <input type="text" value="{{ $persona->num_dc }} - {{ $persona->nombre }}" disabled class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th class="text-center" style="width: 200px">Debito<span class="text-danger">*</span></th>
                            <th class="text-center" style="width: 200px">Crédito<span class="text-danger">*</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Cuenta Bancaria <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="cuentaDeb" id="cuentaDeb">
                                            @foreach($hijosDebito as $hijo)
                                                <option @if($comprobante->cuenta_banco == $hijo->id) selected @endif value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td><input class="form-control" min="0" type="number" name="creditoBanco" id="creditoBanco" value="{{ $comprobante->credito_banco}}"></td>
                            <td><input class="form-control" min="0" type="number" name="debitoBanco" id="debitoBanco" value="{{ $comprobante->debito_banco}}"></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Seleccione cuenta PUC <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="cuentaPUC" id="cuentaPUC">
                                            @foreach($hijos as $hijo)
                                                <option @if($comprobante->cuenta_puc_id == $hijo->id) selected @endif value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td><input class="form-control" min="0" type="number" name="creditoPUC" id="creditoPUC" value="{{ $comprobante->credito_puc}}"></td>
                            <td><input class="form-control" min="0" type="number" name="debitoPUC" id="debitoPUC" value="{{ $comprobante->debito_puc}}"></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Seleccione Rubro Ingresos <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="rubroIngresos" id="rubroIngresos">
                                            @foreach($rubrosIngresos as $rubro)
                                                <option @if($comprobante->rubro_font_ingresos_id == $rubro['id']) selected @endif value="{{$rubro['id']}}">{{$rubro['code']}} - {{$rubro['nombre']}} - {{$rubro['fCode']}}  - {{$rubro['fName']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td><input class="form-control" min="0" type="number" name="creditoIngresos" id="creditoIngresos" value="{{ $comprobante->debito_rubro_ing}}"></td>
                        </tr>
                    </table>
                    <br>
                </form>
            </div>
        </div>
    </div>
@stop
