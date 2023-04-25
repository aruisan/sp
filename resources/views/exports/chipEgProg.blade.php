<table>
    <thead>
    <tr>
        <th class="text-center">S</th>
        <th class="text-center">216488564</th>
        <th class="text-center">11212</th>
        <th class="text-center">{{ $año }}</th>
        <th class="text-center" colspan="7">C_PROGRAMACION_DE_GASTOS</th>
    </tr>
    <tr>
        <th class="text-center">Detalle</th>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Vigencia</th>
        <th class="text-center">Administracion Central</th>
        <th class="text-center">Programa MGA</th>
        <th class="text-center">Codigo BPIN</th>
        <th class="text-center">Codigo Actividad</th>
        <th class="text-center">Nombre Actividad</th>
        <th class="text-center">Apropiacion inicial</th>
        <th class="text-center">Apropiacion definitiva</th>
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
                <td>{{ $codigo['codBpin']}}</td>
                <td>{{ $codigo['codActiv']}}</td>
                <td>{{ $codigo['nameActiv']}}</td>
                <td>{{ $codigo['presupuesto_inicial']}}</td>
                <td>{{ $codigo['presupuesto_def']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>