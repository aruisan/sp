@extends('layouts.dashboard')
@section('titulo')
    Coso Comidas
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Coso Consultas Medicas</b></h4>
        </strong>
    </div>
    <div class="row">
        @include('coso.components.tab', ['url' => 'consultas'])
    </div>
    <br>
        <button onclick="nueva_consulta()" class="btn btn-success">
            Nuevo +
        </button>
    <br>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active">
            <br>
            @include('coso.components.data')
            
            <div class="panel-group" id="accordion">
                @foreach($individuo->veterinarios as $k => $item)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$k}}">
                                    {{$item->nombre_veterinario}} {{$item->created_at->format('Y-m-d')}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$k}}" class="panel-collapse collapse {{$k == 0 ? 'in': ''}}">
                            <div class="panel-body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><b>Tarjeta Profesional:</b></td>
                                            <td>{{$item->tarjeta_profesional}}</td>
                                            <td><b>Cedula:</b></td>
                                            <td>{{$item->cedula}}</td>
                                            <td><b>Celular:</b></td>
                                            <td>{{$item->celular}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <table class="table">
                                    <thead>
                                        <th>Medicamento</th>
                                        <th>Dosis Diaria</th>
                                        <th>Hora</th>
                                        <th>Termino</th>
                                        <th>Aplica</th>
                                    </thead>
                                    <tbody>
                                        @foreach($item->medicinas as $medicina)
                                        <tr>
                                            <td>{{$medicina->medicamento}}</td>
                                            <td>{{$medicina->dosis_diaria}}</td>
                                            <td>{{$medicina->hora}}</td>
                                            <td>{{$medicina->termino}}</td>
                                            <td>{{$medicina->aplica}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                @endforeach
            </div>
            
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="modal-formulario">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Registro Veterinaria</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action="{{route('coso.veterinario.store', $individuo->id)}}" id="form">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Veterinario:<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="nombre_veterinario" required>
                                </div>
                            </div>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-12 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Tarjeta Profesional:<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="tarjeta_profesional" required>
                                </div>
                            </div>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-12 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Cedula de Ciudadania<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="cedula" required>
                                </div>
                            </div>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-12 align-self-center">
                            <div class="form-group">
                                <label class="col-lg-4 col-form-label text-right" for="nombre">Celular:<span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="celular" required>
                                </div>
                            </div>
                        </div>
                    </div><br>

                    <div>
                        <center>
                            <button onclick="aumentar_medicamento()" class="btn btn-primary" type="button" style="margin-bottom:10px">+</button>
                        </center>
                    </div>

                    
                    <div class="row">
                        <table class="table">
                            <thead>
                                <th>X</th>
                                <th>Medicamento</th>
                                <th>Dosis Diaria</th>
                                <th>Hora</th>
                                <th>Termino</th>
                                <th>Aplica</th>
                            </thead>
                            <tbody id="body"></tbody>
                        </table>
                    </div>
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

        const nueva_consulta = () => {
            $('#body').empty();
            aumentar_medicamento();
            $('#modal-formulario').modal();
        }

        const guardar = () => {
            $('#form').submit();
        }

        
        const aumentar_medicamento = () =>{
            let articulo = `<tr>
                    <td><input type="button" class="borrar btn btn-danger" value="X" /></td>
                    <td>
                        <input type="text" class="form-control" name="medicamento[]" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="dosis_diaria[]" required>
                    </td>
                    <td>
                        <input type="time" class="form-control" name="hora[]" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="termino[]" required>
                    </td>
                    <td>
                        <select name="aplica[]" class="form-control">
                            <option>Si</option>
                            <option>No</option>
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