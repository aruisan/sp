<table>
    <thead>
    <tr>
        <th colspan="3">NO PAGO</th>
    </tr>
    <tr>
        <th># PAGO</th>
        <th>FECHA CREACION</th>
        <th>IMPUESTO</th>
        <th>NOMBRE</th>
        <th>CORREO</th>
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
            </tr>
        @endforeach
    </tbody>
</table>