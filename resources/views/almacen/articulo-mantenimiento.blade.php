@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Inventario</b></h4>
        </strong>
    </div>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><b>Nombre del Articulo:</b></td>
                                <td>{{$articulo->nombre_articulo}}</td>
                            </tr>
                            <tr>
                                <td><b>Codigo:</b></td>
                                <td>{{$articulo->codigo}}</td>
                            </tr>
                            <tr>
                                <td><b>Referencia:</b></td>
                                <td>{{$articulo->referencia}}</td>
                            </tr>
                            <tr>
                                <td><b>Stock:</b></td>
                                <td>{{$articulo->stock}}</td>
                            </tr>
                            <tr>
                                <td><b>valor:</b></td>
                                <td>{{$articulo->valor_unitario}}</td>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <br>
                <button data-toggle="modal" data-target="#myModal" class="btn btn-success">
                    Nuevo +
                </button>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered" id="tabl">
                    <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Responsable</th>
                        <th class="text-center">Descripcion</th>
                        <th class="text-center">Fecha</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($articulo->mantenimientos as $key => $item)
                        <tr class="text-center">
                            <td>{{$key+1}}</td>
                            <td>{{$item->responsable->nombre}}</td>
                            <td>{{$item->descripcion}}</td>
                            <td>{{$item->created_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Nuevo Mantenimiento</h4>
                </div>
                <form method="post" action="{{route('almacen.articulo.mantenimiento.store', $articulo->id)}}">
                {{ csrf_field() }}
                    <div class="modal-body">
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
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Descripcion:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <textarea class="form-control" name="descripcion" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><br>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
            </script>
        @stop