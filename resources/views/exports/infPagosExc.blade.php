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
        @foreach($pagos as $codigo)
            @foreach($codigo['info']->banks as $bank)
                <tr>
                    <td>{{ $codigo['info']->ff_fin }}</td>
                    <td>Pagos #{{ $codigo['info']->code }}</td>
                    <td>{{ $codigo['info']->concepto }}</td>
                    <td>{{ $bank->persona->num_dc }} - {{ $bank->persona->nombre }}</td>
                    <td>{{ $bank->data_puc->code}} - {{ $bank->data_puc->concepto }} </td>
                    <td>{{ $bank->debito }}</td>
                    <td>{{ $bank->credito }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>