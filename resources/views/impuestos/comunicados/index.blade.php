@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
        <div class="col-md-12 align-self-center">
            <div class="breadcrumb text-center">
                <strong>
                    <h4><b>COMUNICADOS</b></h4>
                </strong>
            </div>
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link" href="{{ url('/impuestos') }}" ><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#tabPagos">Comunicados</a></li>
            </ul>
            <div class="tab-content" >
                <div id="tabPagos" class="tab-pane fade in active"><br>
                    <br>
                    <div class="col-md-4">
                        <div class="table-responsive">
                            @if(count($comunicados) > 0)
                                <table class="table">
                                    <tbody>
                                    @foreach($comunicados as $index => $comunicado)
                                        <tr onclick='tdclick({{$comunicado->id}})' style="cursor: pointer ">
                                            <td class="text-right" style="vertical-align: middle">
                                                @if($comunicado->estado == "Enviado")
                                                    <i id="iconStatus{{$comunicado->id}}" style="color: #940808" class="fa fa-bell-o"></i>
                                                @else
                                                    <i id="iconStatus{{$comunicado->id}}" class="fa fa-check"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $comunicado->comunicado_title }}
                                                <br>
                                                {{ \Carbon\Carbon::parse($comunicado->enviado)->format('d-m-Y H:m:s') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-danger">
                                    <center>
                                        No ha recibido ningun comunicado.
                                    </center>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="bodyComunicado" class="col-md-8" style="display: none">
                        <div class="col-md-2"><img src="https://www.siex-concejoprovidenciaislas.com.co/img/escudoIslas.png"  height="60"></div>
                        <div id="tituloComunicado" class="col-md-8 text-center">Title</div>
                        <div class="col-md-2"><img src="https://www.siex-concejoprovidenciaislas.com.co/img/masporlasislas.png"  height="60"></div>
                        <br><br>
                        <div id="fechaComunicado" class="col-md-12"></div>
                        <br>
                        <div id="remitenteComunicado" class="col-md-12"></div>
                        <br><br>
                        <div id="textoComunicado" class="col-md-12"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        function tdclick(id){
            $.ajax({
                method: "POST",
                url: "/impuestos/comunicados/message",
                data: { "id": id,
                    "_token": $("meta[name='csrf-token']").attr("content"),
                }
            }).done(function(datos) {
                $("#bodyComunicado").show();
                document.getElementById("tituloComunicado").innerHTML = datos['comunicado_title'];
                document.getElementById("fechaComunicado").innerHTML = "Enviado: "+datos['enviado'];
                document.getElementById("remitenteComunicado").innerHTML = "Enviado Por: "+datos['remitente'];
                document.getElementById("textoComunicado").innerHTML = datos['comunicado_body'];
                document.getElementById("iconStatus"+id).className = "fa fa-check";
                document.getElementById("iconStatus"+id).style.color = "black";
            }).fail(function() {
                toastr.warning('OCURRIO UN ERROR AL OBTENER EL COMUNICADO');
            });
        }
    </script>
@stop
