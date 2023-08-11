@extends('layouts.dashboard')
@section('titulo')
    Coso Comidas
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Coso Comidas</b></h4>
        </strong>
    </div>
    <div class="row">
        @include('coso.components.tab', ['url' => 'comidas'])
    </div>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            @include('coso.components.data')
            <br>
                <button onclick="nueva_comida()" class="btn btn-success">
                    Nuevo +
                </button>
            <br>

            <div class="row">
                <table class="table">
                    <thead>
                        <th>#</th>
                        <th>Procion Diaria (Kg)</th>
                        <th>Mañana (Kg)</th>
                        <th>Tarde (Kg)</th>
                        <th>Noche (Kg)</th>
                    </thead>
                    <tbody id="body">
                        @foreach($individuo->comidas as $k => $comida)
                            <tr>
                                <td>{{$k+1}}</td>
                                <td>{{$comida->porcion_diaria}}</td>
                                <td>{{$comida->porciones[0]}}</td>
                                <td>{{$comida->porciones[1]}}</td>
                                <td>{{$comida->porciones[2]}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>

        <!-- The Modal -->
        <div class="modal" id="modal-formulario">
            <div class="modal-dialog">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Registro de comida</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post" action="{{route('coso.comida.store', $individuo->id)}}" id="form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Porcion diaria:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="porcion_diaria" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Mañana:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="porciones[0]" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Tarde:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="porciones[1]" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Noche:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="porciones[2]" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
                </div>

                </div>
            </div>
        </div>
        @stop
        @section('css')
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

                const nueva_comida = () => {
                    $('#modal-formulario').modal();
                }

                const guardar = () => {
                    $('#form').submit();
                }
            </script>
        @stop