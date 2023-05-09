<table>
    <thead>
    <tr>
        <th class="text-center">Fecha</th>
        <th class="text-center">Documento</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Num Doc</th>
        <th class="text-center">Valor</th>
        <th class="text-center">Tercero</th>
    </tr>
    </thead>
    <tbody>
        @foreach($rps as $rp)
            <tr>
                <td>{{ \Carbon\Carbon::parse($rp['fecha'])->format('d-m-Y') }}</td>
                <td>{{ $rp['id'] }}</td>
                <td>{{ $rp['code'] }}</td>
                <td>{{ $rp['objeto'] }}</td>
                <td>{{ $rp['num_doc'] }}</td>
                <td>{{ $rp['valor'] }}</td>
                <td>{{ $rp['cc'] }} - {{  $rp['nombre'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>