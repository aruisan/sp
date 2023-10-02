@extends('layouts.dashboard')
@section('titulo')
    Crear Registros
@stop
@section('sidebar')
    {{-- <li>
        <a href="{{route('registros.index')}}" class="btn btn-success">
            <span class="hide-menu"> Registros</span></a>
    </li> --}}

@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Nuevo Registro</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link regresar"  href="{{url('administrativo/registros/'.$id) }}">Volver a Registros</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link " data-toggle="pill" href="#cdpDisp">Creación de Registro </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="#valor">Consultar Dinero CDP'S</a>
        </li>

    </ul>


    <div class="col-lg-12 " style="background-color:white;">
        <div class="tab-content">

            <div id="cdpDisp" class="tab-pane fade in active">
                <hr>
                <center>
                    <h3>CDP's Disponibles</h3>
                </center>
                <hr>


                <br>
                {!! Form::open(array('route' => 'registros.store','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                <div class="table-responsive">
                    <input type="hidden" name="numReg" value="{{ $registrocount }}">
                    <table id="tabla_rubrosCdp" class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">CDP's</th>
                            <th scope="col" class="text-center"><i class="fa fa-trash-o"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-center">
                                <select name="cdp_id[]" id="cdps" class="form-control selectF" required onchange="ShowSelected()">
                                    <option>Seleccione el CDP</option>
                                    @foreach($cdps as $cdp)
                                        @php($cdp['name'] = preg_replace("/[\r\n|\n|\r]+/", " ", $cdp['name']))
                                        <option value="{{ $cdp['id'] }}">{{ $cdp['code'] }} - {{ $cdp['name'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                    <center>
                        <div id="prog">
                            <button type="button" v-on:click.prevent="nuevaFilaPrograma" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Agregar Otro CDP</button>
                        </div>
                    </center>
                </div>
                <hr>
                <center>
                    <h3>Información del Registro</h3>
                </center>
                <hr>
                <input type="hidden" name="fecha" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
                <input type="hidden" name="secretaria_e" value="0">
                <input type="hidden" name="vigencia" value="{{ $id }}">

                <div class="row">
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
                        <label>Tercero: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <select class="select-tercero" name="persona_id">
                                @foreach($personas as $persona)
                                    <option value="{{$persona->id}}">{{$persona->num_dc}} - {{$persona->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="form-text text-muted">Relacionar persona</small>
                    </div>

                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label>Objeto: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                            <textarea name="objeto" class="form-control" id="objeto"></textarea>
                        </div>
                        <small class="form-text text-muted">Nombre del registro</small>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <label>Tipo de Documento: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                            <select name="tipo_doc" class="form-control" onchange="changeTipoDoc(this.value)">
                                <option>Seleccione una opción</option>
                                <option value="Contrato">Contrato</option>
                                <option value="Factura">Factura</option>
                                <option value="Resolución">Resolución</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <small class="form-text text-muted">Tipo de Documento</small>
                    </div>

                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6" id="tipo_doc_text_3">
                        <label>Fecha del Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            <input type="date" class="form-control" name="fecha_tipo_doc" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
                        </div>
                        <small class="form-text text-muted"> Fecha del Tipo de Documento</small>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6" style="display: none" id="tipo_doc_text">
                        <label>Cual Otro? </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-question" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="tipo_doc_text">
                        </div>
                        <small class="form-text text-muted"> Cual Otro Tipo de Documento?</small>
                    </div>

                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6" id="tipo_doc_text_2">
                        <label>Número de Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-hashtag" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="num_tipo_doc" placeholder="Número de Documento">
                        </div>
                        <small class="form-text text-muted"> Número del Tipo de Documento</small>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <label>Subir Archivo: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                            <input type="file" name="file" accept="application/pdf" class="form-control">
                        </div>
                    </div>
                </div>

                <div id="infoContrato" style="display: none">
                    <hr>
                    <center>
                        <h3>Información del Contrato</h3>
                    </center>
                    <hr>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Tipo de Contrato </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                <select name="tipo_contrato" class="form-control">
                                    <option value="3">3 - DE OBRA PUBLICA</option>
                                    <option value="4">4 - DE CONSULTORIA</option>
                                    <option value="5">5 - DE INTERVENTORIA</option>
                                    <option value="6">6 - DE SUMINISTRO</option>
                                    <option value="7">7 - TRANSPORTE PASAJEROS TERRESTRE, HOTEL Y RESTAURANTE</option>
                                    <option value="8">8 - TRANSPORTE DE CARGA</option>
                                    <option value="10">10 - DE PRESTACION DE SERVICIOS</option>
                                    <option value="11">11 - DE ENCARGO FIDUCIARIO Y FIDUCIA PUBLICA</option>
                                    <option value="12">12 - ALQUILER O ARRENDAMIENTO</option>
                                    <option value="13">13 - DE CONCESION</option>
                                    <option value="20">20 - DEUDA PUBLICA</option>
                                    <option value="21">21 - CONVENIO INTERADMINISTRATIVO</option>
                                    <option value="22">22 - OTROS NO ESPECIFICADOS ANTERIORMENTE</option>
                                </select>
                            </div>
                            <small class="form-text text-muted"> Seleccione el tipo de contrato.</small>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Modalidad de Seleccion </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                <select name="mod_seleccion" class="form-control">
                                    <option value="0">NO APLICA</option>
                                    <option value="1">1 - LICITACION PUBLICA</option>
                                    <option value="2">2 - CONCURSO DE MERITOS</option>
                                    <option value="3">3 - SELECCION ABREVIADA</option>
                                    <option value="4">4 - CONTRATACION DIRECTA</option>
                                    <option value="8">8 - CUANTIA MINIMA</option>
                                </select>
                            </div>
                            <small class="form-text text-muted"> Seleccione la modalidad de selección.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <label>Estado de Ejecución </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                                <select name="estado_ejec" class="form-control">
                                    <option value="0">NO APLICA</option>
                                    <option value="1">1 - EJECUCION</option>
                                    <option value="2">2 - LIQUIDADO</option>
                                    <option value="3">3 - SUSPENDIDO</option>
                                </select>
                            </div>
                            <small class="form-text text-muted"> Seleccione el estado de ejecución.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                    <center>
                        <button type="submit" class="btn btn-primary btn-raised btn-lg" id="storeRegistro">Guardar</button>
                    </center>
                </div>
                {!! Form::close() !!}
            </div>

            <div class="tab-pane" id="valor" style="background-color:white;">
                <br>
                <center>
                    <h4><b>Dinero en los CDP's</b></h4>
                </center>
                <br>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Dinero</th>
                        </tr    >
                        @foreach($cdps as $cdp)
                            <tr class="text-center">
                                <td>{{ $cdp['code'] }} - {{ $cdp['name'] }}</td>
                                <td>$<?php echo number_format($cdp['saldo'],0) ?></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>

        $('.select-tercero').select2();

        function changeTipoDoc(value){
            var obj= document.getElementById('tipo_doc_text');
            var contratosView= document.getElementById('infoContrato');

            if(value=='Otro'){
                obj.style.display='inline';
                contratosView.style.display='none';
            }else{
                obj.style.display='none';
                if(value == "Contrato"){
                    contratosView.style.display='inline';
                } else {
                    contratosView.style.display='none';
                }
            }
        }

        function ShowSelected()
        {

            /* Para obtener el texto */
            var combo = document.getElementById("cdps");
            var selected = combo.options[combo.selectedIndex].text;
            var count = selected.indexOf('-') + 2;
            var result = selected.substr(count);
            var input = document.getElementById("objeto");
            input.value = result;
        }

        var visto = null;

        function ver(num) {
            obj = document.getElementById(num);
            obj.style.display = (obj==visto) ? 'none' : '';
            if (visto != null)
                visto.style.display = 'none';
            visto = (obj==visto) ? null : obj;
        }

        $(document).ready(function() {

            new Vue({
                el: '#prog',

                methods:{

                    nuevaFilaPrograma: function(){
                        $('#tabla_rubrosCdp tbody tr:last').after('<tr>\n' +
                            '                <td class="text-center">\n' +
                            '                    <select name="cdp_id[]" class="form-control selectF" required>\n' +
                            '                        @foreach($cdps as $cdp)\n' +
                            '                            <option value="{{ $cdp["id"] }}">{{ $cdp["code"] }} - {{ $cdp["name"] }}</option>\n' +
                            '                        @endforeach\n' +
                            '                    </select>\n' +
                            '                </td>\n' +
                            '                <td class="text-center"><button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button></td>\n' +
                            '            </tr>');

                    }
                }
            });

            $('.selectF').select2();

            $('#tabla_rubrosCdp').DataTable( {
                responsive: true,
                "searching": false,
                "ordering" : false
            } );


            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });

        } );


    </script>
@stop