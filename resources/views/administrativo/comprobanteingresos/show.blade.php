@extends('layouts.dashboard')
@section('titulo')
    Información del Comprobante de Ingresos
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Comprobante de Ingresos</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/CIngresos/'.$comprobante->vigencia_id) }}">Volver a Comprobante de Ingresos</a></li>
                <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">Comprobante de Ingresos {{ $comprobante->code }}</a></li>
                <li class="nav-item "><a class="tituloTabs" data-toggle="tab" href="#rubros">Dinero en Rubros</a></li>
            </ul>
        </div>
        <div class="col-lg-12 ">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row ">
                        {{ csrf_field() }}
                        <div class="col-sm-9"><h3>Concepto: {{ $comprobante->concepto }}</h3></div>
                        <div class="col-sm-3"><h4><b>Número del Comprobante:</b>&nbsp;{{ $comprobante->code }}</h4></div>
                        <br>
                        <br>
                        <div class="form-validation">
                            <hr>
                            <div class="col-lg-12 text-center">
                                <br>
                                <b>
                                    <h4><b>Valor del Comprobantes de Ingresos</b></h4>
                                    <h4><b>$<?php echo number_format( $comprobante->val_total,0) ?></b></h4>
                                </b>
                            </div>
                        </div>
                        <div class="col-md-12 align-self-center">

                            <hr>
                            <center>
                                <h3>Rubros del Comprobante de Ingresos</h3>
                            </center>
                            <hr>
                            <div class="table-responsive" id="prog">
                                @if($comprobante->rubros->count() == 0 )
                                    <div class="col-md-12 align-self-center">
                                        <div class="alert alert-danger text-center">
                                            El Comprobante no tiene rubros asigandos. Desea borrar el Comprobante? &nbsp;
                                            <form action="{{ url('/administrativo/CIngresos/'.$comprobante->vigencia_id.'/'.$comprobante->id.'/delete') }}" method="post" class="form">
                                                {!! method_field('DELETE') !!}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Borrar Comprobante
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                @endif
                                <form action="{{url('/administrativo/CIRubro')}}" method="POST" class="form">
                                    {{ csrf_field() }}
                                    <table id="tabla_rubros" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th scope="col" class="text-center">Rubros</th>
                                            <th scope="col" class="text-center"><i class="fa fa-trash-o"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($comprobante->estado != "3")
                                            @if($comprobante->rubros->count() == 0)
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td class="text-center">
                                                        <input type="hidden" name="comprobante_id" value="{{ $comprobante->id }}">
                                                        <select name="rubro_id[]" class="form-control" required>
                                                            @foreach($infoRubro as $rubro)
                                                                <option value="{{ $rubro['id_rubro'] }}">{{ $rubro['codigo'] }} - {{ $rubro['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center"></td>
                                                </tr>

                                            @endif
                                        @endif
                                        @if( $comprobante->rubros != null)
                                            @for($i = 0; $i < $comprobante->rubros->count(); $i++)
                                                @php($rubrosData = $comprobante->rubros[$i] )
                                                <tr>
                                                    <td class="text-center">
                                                        <button type="button" class="btn-sm btn-success" onclick="ver('fuente{{$i}}')" ><i class="fa fa-arrow-down"></i></button>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="col-lg-4">
                                                            <h4>
                                                                <b>{{ $rubrosData->rubros->name }}</b>
                                                            </h4>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <h4>
                                                                @foreach($infoRubro as $infoR)
                                                                    @if($infoR['id_rubro'] == $rubrosData->rubros->id)
                                                                        <b>Rubro: {{ $infoR['codigo'] }}</b>
                                                                    @endif
                                                                @endforeach
                                                            </h4>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            @php( $valorT = $comprobante->rubros->sum('valor') )
                                                            <h4>
                                                                <b>
                                                                    Valor:
                                                                    @if($comprobante->rubros->count() > 0)
                                                                        $<?php echo number_format( $comprobante->rubros->sum('valor') ,0) ?>
                                                                    @else
                                                                        $0.00
                                                                    @endif
                                                                </b>
                                                            </h4>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($comprobante->rubros->sum('valor') == 0)
                                                            <button type="button" class="btn-sm btn-danger" v-on:click.prevent="eliminarV({{ $rubrosData->id }})" ><i class="fa fa-trash-o"></i></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr id="fuente{{$i}}" style="display: none">
                                                    <td style="vertical-align: middle">
                                                        <b>
                                                            Fuentes del
                                                            @foreach($infoRubro as $infoR)
                                                                @if($infoR['id_rubro'] == $rubrosData->rubros->id)
                                                                    <b>Rubro: {{ $infoR['codigo'] }}</b>
                                                                @endif
                                                            @endforeach
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <div class="col-lg-12">
                                                            @foreach($rubrosData->rubros->fontsRubro as $fuentesRubro)
                                                                @if($comprobante->estado == "3")
                                                                    <div class="col-lg-6">
                                                                        <input type="hidden" name="fuente_id[]" value="{{ $fuentesRubro->id }}">
                                                                        <input type="hidden" name="comprobante_id" value="{{ $comprobante->id }}">
                                                                        @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                                                        <li style="list-style-type: none;">
                                                                            {{ $fuentesRubro->sourceFunding->description }} : $<?php echo number_format( $fuentesRubro->valor_disp,0) ?>
                                                                        </li>
                                                                    </div>
                                                                @elseif($fuentesRubro->valor_disp != 0)
                                                                    <div class="col-lg-6">
                                                                        <input type="hidden" name="fuente_id[]" value="{{ $fuentesRubro->id }}">
                                                                        <input type="hidden" name="comprobante_id" value="{{ $comprobante->id }}">
                                                                        @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                                                        <li style="list-style-type: none;">
                                                                            {{ $fuentesRubro->sourceFunding->description }} : $<?php echo number_format( $fuentesRubro->valor_disp,0) ?>
                                                                        </li>
                                                                    </div>
                                                                @endif
                                                                <div class="col-lg-6">
                                                                    @if($comprobante->estado == "3")
                                                                        Valor ingresado a {{ $fuentesRubro->sourceFunding->description}}:
                                                                        @if($comprobante->rubros != null)
                                                                            @foreach($comprobante->rubros as  $valoresFR)
                                                                                @php($id_rubros = $rubrosData->id )
                                                                                @if($valoresFR->comprobante_ingreso_id == $comprobante->id)
                                                                                    <input type="hidden" name="rubros_valor_id[]" value="{{ $valoresFR->id }}">
                                                                                    @if($comprobante->estado == "0")
                                                                                        <input type="number" required  name="valor[]" id="id{{$fuentesRubro->font_id}}" class="valor{{ $valoresFR->id }}" value="{{ $valoresFR->valor }}" min="0" style="text-align: center">
                                                                                    @else
                                                                                        $<?php echo number_format( $valoresFR->valor,0) ?>
                                                                                    @endif
                                                                                @endif
                                                                            @endforeach
                                                                            @if($comprobante->rubros->count() == 0)
                                                                                <input type="hidden" name="rubros_valor_id[]" value="">
                                                                                <input type="number" required  name="valor[]" class="form-group-sm" value="0" min="0" style="text-align: center">
                                                                            @endif
                                                                        @else
                                                                            <input type="hidden" name="rubros_valor_id[]" value="">
                                                                            <input type="number" required  name="valor[]" class="form-group-sm" value="0" min="0" style="text-align: center">
                                                                        @endif
                                                                    @elseif($fuentesRubro->valor_disp != 0)
                                                                        Valor ingresado a {{ $fuentesRubro->sourceFunding->description}}:
                                                                        @if($comprobante->rubros != null)
                                                                            @foreach($comprobante->rubros as  $valoresFR)
                                                                                @php($id_rubros = $rubrosData->id )
                                                                                @if($valoresFR->comprobante_ingreso_id == $comprobante->id)
                                                                                    <input type="hidden" name="rubros_valor_id[]" value="{{ $valoresFR->id }}">
                                                                                    @if($comprobante->estado == "0")
                                                                                        <input type="number" required  name="valor[]" id="id{{$fuentesRubro->font_id}}" class="valor{{ $valoresFR->id }}" value="{{ $valoresFR->valor }}" min="0" style="text-align: center">
                                                                                    @else
                                                                                        $<?php echo number_format( $valoresFR->valor,0) ?>
                                                                                    @endif
                                                                                @endif
                                                                            @endforeach
                                                                            @if($comprobante->rubros->count() == 0)
                                                                                <input type="hidden" name="rubros_valor_id[]" value="">
                                                                                <input type="number" required  name="valor[]" class="form-group-sm" value="0" min="0" style="text-align: center">
                                                                            @endif
                                                                        @else
                                                                            <input type="hidden" name="rubros_valor_id[]" value="">
                                                                            <input type="number" required  name="valor[]" class="form-group-sm" value="0" min="0" style="text-align: center">
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <b>Valor Total</b>
                                                        <br>
                                                        <b>
                                                            @if($comprobante->rubros != null)
                                                                $<?php echo number_format( $comprobante->rubros->sum('valor') ,0) ?>
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </b>
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endif
                                        </tbody>
                                    </table><br>
                                    <center>
                                        @if($comprobante->estado != 3)
                                            <button type="button" v-on:click.prevent="nuevaFilaPrograma" class="btn btn-success">Agregar Fila</button>
                                            <button type="submit" class="btn btn-primary">Guardar Rubros</button>
                                            @if($comprobante->rubros->sum('valor') > 0 )
                                            <a href="{{url('/administrativo/CIngresos/fin/3/'. $comprobante->id)}}" class="btn btn-danger">
                                                Finalizar comprobante
                                            </a>
                                            @endif
                                        @endif

                                    </center>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="rubros" class="tab-pane ">
                    <div class="card">
                        <br>
                        <center>
                            <h4><b>Dinero Disponible en Rubros</b></h4>
                        </center>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col" class="text-center">Rubro</th>
                                    <th scope="col" class="text-center">Concepto</th>
                                    <th scope="col" class="text-center">Dinero Disponible</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($valores as $valor)
                                    @foreach($infoRubro as $info)
                                        @if($valor['id_rubro'] == $info['id_rubro'])
                                            <tr>
                                                <td class="text-center">{{ $info['codigo'] }}</td>
                                                <td class="text-center">{{ $valor['name'] }}</td>
                                                <td class="text-center">$<?php echo number_format($valor['dinero'],0) ?></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>

        var visto = null;
        function ver(num) {
            obj = document.getElementById(num);
            obj.style.display = (obj==visto) ? 'none' : '';
            if (visto != null)
                visto.style.display = 'none';
            visto = (obj==visto) ? null : obj;
        }

        $(document).ready(function() {

            //$('.select-rubro').select2();

            $('#tabla_rubrosCdp').DataTable( {
                responsive: true,
                "searching": false,
                "ordering" : false
            } );

            $(document).on('click', '.borrar', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });

            new Vue({
                el: '#prog',

                methods:{

                    eliminar: function(dato){
                        var urlrubros = '/administrativo/CIngresos/'+dato;
                        axios.delete(urlrubros).then(response => {
                            location.reload();
                        });
                    },

                    eliminarV: function(dato){
                        var urlrubrosValor = '/administrativo/CIRubro/'+dato+'/delete';
                        axios.delete(urlrubrosValor).then(response => {
                            location.reload();
                        });
                    },

                    nuevaFilaPrograma: function(){
                        var nivel=parseInt($("#tabla_rubros tr").length);
                        $('#tabla_rubros tbody tr:first').after('<tr>\n' +
                            '                                <td>&nbsp;</td>\n' +
                            '                                <td class="text-center">\n' +
                            '                                    <input type="hidden" name="comprobante_id" value="{{ $comprobante->id }}">\n' +
                            '                                    <select name="rubro_id[]" class="form-group-lg" required>\n' +
                            '                                        @foreach($infoRubro as $rubro)\n' +
                            '                                            <option value="{{ $rubro['id_rubro'] }}">{{ $rubro['codigo'] }} - {{ $rubro['name'] }}</option>\n' +
                            '                                        @endforeach\n' +
                            '                                    </select>\n' +
                            '                                </td>\n' +
                            '                                <td class="text-center"><button type="button" class="btn-sm btn-danger borrar">&nbsp;-&nbsp; </button></td>\n' +
                            '                            </tr>');

                    }
                }
            });
        } );
    </script>
@stop