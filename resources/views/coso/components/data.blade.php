<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td><b>Nombre:</b></td>
                <td>{{$individuo->nombre}}</td>
                <td><b>Color:</b></td>
                <td>{{$individuo->color}}</td>
            </tr>
            <tr>
                <td><b>Fecha y Hora:</b></td>
                <td>{{$individuo->date_at}}</td>
                    <td><b>Ficha de Ingreso:</b></td>
                <td>{{$individuo->ficha_ingreso}}</td>
            </tr>
            <tr>
                <td><b>Tipo:</b></td>
                <td>{{$individuo->tipo}}</td>
                <td><b>Sexo:</b></td>
                <td>{{$individuo->sexo}}</td>
            </tr>
            <tr>
                <td><b>Peso:</b></td>
                <td>{{$individuo->peso}}</td>
                <td><b>Talla:</b></td>
                <td>{{$individuo->talla}}</td>
            </tr>
                <tr>
                <td><b>Marcas:</b></td>
                <td colspan="3">{{$individuo->marcas}}</td>
            </tr>

        </tbody>
    </table>
</div>