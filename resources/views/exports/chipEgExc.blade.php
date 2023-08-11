<table>
    <thead>
    <tr>
        <th class="text-center">S</th>
        <th class="text-center">216488564</th>
        <th class="text-center">11212</th>
        <th class="text-center">{{ $año }}</th>
        <th class="text-center" colspan="14">D_EJECUCION_DE_GASTOS</th>
    </tr>
    <tr>
        <th class="text-center">Detalle</th>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Vigencia Actual</th>
        <th class="text-center">Administracion Central</th>
        <th class="text-center">Producto MGA</th>
        <th class="text-center">CPC</th>
        <th class="text-center">Detalle Sectorial</th>
        <th class="text-center">Fuente</th>
        <th class="text-center">Codigo BPIN</th>
        <th class="text-center">Codigo Actividad</th>
        <th class="text-center">Nombre Actividad</th>
        <th class="text-center">Seleccione C /Sin fondos</th>
        <th class="text-center">Politica Publica</th>
        <th class="text-center">Tercero</th>
        <th class="text-center">Compromisos</th>
        <th class="text-center">Obligaciones</th>
        <th class="text-center">Pagos</th>
    </tr>
    </thead>
    <tbody>
        @foreach($presupuesto as $codigo)
            <tr>
                <td>D</td>
                <td>{{ $codigo['cod'] }}</td>
                <td>{{ $codigo['name']}}</td>
                <td>1</td>
                <td>16</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>{{ $codigo['fuente']}}</td>
                <td>{{ $codigo['codBpin']}}</td>
                <td>{{ $codigo['codActiv']}}</td>
                <td>{{ $codigo['nameActiv']}}</td>
                <td>C</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>{{ $codigo['pagos']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>