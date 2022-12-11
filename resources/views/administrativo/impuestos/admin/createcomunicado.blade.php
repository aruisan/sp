@extends('layouts.dashboard')
@section('titulo')  NUEVO COMUNICADO  @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center" translate="no">
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link " href="{{ url('/administrativo/impuestos/admin') }}"><i class="fa fa-arrow-circle-left"></i><i class="fa fa-home"></i> </a>
                </li>
                <li class="nav-item active"><a class="nav-link">NUEVO COMUNICADO</a></li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>ENVIO DE COMUNICADOS A USUARIOS</b></h4>
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/administrativo/impuestos/comunicado/make')}}" method="POST" enctype="multipart/form-data" id="prog">
                            {{ csrf_field() }}
                            <table id="TABLA1" class="table text-center">
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">COMUNICADO</th>
                                </tr>
                                <tbody>
                                <tr>
                                    <td>
                                        Titulo de Comunicado
                                        <br>
                                        <input type="text" name="titulo" class="form-control" required>
                                    </td>
                                    <td>
                                        Mensaje del Comunicado
                                        <br>
                                        <textarea name="mensaje" class="form-control" id="" cols="30" rows="10" required></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Usuarios a Enviar Comunicado
                                        <br>
                                        <select style="width: 100%" class="select-user" name="users[]" multiple="multiple" required>
                                            @foreach($rits as $rit)
                                                <option value="{{$rit->user->id}}">{{$rit->user->name}} - {{$rit->user->email}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <button type="submit" class="btn btn-impuesto text-center" style="font-size: 25px; color: white">Enviar Comunicado</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            $('.select-user').select2({
                theme: "classic"
            });
        });
    </script>
@stop
