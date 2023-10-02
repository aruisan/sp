
<div id="creditoRubs" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{url('/presupuesto/rubro/m/1/'.$rubro->id)}}" method="POST" id="cred" enctype="multipart/form-data">
            {!! method_field('PUT') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Credito al Rubro:  {{ $rubro->name }}</h4>
                </div>
                <div class="modal-body">
                    @if($contadorRubDisp == 0)
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                No hay rubros disponibles para realizar algun traslado.
                            </div>
                        </div>
                    @else
                        <div class="table-responsive" >
                            <div class="col-md-12 align-self-center">
                                <div class="alert alert-danger text-center">
                                    Recuerde añadir el archivo en el que esta la resolución del traslado. &nbsp;
                                </div>
                            </div>
                            <div class="form-group-sm">
                                <input type="file" required name="fileCyC" accept="application/pdf" class="form-control">
                            </div>
                            <table id="tabla_rubrosCdp" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th scope="col" class="text-center">Rubros</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for($i = 0; $i < $rubros->count(); $i++)
                                    @if($rubros[$i]->fontsRubro->sum('valor_disp') > 0)
                                        <tr>
                                            <td class="text-center">
                                                <button type="button" class="btn-sm btn-success" onclick="ver('fuente{{$i}}')" ><i class="fa fa-arrow-down"></i></button>
                                            </td>
                                            <td class="text-center">
                                                <div class="col-lg-6">
                                                    <h4>
                                                        <b>{{ $rubros[$i]->name }}</b>
                                                    </h4>
                                                </div>
                                                <div class="col-lg-6">
                                                    <h4>
                                                        <b>
                                                            Dinero Tomado:
                                                            @if($rubro->rubrosMov->count() > 0)
                                                                @foreach($rubros[$i]->fontsRubro as $F)
                                                                    @foreach($F->rubrosMov as $validate)
                                                                        @if($validate->rubro_id == $rubro->id and $validate->mov == 2)
                                                                            @php($val[] = $validate->valor)
                                                                        @else
                                                                            @php($val[] = 0)
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                                @if(!isset($val))
                                                                    $ 0.00
                                                                @else
                                                                    $<?php echo number_format( array_sum($val) ,0) ?>
                                                                @endif
                                                                @php( $val = null )
                                                            @else
                                                                $0
                                                            @endif
                                                        </b>
                                                    </h4>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="fuente{{$i}}" style="display: none">
                                            <td style="vertical-align: middle">
                                                <b>Fuentes del rubro {{ $rubros[$i]->name }}</b>
                                            </td>
                                            <td>
                                                <div class="col-lg-12">
                                                    @foreach($rubros[$i]->fontsRubro as $fuentesRubro)
                                                        @if($fuentesRubro->valor_disp != 0)
                                                            <div class="col-lg-12">
                                                                <input type="hidden" name="fuenteR_id[]" value="{{ $fuentesRubro->id }}">
                                                                @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
                                                                <li style="list-style-type: none;">
                                                                    {{ $fuentesRubro->sourceFunding }} : $<?php echo number_format( $fuentesRubro->valor_disp,0) ?>
                                                                </li>
                                                            </div>
                                                        @endif
                                                        <div class="col-lg-12">
                                                            <br>
                                                            @if($fuentesRubro->valor_disp != 0)
                                                                Valor usado de {{ $fuentesRubro->sourceFunding }}:
                                                                @if($rubro->rubrosMov->count() > 0)
                                                                    <input type="hidden" name="rubro_Mov_id[]" value="@foreach($fuentesRubro->rubrosMov as $mov) @if($mov->rubro_id == $rubro->id) {{  $mov->id }} @endif @endforeach">
                                                                    <input type="text" required class="form-control" name="valorRed[]" value="@foreach($fuentesRubro->rubrosMov as $mov) @if($mov->rubro_id == $rubro->id) {{  $mov->valor }} @endif @endforeach" max="{{ $fuentesRubro->valor_disp }}" style="text-align: center">
                                                                @else
                                                                    <input type="hidden" name="rubro_Mov_id[]" value="">
                                                                    <input type="number" required  name="valorRed[]" class="form-control" value="0" max="{{ $fuentesRubro->valor_disp }}" style="text-align: center">
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <br>
                                                            Fuente destino del dinero:
                                                            <select name="fuente_id[]" class="form-control" required>
                                                                @foreach($fuentesAll as $fonts)
                                                                    <option value="{{ $fonts['id'] }}" @foreach($fuentesRubro->rubrosMov as $mov) @if($mov->fonts_id == $fonts['id'] and $mov->rubro_id == $rubro->id ) selected @endif @endforeach >{{ $fonts->description }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($contadorRubDisp > 0)
                        <center><button type="submit" class="btn-sm btn-primary">Guardar Credito</button></center>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

