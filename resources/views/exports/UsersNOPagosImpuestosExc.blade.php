<table>
    <thead>
    <tr>
        <th>NUMERO DE PAGO</th>
        <th>FECHA CREACION</th>
        <th>IMPUESTO</th>
        <th>NOMBRE</th>
        <th>CORREO</th>
        <th>VALOR</th>
    </tr>
    </thead>
    <tbody>
        @foreach($noPagos as $item)
            <tr>
                <td>{{ $item->id}}</td>
                <td>{{ $item->fechaCreacion}}</td>
                <td>{{ $item->modulo}}</td>
                <td>{{ $item->user->name}}</td>
                <td>{{ $item->user->email}}</td>
                <td>{{ $item->valor}}</td>
            </tr>
        @endforeach
    </tbody>
</table>