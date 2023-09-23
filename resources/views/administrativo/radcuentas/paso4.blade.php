@extends('layouts.dashboard')
@section('titulo') Radicación de Cuentas - Paso 4 @stop
@section('sidebar')@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="breadcrumb text-center"><strong><h4><b>RADICACIÓN DE CUENTA - ANEXOS</b></h4></strong></div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$vigencia_id) }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >RADICACIÓN - PASO 4</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="form-validation">
                    <form class="form-valide" action="{{url('/administrativo/radCuentas/paso/4')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <input type="hidden" name="radicacion_id" value="{{ $radCuenta->id }}">
                        <div class="col-md-12 " style="background-color: white" id="formRP" name="formRP">
                            <table id="TABLA1" class="table text-center table-bordered">
                                <thead>
                                <tr style="background-color: #6c0e03; color: white"><th scope="row" colspan="3">4. ANEXOS</th></tr>
                                <tr class="text-center">
                                    <th>ANEXOS</th>
                                    <th>ARCHIVO</th>
                                    <th>OBSERVACIONES</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>CDP</td>
                                    <td><input type="file" class="form-control" name="cdp" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="cdpObs"></td>
                                </tr>
                                <tr>
                                    <td>CRP</td>
                                    <td><input type="file" class="form-control" name="crp" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="crpObs"></td>
                                </tr>
                                <tr>
                                    <td>CONTRATO</td>
                                    <td><input type="file" class="form-control" name="CONTRATO" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="CONTRATOObs"></td>
                                </tr>
                                <tr>
                                    <td>PLAN DE INVERSIÓN</td>
                                    <td><input type="file" class="form-control" name="planInv" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="planInvObs"></td>
                                </tr>
                                <tr>
                                    <td>ACTA DE INICIO</td>
                                    <td><input type="file" class="form-control" name="actaIni" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="actaIniObs"></td>
                                </tr>
                                <tr>
                                    <td>PÓLIZA</td>
                                    <td><input type="file" class="form-control" name="POLIZA" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="POLIZAObs"></td>
                                </tr>
                                <tr>
                                    <td>APROBACIÓN DE LA PÓLIZA</td>
                                    <td><input type="file" class="form-control" name="aproPol" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="aproPolObs"></td>
                                </tr>
                                <tr>
                                    <td>CÉDULA AL 150%</td>
                                    <td><input type="file" class="form-control" name="cedula" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="cedulaObs"></td>
                                </tr>
                                <tr>
                                    <td>OFICIO % DESCUENTO DIAN</td>
                                    <td><input type="file" class="form-control" name="oficioDian" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="oficioDianObs"></td>
                                </tr>
                                <tr>
                                    <td>INFORME DE EJECUCIÓN</td>
                                    <td><input type="file" class="form-control" name="infEjec" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="infEjecObs"></td>
                                </tr>
                                <tr>
                                    <td>CERTIFICADO DE CUMPLIMIENTO</td>
                                    <td><input type="file" class="form-control" name="certCump" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="certCumpObs"></td>
                                </tr>
                                <tr>
                                    <td>ACTA RECIBO O PARCIAL</td>
                                    <td><input type="file" class="form-control" name="actRec" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="actRecObs"></td>
                                </tr>
                                <tr>
                                    <td>ACTA DE TERMINACIÓN</td>
                                    <td><input type="file" class="form-control" name="actTerm" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="actTermObs"></td>
                                </tr>
                                <tr>
                                    <td>ACTA DE LIQUIDACION</td>
                                    <td><input type="file" class="form-control" name="actLiquid" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="actLiquidObs"></td>
                                </tr>
                                <tr>
                                    <td>ACTA AUTORIZACION INTERVENTOR</td>
                                    <td><input type="file" class="form-control" name="actAutInt" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="actAutIntObs"></td>
                                </tr>
                                <tr>
                                    <td>SEGURIDAD SOCIAL Y PARAFISCALES</td>
                                    <td><input type="file" class="form-control" name="segSocParaf" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="segSocParafObs"></td>
                                </tr>
                                <tr>
                                    <td>CUENTA DE COBRO O FACTURA</td>
                                    <td><input type="file" class="form-control" name="cuentaCobroFact" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="cuentaCobroFactObs"></td>
                                </tr>
                                <tr>
                                    <td>CERTIFICACIÓN BANCARIA</td>
                                    <td><input type="file" class="form-control" name="certBanc" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="certBancObs"></td>
                                </tr>
                                <tr>
                                    <td>ENTRADA  DE ALMACÉN</td>
                                    <td><input type="file" class="form-control" name="entradaAlmac" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="entradaAlmacObs"></td>
                                </tr>
                                <tr>
                                    <td>R.U.T.</td>
                                    <td><input type="file" class="form-control" name="RUT" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="RUTObs"></td>
                                </tr>
                                <tr>
                                    <td>PAZ Y SALVO OFICINA DE TRABAJO</td>
                                    <td><input type="file" class="form-control" name="pazySalvOff" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="pazySalvOffObs"></td>
                                </tr>
                                <tr>
                                    <td>PAGO SENA (FIC) PARA CONTRATOS DE OBRA</td>
                                    <td><input type="file" class="form-control" name="pagoSena" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="pagoSenaObs"></td>
                                </tr>
                                <tr>
                                    <td>FOTOGRAFIAS</td>
                                    <td><input type="file" class="form-control" name="fotografias" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="fotografiasObs"></td>
                                </tr>
                                <tr>
                                    <td>CONTROL DE ASISTENCA</td>
                                    <td><input type="file" class="form-control" name="controlAssist" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="controlAssistObs"></td>
                                </tr>
                                <tr>
                                    <td>INCRIPCION REGIMEN TRIBUTARIO ESPECIAL RTE DIAN</td>
                                    <td><input type="file" class="form-control" name="inscripRegTribut" accept="application/pdf, .docx, .xlsx"></td>
                                    <td><input type="text" class="form-control" name="inscripRegTributObs"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row" id="buttonSend">
                            <div class="col-lg-12 ml-auto text-center">
                                <button type="submit" class="btn btn-primary">Guardar Anexos y Finalizar Radicación</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        const vigencia_id = @json($vigencia_id);
    </script>
@stop
