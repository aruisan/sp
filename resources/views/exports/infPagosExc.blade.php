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
        <th class="text-center">Tipo de Pago</th>
        <th class="text-center">Cuenta Bancaria</th>
    </tr>
    </thead>
    <tbody>
        @foreach($pagos as $codigo)
            <tr>
                <td>{{ $codigo['info']->code }}</td>
                <td>{{ $codigo['info']->ff_fin }}</td>
                <td>{{ $codigo['info']->concepto }}</td>
                <td>{{ $codigo['info']->persona->num_dc }}</td>
                <td>{{ $codigo['info']->persona->nombre }}</td>
                <td>{{ $codigo['info']->valor }}</td>
                <td>{{ $codigo['info']->type_pay }}</td>
                <td>{{ $codigo['info']->cuentaBanco }}</td>
            </tr>
        @endforeach
    </tbody>
</table>