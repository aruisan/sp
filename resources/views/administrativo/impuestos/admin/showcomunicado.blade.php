@extends('layouts.dashboard')
@section('titulo') COMUNICADO  @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center" translate="no">
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link " href="{{ url('/administrativo/impuestos/admin') }}"><i class="fa fa-arrow-circle-left"></i><i class="fa fa-home"></i> </a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="nuevo" class="tab-pane fade in active">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>COMUNICADO ENVIADO</b></h4>
                    </strong>
                </div>
                <div class="col-lg-12">
                    <table class="table text-center">
                        <tr style="background-color: #0e7224; color: white">
                            <th scope="row">Fecha de Envio</th>
                            <th scope="row">Titulo</th>
                            <th scope="row"> Mensaje</th>
                            <th scope="row"> Estado</th>
                        </tr>
                        <tbody>
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($comunicado->enviado)->format('d-m-Y') }}</td>
                            <td>{{ $comunicado->comunicado_title }}</td>
                            <td>{{ $comunicado->comunicado_body }}</td>
                            <td>{{ $comunicado->estado }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <table class="table text-center">
                        <tr style="background-color: #0e7224; color: white">
                            <th scope="row">Remitente</th>
                            <th scope="row">Destinatario</th>
                            <th scope="row"> Fecha Visualización</th>
                        </tr>
                        <tbody>
                        <tr>
                            <td>{{ $comunicado->remitente->name}} - {{$comunicado->remitente->email}}</td>
                            <td>{{ $comunicado->destinatario->name}} - {{$comunicado->destinatario->email}}</td>
                            <td>
                                @if($comunicado->visto != null)
                                    {{ \Carbon\Carbon::parse($comunicado->visto)->format('d-m-Y H:i:s') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
