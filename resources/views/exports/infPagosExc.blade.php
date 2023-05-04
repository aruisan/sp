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

{{--            @if(isset($codigo->cuentaOP))--}}
{{--                @for($x = 0; $x < count($codigo->cuentaOP); $x++)--}}
{{--                    <tr>--}}
{{--                        <td>{{ $codigo['info']->ff_fin }}</td>--}}
{{--                        <td>Pagos #{{ $codigo['info']->code }}</td>--}}
{{--                        <td>{{ $codigo['info']->concepto }}</td>--}}
{{--                        <td>{{ $codigo['info']->perOP[$x] }}</td>--}}
{{--                        <td>{{ $codigo['info']->cuentaOP[$x] }} </td>--}}
{{--                        <td>{{ $codigo['info']->credOP[$x] }}</td>--}}
{{--                        <td>0</td>--}}
{{--                    </tr>--}}
{{--                @endfor--}}
{{--            @endif--}}
            <tr>
                <td>{{ $codigo['info']->ff_fin }}</td>
                <td>Pagos #{{ $codigo['info']->code }}</td>
                <td>{{ $codigo['info']->concepto }}</td>
                <td>{{ $codigo['info']->persona->num_dc }} - {{ $codigo['info']->persona->nombre }}</td>
                <td>{{ $codigo['info']->cuentaBanco }} </td>
                <td>{{ $codigo['info']->totCredOP }}</td>
                <td>{{ $codigo['info']->valor }}</td>
            </tr>
        @endforeach
    </tbody>
</table>