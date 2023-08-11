@extends('layouts.dashboard')
@section('titulo')Creación de Movimiento {{ $año }} @stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>NUEVO MOVIMIENTO {{ $año }}</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/presupuesto/traslados/'.$año) }}">Volver a Traslados</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >NUEVO MOVIMIENTO</a>
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
                                <div class="col-md-12 align-self-center" id="tipoCred" style="display: none">
                                    <div class="form-group">
                                        <select name="tipTras" id="tipTras" class="form-control" required onchange="tipoTraslado(this.value)">
                                            <option value="0">Seleccione el tipo de traslado a realizar</option>
                                            <option value="1">INVERSIÓN</option>
                                            <option value="2">FUNCIONAMIENTO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center" id="rubEgr">
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" for="fontRubEgr">Seleccione el rubro que sera afectado <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <select name="fontRubEgr" id="fontRubEgr" class="form-control" required onchange="rubroEgr(this.value)">
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
                                <div class="col-md-12 align-self-center" id="dCC">
                                    <div class="form-group text-center" id="formGrupCC"></div>
                                    <div class="form-group">
                                        <label class="col-lg-4 col-form-label text-right" id="labelDCC"></label>
                                        <div class="col-lg-6" id="divInput"></div>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <div class="form-group row">
                                    <div class="col-lg-12 ml-auto text-center">
                                       <!--  <button type="submit" class="btn btn-primary">Guardar</button> -->
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

        function mov(value){
            var tipo = document.getElementById('prep').value;
            if(tipo == 0){
                $('#tipoCred').show();
            } else{
                $('#tipoCred').hide();
            }

        }

        function tipoTraslado(value){
            if(value == 2){
                $('#rubEgr').show();
            } else if(value == 1) {
                $('#rubEgr').hide();
            } else{
                $('#rubEgr').hide();
            }
        }

        function rubroEgr(value){
            $("#cargando").show();
            var tipoTras = document.getElementById('movEgr').value;

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
                        divInput.innerHTML = '<input type="number" class="form-control" name="dineroCC" id="dineroCC" value="'+datos+'"' +
                            'max="'+datos+'" min="1">'
                        formGrupCC.innerHTML = '<br><h4 class="text-center">SALDO ACTUAL: '+datos+'</h4><br>';
                    }
                    console.log(datos);
                }

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL CONSULTAR EL RUBRO.');
                $("#cargando").hide();
            });
        }
    </script>
@stop