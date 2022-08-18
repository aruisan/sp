@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white">
            <div class="col-md-12 align-self-center">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>FORMULARIO DECLARACION DE RETENCIONES ICA</b></h4>
                        <h4><b>Municipio de Providencia y Santa Catalina</b></h4>
                        <h4><b>Secretaria de Hacienda Municipal</b></h4>
                        TD: Privada <br>
                        FORMULARIO WEB 02
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/impuestos/ICA/retenedor')}}" method="POST" enctype="multipart/form-data" id="formulario">
                            {{ csrf_field() }}
                            {{-- ENCABEZADO--}}
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">FORMULARIO UNICO NACIONAL DE DECLARACION Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <th scope="row" >MUNICIPIO O DISTRITO </th>
                                    <th scope="row" colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td>DEPARTAMENTO</td>
                                    <td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td>1. Año
                                        <br>
                                        <select class="form-control" id="añoGravable" name="añoGravable">
                                            <option value="2022" @if($action == "Corrección" and $ica->añoGravable == "2022" ) selected @endif>2022</option>
                                            <option value="2023" @if($action == "Corrección" and $ica->añoGravable == "2023" ) selected @endif>2023</option>
                                        </select>
                                    </td>
                                    <td>2. Periodo<br>
                                        <select class="form-control" id="periodo" name="periodo">
                                            <option value="1" @if($action == "Corrección" and $ica->periodo == "1" ) selected @endif>Enero</option>
                                            <option value="2" @if($action == "Corrección" and $ica->periodo == "2" ) selected @endif>Febrero</option>
                                            <option value="3" @if($action == "Corrección" and $ica->periodo == "3" ) selected @endif>Marzo</option>
                                            <option value="4" @if($action == "Corrección" and $ica->periodo == "4" ) selected @endif>Abril</option>
                                            <option value="5" @if($action == "Corrección" and $ica->periodo == "5" ) selected @endif>Mayo</option>
                                            <option value="6" @if($action == "Corrección" and $ica->periodo == "6" ) selected @endif>Junio</option>
                                            <option value="7" @if($action == "Corrección" and $ica->periodo == "7" ) selected @endif>Julio</option>
                                            <option value="8" @if($action == "Corrección" and $ica->periodo == "8" ) selected @endif>Agosto</option>
                                            <option value="9" @if($action == "Corrección" and $ica->periodo == "9" ) selected @endif>Septiembre</option>
                                            <option value="10" @if($action == "Corrección" and $ica->periodo == "10" ) selected @endif>Octubre</option>
                                            <option value="11" @if($action == "Corrección" and $ica->periodo == "11" ) selected @endif>Noviembre</option>
                                            <option value="12" @if($action == "Corrección" and $ica->periodo == "12" ) selected @endif>Diciembre</option>
                                        </select>
                                    </td>
                                    <td style="vertical-align: middle">
                                        @if($action == "Declaración")
                                            DECLARACIÓN INICIAL
                                            <input type="hidden" name="opciondeUso" value="Declaración">
                                        @else
                                            CORRECCIÓN
                                            <input type="hidden" name="opciondeUso" value="Corrección">
                                            <input type="hidden" name="ica_id" value="{{ $ica->id }}">
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA A. INFORMACIÓN DEL CONTRIBUYENTE --}}
                            <table id="TABLA2" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="4">INFORMACIÓN DEL CONTRIBUYENTE</th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table class="table text-center table-bordered">
                                            <tr>
                                                <td colspan="3">
                                                    Naturaleza Juridica: <b>{{$rit->natJuridiContri}}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> Nombre y apellidos o razón Social: {{ $rit->apeynomContri }}</td>
                                                <td>{{ $rit->tipoDocContri }} No. {{ $rit->numDocContri }}</td>
                                                <td>Dirección de Notificación: {{ $rit->dirNotifContri }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Municipio o Distrito de la Dirección de Notificación: PROVIDENCIA Y SANTA CATALINA ISLAS </td>
                                                <td>Departamento: ARCHIPIELAGO DE SAN ANDRES </td>
                                            </tr>
                                            <tr>
                                                <td>Teléfono Móvil: {{ $rit->movilContri }}</td>
                                                <td colspan="2">Correo electrónico: {{ $rit->emailContri }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    11. Calidad de agente reteción <br> Código Agente
                                                    <select class="form-control" id="codAgente" name="codAgente">
                                                        <option value="1" @if($action == "Corrección" and $ica->codAgente == "1" ) selected @endif>01. Entidad pública</option>
                                                        <option value="2" @if($action == "Corrección" and $ica->codAgente == "2" ) selected @endif>02. Gran contribuyente</option>
                                                        <option value="3" @if($action == "Corrección" and $ica->codAgente == "3" ) selected @endif>03. Consoricio uniones Temporales</option>
                                                        <option value="4" @if($action == "Corrección" and $ica->codAgente == "4" ) selected @endif>04. Autorretenedor</option>
                                                        <option value="5" @if($action == "Corrección" and $ica->codAgente == "5" ) selected @endif>05. Designado</option>
                                                        <option value="6" @if($action == "Corrección" and $ica->codAgente == "6" ) selected @endif>06. Otro</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">Retenciones practicadas</th>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>Por contratos de obra o consultoría
                                    </td>
                                    <td><input type="number" class="form-control" min="0" name="contratosObra" @if($action == "Corrección" ) value="{{ $ica->contratosObra }}" @else value="0" @endif id="contratosObra"
                                        onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>Por Contratos de Prestación de servicios</td>
                                    <td><input type="number" class="form-control" min="0" name="contratosPrestServ" @if($action == "Corrección" ) value="{{ $ica->contratosPrestServ }}" @else value="0" @endif id="contratosPrestServ"
                                               onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td>Por Compras de bienes y servicios diferentes a los anteriors</td>
                                    <td><input type="number" class="form-control" min="0" name="compraBienes" id="compraBienes" @if($action == "Corrección" ) value="{{ $ica->compraBienes }}" @else value="0" @endif
                                        onchange="operation()">
                                    </td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td>Por otras actividades gravadas</td>
                                    <td><input type="number" class="form-control" min="0" name="otrasActiv" id="otrasActiv" @if($action == "Corrección" ) value="{{ $ica->otrasActiv }}" @else value="0" @endif
                                        onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>16</td>
                                    <td>Practicadas en periodos anteriores dejadas de declarar</td>
                                    <td><input type="number" class="form-control" min="0" name="practicadasPeriodosAnt" id="practicadasPeriodosAnt" @if($action == "Corrección" ) value="{{ $ica->practicadasPeriodosAnt }}" @else value="0" @endif
                                               onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>17</td>
                                    <td>Total Retenciones practicadas</td>
                                    <td>
                                        <span id="totRetencionesSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totRetenciones,0) ?> @else $0 @endif</span>
                                        <input type="hidden" name="totRetenciones" id="totRetenciones" @if($action == "Corrección" ) value="{{ $ica->totRetenciones }}" @else value="0" @endif>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">Pagos</th>
                                </tr>
                                <tr>
                                    <td>18</td>
                                    <td>Devolución por exceso de cobro
                                    </td>
                                    <td><input type="number" class="form-control" min="0" name="devolucionExceso" @if($action == "Corrección" ) value="{{ $ica->devolucionExceso }}" @else value="0" @endif id="devolucionExceso"
                                               onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>19</td>
                                    <td>Devolución retención practicada no aplicable</td>
                                    <td><input type="number" class="form-control" min="0" name="devolucionRetencion" @if($action == "Corrección" ) value="{{ $ica->devolucionRetencion }}" @else value="0" @endif id="devolucionRetencion"
                                               onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>20</td>
                                    <td>Total retención neta</td>
                                    <td> <span id="totalRetencionSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->totalRetencion,0) ?> @else $0 @endif</span>
                                        <input type="hidden" name="totalRetencion" id="totalRetencion" @if($action == "Corrección" ) value="{{ $ica->totalRetencion }}" @else value="0" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td>Sanción por extemporaneidad o declarar</td>
                                    <td><input type="number" class="form-control" min="0" name="sancionExtemp" id="sancionExtemp" @if($action == "Corrección" ) value="{{ $ica->sancionExtemp }}" @else value="0" @endif
                                        onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>22</td>
                                    <td>Sanción por corrección o inexactitud</td>
                                    <td><input type="number" class="form-control" min="0" name="sancionCorreccion" id="sancionCorreccion" @if($action == "Corrección" ) value="{{ $ica->sancionCorreccion }}" @else value="0" @endif
                                        onchange="operation()"></td>
                                </tr>
                                <tr>
                                    <td>23</td>
                                    <td>Intereses Moratorios</td>
                                    <td>
                                        <input type="number" class="form-control" min="0" name="interesMoratorio" @if($action == "Corrección" ) value="{{ $ica->interesMoratorio }}" @else value="0" @endif id="interesMoratorio"
                                               onchange="operation()">
                                    </td>
                                </tr>
                                <tr>
                                    <td>24</td>
                                    <td>Pago Total retenciones netas mas sanciones e intereses</td>
                                    <td>
                                        <span id="pagoTotalSpan">@if($action == "Corrección" ) $<?php echo number_format($ica->pagoTotal,0) ?> @else $0 @endif</span>
                                        <input type="hidden" name="pagoTotal" id="pagoTotal" @if($action == "Corrección" ) value="{{ $ica->pagoTotal }}" @else value="0" @endif>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            {{-- TABLA E. FIRMAS --}}
                            <table id="TABLA7" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">E. FIRMAS</th>
                                </tr>
                                <tr>
                                    <td style="width: 300px">25. Identificación del signatario<br>
                                        <input style="width: 300px" type="text" class="form-control" name="idSignatario" id="idSignatario" @if($action == "Corrección" ) value="{{ $ica->idSignatario }}" @endif required>
                                    </td>
                                    <td>26. Nombre del signatario <br>
                                        <input type="text" class="form-control" name="nameSignatario" id="nameSignatario" @if($action == "Corrección" ) value="{{ $ica->nameSignatario }}" @endif required>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <table class="table text-center">
                                            <tr>
                                                <td>
                                                    <div class="form-check-inline">
                                                        <input class="form-check-input" type="radio" name="signatario" value="repLegal" id="signatario1" @if($action == "Corrección" and $ica->signatario == "repLegal" ) checked @else checked @endif>
                                                        <label class="form-check-label" for="signatario1">27. Signatario representante legal</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="signatario" value="delegado" id="signatario2" @if($action == "Corrección" and $ica->signatario == "delegado" ) checked @endif>
                                                        <label class="form-check-label" for="signatario2">28. Signatario delegado o con poder</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="signatario" value="principal" id="signatario3" @if($action == "Corrección" and $ica->signatario == "principal" ) checked @endif>
                                                        <label class="form-check-label" for="signatario3">29. Signatario Principal</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 300px">30. T.P. Contador Revisor Fiscal<br>
                                        <input style="width: 300px" type="text" class="form-control" name="tpRevFisc" id="tpRevFisc" @if($action == "Corrección" ) value="{{ $ica->tpRevFisc }}" @endif>
                                    </td>
                                    <td>31. Nombre del Contador o Revisor Fiscal<br>
                                        <input type="text" class="form-control" name="nameRevFisc" id="nameRevFisc" @if($action == "Corrección" ) value="{{ $ica->nameRevFisc }}" @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        37. Fecha de presentación
                                        <br>
                                        <h3>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</h3>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">
                                            @if($action == "Corrección" )
                                                Corregir
                                            @else
                                                Presentar
                                            @endif
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            @if($action != "Corrección" )
                                <table class="table text-center">
                                    <tbody>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th scope="row">ESTE FORMULARIO Y SU PRESENTACIÓN NO TIENE COSTO ALGUNO</th>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
    </div>
@stop
@section('scripts')
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("formulario").addEventListener('submit', validarFormulario);
        });

        function validarFormulario(evento) {
            evento.preventDefault();
            var pagoTotal = document.getElementById('pagoTotal').value;
            if(pagoTotal <= 0) {
                alert('Debe tener un pago total superior a 0');
                return;
            }
            this.submit();
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0
        })

        function operation(){
            var contratosObra = document.getElementById("contratosObra").value;
            var contratosPrestServ = document.getElementById("contratosPrestServ").value;
            var compraBienes = document.getElementById("compraBienes").value;
            var otrasActiv = document.getElementById("otrasActiv").value;
            var practicadasPeriodosAnt = document.getElementById("practicadasPeriodosAnt").value;
            var tot = parseInt(contratosObra) + parseInt(contratosPrestServ) + parseInt(compraBienes)+ parseInt(otrasActiv)+ parseInt(practicadasPeriodosAnt);

            document.getElementById('totRetencionesSpan').innerHTML = formatter.format(tot);
            document.getElementById('totRetenciones').value = tot;

            var devolucionExceso = document.getElementById("devolucionExceso").value;

            var totRetencion = tot - parseInt(practicadasPeriodosAnt) - parseInt(devolucionExceso);

            document.getElementById('totalRetencionSpan').innerHTML = formatter.format(totRetencion);
            document.getElementById('totalRetencion').value = totRetencion;

            var devolucionRetencion = document.getElementById("devolucionRetencion").value;
            var sancionExtemp = document.getElementById("sancionExtemp").value;
            var sancionCorreccion = document.getElementById("sancionCorreccion").value;

            var pagoTotal = parseInt(devolucionRetencion) + totRetencion + parseInt(sancionExtemp) + parseInt(sancionCorreccion);

            document.getElementById('pagoTotalSpan').innerHTML = formatter.format(pagoTotal);
            document.getElementById('pagoTotal').value = pagoTotal;
        }
    </script>
@stop