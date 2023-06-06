<table>
    <thead>
    <tr>
        <th colspan="3">NO PAGO PREDIAL</th>
    </tr>
    <tr>
        <th>Num Catastral</th>
        <th>Num Identidad</th>
        <th>Contribuyente</th>
    </tr>
    </thead>
    <tbody>
        @foreach($predial as $pred)
            <tr>
                <td>{{ $pred['numCatastral']}}</td>
                <td>{{ $pred['numIdent']}}</td>
                <td>{{ $pred['contribuyente']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<table>
    <thead>
    <tr><th colspan="2">NO PAGO ICA RETENEDOR</th></tr>
    <tr>
        <th>Nombre</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($icaReten as $iRet)
        <tr>
            <td>{{ $iRet['name']}}</td>
            <td>{{ $iRet['email']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table>
    <thead>
    <tr><th colspan="2">NO PAGO ICA CONTRIBUYENTE</th></tr>
    <tr>
        <th>Nombre</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($icaContri as $iCon)
        <tr>
            <td>{{ $iCon['name']}}</td>
            <td>{{ $iCon['email']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>