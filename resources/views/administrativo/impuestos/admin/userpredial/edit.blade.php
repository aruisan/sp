@extends('layouts.dashboard')
@section('titulo') Editar Usuario @stop
@section('content')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Editar Información del Usuario</b></h4>
            </strong>
        </div>
        <div class="col-lg-12">
            <ul class="nav nav-pills">
                <li class="nav-item regresar"><a class="nav-link "  href="{{ url('/administrativo/impuestos/admin') }}">Volver a Administracion</a></li>
                <li class="nav-item active"><a class="tituloTabs" data-toggle="tab" href="#info">{{ $user->numIdent }} - {{ $user->contribuyente }}</a></li>
            </ul>
        </div>
        <div class="col-lg-12" id="prog">
            <div class="tab-content">
                <div id="info" class="tab-pane fade in active">
                    <div class="row ">
                        <br>
                        <div class="col-sm-4"><h4>NUM CATASTRAL: {{ $user->numCatastral }} </h4></div>
                        <div class="col-sm-4"><h4>NUM IDENTI: {{ $user->numIdent }} </h4></div>
                        <div class="col-sm-4"><h4>NOMBRE: {{ $user->contribuyente }}</h4></div>
                        <br><br>
                        <div class="form-validation">
                            <form class="form" action="{{url('/administrativo/impuestos/admin/predial/user/'.$user->id)}}" method="POST">
                                <hr>
                                {!! method_field('PUT') !!}
                                {{ csrf_field() }}
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="correo">Email <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="email" class="form-control" required name="correo" id="correo" style="text-align:center" value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="area">Area Terreno <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" required name="area" id="area" style="text-align:center" value="{{ $user->areaTerreno }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirPred">Dirección del Predio <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" required name="dirPred" id="dirPred" style="text-align:center" value="{{ $user->dir_predio }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="otraRed">Otra Red</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="otraRed" id="otraRed" style="text-align:center" value="{{ $user->otra_red }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirNoti">Dirección de Notificación <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" required name="dirNoti" id="dirNoti" style="text-align:center" value="{{ $user->dir_notificacion }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="municipio">Municipio <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" required name="municipio" id="municipio" style="text-align:center" value="{{ $user->municipio }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="whatsapp">WhatsApp</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" style="text-align:center" value="{{ $user->whatsapp }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="facebook">Facebook</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="facebook" id="facebook" style="text-align:center" value="{{ $user->facebook }}">
                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                                        </div>
                                    </div>
                            </form>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
    </script>
@stop