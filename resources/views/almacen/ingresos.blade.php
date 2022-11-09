@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de ingreso</b></h4>
        </strong>
    </div>
    <div class="row">
        <form action="{{route('almacen.ingreso.update', $factura->id)}}" method="post">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Comprobante de Ingreso No. {{$factura->id}}<span class="text-danger">*</span></label>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">No. Factura:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="numero_factura" required>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha Factura:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control" name="fecha_factura" required>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Contrato:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="contrato" required>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha del Contrato:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control" name="fecha_contrato" required>
                        </div>
                    </div>
                </div>
            </div><br>
           
             <div class="row">
                 <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Proovedor:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class="form-control" name="proovedor_id" required>
                                @foreach($proovedores as $proovedor)
                                    <option value="{{$proovedor->id}}">
                                        {{$proovedor->nombre}}
                                    </option>
                                @endforeach
                            </select>
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
                        <th>Nombre</th>
                        <th>Codigo</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>ccd</th>
                        <th>ccc</th>
                        <th>tipo</th>
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
                    <td><input type="text" class="form-control" name="nombre_articulo[]" required></td>
                    <td><input type="text" class="form-control" name="codigo[]" required></td>
                    <td><input type="text" class="form-control" name="referencia[]" required></td>
                    <td><input type="text" class="form-control" name="cantidad[]" required></td>
                    <td><input type="text" class="form-control" name="valor_unitario[]" required></td>
                    <td><input type="text" class="form-control" name="ccd[]" required></td>
                    <td><input type="text" class="form-control" name="ccc[]" required></td>
                    <td> 
                        <select class="form-control" name="tipo[]">
                            <option>Devolutivo</option>
                            <option>Consumo</option>
                            <option>Inmueble</option>
                            <option>Terreno</option>
                        </select>
                    </td>

                </tr>`;
            $('#body').append(articulo);
        }


        $(document).on('click', '.borrar', function(event) {
  event.preventDefault();
  $(this).closest('tr').remove();
});
    </script>
@stop