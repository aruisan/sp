@extends('layouts.dashboard')
@section('titulo')Automatización de Nomina {{ $año }} @stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NUEVA NOMINA {{ $año }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/nominapre/'.$año) }}">Volver al Inicio</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVA NOMINA</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/nominapre')}}" method="POST" enctype="multipart/form-data">
                            <hr>
                            {{ csrf_field() }}
                            <div class="text-center" id="cargando" style="display: none">
                                <br>
                                <h4>Cargando... Un momento por favor</h4>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-md-12 align-self-center">
                                    <div class="form-group">
                                        <select name="movEgr" id="movEgr" class="form-control" required onchange="mes(this.value)">
                                            <option value="0">Seleccione el mes a elaborar</option>
                                            <option value="1">ENERO</option>
                                            <option value="2">FEBRERO</option>
                                            <option value="3">MARZO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center">
                                    <div class="form-group">
                                        <select name="movEgr" id="movEgr" class="form-control" required onchange="tipoNom(this.value)">
                                            <option value="0">Seleccione el tipo de nomina</option>
                                            <option value="1">EMPLEADOS</option>
                                            <option value="2">MESADAS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="dCC">
                                    <div class="form-group text-center" id="formGrupCC"></div>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" id="labelDCC"></label>
                                        <div class="col-lg-6" id="divInput"></div>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <div class="form-group row" id="buttonMake">
                                    <div class="col-lg-12 ml-auto text-center">
                                       <button type="submit" class="btn btn-primary">Elaborar Nomina</button>
                                    </div>
                                </div>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  </div>
  
@stop
@section('js')
    <script>

        $('.selectActivCC').select2();
        $('.selectActivCred').select2();
        $('.selectRubroCC').select2();
        $('.selectRubCred').select2();

        var año = @json($año);

        //tras-add-red
        function mes(value){
            console.log(value);
        }

        function tipoNom(value){
            console.log(value);
        }

        function rubroEgr(value){
            $("#cargando").show();
            var tipoTras = 1;

            $.ajax({
                method: "POST",
                url: "/presupuesto/traslados/"+año+"/findDepCred",
                data: { "id": value, "tipoTras": tipoTras, "año": año,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                if(datos == 'SIN SALDO') toastr.warning('EL RUBRO ESCOGIDO NO TIENE DINERO DISPONIBLE.');
                else {
                    if(tipoTras == '1'){
                        var labelCC = document.getElementById('labelDCC');
                        var divInput = document.getElementById('divInput');
                        var formGrupCC = document.getElementById('formGrupCC');
                        labelCC.innerHTML = 'Dinero a CONTRA ACREDITAR <span class="text-danger">*</span>'
                        divInput.innerHTML = '<input type="number" class="form-control" name="dineroCC" id="dineroCC" value="'+datos[0]+'"' +
                            'max="'+datos[0]+'" min="1">'
                        formGrupCC.innerHTML = '<br><h4 class="text-center">'+datos[1]+'</h4><h4 class="text-center">SALDO ACTUAL: '+formatter.format(datos[0])+'</h4>';
                        $('#rubCredito').show();
                    }
                }

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL CONSULTAR EL RUBRO.');
                $("#cargando").hide();
            });
        }

        function actividadFind(value){
            $("#cargando").show();
            var tipoTras = 1;

            $.ajax({
                method: "POST",
                url: "/presupuesto/traslados/"+año+"/findActividadCred",
                data: { "id": value, "tipoTras": tipoTras, "año": año,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                if(datos[0] == 'SIN SALDO') toastr.warning('LA ACTIVIDAD ESCOGIDA NO TIENE DINERO DISPONIBLE.');
                else {
                    if(tipoTras == '1'){
                        var labelCC = document.getElementById('labelDCC');
                        var divInput = document.getElementById('divInput');
                        var formGrupCC = document.getElementById('formGrupCC');
                        labelCC.innerHTML = 'Dinero a CONTRA ACREDITAR <span class="text-danger">*</span>'
                        divInput.innerHTML = '<input type="number" class="form-control" name="dineroCC" id="dineroCC" value="'+datos[0]+'"' +
                            'max="'+datos[0]+'" min="1">'
                        formGrupCC.innerHTML = '<br><h4 class="text-center">'+datos[1]+'</h4><h4 class="text-center">SALDO ACTUAL: '+formatter.format(datos[0])+'</h4>';
                        $('#activCredito').show();
                    }
                }

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL CONSULTAR LA ACTIVIDAD.');
                $("#cargando").hide();
            });
        }

        function rubroCred(value){
            if(value != 0) $('#buttonMake').show();
            else $('#buttonMake').hide();
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })
    </script>
@stop