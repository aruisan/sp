<table>
    <thead>
    <tr>
        <th>NUMERO DE PAGO</th>
        <th>FECHA CREACION</th>
        <th>IMPUESTO</th>
        <th>NOMBRE</th>
        <th>CORREO</th>
        <th>CC o NIT</th>
        <th>VALOR</th>
    </tr>
    </thead>
    <tbody>
        @foreach($noPagos as $item)
            @if($item->valor > 1000000)
                <tr>
                    <td>{{ $item->id}}</td>
                    <td>{{ $item->fechaCreacion}}</td>
                    <td>{{ $item->modulo}}</td>
                    <td>
                        @if($item->rit)
                            {{ $item->rit->apeynomContri }}
                        @else
                            {{ $item->user->name}}
                        @endif
                    </td>
                    <td>{{ $item->user->email}}</td>
                    <td>
                        @if($item->rit)
                            {{ $item->rit->numDocContri }}
                        @elseif($item->modulo == "PREDIAL")
                            {{ $item->contribuyente->numIdent }}
                        @elseif($item->modulo == "MUELLAJE")
                            {{ $item->NITNaviera }}
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $item->valor}}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<table>
    <thead>
    <tr><th colspan="5">PREDIAL</th></tr>
    <tr>
        <th>NUMERO CATASTRAL</th>
        <th>CONTRIBUYENTE</th>
        <th>DIRECCION</th>
        <th>CORREO</th>
        <th>LIQUIDACION</th>
        <th>NATURAL O JURIDICO</th>
        <th>NUM IDENTIDAD</th>
    </tr>
    </thead>
    <tbody>
    @foreach($predial as $item)
        @if($item['valorDeuda'] > 1000000)
            <tr>
                <td>{{ $item['numCatastral']}}</td>
                <td>{{ $item['contribuyente']}}</td>
                <td>{{ $item['dir_predio']}}</td>
                <td>{{ $item['email']}}</td>
                <td>{{ $item['valorDeuda']}}</td>
                <td>1</td>
                <td>{{ $item['numIdent']}}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>