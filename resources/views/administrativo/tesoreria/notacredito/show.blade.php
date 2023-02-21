@extends('layouts.dashboard')
@section('titulo')
    Creación de la Nota Credito {{ $notaCredito->code }}
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NOTA CREDITO {{ $notaCredito->code }} - {{ $notaCredito->año }} </b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/tesoreria/notasCredito/') }}">Volver a Notas Credito</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo"> NOTA CREDITO {{ $notaCredito->code }} - {{ $notaCredito->año }}</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <br>
                        <div class="row">
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Concepto <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <textarea name="concepto" class="form-control" required>{{ $notaCredito->concepto }} </textarea>
                                    </div>
                                </div>
                            </div>
                            @if($notaCredito->ruta)
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="file">Archivo: </label>
                                        <div class="col-lg-6">
                                            <a target="_blank" class="btn btn-sm btn-danger" href="/uploads/NotaCredito/{{ $notaCredito->ruta }}"><i class="fa fa-file-pdf-o"></i></a>
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
                                            <option value="Nota Debito" @if($notaCredito->tipo == 'Nota Debito') selected @endif>Nota Debito</option>
                                            <option value="Nota Credito" @if($notaCredito->tipo == 'Nota Credito') selected @endif>Nota Credito</option>
                                            <option value="Consignacion" @if($notaCredito->tipo == 'Consignacion') selected @endif>Consignacion</option>
                                            <option value="Transferencia" @if($notaCredito->tipo == 'Transferencia') selected @endif>Transferencia</option>
                                            <option value="Ingreso sin indetificar" @if($notaCredito->tipo == 'Ingreso sin identificar') selected @endif>Ingreso sin identificar</option>
                                            <option value="Impuestos" @if($notaCredito->tipo == 'Impuestos') selected @endif>Impuestos</option>
                                            <option value="SGP Salud" @if($notaCredito->tipo == 'SGP Salud') selected @endif>SGP Salud</option>
                                            <option value="SGP Educacion" @if($notaCredito->tipo == 'SGP Educacion') selected @endif>SGP Educacion</option>
                                            <option value="SGP Otros sectores" @if($notaCredito->tipo == 'SGP Otros sectores') selected @endif>SGP Otros sectores</option>
                                            <option value="Otro" @if($notaCredito->tipo == 'Otro') selected @endif>Otro</option>
                                        </select>
                                        <span style="display: none" id="otroTipo">
                                        <input class="form-control" value="{{ $notaCredito->cualOtroTipo }}" type="text" name="cualOtroTipo" id="cualOtroTipo" placeholder="Cual otro?">
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="file">Fecha: </label>
                                    <div class="col-lg-6">
                                        {{ \Carbon\Carbon::parse($notaCredito->fecha)->format('d-m-Y') }}
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
                                        <input type="number" name="valor" min="1" value="{{ $notaCredito->valor}}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="observacion">Valor Iva <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" name="valorIva" value="{{ $notaCredito->iva}}" min="0" max="99999999" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <br>
                        <table class="table">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Cuenta Bancaria <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="cuentaDeb" id="cuentaDeb">
                                                @foreach($hijos as $hijo)
                                                    <option @if($notaCredito->cuenta_banco == $hijo->id) selected @endif value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" type="number" min="0" name="creditoBanco" id="creditoBanco" value="{{ $notaCredito->credito_banco}}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" type="number" min="0" name="debitoBanco" id="debitoBanco" value="{{ $notaCredito->debito_banco}}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Seleccione cuenta PUC <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="cuentaPUC" id="cuentaPUC">
                                                @foreach($hijos as $hijo)
                                                    <option @if($notaCredito->cuenta_puc_id == $hijo->id) selected @endif value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" min="0" type="number" name="creditoPUC" id="creditoPUC" value="{{ $notaCredito->credito_puc}}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" min="0" type="number" name="debitoPUC" id="debitoPUC" value="{{ $notaCredito->debito_puc}}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Seleccione Rubro Gastos <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="rubroGastos" id="rubroGastos">
                                                @foreach($rubrosEgresos as $rubro)
                                                    <option @if($notaCredito->rubro_dep_egresos_id == $rubro['id']) selected @endif value="{{$rubro['id']}}">{{$rubro['code']}} - {{$rubro['nombre']}} - {{$rubro['fCode']}}  - {{$rubro['fName']}} - {{$rubro['dep']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" min="0" type="number" name="creditoGastos" id="creditoGastos" value="{{ $notaCredito->credito_rubro_egresos}}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" min="0" type="number" name="debitoGastos" id="debitoGastos" value="{{ $notaCredito->debito_rubro_egresos}}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Seleccione Rubro Ingresos <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="rubroIngresos" id="rubroIngresos">
                                                @foreach($rubrosIngresos as $rubro)
                                                    <option @if($notaCredito->rubro_font_ingresos_id == $rubro['id']) selected @endif value="{{$rubro['id']}}">{{$rubro['code']}} - {{$rubro['nombre']}} - {{$rubro['fCode']}}  - {{$rubro['fName']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" type="number" min="0" name="creditoIngresos" id="creditoIngresos" value="{{ $notaCredito->credito_rubro_ing}}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input class="form-control" type="number" min="0" name="debitoIngresos" id="debitoIngresos" value="{{ $notaCredito->debito_rubro_ing}}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
  </div>
@stop
@section('js')
    <script>
        function cambioTipo(value){
            if(value == "Otro"){
                document.getElementById("cualOtroTipo").value = null;
                $("#otroTipo").show();
            }
            else {
                document.getElementById("otroTipo").style.display = "none";
                document.getElementById("cualOtroTipo").value = null;
            }
        }
    </script>
@stop
