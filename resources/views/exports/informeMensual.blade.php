<table>
    <thead>
    <tr class="text-center">
        <th colspan="16"><b>Informe Mensual {{ $meses }}</b></th>
    </tr>
    <tr>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Presupuesto Inicial</th>
        <th class="text-center">Adición</th>
        <th class="text-center">Reducción</th>
        <th class="text-center">Credito</th>
        <th class="text-center">Contra Credito</th>
        <th class="text-center">Presupuesto Definitivo</th>
        <th class="text-center">CDP's</th>
        <th class="text-center">Registros</th>
        <th class="text-center">Saldo Disponible</th>
        <th class="text-center">Saldo de CDP</th>
        <th class="text-center">Ordenes de Pago</th>
        <th class="text-center">Pagos</th>
        <th class="text-center">Cuentas Por Pagar</th>
        <th class="text-center">Reservas</th>
    </tr>
    </thead>
    <tbody>
    @foreach($codigos as $codigo)
        <tr>
            <td>{{ $codigo['codigo']}}</td>
            <td>{{ $codigo['name']}}</td>
            <!-- PRESUPUESTO INICIAL-->
            @foreach($valoresIniciales as $valorInicial)
                @if($valorInicial['id'] == $codigo['id'])
                    <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($valorInicial['valor'],0);?></td>
                @endif
            @endforeach
            @if($codigo['valor'])
                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
            @elseif($codigo['valor'] == 0 and $codigo['id_rubro'] != "")
                <td class="text-center text-dark" style="vertical-align:middle;">$ <?php echo number_format($codigo['valor'],0);?></td>
            @endif
        <!-- ADICIÓN -->
            @foreach($valoresFinAdd as $valorFinAdd)
                @if($valorFinAdd['id'] == $codigo['id'])
                    <td>{{$valorFinAdd['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresAdd as $valorAdd)
                @if($codigo['id_rubro'] == $valorAdd['id'])
                    <td>{{$valorAdd['valor']}}</td>
                @endif
            @endforeach
        <!-- REDUCCIÓN -->
            @foreach($valoresFinRed as $valorFinRed)
                @if($valorFinRed['id'] == $codigo['id'])
                    <td>{{$valorFinRed['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresRed as $valorRed)
                @if($codigo['id_rubro'] == $valorRed['id'])
                    <td>{{$valorRed['valor']}}</td>
                @endif
            @endforeach
        <!-- CREDITO -->
            @foreach($valoresFinCred as $valorFinCred)
                @if($valorFinCred['id'] == $codigo['id'])
                    <td>{{$valorFinCred['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresCred as $valorCred)
                @if($codigo['id_rubro'] == $valorCred['id'])
                    <td>{{$valorCred['valor']}}</td>
                @endif
            @endforeach
        <!-- CONTRACREDITO -->
            @foreach($valoresFinCCred as $valorFinCCred)
                @if($valorFinCCred['id'] == $codigo['id'])
                    <td>{{$valorFinCCred['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresCcred as $valorCcred)
                @if($codigo['id_rubro'] == $valorCcred['id'])
                    <td>{{$valorCcred['valor']}}</td>
                @endif
            @endforeach
        <!-- PRESUPUESTO DEFINITIVO -->
            @foreach($valoresDisp as $valorDisponible)
                @if($valorDisponible['id'] == $codigo['id'])
                    <td>{{$valorDisponible['valor']}}</td>
                @endif
            @endforeach
            @foreach($ArrayDispon as $valorPD)
                @if($codigo['id_rubro'] == $valorPD['id'])
                    <td>{{$valorPD['valor']}}</td>
                @endif
            @endforeach
        <!-- CDP'S -->
            @foreach($valoresFinCdp as $valorFinCdp)
                @if($valorFinCdp['id'] == $codigo['id'])
                    <td>{{$valorFinCdp['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresCdp as $valorCdp)
                @if($codigo['id_rubro'] == $valorCdp['id'])
                    <td>{{$valorCdp['valor']}}</td>
                @endif
            @endforeach
        <!-- REGISTROS -->
            @foreach($valoresFinReg as $valorFinReg)
                @if($valorFinReg['id'] == $codigo['id'])
                    <td>{{$valorFinReg['valor']}}</td>
                @endif
            @endforeach
            @foreach($valoresRubro as $valorRubro)
                @if($codigo['id_rubro'] == $valorRubro['id'])
                    <td>{{$valorRubro['valor']}}</td>
                @endif
            @endforeach
        <!-- SALDO DISPONIBLE -->
            @foreach($valorDisp as $vDisp)
                @if($vDisp['id'] == $codigo['id'])
                    <td>{{$vDisp['valor']}}</td>
                @endif
            @endforeach
            @foreach($saldoDisp as $salD)
                @if($codigo['id_rubro'] == $salD['id'])
                    <td>{{$salD['valor']}}</td>
                @endif
            @endforeach
        <!-- SALDO DE CDP -->
            @foreach($valorFcdp as $valFcdp)
                @if($valFcdp['id'] == $codigo['id'])
                    <td>{{$valFcdp['valor']}}</td>
                @endif
            @endforeach
            @foreach($valorDcdp as $valorDCdp)
                @if($codigo['id_rubro'] == $valorDCdp['id'])
                    <td>{{$valorDCdp['valor']}}</td>
                @endif
            @endforeach
        <!-- ORDENES DE PAGO -->
            @foreach($valoresFinOp as $valFinOp)
                @if($valFinOp['id'] == $codigo['id'])
                    <td>{{$valFinOp['valor']}}</td>
                @endif
            @endforeach
            @foreach($valOP as $valorOP)
                @if($codigo['id_rubro'] == $valorOP['id'])
                    <td>{{$valorOP['valor']}}</td>
                @endif
            @endforeach
        <!-- PAGOS -->
            @foreach($valoresFinP as $valFinP)
                @if($valFinP['id'] == $codigo['id'])
                    <td>{{$valFinP['valor']}}</td>
                @endif
            @endforeach
            @foreach($valP as $valorP)
                @if($codigo['id_rubro'] == $valorP['id'])
                    <td>{{$valorP['valor']}}</td>
                @endif
            @endforeach
        <!-- CUENTAS POR PAGAR -->
            @foreach($valoresFinC as $valFinC)
                @if($valFinC['id'] == $codigo['id'])
                    <td>{{$valFinC['valor']}}</td>
                @endif
            @endforeach
            @foreach($valCP as $valorCP)
                @if($codigo['id_rubro'] == $valorCP['id'])
                    <td>{{$valorCP['valor']}}</td>
                @endif
            @endforeach
        <!-- RESERVAS -->
            @foreach($valoresFinRes as $valFinRes)
                @if($valFinRes['id'] == $codigo['id'])
                    <td>{{$valFinRes['valor']}}</td>
                @endif
            @endforeach
            @foreach($valR as $valorR)
                @if($codigo['id_rubro'] == $valorR['id'])
                    <td>{{$valorR['valor']}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>