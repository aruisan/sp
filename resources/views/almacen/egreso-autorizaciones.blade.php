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
                <table class="table">
                    <thead>
                        <th>Codigo</th>
                        <th>Articulo</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>Total</th>
                        <th>Dependencia</th>
                        <th>Secretaria</th>
                        <th>Almacenista</th>
                    </thead>
                    <tbody>
                        @foreach($egreso->salidas_pivot as $item)
                            <tr>
                                <td>{{$item->articulo->codigo}}</td>
                                <td>{{$item->articulo->nombre_articulo}}</td>
                                <td>{{$item->articulo->referencia}}</td>
                                <td>{{$item->cantidad}}</td>
                                <td>{{$item->articulo->valor_unitario}}</td>
                                <td>{{$item->cantidad * $item->articulo->valor_unitario}}</td>
                                <td>Aprobado</td>
                                <td>
                                    @if(count($item->status) == 0)
                                        @if(auth()->user()->validar_cargo('secretaria'))
                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Sin Seleccionar
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="open_modal('{{route('almacen.aticulo.salida.autorizar', $item->salida)}}', 1, '{{$item->articulo->nombre_articulo}}', {{count($item->status) > 0 ? 1 : 0}}, {{$item->cantidad}})">Aprobar</a></li>
                                                    <li><a href="{{route('almacen.aticulo.salida.autorizar', $item->salida)}}', 0, '{{$item->articulo->nombre_articulo}}', {{count($item->status) > 0 ? 1 : 0}}, {{$item->cantidad}})">Desaprobar</a></li>
                                                </ul>
                                            </div>
                                        @else
                                            En espera...
                                        @endif
                                    @else
                                        {{$item->status[0] ? "Aceptadeo" : "Rechazado"}}
                                    @endif
                                </td>
                                <td>
                                    @if(count($item->status) == 1)
                                        @if(auth()->user()->validar_cargo('almacenista'))
                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Sin Seleccionar
                                                <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="open_modal('{{route('almacen.aticulo.salida.autorizar', $item->salida)}}', 1, '{{$item->articulo->nombre_articulo}}', {{count($item->status) > 0 ? 1 : 0}}, {{$item->cantidad}})">Aprobar</a></li>
                                                    <li><a href="{{route('almacen.aticulo.salida.autorizar', $item->salida)}}', 0, '{{$item->articulo->nombre_articulo}}', {{count($item->status) > 0 ? 1 : 0}}, {{$item->cantidad}})">Desaprobar</a></li>
                                                </ul>
                                            </div>
                                        @else
                                            En espera...
                                        @endif
                                    @else
                                        {{$item->status[0] ? "Aceptadeo" : "Rechazado"}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

        const guardar = () => {
            $('#form_status').submit();
        }
    </script>
@stop