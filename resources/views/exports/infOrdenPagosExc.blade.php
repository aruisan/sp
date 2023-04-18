<table>
    <thead>
    <tr>
        <th class="text-center">#</th>
        <th class="text-center">Fecha</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Num Identidad Tercero</th>
        <th class="text-center">Nombre Tercero</th>
        <th class="text-center">Nombre Tercero</th>
        <th class="text-center">Valor</th>
    </tr>
    </thead>
    <tbody>
        @foreach($ordenPagos as $codigo)
            <tr>
                <td>{{ $codigo['info']->code }}</td>
                <td>{{ \Carbon\Carbon::parse($codigo['info']->created_at)->format('d-m-Y') }}</td>
                <td>{{ $codigo['info']->name }}</td>
                <td>{{ $codigo['ccH']}}</td>
                <td>{{ $codigo['tercero'] }}</td>
                <td>{{ $codigo['info']->valor }}</td>
                <td>{{ $codigo['info']->saldo }}</td>
            </tr>
        @endforeach
    </tbody>
</table>