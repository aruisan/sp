@extends('layouts.dashboard')
@section('titulo')
    Nuevo Comprobante de Ingreso
@stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Nuevo Comprobante de Ingreso</b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">

        <li class="nav-item regresar">
            <a class="tituloTabs" href="{{ url('/administrativo/muebles') }}">Volver a los Bienes</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabHome">Comprobante de Ingreso</a>
        </li>
        <li class="nav-item">
            <a class="tituloTabs" href="{{ url('/administrativo/salida/create') }}">Nuevo Comprobante de Salida</a>
        </li>
    </ul>
    <div class="tab-content" style="background-color: white">
        <div id="tabHome" class="tab-pane active"><br>
            <div class="form-validation" id="crud">
                <form class="form-valide" action="{{url('/administrativo/muebles')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right" for="nombre">Número de Factura <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="num_fact" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Fecha</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" value="{{ Carbon\Carbon::today()->Format('d/m/Y')}}" disabled>
                                <input type="hidden" class="form-control" name="fecha" value="{{ Carbon\Carbon::today()->Format('Y-m-d')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 align-self-center">
                        <div class="form-group">
                            <label class="col-lg-4 col-form-label text-right">Factura<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="file" name="file" accept="application/pdf" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <br>
                        <table class="table table-bordered" id="tabla">
                            <thead>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Avaluo</th>
                            <th class="text-center">Vida Util</th>
                            <th class="text-center">Funcionario Asignado</th>
                            <th class="text-center"><i class="fa fa-trash-o"></i></th>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center">
                                    <select class="form-control" name="producto[]" required>
                                        <option>Selecciona un Producto</option>
                                        @foreach($productos as $producto)
                                            <option value="{{$producto->id}}">{{ $producto->nombre}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <textarea name="descripcion[]" class="form-control" required></textarea>
                                </td>
                                <td class="text-center">
                                    <select class="form-control" name="estado[]">
                                        <option value="0">Bueno</option>
                                        <option value="1">Regular</option>
                                        <option value="2">Malo</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control" name="cantidad[]" min="0" value="0" required>
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control" name="avaluo[]" min="0" value="0" required>
                                </td>
                                <td class="text-center">
                                    <input type="number" class="form-control" name="vida[]" min="0" value="0" required>
                                </td>
                                <td class="text-center">
                                    <select class="form-control" name="user[]" required>
                                        <option>Selecciona un Funcionario</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{ $user->name}} @if($user->type) - {{$user->type->nombre}} @endif</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 ml-auto">
                            <br>
                            <center>
                                <button type="button" v-on:click.prevent="nuevaFila" class="btn btn-primary"><i class="fa fa-plus"></i> &nbsp; Agregar Fila</button>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;Almacenar Comprobante </button>
                            </center>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>

        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        new Vue({
            el: '#crud',
            methods:{

                nuevaFila: function(){

                    $('#tabla tr:last').after('<tr>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <select class="form-control" name="producto[]" required>\n' +
                        '                                        <option>Selecciona un Producto</option>\n' +
                        '                                        @foreach($productos as $producto)\n' +
                        '                                            <option value="{{$producto->id}}">{{ $producto->nombre}}</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <textarea name="descripcion[]" class="form-control" required></textarea>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <select class="form-control" name="estado[]">\n' +
                        '                                        <option value="0">Bueno</option>\n' +
                        '                                        <option value="1">Regular</option>\n' +
                        '                                        <option value="2">Malo</option>\n' +
                        '                                    </select>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <input type="text" class="form-control" name="cantidad[]" required>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <input type="number" class="form-control" name="avaluo[]" min="0" value="0" required>\n' +
                        '                                </td>\n' +
                        '                                <td class="text-center">\n' +
                        '                                    <input type="number" class="form-control" name="vida[]" min="0" value="0" required>\n' +
                        '                                </td>' +
                        '                               <td class="text-center">\n' +
                        '                                    <select class="form-control" name="user[]" required>\n' +
                        '                                        <option>Selecciona un Funcionario</option>\n' +
                        '                                        @foreach($users as $user)\n' +
                        '                                            <option value="{{$user->id}}">{{ $user->name}} @if($user->type) - {{$user->type->nombre}} @endif</option>\n' +
                        '                                        @endforeach\n' +
                        '                                    </select>\n' +
                        '                                </td>' +
                        '                               <td class="text-center">' +
                        '                                   <input type="button" class="borrar btn-sm btn-danger" value=" - " /></td>\n' +
                        '                            </tr>');
                }
            }
        });
    </script>
@stop