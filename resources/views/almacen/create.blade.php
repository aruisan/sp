@extends('layouts.dashboard')
@section('titulo')
    Inventario
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Crear Articulo</b></h4>
        </strong>
    </div>
    <div class="row">
        <form action="{{route('almacen.store')}}" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Nombre Articulo:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="nombre_articulo" required>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Dependencia:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class="form-control" name="dependencia_id" required>
                                @foreach($dependencias as $dependencia)
                                    <option value="{{$dependencia->id}}">
                                        {{$dependencia->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-3 col-form-label text-right" for="nombre">Codigo:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="codigo" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-3 col-form-label text-right" for="nombre">cantidad:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="cantidad" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-3 col-form-label text-right" for="nombre">referencia:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="referencia" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-3 col-form-label text-right" for="nombre">Valor unitario:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="valor_unitario" required>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Ncomin Ingreso:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="ncomin_ingreso" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha Ingreso:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" class="form-control" name="fecha_ingreso" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Ncomin Egreso:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="ncomin_egreso" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 align-self-center">
                    <div class="form-group">
                        <label class="col-lg-4 col-form-label text-right" for="nombre">Fecha Egreso:<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="date" class="form-control" name="fecha_egreso" required>
                        </div>
                    </div>
                </div>
            </div>
            <br><br><br><br>
            <center>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </center>
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
    </script>
@stop