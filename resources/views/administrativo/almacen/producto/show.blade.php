@extends('layouts.dashboard')
@section('titulo')
    {{ $item->nombre }}
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>{{ $item->nombre }}</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/productos') }}">Volver a Productos</a>
        </li>
        <li class="nav-item active">
            <a class="tituloTabs" data-toggle="pill" href="#tabHome">{{ $item->nombre }}</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/salida/create') }}">Nuevo Comprobante de Salida</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation">
                <form class="form-valide"  enctype="multipart/form-data">
                    <div class="col-md-2 align-self-center"></div>
                    <div class="col-md-8 align-self-center">
                        <table class="table-bordered" width="100%">
                            <tbody>
                            <tr class="text-center">
                                <td rowspan="2"><img src="{{ asset('img/productos/'.$item->id.'.jpg')}}" width="30%" height="30%"></td>
                                <td colspan="2">Producto = {{ $item->nombre }}</td>
                                <td colspan="2">{{ Carbon\Carbon::today()->Format('d-m-Y')}}</td>
                            </tr>
                            <tr class="text-center">
                                <td>Cantidad Maxima = <?php echo number_format($item->cant_maxima,0) ?></td>
                                <td>Cantidad Minima = <?php echo number_format($item->cant_minima,0) ?></td>
                                <td>
                                    Método = @if($item->metodo == 0) U.E.P.S @else P.E.P.S @endif
                                </td>
                                <td>
                                    Tipo = @if($item->tipo == 0) Consumo @else Devolutivo @endif
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <br>
                        <table class="table-bordered" width="100%">
                            <tbody>
                            <tr class="text-center">
                                <td rowspan="2">NÚMERO</td>
                                <td rowspan="2">FECHA</td>
                                <td colspan="2">DETALLE</td>
                                <td colspan="3">ENTRADAS</td>
                                <td colspan="3">SALIDAS</td>
                                <td colspan="2">SALDOS</td>
                            </tr>
                            <tr class="text-center">
                                <td>CONCEPTO</td>
                                <td>FRAND</td>
                                <td>CANTIDAD</td>
                                <td>VR. UNITARIO</td>
                                <td>VR. TOTAL</td>
                                <td>CANTIDAD</td>
                                <td>VR. UNITARIO</td>
                                <td>VR. TOTAL</td>
                                <td>CANTIDAD</td>
                                <td>TOTAL</td>
                            </tr>
                            @if($item->tipo == "0")
                                <tr class="text-center">
                                    <td>0</td>
                                    <td>{{ $item->created_at->Format('Y-m-d') }}</td>
                                    <td>Producto inicial en la plataforma</td>
                                    <td></td>
                                    <td>{{ $item->cant_inicial }}</td>
                                    <td></td>
                                    <td>$<?php echo number_format($item->valor_inicial,0) ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $item->cant_inicial }}</td>
                                    <td>$<?php echo number_format($item->valor_inicial,0) ?></td>
                                </tr>
                                @for($x=0;$x< count($data); $x++)
                                    <tr class="text-center">
                                        <td>{{ $x+1 }}</td>
                                        <td>{{ $data[$x]->created_at->Format('Y-m-d') }}</td>
                                        <td>{{ $data[$x]->descripcion }}</td>
                                        <td></td>
                                        @if($data[$x]->tipo == 0)
                                            <td>{{ $data[$x]->cantidad }}</td>
                                            <td>$<?php echo number_format($data[$x]->valor_unidad,0) ?></td>
                                            <td>$<?php echo number_format($data[$x]->valor_final,0) ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $data[$x]->cantidad }}</td>
                                            <td>$<?php echo number_format($data[$x]->valor_unidad,0) ?></td>
                                            <td>$<?php echo number_format($data[$x]->valor_final,0) ?></td>
                                        @endif
                                        <td><?php echo number_format($saldos[$x]['cantidad'],0)?></td>
                                        <td>$<?php echo number_format($saldos[$x]['total'],0) ?></td>
                                    </tr>
                                @endfor
                                <tr class="text-center">
                                    <td colspan="4">TOTALES</td>
                                    <td></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finEntrada,0) ?></td>
                                    <td></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finSalida,0) ?></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finSaldo,0) ?></td>
                                </tr>
                            @else
                                <tr class="text-center">
                                    <td>0</td>
                                    <td>{{ $item->created_at->Format('Y-m-d') }}</td>
                                    <td>Producto inicial en la plataforma</td>
                                    <td></td>
                                    <td>{{ $item->cant_inicial }}</td>
                                    <td></td>
                                    <td>$<?php echo number_format($item->valor_inicial,0) ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $item->cant_inicial }}</td>
                                    <td>$<?php echo number_format($item->valor_inicial,0) ?></td>
                                </tr>
                                @for($x=0;$x< count($data); $x++)
                                    <tr class="text-center">
                                        <td>{{ $x+1 }}</td>
                                        <td>{{ $data[$x]->created_at->Format('Y-m-d') }}</td>
                                        <td>{{ $data[$x]->descripcion }}</td>
                                        <td></td>
                                        @if($data[$x]->tipo == 0)
                                            <td>{{ $data[$x]->cantidad }}</td>
                                            <td>$<?php echo number_format($data[$x]->valor_unidad,0) ?></td>
                                            <td>$<?php echo number_format($res= $data[$x]->valor_unidad * $data[$x]->cantidad,0) ?></td>
                                            <?php unset($res) ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $data[$x]->cantidad }}</td>
                                            <td>$<?php echo number_format($data[$x]->valor_unidad,0) ?></td>
                                            <td>$<?php echo number_format($res2= $data[$x]->valor_unidad * $data[$x]->cantidad,0) ?></td>
                                            <?php unset($res2) ?>
                                        @endif
                                        <td><?php echo number_format($saldos[$x]['cantidad'],0)?></td>
                                        <td>$<?php echo number_format($saldos[$x]['total'],0) ?></td>
                                    </tr>
                                @endfor
                                <tr class="text-center">
                                    <td colspan="4">TOTALES</td>
                                    <td></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finEntrada,0) ?></td>
                                    <td></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finSalida,0) ?></td>
                                    <td></td>
                                    <td>$<?php echo number_format($finSaldo,0) ?></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 ml-auto">
                            <br>
                            <center>
                                <button type="submit" disabled class="btn btn-primary"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;PDF</button>
                            </center>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop