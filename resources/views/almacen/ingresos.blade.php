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
        <form action="{{route('almacen.ingreso.update', $ingreso->id)}}" method="post">
            {{ csrf_field() }}
            {!! method_field('PUT') !!}
            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Comprobante de Ingreso No. {{$ingreso->id}}<span class="text-danger">*</span></label>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-12 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">No. Factura:<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="factura" required>
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
                            <select class="form-control select" name="proovedor_id" required>
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
                    <button onclick="aumentar_articulo()" class="btn btn-primary" type="button" style="margin-bottom:10px"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Cargue Plantilla</button>
                    <button onclick="window.open('{{asset('file_public/PLANTILLA CARGA MASIVA ALMACEN ENTRADAS V1.xlsx')}}')" class="btn btn-primary" type="button" style="margin-bottom:10px"><i class="fa fa-cloud-download" aria-hidden="true"></i> Descargue Plantilla</button>
                </center>
            </div>

            
            <div class="row">
                <table class="table">
                    <thead>
                        <th>X</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Codigo</th>
                        <th>Referencia</th>
                        <th>Presentación</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>Vida Util</th>
                        <th>estado</th>
                        <th>tipo</th>
                        <th>Debito</th>
                        <th>Credito</th>
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
        const pucs_creditos = @json($pucs_credito);
        const pucs_debitos = @json($pucs_debito);
        let contador_item = 0;

        $('#tabla_INV').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $(document).ready(function(){
            console.log('debito', pucs_debitos);
            console.log('credito', pucs_creditos);
            aumentar_articulo();
            $('.select').select2();
        });

        const seleccionar_debito = index => {
            let puc_debito_id = $(`#select_ccd_${index}`).val();
            let puc_debito = pucs_debitos.find(pd => pd.id == puc_debito_id);
            let puc_credito = pucs_creditos.find(p => p.id == puc_debito.almacen_pucs_creditos[0].id);
            $(`#puc_credito_${index}`).text(`${puc_credito.code} ${puc_credito.concepto}`);
        }
        
        

        const aumentar_articulo = () =>{
            let articulo = `<tr>
                    <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                    <td><input type="text" class="form-control" name="nombre_articulo[]" required></td>
                    <td><input type="text" class="form-control" name="marca[]" required></td>
                    <td><input type="text" class="form-control" name="codigo[]" required></td>
                    <td><input type="text" class="form-control" name="referencia[]" required></td>
                    <td><input type="text" class="form-control" name="presentacion[]" required></td>
                    <td><input type="number" class="form-control" name="cantidad[]" required></td>
                    <td><input type="number" class="form-control" name="valor_unitario[]" required></td>
                    <td><input type="number" class="form-control" name="vida_util[]" required></td>
                    <td> 
                        <select class="form-control" name="estado[]">
                            <option>Bueno</option>
                            <option>Regular</option>
                            <option>Malo</option>
                        </select>
                    </td>
                    <td> 
                        <select class="form-control" name="tipo[]">
                            <option>Devolutivo</option>
                            <option>Consumo</option>
                            <option>Inmueble Terreno</option>
                            <option>Inmueble Edificio</option>
                        </select>
                    </td>
                    <td>
                        <select name="ccd[]" class="form-control" onchange="seleccionar_debito(${contador_item})" required id="select_ccd_${contador_item}">
                            @foreach($pucs_debito as $puc)
                                <option value="{{$puc->id}}">{{$puc->code}} -- {{$puc->concepto}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <span id="puc_credito_${contador_item}"></span>
                    </td>

                </tr>`;
            $('#body').append(articulo);
            seleccionar_debito(contador_item);
            contador_item +=1;
        }


        $(document).on('click', '.borrar', function(event) {
  event.preventDefault();
  $(this).closest('tr').remove();
});
    </script>
@stop