@extends('layouts.predial_pdf')
@section('contenido')
    <div class="col-md-12 align-self-center" style="background-color: white">
        <div class="table-responsive br-black-1">
            <table class="table text-center table-condensed">
                <tbody>
                <tr style="font-size: 20px; font-style: italic">
                    <td>
                        <b>LIQUIDACION OFICIAL DE IMPUESTO PREDIAL UNIFICADO</b>
                    </td>
                </tr>
                <tr style="font-size: 18px">
                    <td>Formulario Número: {{$numFacturaCodebar}}</td>
                </tr>
                </tbody>
            </table>
            <table class="table text-center table-bordered table-condensed">
                <tbody>
                <tr style="background-color: #0e7224; color: white">
                    <td colspan="4">IDENTIFICACION DEL PREDIO</td>
                </tr>
                <tr>
                    <td>Número Catastral:<br><b>{{ $predial->numCatas }}</b></td>
                    <td colspan="2">Dirección:<br><b>{{ $predial->dirPredio }}</b></td>
                    <td>Matricula Inmobiliaria:<br><b>{{$predial->matricula}}</b></td>
                </tr>
                <tr>
                    <td colspan="4">
                        Propietario(s):<br>
                        <b>{{ $contribuyente->numIdent }} {{ $contribuyente->contribuyente }}</b><br>
                        @if(count($contribuyentes) > 0)
                            @foreach($contribuyentes as $otherProp)
                                <b>{{$otherProp}}</b><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Area de Terreno:<br><b>{{$contribuyente->hect}} Hc - {{ $contribuyente->metros }} M2</b></td>
                    <td>Fecha Liquidación:<br><b>{{ \Carbon\Carbon::parse($predial->presentacion)->format('d-m-Y') }}</b></td>
                    <td>Tarifa:<br><b>{{$predial->tarifaMil}}</b></td>
                    <td>Descuento<br><b>{{$predial->tasaDesc}} %</b></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table class="table text-center table-bordered table-condensed" style="font-size: 7px !important;">
                <thead>
                <tr style="background-color: #0e7224; color: white">
                    <td>Años</td>
                    <td>Avalúos</td>
                    <td>Imp Predial</td>
                    <td>Coralina</td>
                    <td>Int. Predial</td>
                    <td>TOTALES</td>
                </tr>
                </thead>
                <tbody>
                @foreach($liquidacion as $item)
                    <tr>
                        <td>{{ $item->año }}</td>
                        <td>$ <?php echo number_format($item->avaluo,0) ?></td>
                        <td>$ <?php echo number_format($item->imp_predial,0) ?></td>
                        <td>$ <?php echo number_format($item->tasa_bomberil,0) ?></td>
                        <td>
                            @if($item->año == 2023)
                                $ <?php echo number_format($item->int_mora,0) ?>
                            @elseif($item->año == 2022)
                                @php($totValue = intval($item->int_mora) / 2 )
                                $ <?php echo number_format($totValue,0) ?>
                            @elseif($item->año == 2021)
                                $ <?php echo number_format(0,0) ?>
                            @elseif($item->año == 2020)
                                $ <?php echo number_format(0,0) ?>
                            @elseif($item->año == 2019)
                                @php($totValue = intval($item->int_mora) * 0.03 )
                                $ <?php echo number_format($totValue,0) ?>
                            @else
                                $ <?php echo number_format($item->int_mora,0) ?>
                            @endif
                        </td>
                        <td>$ <?php echo number_format($item->tot_año,0) ?></td>

                    </tr>
                @endforeach
                <tr>
                    <td colspan="2"><b>TOTALES</b></td>
                    <td>$ <?php echo number_format($totImpPredial,0) ?></td>
                    <td>$ <?php echo number_format($totImpAdi,0) ?></td>
                    <td>
                        @php($totDesc = 0)
                        @foreach($liquidacion as $item)
                            @if($item->año == 2023)
                                @php($totDesc = $totDesc + intval($item->int_mora))
                            @elseif($item->año == 2022)
                                @php($totDesc = $totDesc + intval($item->int_mora) / 2)
                            @elseif($item->año == 2021)
                                @php($totDesc = $totDesc + 0)
                            @elseif($item->año == 2020)
                                @php($totDesc = $totDesc + 0)
                            @elseif($item->año == 2019)
                                @php($totDesc = $totDesc + intval($item->int_mora) * 0.03)
                            @else
                                @php($totDesc = $totDesc + intval($item->int_mora))
                            @endif
                        @endforeach
                        $ <?php echo number_format($totDesc,0) ?>
                    </td>
                    <td>$ <?php echo number_format($predial->tot_pago,0) ?></td>
                </tr>
                </tbody>
            </table>
            <table class="table text-center table-bordered table-condensed" style="font-size: 7px">
                <thead>
                <tr style="background-color: #0e7224; color: white">
                    <td rowspan="2" style="vertical-align: middle">RESUMEN</td>
                    <td>Total Predial </td>
                    <td>Corp Autonoma</td>
                    <td>Papeleria</td>
                    <td>Otros Descuentos</td>
                    <td>Descuento</td>
                    <td>TOTALES</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>$ <?php echo number_format($predial->tot_pago,0) ?></td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>$ <?php echo number_format($predial->desc_imp,0) ?></td>
                    <td>$ <?php echo number_format($predial->tot_pago,0) ?></td>
                </tr>
                </tbody>
            </table>
            Contra la presente liquidación procede el recurso de reconsideracion dentro de los quince (15) días siguientes a su notificación
            <table class="table text-center table-condensed">
                <tbody>
                <tr style="background-color: #818785;">
                    <td style="vertical-align: middle"><b>Paguese Hasta</b></td>
                    <td>Fecha <br>{{ \Carbon\Carbon::parse($predial->presentacion)->format('d-m-Y') }}</td>
                    <td>Valor <br>$ <?php echo number_format($predial->tot_pago,0) ?></td>
                </tr>
                </tbody>
            </table>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            --- CONTRIBUYENTE --- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _
            <br><br><br>
            <div class="row">
                <div class="col-md-4 s7">
                    &nbsp;&nbsp;&nbsp;&nbsp; <img src="https://www.siex-concejoprovidenciaislas.com.co/img/escudoIslas.png"  height="60">
                </div>
                <div class="col-md-4 s17 text-center" style="font-size: 8px">
                    MUNICIPIO DE PROVIDENCIA Y SANTA CATALINA ISLAS<br>
                    NIT 800103021.1<br>
                    <i>United For A Social Work</i><br>
                </div>
                <div class="col-md-4 s57">
                    {!! DNS1D::getBarcodeHTML('(415)7709998144460(8020)'.$numFacturaCodebar.'(3900)'.$newValue.'(96)'. \Carbon\Carbon::parse($predial->presentacion)->format('Ymd'), 'C128',1.07,45) !!}
                    <br><br>
                    (415)7709998144460(8020){{$numFacturaCodebar}}(3900){{$newValue}}(96){{ \Carbon\Carbon::parse($predial->presentacion)->format('Ymd') }}
                    <br>
                    Señor Cajero por favor no colocar el sello en el código de barras
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 s50" style="font-size: 8px">
                    <br>
                    Número Catastral: {{ $predial->numCatas }}<br>
                    {{ $predial->cedula }} {{ $predial->propietario }} <br>
                    FORMA DE PAGO
                    <table class="table text-center table-condensed table-bordered">
                        <tr style="background-color: #818785; font-size: 8px">
                            <td>EFEC</td>
                            <td>CHEQ</td>
                            <td>BANCO</td>
                            <td>CHEQUE No</td>
                            <td>VALOR PAGADO</td>
                        </tr>
                    </table>
                    <br>
                    PUNTOS DE PAGO <br>
                    Banco de Bogota No. 540047529 Cuente Corriente <br>
                    Banco Agrario No.381100000557 Cuenta Corriente
                </div>
                <div class="col-md-4 s50 text-center">
                    <b>FACTURA OFICIAL</b><br>
                    <b>IMPUESTO PREDIAL UNIFICADO</b><br>
                    <b>FACTURA NO {{$numFacturaCodebar}}</b><br>
                    <b>PERIODO FACTURADO {{$liquidacion[0]->año}} AL {{$liquidacion[$liquidacion->count() - 1]->año}}</b><br>
                    <b>Paguese Hasta {{ \Carbon\Carbon::parse($predial->presentacion)->format('d-m-Y') }}</b><br>
                    <b>Valor $ <?php echo number_format($predial->tot_pago,0) ?></b><br>
                </div>
            </div>
        </div>
    </div>
@stop