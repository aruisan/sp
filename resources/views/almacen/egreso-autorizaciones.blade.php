@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="container">
        <form method="post" id="formulario_status_salida" action="{{route('almacen.comprobante.egreso.autorizar', $egreso->id)}}">
        {{ csrf_field() }}
        <input type="hidden" id="get_observacion" name="observacion">
        <input type="hidden" id="get_status" name="status">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Salida de Almacen</b></h4>
            </strong>
        </div>
        <div class="row">
                <div class="row">
                    <div class="col-md-12 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">{{$egreso->nombre}}<span class="text-danger">*</span></label>
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-12 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Dependencia: {{$egreso->dependencia->name}}</label>
                        </div>
                    </div>
                </div><br>

                <div class="row">
                    <div class="col-md-12 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Responsable: {{$egreso->responsable->nombre}}</label>
                        </div>
                    </div>
                </div><br>

                <div class="row">
                    <div class="col-md-12 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha: {{$egreso->fecha}}</label>
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-12 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-1 col-form-label text-right" for="nombre">seleccione Clase:<span class="text-danger">*</span></label>
                            <div class="col-lg-5">
                                <select class="form-control select" name="ccc" required id="debito_id">
                                    @foreach($pucs_credito as $puc)
                                        <option value="{{$puc->id}}">
                                            {{$puc->concepto}} -- {{$puc->code}}
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
                            <th>Codigo</th>
                            <th>Articulo</th>
                            <th>Referencia</th>
                            <th>Existencia</th>
                            <th>Cantidad</th>
                            <th>Valor Unitario</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                            @foreach($egreso->salidas_pivot as $item)
                                <tr>
                                    <td>{{$item->articulo->codigo}}</td>
                                    <td>{{$item->articulo->nombre_articulo}}</td>

                                    <td>{{$item->articulo->referencia}}</td>
                                    <td>
                                        {{$item->articulo->stock}}
                                    </td>
                                    <td>
                                        @if(auth()->user()->validar_cargo('Secretaria'))
                                            <input type="hidden" value="{{$item->id}}" class="form-control" name="id[]">
                                            <input type="text" value="{{$item->cantidad}}" class="form-control" name="cantidad[{{$item->id}}]">
                                        @else
                                            {{$item->cantidad}}
                                        @endif
                                    </td>
                                    <td>{{$item->articulo->valor_unitario}}</td>
                                    <td>{{$item->cantidad * $item->articulo->valor_unitario}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="row">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="button" onclick="guardar(0)">Rechazar</button>
                    </div>
                    <input type="text" class="form-control" placeholder="Observaciön" aria-label="Recipient's username" aria-describedby="basic-addon2" id="set_observacion">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="button" onclick="guardar(1)">Aprobar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="modal-status" class="modal fade" role="dialog">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form method="post" id="form_status">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" name="estado" id="estado">
                    <div class="row" id="componen_cantidad">
                        <div class="col-md-12 align-self-center"> 
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Cantidad:<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" name="cantidad" id="cantidad" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Observación:<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <textarea  class="form-control" name="observacion" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
        </div>
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

        $('.select').select2();

        const open_modal = (url, status, nombre_articulo, tiene_cantidad, cantidad) => {
            let title_status = status ? 'Aceptado' : 'Rechazado';
            if(tiene_cantidad && status){
                $('#cantidad').val(cantidad).show();
                $('#componen_cantidad').show();
            }else{
                $('#componen_cantidad').hide();
            }
            $(`#estado`).val(status);
            $(`#modal-title`).text(`${nombre_articulo} ${title_status}`);
            $(`#modal-status`).modal();
            $('#form_status').attr('action', `${url}`);
        }

        const guardar = status => {
            let observacion = $('#set_observacion').val();
            $('#get_observacion').val(observacion);
            $('#get_status').val(status);
            $('#formulario_status_salida').submit();
        }
    </script>
@stop