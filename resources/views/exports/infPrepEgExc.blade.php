<table>
    <thead>
    <tr class="text-center">
        <th colspan="16"><b>Presupuesto de Egresos {{ $año }}-{{ $mesActual }}-{{ $dia }}</b></th>
    </tr>
    <tr>
        {{--
        <th class="text-center">Codigo BPIN</th>
        <th class="text-center">Codigo Actividad</th>
        <th class="text-center">Nombre Actividad</th>
        --}}
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        {{--
        <th class="text-center">P. Inicial</th>
        <th class="text-center">Adición</th>
        <th class="text-center">Reducción</th>
        <th class="text-center">Credito</th>
        <th class="text-center">CCredito</th>
        <th class="text-center">P.Definitivo</th>
        <th class="text-center">CDP's</th>
        <th class="text-center">Registros</th>
        <th class="text-center">Saldo Disponible</th>
        <th class="text-center">Saldo de CDP</th>
        <th class="text-center">Ordenes de Pago</th>
        <th class="text-center">Pagos</th>
        <th class="text-center">Cuentas Por Pagar</th>
        <th class="text-center">Reservas</th>
        <th class="text-center">Cod Dependencia</th>
        <th class="text-center">Dependencia</th>
        <th class="text-center">Fuente</th>
        --}}
    </tr>
    </thead>
    <tbody>
           <tr><td>{{$plantilla['code']}}</td><td>{{$plantilla['name']}}</td></tr>
           {!!$plantilla['format_hijos']!!}
        {{--
    @foreach($presupuesto as $codigo)
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $codigo['codBpin']}}</td>
            <td>{{ $codigo['codActiv']}}</td>
            <td>{{ $codigo['nameActiv']}}</td>
            <td>{{ $codigo['cod'] }}</td>
            <td>{{ $codigo['name']}}</td>
            <td>{{ $codigo['presupuesto_inicial']}}</td>
            <td>{{ $codigo['adicion']}}</td>
            <td>{{ $codigo['reduccion']}}</td>
            <td>{{ $codigo['credito']}}</td>
            <td>{{ $codigo['ccredito']}}</td>
            <td>{{ $codigo['presupuesto_def']}}</td>
            <td>{{ $codigo['cdps']}}</td>
            <td>{{ $codigo['registros']}}</td>
            <td>{{ $codigo['saldo_disp']}}</td>
            <td>{{ $codigo['saldo_cdp']}}</td>
            <td>{{ $codigo['ordenes_pago']}}</td>
            <td>{{ $codigo['pagos']}}</td>
            <td>{{ $codigo['cuentas_pagar']}}</td>
            <td>{{ $codigo['reservas']}}</td>
            <td>{{ $codigo['codDep']}}</td>
            <td>{{ $codigo['dep']}}</td>
            <td>{{ $codigo['fuente']}}</td>
        </tr>
    @endforeach
        --}}
    </tbody>
</table>