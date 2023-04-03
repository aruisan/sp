<table>
    <thead>
    <tr class="text-center">
        <th colspan="16"><b>Balance General {{ $año }}-{{ $mesActual }}-{{ $dia }}</b></th>
    </tr>
    <tr>
        <th class="text-center">Codigo</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Debito</th>
        <th class="text-center">Credito</th>
        {{--
        <th class="text-center">naturaleza</th>
        <th class="text-center">saldo inicial</th>
        <th class="text-center">padre</th>
        <th class="text-center">hijos</th>
        --}}
    </tr>
    </thead>
    <tbody>
            @foreach($pucs as $puc)
            <tr>
                <td>{{$puc->code}}</td>
                <td>{{$puc->concepto}}</td>
                <td>{{$puc->naturaleza == "DEBITO" ? $puc->v_inicial : 0}}</td>
                <td>{{$puc->naturaleza != "DEBITO" ? $puc->v_inicial : 0}}</td>
                {{--
                <td>{{$puc->naturaleza}}</td>
                <td>{{$puc->saldo_inicial}}</td>
                <td>{{is_null($puc->padre) ? 'no tiene' : $puc->padre->code}}</td>
                <td>{{$puc->hijos->pluck('id')}}</td>
                --}}
            </tr>
           {!!$puc['format_hijos']!!}
           @endforeach
    </tbody>
</table>