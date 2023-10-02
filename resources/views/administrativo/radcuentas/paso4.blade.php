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
                <li class="nav-item regresar">
                    <a class="nav-link "  href="{{ url('/administrativo/radCuentas/'.$radCuenta->id.'/3') }}"><i class="fa fa-arrow-left"></i> PASO 3</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#nuevo" >ANEXOS - PASO 4</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="form-validation">
                    <form class="form-valide" action="{{url('/administrativo/radCuentas/paso/4')}}" method="POST" enctype="multipart/form-data">
                        <hr>
                        {{ csrf_field() }}
                        <input type="hidden" name="radicacion_id" value="{{ $radCuenta->id }}">
                        <div class="col-md-12 " style="background-color: white" id="formRP" name="formRP">
                            @if($radCuenta->rad_cuenta_anex_id)
                                <h4 class="text-center">Al agregar una radicacion de cuenta ya elaborada en el proceso de creación, se muestran los anexos de dicha radicacion de cuenta. De esta manera el usuario no requiere cargar de nuevo todos los anexos.</h4>
                            @endif
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
                                    <td>
                                        @foreach($cdps as $cdp)
                                            <a href="{{ url('administrativo/cdp/pdf/'.$cdp->id.'/'.$vigencia_id) }}" target="_blank" title="File" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                        @endforeach
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>CRP</td>
                                    <td>
                                        <a href="{{ url('administrativo/registro/pdf/'.$registro->id.'/'.$vigencia_id) }}" target="_blank" title="Ver Archivo" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>CONTRATO <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CONTRATO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CONTRATO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="CONTRATO" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="CONTRATO" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CONTRATO")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CONTRATO")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="CONTRATOObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="CONTRATOObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ACTA DE INICIO <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA DE INICIO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA DE INICIO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                    @if($show2)
                                                        <input type="file" required class="form-control" name="actaIni" accept="application/pdf, .docx, .xlsx">
                                                    @endif
                                            @else
                                                <input type="file" required class="form-control" name="actaIni" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA DE INICIO")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CONTRATO")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="actaIniObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="actaIniObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>CERTIFICACIÓN BANCARIA <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CERTIFICACION BANCARIA")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CERTIFICACION BANCARIA")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                        <input type="file" required class="form-control" name="certBanc" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="certBanc" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CERTIFICACION BANCARIA")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CERTIFICACION BANCARIA")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="certBancObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="certBancObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ENTRADA  DE ALMACÉN <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ENTRADA ALMACEN")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ENTRADA ALMACEN")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="entradaAlmac" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="entradaAlmac" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ENTRADA ALMACEN")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ENTRADA ALMACEN")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="entradaAlmacObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="entradaAlmacObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>R.U.T. <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "RUT")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "RUT")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="RUT" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="RUT" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "RUT")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "RUT")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="RUTObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="RUTObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>CÉDULA AL 150% <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CEDULA")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CEDULA")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="cedula" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="cedula" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CEDULA")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CEDULA")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="cedulaObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="cedulaObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>OFICIO % DESCUENTO DIAN <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "OFICIO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "OFICIO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="oficioDian" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" required class="form-control" name="oficioDian" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "OFICIO")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "OFICIO")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="oficioDianObs">@endif
                                            @else
                                                <input type="text" class="form-control" name="oficioDianObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>INFORME DE EJECUCIÓN <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "INFORME")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "INFORME")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="infEjec" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" required class="form-control" name="infEjec" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "INFORME")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "INFORME")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)<input type="text" class="form-control" name="infEjecObs">@endif
                                            @endif
                                        @else
                                            <input type="text" class="form-control" name="infEjecObs">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>CERTIFICADO DE CUMPLIMIENTO <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CERTIFICADO CUMPLIMIENTO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CERTIFICADO CUMPLIMIENTO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="certCump" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="certCump" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CERTIFICADO CUMPLIMIENTO")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($radCuenta->rad_cuenta_anex_id)
                                            @foreach($radCuentaAnex->anexos as $anexo)
                                                @if($anexo->anexo == "CERTIFICADO CUMPLIMIENTO")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @else
                                                <input type="text" class="form-control" name="certCumpObs">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ACTA RECIBO O PARCIAL <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA RECIBO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA RECIBO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="actRec" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="actRec" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA RECIBO")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA RECIBO")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="actRecObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ACTA DE TERMINACIÓN <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA TERMINACION")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA TERMINACION")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="actTerm" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="actTerm" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA TERMINACION")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA TERMINACION")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="actTermObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ACTA DE LIQUIDACION <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA LIQUIDACION")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA LIQUIDACION")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="actLiquid" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="actLiquid" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA LIQUIDACION")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA LIQUIDACION")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="actLiquidObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>ACTA AUTORIZACION INTERVENTOR <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA INTERVENTOR")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA INTERVENTOR")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="actAutInt" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="actAutInt" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "ACTA INTERVENTOR")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "ACTA INTERVENTOR")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="actAutIntObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>SEGURIDAD SOCIAL Y PARAFISCALES <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "SEGURIDAD SOCIAL")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "SEGURIDAD SOCIAL")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="segSocParaf" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="segSocParaf" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "SEGURIDAD SOCIAL")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "SEGURIDAD SOCIAL")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="segSocParafObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>CUENTA DE COBRO O FACTURA <span class="text-danger">*</span></td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CUENTA COBRO")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CUENTA COBRO")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" required class="form-control" name="cuentaCobroFact" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @endif
                                        @else
                                            <input type="file" class="form-control" name="cuentaCobroFact" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CUENTA COBRO")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CUENTA COBRO")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @else
                                                <input type="text" class="form-control" name="cuentaCobroFactObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>PÓLIZA</td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "POLIZA")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @php($show2 = true)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "POLIZA")
                                                        @php($show2 = false)
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="file" class="form-control" name="POLIZA" accept="application/pdf, .docx, .xlsx">
                                                @endif
                                            @else
                                                <input type="file" class="form-control" name="POLIZA" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "POLIZA")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @php($show2 = true)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "POLIZA")
                                                        @php($show2 = false)
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                                @if($show2)
                                                    <input type="text" class="form-control" name="POLIZAObs">
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="POLIZAObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>APROBACIÓN DE LA PÓLIZA</td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "APROBACION DE LA POLIZA")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "APROBACION DE LA POLIZA")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="aproPol" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "APROBACION DE LA POLIZA")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "APROBACION DE LA POLIZA")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="aproPolObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>PAZ Y SALVO OFICINA DE TRABAJO</td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PAZ Y SALVO OFICINA")
                                                    @php($show = false)
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "PAZ Y SALVO OFICINA")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="pazySalvOff" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php($show = true)
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PAZ Y SALVO OFICINA")
                                                    @php($show = false)
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($show)
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "PAZ Y SALVO OFICINA")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="pazySalvOffObs">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>PLAN DE INVERSIÓN</td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PLAN DE INVERSIÓN")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($radCuenta->rad_cuenta_anex_id)
                                            @foreach($radCuentaAnex->anexos as $anexo)
                                                @if($anexo->anexo == "PLAN DE INVERSIÓN")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                            @else
                                                <input type="file" class="form-control" name="planInv" accept="application/pdf, .docx, .xlsx">
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PLAN DE INVERSIÓN")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($radCuenta->rad_cuenta_anex_id)
                                            @foreach($radCuentaAnex->anexos as $anexo)
                                                @if($anexo->anexo == "PLAN DE INVERSIÓN")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                            @else
                                                <input type="text" class="form-control" name="planInvObs">

                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>PAGO SENA (FIC) PARA CONTRATOS DE OBRA</td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PAGO SENA")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "PAGO SENA")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="pagoSena" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "PAGO SENA")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "PAGO SENA")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="pagoSenaObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>FOTOGRAFIAS</td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "FOTOGRAFIAS")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "FOTOGRAFIAS")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="fotografias" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "FOTOGRAFIAS")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "FOTOGRAFIAS")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="fotografiasObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>CONTROL DE ASISTENCA</td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CONTROL ASISTENCA")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CONTROL ASISTENCA")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="controlAssist" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "CONTROL ASISTENCA")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "CONTROL ASISTENCA")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="controlAssistObs">
                                            @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>INCRIPCION REGIMEN TRIBUTARIO ESPECIAL RTE DIAN</td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "INCRIPCION REGIMEN")
                                                    <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "INCRIPCION REGIMEN")
                                                        <a href="{{Storage::url($anexo->file->ruta)}}" target="_blank" title="Ver" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="file" class="form-control" name="inscripRegTribut" accept="application/pdf, .docx, .xlsx">
                                            @endif
                                    </td>
                                    <td>
                                        @if(count($radCuenta->anexos) > 0)
                                            @foreach($radCuenta->anexos as $anexo)
                                                @if($anexo->anexo == "INCRIPCION REGIMEN")
                                                    {{ $anexo->observacion }}
                                                @endif
                                            @endforeach
                                        @endif
                                            @if($radCuenta->rad_cuenta_anex_id)
                                                @foreach($radCuentaAnex->anexos as $anexo)
                                                    @if($anexo->anexo == "INCRIPCION REGIMEN")
                                                        {{ $anexo->observacion }}
                                                    @endif
                                                @endforeach
                                            @else
                                                <input type="text" class="form-control" name="inscripRegTributObs">
                                            @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row" id="buttonSend">
                            <div class="col-lg-12 ml-auto text-center">
                                @if(count($radCuenta->anexos) > 0)
                                    <a onclick="deleteAnexos({{ $radCuenta->id }})" class="btn btn-primary">Eliminar Anexos</a>
                                @endif
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

        function deleteAnexos(id){
            var opcion = confirm("Esta seguro de querer eliminar todos los anexos de la radicación?");
            if (opcion == true) {
                $.ajax({
                    method: "POST",
                    url: "/administrativo/radCuentas/delete/ANEXOS",
                    data: { "idRad": id, "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(data) {
                    if (data == 200){
                        toastr.warning('ANEXOS ELIMINADOS. RECARGANDO PAGINA...');
                        location.reload();
                    }
                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL INTENTAR ELIMINAR LOS ANEXOS. INTENTE NUEVAMENTE EN UNOS MINUTOS POR FAVOR');
                });
            }
        }
    </script>
@stop
