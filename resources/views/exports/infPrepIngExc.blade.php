<table>
    <thead>
    <tr class="text-center">
        <th colspan="9"><b>Presupuesto de Ingresos {{ $dia }}-{{ $mesActual }}-{{ $año }}</b></th>
    </tr>
    <tr>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">INICIAL</th>
        <th class="text-center">Adición</th>
        <th class="text-center">Reducción</th>
        <th class="text-center">Anulados</th>
        <th class="text-center">DEFINITIVO</th>
        <th class="text-center">Total Recaudado</th>
        <th class="text-center">Saldo Por Recaudar</th>
        <th class="text-center">Fuente</th>
    </tr>
    </thead>
    <tbody>
    @foreach($prepIng as $rubro)
        <tr>
            <td>{{ $rubro['code']}}</td>
            <td>{{ $rubro['name']}}</td>
            <td>{{$rubro['inicial']}}</td>
            <td>{{$rubro['adicion']}}</td>
            <td>{{$rubro['reduccion']}}</td>
            <td>{{$rubro['anulados']}}</td>
            <td>{{$rubro['definitivo']}}</td>
            <td>{{$rubro['recaudado']}}</td>
            <td>{{$rubro['porRecaudar']}}</td>
            <td>{{$rubro['cod_fuente']}} {{$rubro['name_fuente']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>