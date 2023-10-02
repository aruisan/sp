@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Almacen Salidas</b></h4>
        </strong>
    </div>
    <div class="row">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#recientes">Recientes</a></li>
        <li><a data-toggle="tab" href="#historicos">Historicos</a></li>
    </ul>

    <div class="tab-content">
        <div id="recientes" class="tab-pane fade in active">
            <h3>Salidas Recientes  <a class="btn btn-primary" href="{{route('almacen.comprobante.egreso')}}">Nuevo</a></h3> 
            <table class="table">
                <thead>
                    <th>Salida</th>
                    <th>Fecha</th>
                    <th>Dependencia</th>
                    <th>Responsable</th>
                    <th>Valor</th>
                    <th>Estado Dependencia</th>
                    <th>Estado Secretaria</th>
                    <th>Estado Almacenista</th>
                    <th>Ver</th>
                </thead>
                <tbody>
                    @if($recientes->count() > 0)
                        @foreach($recientes as $salida)
                                <tr>
                                    <td>{{$salida->nombre}}</td>
                                    <td>{{$salida->fecha}}</td>
                                    <td>{{$salida->dependencia->name}}</td>
                                    <td>{{$salida->responsable->nombre}}</td>
                                    <td>${{number_format($salida->salidas_pivot->sum('total'), 0, ',','.')}}</td>
                                    <td>Aprobado</td>
                                    <td>
                                        @if(count($salida->status) == 0)
                                            @if(auth()->user()->validar_cargo('Secretaria'))
                                                <a href="{{ route('almacen.salida.autorizar.dependencia', $salida->id)}}" target="_blank">Editar y Aprobar</a> </br>
                                                {{--
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Sin Seleccionar
                                                    <span class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="open_modal('{{route('almacen.comprobante.egreso.autorizar', $salida->id)}}', 1)">Aprobar</a></li>
                                                        <li><a href="{{route('almacen.comprobante.egreso.autorizar', $salida->id)}}', 0)">Desaprobar</a></li>
                                                    </ul>
                                                </div>
                                                --}}
                                            @else
                                                En espera...
                                            @endif
                                        @else
                                            {{$salida->status[0] ? "Aprobado" : "Rechazado"}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($salida->status) > 1)
                                            {{$salida->status[0] ? "Aprobado" : "Rechazado"}}
                                        @elseif(count($salida->status) > 0)
                                            @if(auth()->user()->validar_cargo('almacenista') && $salida->status[0])
                                                <a href="{{ route('almacen.salida.autorizar.dependencia', $salida->id)}}" target="_blank">Editar y Aprobar</a> </br>
                                            @else
                                                En espera...
                                            @endif
                                        @else
                                            En espera....
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('almacen.egreso.show', $salida->id)}}" class="btn btn-primary"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a> </br>
                                    </td>
                                </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No tiene Salidas </td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div id="historicos" class="tab-pane fade">
            <h3>Salidas Historicas</h3>
            <table class="table">
                <thead>
                    <th>Salida</th>
                    <th>Fecha</th>
                    <th>Dependencia</th>
                    <th>Responsable</th>
                    <th>Valor</th>
                    <th>Estado Dependencia</th>
                    <th>Estado Secretaria</th>
                    <th>Estado Almacenista</th>
                    <th>Ver</th>
                </thead>
                <tbody>
                    @if($historicos->count() > 0)
                        @foreach($historicos as $salida)
                            <tr>
                                <td>{{$salida->nombre}}</td>
                                <td>{{$salida->fecha}}</td>
                                <td>{{$salida->dependencia->name}}</td>
                                <td>{{$salida->responsable->nombre}}</td>
                                <td>${{number_format($salida->salidas_pivot->sum('total'), 0, ',', '.')}}</td>
                                <td>Aprobado</td>
                                    <td>
                                        {{$salida->status[0] ? "Aprobado" : "Rechazado"}}
                                    </td>
                                    <td>
                                        {{$salida->status[1] ? "Aprobado" : "Rechazado"}}
                                    </td>
                                    <td>
                                        <a href="{{ route('almacen.egreso.show', $salida->id)}}" class="btn btn-primary"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a> </br>
                                    </td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No tiene Salidas </td></tr>
                    @endif
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

        const open_modal = (url, status) => {
            let title_status = status ? 'Aceptado' : 'Rechazado';
            $(`#estado`).val(status);
            $(`#modal-status`).modal();
            $('#form_status').attr('action', `${url}`);
        }

        const guardar = () => {
            $('#form_status').submit();
        }
    </script>
@stop