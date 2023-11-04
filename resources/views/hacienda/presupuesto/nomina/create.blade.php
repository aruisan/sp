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
                        <form class="form-valide" action="{{url('/nominapre/makeNomina')}}" method="POST" enctype="multipart/form-data">
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
                                        <select name="mes" id="mes" class="form-control" required onchange="mesFind(this.value)">
                                            <option value="0">Seleccione el mes a elaborar</option>
                                            <option value="1">ENERO</option>
                                            <option value="2">FEBRERO</option>
                                            <option value="3">MARZO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 align-self-center">
                                    <div class="form-group">
                                        <select name="tipo" id="tipo" class="form-control" required onchange="tipoNom(this.value)">
                                            <option value="0">Seleccione el tipo de nomina</option>
                                            <option value="1">EMPLEADOS</option>
                                            <option value="2">MESADAS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <div class="form-group row" id="buttonMake" style="display: none">
                                    <div class="col-lg-12 ml-auto text-center">
                                       <a class="btn btn-primary" onclick="elaborarNomina()">Elaborar Nomina</a>
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
        function mesFind(value){
            var tipo = document.getElementById('tipo').value;
            if(tipo > 0) validateNom();
        }

        function tipoNom(value){
            var mes = document.getElementById('mes').value;
            if(mes > 0) validateNom();
        }

        function validateNom(){
            $("#cargando").show();
            var tipo = document.getElementById('tipo').value;
            var mes = document.getElementById('mes').value;
            if (tipo == 1) tipo = "EMPLEADOS";
            else tipo = "MESADAS";

            $.ajax({
                method: "POST",
                url: "/nominapre/findNomina",
                data: { "tipo": tipo, "año": año, "mes": mes,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                if(datos == 0) {
                    toastr.warning('YA HAY UNA NOMINA ELABORADA DE ESOS MESES O NO HA SIDO GENERADA.');
                    $("#buttonMake").hide();
                }
                else $("#buttonMake").show();

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL BUSCAR LA NOMINA.');
                $("#cargando").hide();
            });
        }

        function elaborarNomina(){
            $("#cargando").show();
            var tipo = document.getElementById('tipo').value;
            var mes = document.getElementById('mes').value;
            if (tipo == 1) tipo = "EMPLEADOS";
            else tipo = "MESADAS";

            $.ajax({
                method: "POST",
                url: "/nominapre/makeNomina",
                data: { "tipo": tipo, "año": año, "mes": mes,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                if(datos == 0) {
                    toastr.warning('YA HAY UNA NOMINA ELABORADA DE ESOS MESES O NO HA SIDO GENERADA.');
                    $("#buttonMake").hide();
                }
                else $("#buttonMake").show();

                $("#cargando").hide();
            }).fail(function() {
                toastr.warning('SE PRESENTO UN ERROR AL ELABORAR LA NOMINA.');
                $("#cargando").hide();
            });

            console.log(tipo,mes);
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })
    </script>
@stop