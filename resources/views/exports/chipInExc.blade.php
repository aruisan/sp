<table>
    <thead>
    <tr>
        <th class="text-center">S</th>
        <th class="text-center">216488564</th>
        <th class="text-center">11212</th>
        <th class="text-center">{{ $año }}</th>
        <th class="text-center" colspan="10">B_EJECUCION_DE_INGRESOS</th>
    </tr>
    <tr>
        <th class="text-center">Detalle</th>
        <th class="text-center">Rubro</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">CPC</th>
        <th class="text-center">Detalle Sectorial</th>
        <th class="text-center">Fuente</th>
        <th class="text-center">Tercero</th>
        <th class="text-center">Política publica</th>
        <th class="text-center">Número y fecha norma</th>
        <th class="text-center">Tipo de norma</th>
        <th class="text-center">Recaudo vigencia actual sin situación de fondos</th>
        <th class="text-center">Recaudo vigencia actual con fondos</th>
        <th class="text-center">Recaudo vigencia anterior sin situación de fondos</th>
        <th class="text-center">Recaudo Anterior  con fondos</th>
    </tr>
    </thead>
    <tbody>
        @foreach($presupuesto as $codigo)
            <tr>
                <td>D</td>
                <td>{{ $codigo['code']}}</td>
                <td>{{ $codigo['name']}}</td>
                <td>0</td>
                <td>0</td>
                <td>1</td>
                <td>0</td>
                <td>ley 99 de 1993</td>
                <td>5</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
        @endforeach
    </tbody>
</table>