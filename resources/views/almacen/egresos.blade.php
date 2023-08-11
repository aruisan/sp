@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de Egreso</b></h4>
        </strong>
    </div>
    <div class="row">
        <form action="{{route('almacen.egreso.update', $egreso->id)}}" method="post">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Comprobante de Egreso No. {{$egreso->id}}<span class="text-danger">*</span></label>
                    </div>
                </div>
            </div><br>
            <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Dependencia:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class="form-control" name="dependencia_id" required>
                                @foreach($dependencias as $dependencia)
                                    <option value="{{$dependencia->id}}">
                                        {{$dependencia->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div><br>

             <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Responsable:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class="form-control" name="responsable_id" required>
                                @foreach($responsables as $responsable)
                                    <option value="{{$responsable->id}}">
                                        {{$responsable->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                    </div>
                </div>
            </div><br>

            <div>
                <center>
                    <button onclick="aumentar_articulo()" class="btn btn-primary" type="button" style="margin-bottom:10px">+</button>
                </center>
            </div>

            
            <div class="row">
                <table class="table">
                    <thead>
                        <th>X</th>
                        <th>Codigo</th>
                        <th>Articulo</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>Total</th>
                    </thead>
                    <tbody id="body"></tbody>
                </table>
            </div>
            <div class="row" style="margin-top:10px;">
                <center>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </center>
            </div>
        </form>
    </div>
@stop
@section('js')
    <script>
        let contador_articulo = 0;


        $('#tabla_INV').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $(document).ready(function(){
            aumentar_articulo();
        });

        const aumentar_articulo = () =>{
            let articulo = `<tr>
                    <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                    <td>
                        <input type="hidden" class="form-control" name="id[]" id="id_${contador_articulo}"  readonly>
                        <input type="text" class="form-control" onchange="load_info(${contador_articulo})" id="codigo_${contador_articulo}" required>
                    </td>
                    <td><input type="text" class="form-control" id="nombre_${contador_articulo}" readonly></td>
                    <td><input type="text" class="form-control" id="referencia_${contador_articulo}" readonly></td>
                    <td><input type="text" class="form-control" name="cantidad[]" id="cantidad_${contador_articulo}" onchange="total(${contador_articulo})" required></td>
                    <td><input type="text" class="form-control" id="valor_${contador_articulo}" readonly></td>
                    <td><input type="text" class="form-control" id="total_${contador_articulo}" readonly></td>
                </tr>`;
            $('#body').append(articulo);
            contador_articulo+=1;
        }

        const load_info = async contador => {
            let code = $(`#codigo_${contador}`).val();
            let data = await fetch(`/almacen/articulo/ajax/${code}`)
                            .then(response => response.json())
                            .then(data => data);
            $(`#id_${contador}`).val(data.id);
            $(`#nombre_${contador}`).val(data.nombre_articulo);
            $(`#referencia_${contador}`).val(data.referencia);
            $(`#nombre_${contador}`).val(data.nombre_articulo);
            $(`#valor_${contador}`).val(data.valor_unitario);
            $(`#cantidad_${contador}`).val(data.stock);
             $(`#total_${contador}`).val(parseInt(data.stock) * parseInt(data.valor_unitario));
        }

        const total = contador => {
            let cant = $(`#cantidad_${contador}`).val();
            let valor = $(`#valor_${contador}`).val();
            $(`#total_${contador}`).val(parseInt(cant) * parseInt(valor));
        }


        $(document).on('click', '.borrar', function(event) {
  event.preventDefault();
  $(this).closest('tr').remove();
});
    </script>
@stop