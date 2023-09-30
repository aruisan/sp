@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobante de Egreso No. {{$egreso->index + 1}} </b></h4>
        </strong>
    </div>
    <div class="row">
        <form action="{{route('almacen.egreso.update', $egreso->id)}}" method="post">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Dependencia:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select class="form-control select" name="dependencia_id" required>
                                @foreach($dependencias as $dependencia)
                                    <option value="{{$dependencia->id}}">
                                        {{$dependencia->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Responsable:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select class="form-control select" name="responsable_id" required>
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
                 <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-1 col-form-label text-right" for="nombre">seleccione Clase:<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select class="form-control select" name="ccd[]" multiple required id="debito_id">
                                @foreach($pucs_debito as $puc)
                                    <option value="{{$puc->id}}" {{$puc->almacen_items->count() == 0 ? "disabled='disabled'" : ""}} >
                                        {{$puc->concepto}} -- {{$puc->code}} {{$puc->almacen_items->count() == 0 ? " -- No tiene articulos Disponibles" : ""}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div><br>

            
            <div class="row">
                <table class="table">
                    <thead>
                        <th>X</th>
                        <th>Codigo</th>
                        <th>Articulo</th>
                        <th>Referencia</th>
                        <th>Cantidad disponible</th>
                        <th>Cantidad solicitar</th>
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
        const articulos = @json($articulos);
        let articulos_select = [];



        $('#tabla_INV').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $(document).ready(function(){
            console.log('articulos', articulos);
        });

        $('#debito_id').on('change', function(){
            select_debito();
        })

        const select_debito = () =>{
            let debitos_id = $('#debito_id').val();
            articulos_select = articulos.filter(a => debitos_id.includes(a.ccd.toString()) && a.stock > 0 );
            console.log([debitos_id, articulos, articulos_select])
            aumentar_articulo();
        }

        $('.select').select2();

        const aumentar_articulo = () =>{
            contador_articulo = 0;
            $('#body').empty();
            articulos_select.forEach(a => {
                let articulo = `<tr>
                        <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                        <td>
                            <input type="hidden" class="form-control" name="id[]" value="${a.id}"  readonly>
                            <input type="text" class="form-control"  value="${a.codigo}" readonly>
                        </td>
                        <td><input type="text" class="form-control" value="${a.nombre_articulo}" readonly></td>
                        <td><input type="text" class="form-control" value="${a.referencia}" readonly></td>
                        <td><input type="text" class="form-control" value="${a.stock}" readonly></td>
                        <td><input type="number" class="form-control" name="cantidad[]" onchange="total(${contador_articulo})" id="cantidad_${contador_articulo}"  value="0" required></td>
                        <td><input type="text" class="form-control" value="${a.valor_unitario}" id="valor_${contador_articulo}" readonly></td>
                        <td><input type="text" class="form-control" id="total_${contador_articulo}" readonly></td>
                    </tr>`;
                $('#body').append(articulo);
                total(contador_articulo);
            contador_articulo+=1;
            })
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
            $(`#ccd_${contador}`).val(`${data.puc_debito.concepto} - ${data.puc_debito.code}`);
            $(`#ccc_${contador}`).val(`${data.puc_credito.concepto} - ${data.puc_credito.code}`);
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