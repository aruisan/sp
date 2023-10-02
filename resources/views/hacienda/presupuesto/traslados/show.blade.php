@extends('layouts.dashboard')
@section('titulo')@if($rubroMov->movimiento == "1") Traslado @elseif($rubroMov->movimiento == "2") Adición @elseif($rubroMov->movimiento == "3") Reducción @endif {{ $año }} @stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>@if($rubroMov->movimiento == "1") Traslado @if($depCC->bpinVig) Inversión @else Funcionamiento @endif @elseif($rubroMov->movimiento == "2") Adición @elseif($rubroMov->movimiento == "3") Reducción @endif
                        - {{ \Carbon\Carbon::parse($rubroMov->created_at)->format('d/m/y') }} - $<?php echo number_format($rubroMov->valor,0) ?></b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/presupuesto/traslados/'.$año) }}"><i class="fa fa-arrow-left"></i> Volver a Movimientos</a>
                </li>
                <li class="nav-item active"><a class="nav-link" href="#nuevo" ><i class="fa fa-home"></i></a></li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                    <div class="form-validation">
                        @if($rubroMov->movimiento == "1")
                            <div class="row">
                                <br>
                                <div class="col-md-6 align-self-center text-center">
                                    <h4>Contra Acreditado</h4>
                                    Rubro: {{$depCC->fontRubro->rubro->cod}} - {{$depCC->fontRubro->rubro->name}}
                                    <br>
                                    Fuente: {{$depCC->fontRubro->sourceFunding->code }} - {{$depCC->fontRubro->sourceFunding->description }}
                                    <br>
                                    Dependencia: {{ $depCC->dependencias->name }}
                                    @if($depCC->bpinVig)
                                        <br>
                                        Proyecto: {{ $depCC->bpinVig->bpin->cod_proyecto }} - {{ $depCC->bpinVig->bpin->nombre_proyecto }}
                                        <br>
                                        Actividad: {{ $depCC->bpinVig->bpin->cod_actividad }} - {{ $depCC->bpinVig->bpin->actividad }}
                                    @endif
                                </div>
                                <div class="col-md-6 align-self-center text-center">
                                    <h4>Acreditado</h4>
                                    Rubro: {{$depCred->fontRubro->rubro->cod}} - {{$depCred->fontRubro->rubro->name}}
                                    <br>
                                    Fuente: {{$depCred->fontRubro->sourceFunding->code }} - {{$depCred->fontRubro->sourceFunding->description }}
                                    <br>
                                    Dependencia: {{ $depCred->dependencias->name }}
                                    @if($depCred->bpinVig)
                                        <br>
                                        Proyecto: {{ $depCred->bpinVig->bpin->cod_proyecto }} - {{ $depCred->bpinVig->bpin->nombre_proyecto }}
                                        <br>
                                        Actividad: {{ $depCred->bpinVig->bpin->cod_actividad }} - {{ $depCred->bpinVig->bpin->actividad }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-12 align-self-center text-center">
                                    <h4>
                                        <i class="fa fa-arrow-right" style="color: green"></i>&nbsp;
                                        $<?php echo number_format($rubroMov->valor,0) ?>
                                        &nbsp;<i class="fa fa-arrow-right" style="color: green"></i>
                                    </h4>
                                    <hr>
                                    <h4>Resolución</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 align-self-center text-center">
                                            @foreach($files as $file)
                                                @if($file['mov'] == 1)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Traslado - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 2)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Adición - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 3)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Reducción - {{ $file['fecha'] }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($rubroMov->movimiento == "2")
                            <div class="row">
                                <div class="col-md-12 align-self-center text-center">
                                    <h4>Acreditado</h4>
                                    @if($depCred->dependencia_id)
                                        Rubro: {{$depCred->fontRubro->rubro->cod}} - {{$depCred->fontRubro->rubro->name}}
                                        <br>
                                        Fuente: {{$depCred->fontRubro->sourceFunding->code }} - {{$depCred->fontRubro->sourceFunding->description }}
                                        <br>
                                        Dependencia: {{ $depCred->dependencias->name }}
                                    @else
                                        Rubro: {{$depCred->rubro->cod}} - {{$depCred->rubro->name}}
                                        <br>
                                        Fuente: {{$depCred->sourceFunding->code }} - {{$depCred->sourceFunding->description }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-12 align-self-center text-center">
                                    <h4>
                                        <i class="fa fa-arrow-up" style="color: green"></i>&nbsp;
                                        $<?php echo number_format($rubroMov->valor,0) ?>
                                        &nbsp;<i class="fa fa-arrow-up" style="color: green"></i>
                                    </h4>
                                    <hr>
                                    <h4>Resolución</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 align-self-center text-center">
                                            @foreach($files as $file)
                                                @if($file['mov'] == 1)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Traslado - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 2)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Adición - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 3)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Reducción - {{ $file['fecha'] }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($rubroMov->movimiento == "3")
                            <div class="row">
                                <div class="col-md-12 align-self-center text-center">
                                    <h4>Reducido</h4>
                                    @if($depCred->dependencia_id)
                                        Rubro: {{$depCred->fontRubro->rubro->cod}} - {{$depCred->fontRubro->rubro->name}}
                                        <br>
                                        Fuente: {{$depCred->fontRubro->sourceFunding->code }} - {{$depCred->fontRubro->sourceFunding->description }}
                                        <br>
                                        Dependencia: {{ $depCred->dependencias->name }}
                                    @else
                                        Rubro: {{$depCred->rubro->cod}} - {{$depCred->rubro->name}}
                                        <br>
                                        Fuente: {{$depCred->sourceFunding->code }} - {{$depCred->sourceFunding->description }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-12 align-self-center text-center">
                                    <h4>
                                        <i class="fa fa-arrow-down" style="color: green"></i>&nbsp;
                                        $<?php echo number_format($rubroMov->valor,0) ?>
                                        &nbsp;<i class="fa fa-arrow-down" style="color: green"></i>
                                    </h4>
                                    <hr>
                                    <h4>Resolución</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12 align-self-center text-center">
                                            @foreach($files as $file)
                                                @if($file['mov'] == 1)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Traslado - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 2)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Adición - {{ $file['fecha'] }}</a>
                                                @elseif($file['mov'] == 3)
                                                    <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Reducción - {{ $file['fecha'] }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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

        //tras-add-red
        function mov(value){
            var labelCC = document.getElementById('labelDCC');
            var divInput = document.getElementById('divInput');
            var formGrupCC = document.getElementById('formGrupCC');
            labelCC.innerHTML = ''
            divInput.innerHTML = ''
            formGrupCC.innerHTML = '';
            $('#tipoCred').show();
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
                        divInput.innerHTML = '<input type="number" class="form-control" name="dineroCC" id="dineroCC" value="'+datos+'"' +
                            'max="'+datos+'" min="1">'
                        formGrupCC.innerHTML = '<br><h4 class="text-center">SALDO ACTUAL: '+datos+'</h4><br>';
                        $('#rubCredito').show();
                    }
                }

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL CONSULTAR EL RUBRO.');
                $("#cargando").hide();
            });
        }

        function rubroCred(value){
            if(value != 0) $('#buttonMake').show();
            else $('#buttonMake').hide();
        }
    </script>
@stop