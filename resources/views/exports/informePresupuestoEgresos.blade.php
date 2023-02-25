<table>
    <thead>
    <tr class="text-center">
        <th colspan="16"><b>Presupuesto de Egresos {{ $año }}-{{ $mesActual }}-{{ $dia }}</b></th>
    </tr>
    <tr>
        <th class="text-center">Codigo BPIN</th>

    </tr>
    </thead>
    <tbody>
    @foreach($presupuesto as $codigo)
        <tr>

            <td>{{ $codigo['nameActiv']}}</td>

        </tr>
    @endforeach
    </tbody>
</table>