@extends('impuestos.layout')
@section('container')
    @include('modal.Impuestos.formularios')
    <div class="container">
    <div class="row">
        <div class="col-md-2 text-center">
            <img src="{{ asset('img/escudoIslas.png') }}" alt="Card image cap" width="80">
        </div>
        <div class="col-md-8 text-center">
            <h2 style="color: #319b31">Bienvenidos al Portal de Impuestos Municipales Municipio de Providencia y Santa Catalina Islas</h2>
        </div>
        <div class="row">
            <div class="col-md-4 text-center">
                <h3>Comunicados</h3>
                <hr>
                @if(!$rit)
                    <div class="row">
                        <div class="col-md-8">
                            <a class="btn btn-impuesto" title="Registrar RIT en el sistema" href="{{ route('impuestos.rit.create') }}"><i class="fa fa-file"></i></a>
                        </div>
                        <div class="col-md-4">
                            <h4 >No tiene registrado el RIT</h4>
                            Debe realizar el registro del RIT en el portal.
                        </div>
                    </div>
                    <hr>
                @elseif($rit->opciondeUso == "Cancelación")
                    <div class="row">
                        <div class="col-md-8">
                            <a class="btn btn-impuesto" title="Registrar RIT en el sistema" href="{{ route('impuestos.rit.restore') }}"><i class="fa fa-file"></i></a>
                        </div>
                        <div class="col-md-4">
                            <h4 >El RIT esta cancelado</h4>
                            Debe realizar el registro del RIT de nuevo en el portal.
                        </div>
                    </div>
                    <hr>
                @else
                    <div class="row">
                        <div class="col-md-8">
                            <button class="btn btn-impuesto" ><i class="fa fa-envelope"></i></button>
                        </div>
                        <div class="col-md-4">
                            <h4>A su correo electrónico</h4>
                            Tiene <b>{{$numComunicados}}</b> Correos por leer
                        </div>
                    </div>
                    <br>
                    <h3>Calendario Tributario</h3>
                    <table class="table">
                        <tbody>
                        <tr>
                            <th scope="row"><i class="fa fa-calendar"></i></th>
                            <td>Jul-08</td>
                            <td>Retefuente AG 2021 y Siguientes</td>
                        </tr>
                        <tr>
                            <th scope="row"><i class="fa fa-calendar"></i></th>
                            <td>Ago-09</td>
                            <td>Retefuente AG 2021 y Siguientes</td>
                        </tr>
                        </tbody>
                    </table>
                @endif
                <h3>Atención Inmediata</h3>
                <h4>Soporte técnico <i class="fa fa-whatsapp"></i> 3212420644</h4>
                <h4><i class="fa fa-whatsapp"></i> 3160100288</h4>
                <hr>
                <h3>Documentación de Ayuda</h3>
                <table class="table">
                    <tbody>
                    <tr><td><a href="impuestos/download/RESOLUCION_210_2.pdf"><i class="fa fa-file-pdf-o"></i> RESOLUCION 210 2</a></td></tr>
                    <tr><td><a href="impuestos/download/BANCO_BOGOTA.pdf"><i class="fa fa-file-pdf-o"></i> BANCO BOGOTA</a></td></tr>
                    <tr><td><a href="impuestos/download/calendario.pdf"><i class="fa fa-file-pdf-o"></i> calendario</a></td></tr>
                    <tr><td><a href="impuestos/download/Formato_Exogena_ICA_providencia_islas.xls"><i class="fa fa-file-excel-o"></i> Formato Exogena ICA providencia islas</a></td></tr>
                    <tr><td><a href="impuestos/download/Formulario_de_Retenciones_Industria_y_Comercio_2020.xlsx"><i class="fa fa-file-excel-o"></i> Formulario de Retenciones Industria y Comercio 2020</a></td></tr>
                    <tr><td><a href="impuestos/download/FORMULARIOUNICODECLARACIONPAGOINDUSTRIACOMERCIO.pdf"><i class="fa fa-file-pdf-o"></i> FORMULARIOUNICODECLARACIONPAGOINDUSTRIACOMERCIO</a></td></tr>
                    <tr><td><a href="impuestos/download/RUT_MUNICIPIO.pdf"><i class="fa fa-file-pdf-o"></i> RUT MUNICIPIO</a></td></tr>

                    </tbody>
                </table>
            </div>
            @if($rit)
                @if($rit->opciondeUso == "Cancelación")
                    <div class="col-md-8 text-center">
                        <h3>SU USUARIO CUENTA CON UN RIT CANCELADO. POR FAVOR REALICE EL REGISTRO DE NUEVO DANDO CLICK EN EL SIGUIENTE BOTÓN</h3>
                        <div class="row">
                            <a title="Registrar RIT en el sistema" class="btn btn-impuesto" href="{{ route('impuestos.rit.restore') }}" ><i class="fa fa-file"></i></a>
                        </div>
                        <hr>
                        @if($contribuyente->count() > 0)
                            <h3>Impuesto Predial y Sobretasa Bomberil</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-8">
                                        <a class="btn btn-impuesto" href="#"><i class="fa fa-usd"></i></a>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Pagos</h4>
                                        Pago electrónico o impresión
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-8">
                                        <a href="#" class="btn btn-impuesto" ><i class="fa fa-clipboard"></i></a>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Diligenciar y Presentar</h4>
                                        Presentación de Impuestos
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                    </div>
                @else
                    <div class="col-md-8 text-center">
                        <h3>Impuesto de Industria y Comercio Avisos y Tablero</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Destacados del mes</h3>
                                <hr>
                                <div class="col-md-8">
                                    <a href="#" class="btn btn-impuesto" @if(!$rit) disabled @endif><i class="fa fa-users"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Consultar información Exógena</h4>
                                    Información Reportada por Terceros
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Favoritos</h3>
                                <hr>
                                <div class="col-md-8">
                                    <a class="btn btn-impuesto" target="_blank" href="{{ route('impuestos.rit.pdf') }}"><i class="fa fa-file"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Obtener Copia RIT</h4>
                                    Descargue su certificado con solo un click
                                </div>
                            </div>
                            <div class="col-md-6">
                                <hr>
                                <div class="col-md-8">
                                    <button class="btn btn-impuesto" ><i class="fa fa-file-text"></i></button>
                                </div>
                                <div class="col-md-4">
                                    <h4>Consulta Obligación</h4>
                                    Consultar el estado de tus responsabilidades
                                </div>
                            </div>
                            <div class="col-md-6">
                                <hr>
                                <div class="col-md-8">
                                    <a class="btn btn-impuesto" href="{{ route('impuestos.rit.update') }}"><i class="fa fa-refresh"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Actualización</h4>
                                    Realice la actualización de su RIT
                                </div>
                            </div>
                            <div class="col-md-6">
                                <hr>
                                <div class="col-md-8">
                                    <a class="btn btn-impuesto" href="#"><i class="fa fa-dollar"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Pagos</h4>
                                    Pago electrónico o impresión
                                </div>
                            </div>
                            <div class="col-md-6">
                                <hr>
                                <div class="col-md-8">
                                    <a onclick="getModal()"  class="btn btn-impuesto" ><i class="fa fa-clipboard"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Diligenciar y Presentar</h4>
                                    Presentación de Impuestos
                                </div>
                            </div>
                        </div>
                        <hr>

                            <h3>Impuesto Predial y Sobretasa Bomberil</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-8">
                                        <a class="btn btn-impuesto" href="#"><i class="fa fa-usd"></i></a>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Pagos</h4>
                                        Pago electrónico o impresión
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-8">
                                        <a href="#" class="btn btn-impuesto" ><i class="fa fa-clipboard"></i></a>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Diligenciar y Presentar</h4>
                                        Presentación de Impuestos
                                    </div>
                                </div>
                            </div>
                            <hr>
                        
                    </div>
                @endif
            @else
                <div class="col-md-8 text-center">
                    <h3>SU USUARIO NO CUENTA CON UN RIT REGISTRADO EN EL SISTEMA. POR FAVOR REALICE EL REGISTRO DANDO CLICK EN EL SIGUIENTE BOTÓN</h3>
                    <div class="row">
                        <a title="Registrar RIT en el sistema" class="btn btn-impuesto" href="{{ route('impuestos.rit.create') }}" ><i class="fa fa-file"></i></a>
                    </div>
                    <hr>
                    @if($contribuyente->count() > 0)
                        <h3>Impuesto Predial y Sobretasa Bomberil</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-8">
                                    <a class="btn btn-impuesto" href="#"><i class="fa fa-usd"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Pagos</h4>
                                    Pago electrónico o impresión
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-8">
                                    <a href="#" class="btn btn-impuesto" ><i class="fa fa-clipboard"></i></a>
                                </div>
                                <div class="col-md-4">
                                    <h4>Diligenciar y Presentar</h4>
                                    Presentación de Impuestos
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@stop
@section('scripts')
    <script>
        function getModal(){
            $('#formularios').modal('show');
        }
    </script>
@stop