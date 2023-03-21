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
                                        <option value="SGP Otros sectores" @if($comprobante->tipoCI == 'Comprobante de Ingresos') selected @endif>Comprobante de Ingresos</option>
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
                    <br><br>
                    <table class="table table-bordered" id="tablaCont" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">CONTABILIZACIÓN</th>
                        </tr>
                        <tr>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Debito</th>
                            <th class="text-center">Credito</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($comprobante->movs as $mov)
                            @if(isset($mov->cuenta_banco))
                                <tr class="text-center">
                                    <td>{{ $mov->banco->code}}</td>
                                    <td>{{ $mov->banco->concepto}}</td>
                                    <td>$ <?php echo number_format($mov->debito,0);?></td>
                                    <td>$ <?php echo number_format($mov->credito,0);?></td>
                                </tr>
                            @endif
                            @if(isset($mov->cuenta_puc_id))
                                <tr class="text-center">
                                    <td>{{ $mov->puc->code}}</td>
                                    <td>{{ $mov->puc->concepto}}</td>
                                    <td>$ <?php echo number_format($mov->debito,0);?></td>
                                    <td>$ <?php echo number_format($mov->credito,0);?></td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <br>
                    <table class="table table-bordered" id="tablaP" style="width: 100%">
                        <thead>
                        <tr>
                            <th class="text-center" colspan="4" style="background-color: rgba(19,165,255,0.14)">PRESUPUESTO</th>
                        </tr>
                        <tr>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Fuente Financiación</th>
                            <th class="text-center">Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($comprobante->movs as $mov)
                            @if(isset($mov->rubro_font_ingresos_id))
                                <tr class="text-center">
                                    <td>{{ $mov->fontRubro->rubro->cod}}</td>
                                    <td>{{ $mov->fontRubro->rubro->name}}</td>
                                    <td>{{ $mov->fontRubro->sourceFunding->code}} - {{$mov->fontRubro->sourceFunding->description}}</td>
                                    <td>$ <?php echo number_format($mov->debito,0);?></td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@stop
