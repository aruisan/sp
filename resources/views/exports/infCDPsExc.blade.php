<table>
    <thead>
    <tr>
        <th class="text-center">Fecha</th>
        <th class="text-center">Documento</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Tipo</th>
        <th class="text-center">Dependencia</th>
        <th class="text-center">Valor</th>
        <th class="text-center">Rubros</th>
        <th class="text-center">Fuentes</th>
    </tr>
    </thead>
    <tbody>
        @foreach($cdps as $cdp)
            <tr>
                <td>{{ \Carbon\Carbon::parse($cdp->created_at)->format('d-m-Y') }}</td>
                <td>CDP #{{ $cdp->code }}</td>
                <td>{{ $cdp->name }}</td>
                <td>{{ $cdp->tipo }}</td>
                <td>{{ $cdp->dependencia->name }}</td>
                <td>{{ $cdp->valor }}</td>
                <td>
                    @foreach($cdp->rubros as $rubro)
                        {{ $rubro }} <br>
                    @endforeach
                </td>
                <td>
                    @foreach($cdp->fuentes as $fuentes)
                        {{ $fuentes }} <br>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>