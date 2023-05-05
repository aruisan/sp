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
        @foreach($compContables as $comprobante)
            <tr>
                <td>{{ \Carbon\Carbon::parse($comprobante->ff)->format('d-m-Y') }}</td>
                <td>Comprobante de Contabilidad #{{ $comprobante->code }}</td>
                <td>{{ $comprobante->concepto }}</td>
                <td>{{ $comprobante->persona->num_dc }} - {{ $comprobante->persona->nombre }}</td>
                    @foreach($comprobante->movs as $mov)
                        @if(isset($mov->cuenta_banco))
                            <td>{{ $mov->banco->code}} - {{ $mov->banco->concepto}}</td>
                            @if(!isset($mov->debito))
                                <td>0</td>
                            @else
                                <td>{{$mov->debito}}</td>
                            @endif
                            <td>{{$comprobante->totalCredito}}</td>
                        @endif
                    @endforeach
            </tr>
        @endforeach
    </tbody>
</table>