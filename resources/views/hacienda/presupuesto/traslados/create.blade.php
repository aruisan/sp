@extends('layouts.dashboard')
@section('titulo')Creación de Movimiento {{ $año }} @stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NUEVO TRASLADO {{ $año }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/presupuesto/traslados/'.$año) }}">Volver a Movimientos</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVO TRASLADO</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/presupuesto/traslados')}}" method="POST" enctype="multipart/form-data">
                            <hr>
                            {{ csrf_field() }}
                            <div class="text-center" id="cargando" style="display: none">
                                <br>
                                <h4>Cargando... Un momento por favor</h4>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-md-12 align-self-center">
                                    <div class="alert alert-danger text-center">
                                        Recuerde añadir el archivo en el que esta la resolución del traslado. &nbsp;
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center">
                                    <input type="file" required name="fileRes" accept="application/pdf" class="form-control">
                                </div>
                                <div class="col-md-12 align-self-center" style="display: none">
                                    <div class="form-group">
                                        <select name="prep" id="prep" class="form-control" required onchange="presupuesto(this.value)">
                                            <option>Seleccione el presupuesto a realizar el traslado</option>
                                            @foreach($presupuestos as $presupuesto)
                                                @if($presupuesto->tipo == 0)
                                                    <option value="{{ $presupuesto->tipo }}">EGRESOS</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center">
                                    <div class="form-group">
                                        <select name="movEgr" id="movEgr" class="form-control" required onchange="mov(this.value)" style="display: none">
                                            <option value="0">Seleccione el movimiento a realizar</option>
                                            <option value="1">TRASLADO</option>
                                            <option value="2">ADICIÓN</option>
                                            <option value="3">REDUCCIÓN</option>
                                        </select>
                                        <select name="movIng" id="movIng" class="form-control" required onchange="mov(this.value)" style="display: none">
                                            <option value="0">Seleccione el movimiento a realizar</option>
                                            <option value="2">ADICIÓN</option>
                                            <option value="3">REDUCCIÓN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="tipoCred">
                                    <div class="form-group">
                                        <select name="tipTras" id="tipTras" class="form-control" required onchange="tipoTraslado(this.value)">
                                            <option value="0">Seleccione el tipo de traslado a realizar</option>
                                            <option hidden value="1">INVERSIÓN</option>
                                            <option value="2">FUNCIONAMIENTO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="rubEgr" style="display: none">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="fontRubEgr">Seleccione el rubro que será afectado <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select name="fontRubEgr" id="fontRubEgr" style="width: 100%" class="selectRubroCC" required onchange="rubroEgr(this.value)">
                                                <option value="0">Seleccione el Rubro de Egresos</option>
                                                @foreach($rubrosEgresos as $rubro)
                                                    <option value="{{ $rubro['id'] }}">{{ $rubro['code'] }}
                                                        {{ $rubro['nombre'] }} - {{ $rubro['fCode'] }} {{ $rubro['fName'] }}
                                                    - {{ $rubro['dep']['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="actividades" style="display: none">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="fontRubEgr">Seleccione la actividad que será afectada <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select name="activCC" id="activCC" style="width: 100%" class="selectActivCC" required onchange="actividadFind(this.value)">
                                                <option value="0">Seleccione la actividad</option>
                                                @foreach($bpins as $bpin)
                                                    <option value="{{ $bpin['id'] }}">{{ $bpin->bpin->cod_actividad }} - {{ $bpin->bpin->actividad }}
                                                    - {{ $bpin->rubro->dependencias->name }} - {{ $bpin->rubro->fontRubro->sourceFunding->code }}
                                                        - {{ $bpin->rubro->fontRubro->sourceFunding->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="dCC">
                                    <div class="form-group text-center" id="formGrupCC"></div>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" id="labelDCC"></label>
                                        <div class="col-lg-6" id="divInput"></div>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="rubCredito" style="display: none">
                                    <div class="form-group">
                                        <br>
                                        <label class="col-lg-4 col-form-label text-right" for="fontRubCred">Seleccione el rubro que recibira el credito <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select name="fontRubCred" id="fontRubCred" style="width: 100%" class="selectRubCred" required onchange="rubroCred(this.value)">
                                                <option value="0">Seleccione el Rubro de Egresos</option>
                                                @foreach($rubrosEgresosAll as $rubro)
                                                    <option value="{{ $rubro['id'] }}">{{ $rubro['code'] }}
                                                        {{ $rubro['nombre'] }} - {{ $rubro['fCode'] }} {{ $rubro['fName'] }}
                                                        - {{ $rubro['dep']['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="activCredito" style="display: none">
                                    <div class="form-group">
                                        <br>
                                        <label class="col-lg-4 col-form-label text-right" for="fontRubCred">Seleccione la actividad que recibira el credito <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select name="actividadCred" id="actividadCred" style="width: 100%" class="selectActivCred" required onchange="rubroCred(this.value)">
                                                <option value="0">Seleccione la Actividad</option>
                                                @foreach($bpinsAll as $bpin)
                                                    <option value="{{ $bpin['id'] }}">{{ $bpin->bpin->cod_actividad }} - {{ $bpin->bpin->actividad }}
                                                        - {{ $bpin->rubro->dependencias->name }} - {{ $bpin->rubro->fontRubro->sourceFunding->code }}
                                                        - {{ $bpin->rubro->fontRubro->sourceFunding->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <div class="form-group row" id="buttonMake" style="display: none">
                                    <div class="col-lg-12 ml-auto text-center">
                                        <br><br>
                                       <button type="submit" class="btn btn-primary">Realizar Traslado</button>
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
        function presupuesto(value)
        {
            $('#tipoCred').hide();
            $('#rubIng').hide();
            if(value == 0){
                $('#movEgr').show();
                $('#movIng').hide();
            } else if(value == 1){
                $('#movEgr').hide();
                $('#movIng').show();
            }else{
                $('#movEgr').hide();
                $('#movIng').hide();
            }
        }

        //tras-add-red
        function mov(value){
            var labelCC = document.getElementById('labelDCC');
            var divInput = document.getElementById('divInput');
            var formGrupCC = document.getElementById('formGrupCC');
            labelCC.innerHTML = ''
            divInput.innerHTML = ''
            formGrupCC.innerHTML = '';
            $('#activCredito').hide();
            $('#tipoCred').show();
        }

        function tipoTraslado(value){
            var labelCC = document.getElementById('labelDCC');
            var divInput = document.getElementById('divInput');
            var formGrupCC = document.getElementById('formGrupCC');
            labelCC.innerHTML = ''
            divInput.innerHTML = ''
            formGrupCC.innerHTML = '';
            $('#activCredito').hide();
            $('#buttonMake').hide();
            if(value == 2){
                $('#rubEgr').show();
                $('#actividades').hide();
            } else if(value == 1) {
                $('#rubEgr').hide();
                $('#actividades').show();
            } else{
                $('#rubEgr').hide();
                $('#actividades').hide();
            }
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