@extends('layouts.dashboard')
@section('titulo')
    Registro Coso
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Registro Coso</b></h4>
        </strong>
    </div>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <button class="btn btn-sm btn-primary" onclick="nuevo_individuo()">
                Nuevo +
            </button>
            <br>
            <div class="table-responsive">
                @if($individuos->count() > 0)
                    <table class="table table-bordered" id="tabla_INV">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Fecha y Hora</th>
                            <th class="text-center">Ficha de Ingreso</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Peso</th>
                            <th class="text-center">Talla</th>
                            <th class="text-center">Sexo</th>
                            <th class="text-center">Color</th>
                            <th class="text-center">Marcas</th>
                            <th>Opcion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($individuos as $key => $item)
                            <tr class="text-center">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->date_at}}</td>
                                <td>{{ $item->ficha_ingreso }}</td>
                                <td>{{ $item->nombre}}</td>
                                <td>{{ $item->tipo}}</td>
                                <td>{{ $item->peso}}</td>
                                <td>{{ $item->talla}}</td>
                                <td>{{ $item->sexo}}</td>
                                <td>{{ $item->color}}</td>
                                <td>{{ $item->marcas}}</td>
                                <td>
                                    <a class="btn btn-success" href="{{route('coso.individuo.show', $item->id)}}" title="Ver">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-success" href="{{route('coso.archivo.create', $item->id)}}" title="Fotos">
                                        <i class="fa fa-picture-o" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-success" href="{{route('coso.comida.create', $item->id)}}" title="Comidas">
                                        <i class="fa fa-cutlery" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-success" href="{{route('coso.veterinario.create', $item->id)}}" title="Veterinario">
                                        <i class="fa fa-medkit" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No se encuentra ningun Registro.
                        </center>
                    </div>
                @endif
            </div>
        </div>

        <!-- The Modal -->
        <div class="modal" id="modal-formulario">
            <div class="modal-dialog">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Registro</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post" action="{{route('coso.individuo.store')}}" id="formulario">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha y Hora:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime-local" class="form-control" name="date_at" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Ficha de Ingreso:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="ficha_ingreso" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="nombre" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Tipo:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="tipo" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Peso:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="peso" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Talla:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="talla" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">sexo:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <select class="form-control" name="sexo">
                                            <option>Masculino</option>
                                            <option>Femenino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div><br>


                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Color:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="color" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Marcas:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="datetime" class="form-control" name="marcas" required>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                        <div class="row">
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="nombre">Observaciones:<span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <textarea class="form-control" name="observacion"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><br>

                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="save_form()">Guardar</button>
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



                const save_form = () => {
                    $('#formulario').submit();
                }

                const nuevo_individuo = () => {
                    $('#modal-formulario').modal();
                }
            </script>
        @stop