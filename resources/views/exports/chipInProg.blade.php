<table>
    <thead>
    <tr>
        <th class="text-center">S</th>
        <th class="text-center">216488564</th>
        <th class="text-center">11212</th>
        <th class="text-center">{{ $año }}</th>
        <th class="text-center">A_PROGRAMACION_DE_INGRESOS</th>
    </tr>
    <tr>
        <th class="text-center">Detalle</th>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Ppto Inicial</th>
        <th class="text-center">Ppto Final</th>
    </tr>
    </thead>
    <tbody>
        @foreach($presupuesto as $codigo)
            <tr>
                <td>D</td>
                <td>{{ $codigo['code']}}</td>
                <td>{{ $codigo['name']}}</td>
                <td>{{ $codigo['inicial']}}</td>
                <td>{{ $codigo['definitivo']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>