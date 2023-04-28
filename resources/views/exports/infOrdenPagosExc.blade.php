<table>
    <thead>
    <tr>
        <th class="text-center">Fecha</th>
        <th class="text-center">Documento</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Tercero</th>
        <th class="text-center">Cuenta</th>
        <th class="text-center">Debito</th>
        <th class="text-center">Credito</th>
    </tr>
    </thead>
    <tbody>
        @foreach($ordenPagos as $codigo)
            <tr>
                <td>{{ \Carbon\Carbon::parse($codigo['info']->created_at)->format('d-m-Y') }}</td>
                <td>Orden de pago #{{ $codigo['info']->code }}</td>
                <td>{{ $codigo['info']->nombre }}</td>
                <td>{{ $codigo['ccH']}} - {{ $codigo['tercero'] }}</td>
                <td></td>
                <td>{{ $codigo['pucV'] }}</td>
                <td>{{ $codigo['descuentos']->sum('valor') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>