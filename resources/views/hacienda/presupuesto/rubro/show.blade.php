@extends('layouts.dashboard')
@section('titulo')
    Información del Rubro
@stop
@section('sidebar')
{{--
   <li> <a href="{{ url('/presupuesto') }}" class="btn btn-success"><i class="fa fa-money"></i><span class="hide-menu">&nbsp; Presupuesto</span></a></li>
    <div class="card">

    </div>
    @if( $rol == 2)
    <li class="dropdown">
        <a class="dropdown-toggle btn btn btn-primary" data-toggle="dropdown">
            ACCIONES
            <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            <li>
                <a data-toggle="modal" data-target="#adicion" class="btn btn-primary text-left">Adición</a>
            </li>
            <li>
                <a data-toggle="modal" data-target="#reduccion" class="btn btn-primary text-left">Reducción</a>
            </li>
            <li>
                <a data-toggle="modal" data-target="#credito" class="btn btn-primary text-left">Credito</a>
            </li>
        </ul>
    </li>
    @endif --}}
@stop
@section('content')

    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Detalles del Rubro: {{ $rubro->name }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        @if($vigens->tipo == 1)
            <li class="nav-item">
                <a class="nav-link regresar"  href="{{ url('/presupuestoIng/') }}">Volver a Presupuesto</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#datos"> Datos Básicos Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#fuentes"> Fuentes del Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#movimientos"> Movimientos del Rubro </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link regresar"  href="{{ url('/presupuesto/') }}">Volver a Presupuesto</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" data-toggle="pill" href="#datos"> Datos Básicos Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#fuentes"> Fuentes del Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#cdp"> CDP's del Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#registros"> Registros del Rubro </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" data-toggle="pill" href="#movimientos"> Movimientos del Rubro </a>
            </li>
        @endif
        @if( $rol == 2)
            <li class="dropdown">
                <a class="nav-item dropdown-toggle" data-toggle="dropdown" href="#">Acciones<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a data-toggle="modal" data-target="#adicion" class="btn btn-drop text-left">Adición</a>
                    </li>
                    <li>
                        <a data-toggle="modal" data-target="#reduccion" class="btn btn-drop  text-left">Reducción</a>
                    </li>
                    @if($vigens->tipo != 1)
                        <li>
                            <a data-toggle="modal" data-target="#credito" class="btn btn-drop  text-left">Credito</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
    </ul>

    <div class="col-lg-12 " style="background-color:white;">
        <div class="tab-content">
            <div id="datos" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane fade in active">
                <div class="row justify-content-center">
                    <center><h2>{{ $rubro->name }}</h2></center><br>
                </div>
                <div class="form-validation">
                    <form class="form" action="">
                        <hr>
                        {{ csrf_field() }}
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="control-label text-right col-md-4" for="nombre">Nombre:</label>
                                <div class="col-lg-6">
                                    <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $rubro->name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label text-right col-md-4" for="valor">Codigo Rubro:</label>
                                <div class="col-lg-6">
                                    <input type="text" disabled class="form-control" style="text-align:center" name="valor" value="{{ $rubro->cod }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="form-group">
                                <label class="control-label text-right col-md-4" for="nombre">Subproyecto:</label>
                                <div class="col-lg-6">
                                    <input type="text" disabled class="form-control" name="name" style="text-align:center" value="{{ $rubro->SubProyecto->name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label text-right col-md-4" for="valor">Vigencia:</label>
                                <div class="col-lg-6">
                                    <input type="number" disabled class="form-control" style="text-align:center" name="valor" value="{{ $rubro->vigencia->vigencia }}">
                                </div>
                            </div>
                            <br><br>
                        </div>
                        <div class="col-md-12 align-self-center">
                            <div class="row">
                                <br> <br>
                                @if($vigens->tipo != 1)
                                    <div class="col-lg-6 form-group">
                                        <center><h4><b>Valor Total del Rubro</b></h4></center>
                                        <div class="text-center">$ <?php echo number_format($valor,0);?>.00</div>
                                        <br>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <center><h4><b>Valor Disponible del Rubro</b></h4></center>
                                        <div class="text-center">
                                            $ <?php echo number_format($valorDisp,0);?>.00
                                        </div>
                                    </div>
                                @else
                                    <div class="col-lg-4 form-group">
                                        <center><h4><b>Valor Asignado al Rubro</b></h4></center>
                                        <div class="text-center">$ <?php echo number_format($valor,0);?>.00</div>
                                        <br>
                                    </div>
                                    <div class="col-lg-4 form-group">
                                        <center><h4><b>Valor Total Recaudado</b></h4></center>
                                        <div class="text-center">
                                            $ <?php echo number_format($rubro->compIng->sum('valor'),0);?>.00
                                        </div>
                                    </div>
                                    <div class="col-lg-4 form-group">
                                        <center><h4><b>Saldo Por Recaudar</b></h4></center>
                                        <div class="text-center">
                                            $ <?php echo number_format($valor - $rubro->compIng->sum('valor'),0);?>.00
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="fuentes" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane">
                <br>
                <center><h2>Fuentes del Rubro</h2></center>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaFuentesR">
                        <thead>
                        <tr>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Valor Inicial</th>
                            @if($vigens->tipo != 1)
                                <th class="text-center">Valor Disponible</th>
                            @else
                                <th class="text-center">Valor Actual</th>
                            @endif
                            <th class="text-center">Valor Disponible Asignación</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($fuentesR as  $fuentes)
                            <tr>
                                <td>{{ $fuentes->sourceFunding->code }}</td>
                                <td>{{ $fuentes->sourceFunding->description }}</td>
                                <td class="text-center">$ <?php echo number_format($fuentes['valor'],0);?>.00</td>
                                <td class="text-center">$ <?php echo number_format($fuentes['valor_disp'],0);?>.00</td>
                                <td class="text-center">$ <?php echo number_format($fuentes['valor_disp_asign'],0);?>.00</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if( $rol == 3 or $rol == 1)
                    <br><center><h2>ASIGNACIÓN DE DINERO A DEPENDENCIAS</h2></center>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaAsignarDineroDep">
                            <thead>
                            <tr>
                                <th class="text-center">Dependencia</th>
                                <th class="text-center">Valor Tomado de Fuentes</th>
                                <th class="text-center"><i class="fa fa-cogs"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dependencias as  $dependencia)
                                <tr>
                                    <td>{{ $dependencia->num }} - {{ $dependencia->name }}</td>
                                    <td class="text-center">
                                        @foreach($fuentesR as  $fuentes)
                                            {{ $fuentes->sourceFunding->code }}
                                            {{ $fuentes->sourceFunding->description }}<br>
                                            @if(count($fuentes->dependenciaFont) > 0)
                                                @foreach($fuentes->dependenciaFont as $depFont)
                                                    @if($depFont->dependencia_id == $dependencia->id)
                                                        $ <?php echo number_format($depFont->value,0);?>.00
                                                    @endif
                                                @endforeach
                                            @else
                                                0 $
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @foreach($fuentesR as  $fuentes)
                                            @if(count($fuentes->dependenciaFont) > 0)
                                                @php($bandera = false)
                                                @foreach($fuentes->dependenciaFont as $depFont)
                                                    @if($depFont->dependencia_id == $dependencia->id)
                                                        @php($bandera = true)
                                                        <button onclick="getModalDependencia({{$dependencia->id}}, '{{$dependencia->name}}', {{ $depFont->value }}, {{$fuentes->id}}, {{ $fuentes->sourceFunding->id }}, {{ $fuentes->valor_disp_asign }}, {{$depFont->id}})" class="btn btn-success">{{ $fuentes->sourceFunding->description }}</button>
                                                    @endif
                                                @endforeach
                                                @if($bandera == false and $fuentes->valor_disp_asign > 0)
                                                    <button onclick="getModalDependencia({{$dependencia->id}}, '{{$dependencia->name}}', 0, {{$fuentes->id}}, {{ $fuentes->sourceFunding->id }}, {{ $fuentes->valor_disp_asign }}, 0)" class="btn btn-success">{{ $fuentes->sourceFunding->description }}</button>
                                                @endif
                                            @else
                                                <button onclick="getModalDependencia({{$dependencia->id}}, '{{$dependencia->name}}', 0, {{$fuentes->id}}, {{ $fuentes->sourceFunding->id }}, {{ $fuentes->valor_disp_asign }}, 0)" class="btn btn-success">{{ $fuentes->sourceFunding->description }}</button>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div id="cdp" class="col-xs-12 col-sm-12 col-md-12 col-lg-12  tab-pane">
                <center><h2>CDP's Asignados al Rubro</h2></center>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaCDPs">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Estado Actual</th>
                            <th class="text-center">Valor Inicial</th>
                            <th class="text-center">Valor Disponible</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rubro->rubrosCdp as  $data)
                            <tr class="text-center">
                                <td><a href="{{ url('administrativo/cdp/'. $data->cdps->vigencia_id.'/'.$data->cdps->id) }}">{{ $data->cdps->code }}</a></td>
                                <td>{{ $data->cdps->name }}</td>
                                <td>
                                    <span class="badge badge-pill badge-danger">
                                        @if( $data->cdps->jefe_e == "0")
                                            Pendiente
                                        @elseif( $data->cdps->jefe_e == "1")
                                            Rechazado
                                        @elseif( $data->cdps->jefe_e == "2")
                                            Anulado
                                        @elseif( $data->cdps->jefe_e == "3")
                                            Aprobado
                                        @else
                                            En Espera
                                        @endif
                                    </span>
                                </td>
                                <td>$ <?php echo number_format($data->cdps->valor,0);?>.00</td>
                                <td>$ <?php echo number_format( $data->cdps->saldo,0);?>.00</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="registros" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane">
                <center>
                    <h2>Registros Asignados al Rubro</h2>
                </center>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaRegistros">
                        <thead>
                        <tr>
                            <th class="text-center">Id</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Valor Inicial</th>
                            <th class="text-center">Valor Disponible</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rubro->cdpRegistroValor as  $data2)
                            <tr class="text-center">
                                <td><a href="{{ url('administrativo/registros/show/'.$data2->registro_id) }}">{{ $data2->registro->code }}</a></td>
                                <td>{{ $data2->registro->objeto }}</td>
                                <td>$ <?php echo number_format($data2->valor,0);?>.00</td>
                                <td>$ <?php echo number_format( $data2->saldo,0);?>.00</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
     
            <div id="movimientos" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 tab-pane">
                <center><h2>Movimientos del Rubro</h2></center>
                <br>
                <div class="col-md-8 align-self-center">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaMovimientos">
                            <thead>
                            <tr>
                                <th class="text-center">Id</th>
                                <th class="text-center">Nombre Fuente</th>
                                <th class="text-center">Valor Inicial</th>
                                <th class="text-center">Adición</th>
                                <th class="text-center">Reducción</th>
                                @if($vigens->tipo != 1)
                                    <th class="text-center">Credito</th>
                                    <th class="text-center">Contra Credito</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fuentesR as $fuentes)
                                <tr>
                                    <td>{{ $fuentes->id }}</td>
                                    <td>{{ $fuentes->sourceFunding->description }}</td>
                                    <td class="text-center">$ <?php echo number_format($fuentes['valor'],0);?>.00</td>
                                    <td class="text-center">
                                        @foreach($valores as $valAdd)
                                            @if($fuentes->font_vigencia_id == $valAdd['id'])
                                                $ <?php echo number_format($valAdd['adicion'],0);?>.00
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @foreach($valores as $valAdd)
                                            @if($fuentes->font_vigencia_id == $valAdd['id'])
                                                $ <?php echo number_format($valAdd['reduccion'],0);?>.00
                                            @endif
                                        @endforeach
                                    </td>
                                    @if($vigens->tipo != 1)
                                        <td class="text-center">
                                            @foreach($valores as $valAdd)
                                                @if($fuentes->font_vigencia_id == $valAdd['id'])
                                                    $ <?php echo number_format($valAdd['credito'],0);?>.00
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            @foreach($valores as $valAdd)
                                                @if($fuentes->font_vigencia_id == $valAdd['id'])
                                                    $ <?php echo number_format($valAdd['ccredito'],0);?>.00
                                                @endif
                                            @endforeach
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 align-self-center">
                    @if($files != 0)
                        <center>
                            <h3>Resoluciones del Rubro</h3>
                        </center>
                        <br>
                        <div class="input-group">
                            <div class="row text-center">
                                @foreach($files as $file)
                                    @if($file['mov'] == 1)
                                        <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Credito y Contracredito</a>
                                    @elseif($file['mov'] == 2)
                                        <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o">&nbsp; Adición</i></a>
                                    @elseif($file['mov'] == 3)
                                        <a href="{{Storage::url($file['ruta'])}}" title="Ver" class="btn btn-success"><i class="fa fa-file-pdf-o">&nbsp; Reducción</i></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <center>
                            <h3>El rubro no ha recibido ningun movimiento</h3>
                            <br>
                            <a href="{{ url('administrativo/cdp/') }}" class="btn btn-primary btn-block m-b-12 disabled">VER TODOS LOS MOVIMIENTOS</a>
                        </center>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('modal.adicionRubro')
    @include('modal.reduccionRubro')
    @include('modal.creditoRubro')
    @include('modal.asignarDineroDep')
    @stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script src="{{ asset('/js/datatableRubro.js') }}"></script>
    <script>

        const fuentesR = @json($fuentesR);

        function getModalDependencia(id, name, value, fuenteRid, fuente_id, valorDisp, depFontID){
            document.getElementById("nameDep").innerHTML = name;
            $('#idDep').val(id);
            $('#fuenteRid').val(fuenteRid);
            $('#depFontID').val(depFontID);
            $('#asignarDineroDep').modal('show');
            $("#bodyTableFonts").html("");

            var tr = `<tr>
            <td><input type="hidden" name="fuente_id[]" value="`+ fuente_id +`"><input type="number" required  name="valorAsignar[]" class="form-control" min="0" value="`+ value +`" max="`+ valorDisp +`" style="text-align: center"></td>
            </tr>`;

            $("#bodyTableFonts").append(tr)
        }

        var visto = null;
        function ver(num) {
            obj = document.getElementById(num);
            obj.style.display = (obj==visto) ? 'none' : '';
            if (visto != null)
                visto.style.display = 'none';
            visto = (obj==visto) ? null : obj;
        }

        new Vue({
            el: '#add',

            methods:{

                eliminar: function(dato){
                    var urlrubrosCdp = '/administrativo/rubrosCdp/'+dato;
                    axios.delete(urlrubrosCdp).then(response => {
                        location.reload();
                });
                },

                eliminarV: function(dato){
                    var urlrubrosCdpValor = '/administrativo/rubrosCdp/valor/'+dato;
                    axios.delete(urlrubrosCdpValor).then(response => {
                        location.reload();
                });
                },
            },
        });

        new Vue({
            el: '#prog',
            methods:{
                eliminarDatos2: function(dato2){
                    var urlVigencia2 = '/pdd/programa/'+dato2;
                    axios.delete(urlVigencia2).then(response => {
                        location.reload();
                    });
                },

                nuevaFilaPrograma: function(){
                    var nivel=parseInt($("#tabla_rubrosCdp tr").length);
                    $('#tabla_rubrosCdp tbody tr:first').before('<td>\n' +
                        '                                  <div class="col-lg-12">\n' +
                        '                                      @foreach($fuentesR as $fuentesRubro)\n' +
                '                                                  <input type="hidden" name="rubro_Mov_id[]" value="{{ $fuentesRubro->id }}">\n' +
                '                                                  <input type="number" required  name="valorRed{{ $fuentesRubro->id }}[]" class="form-group-sm" value="0" style="text-align: center">\n' +
                        '                                      @endforeach\n' +
                        '                                  </div>\n' +
                        '                              </td>');

                }
            }
        });
    </script>
@stop
