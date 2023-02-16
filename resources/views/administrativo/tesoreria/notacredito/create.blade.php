@extends('layouts.dashboard')
@section('titulo')
    Creación de la Nota Credito
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NUEVA NOTA CREDITO</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/tesoreria/notasCredito/') }}">Volver a Notas Credito</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVA NOTA CREDITO</a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <br>
                        <form class="form-valide" action="{{url('/administrativo/tesoreria/notasCredito')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="nombre">Concepto <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <textarea name="concepto" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="file">Subir Archivo: </label>
                                        <div class="col-lg-6">
                                            <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                            <input type="file" name="file" accept="application/pdf" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="tipo">Tipo <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="tipoCI" id="tipoCI" onchange="cambioTipo(this.value)">
                                                <option value="SGP Salud">SGP Salud</option>
                                                <option value="SGP Educacion">SGP Educacion</option>
                                                <option value="SGP Otros sectores">SGP Otros sectores</option>
                                                <option value="Otro">Otro</option>
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
                                            <input type="date" name="fecha" class="form-control" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
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
                                            <input type="number" name="valor" min="1" value="0" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="observacion">Valor Iva <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" name="valorIva" value="0" min="0" max="99999999" class="form-control" required>
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
                                                        <option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="creditoBanco" id="creditoBanco" value="0">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="debitoBanco" id="debitoBanco" value="0">
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
                                                        <option value="{{$hijo->id}}">{{$hijo->code}} - {{$hijo->concepto}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="creditoPUC" id="creditoPUC" value="0">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="debitoPUC" id="debitoPUC" value="0">
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
                                                        <option value="{{$rubro->id}}">{{$rubro->cod}} - {{$rubro->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="creditoGastos" id="creditoGastos" value="0">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="debitoGastos" id="debitoGastos" value="0">
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
                                                        <option value="{{$rubro->id}}">{{$rubro->cod}} - {{$rubro->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Credito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="creditoIngresos" id="creditoIngresos" value="0">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-lg-4 col-form-label text-right" for="nombre">Debito<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input class="form-control" min="0" type="number" name="debitoIngresos" id="debitoIngresos" value="0">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-center">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </form>
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
