@extends('layouts.dashboard')
@section('titulo') Editar Contribuyente @stop
@section('content')
    @php( $fechaActual = Carbon\Carbon::today()->Format('Y-m-d') )
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Editar Información del Contribuyente</b></h4>
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
                    <div class="row text-center">
                        <div class="form-validation">
                            <form class="form" action="{{url('/administrativo/impuestos/admin/predial/user/'.$user->id)}}" method="POST">
                                <h3 class="text-center">CONTRIBUYENTE</h3>
                                <hr>
                                {!! method_field('PUT') !!}
                                {{ csrf_field() }}
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="numIdent">Num Identidad <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" required name="numIdent" id="numIdent" style="text-align:center" value="{{ $user->numIdent }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="correo">Email</label>
                                        <div class="col-lg-6">
                                            <input type="email" class="form-control" name="correo" id="correo" style="text-align:center" value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="otraRed">Otra Red</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="otraRed" id="otraRed" style="text-align:center" value="{{ $user->otra_red }}">
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
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="name">Nombre <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" required name="name" id="name" style="text-align:center" value="{{ $user->contribuyente }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirNoti">Dirección de Notificación</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="dirNoti" id="dirNoti" style="text-align:center" value="{{ $user->dir_notificacion }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="municipio">Municipio</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="municipio" id="municipio" style="text-align:center" value="{{ $user->municipio }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="whatsapp">WhatsApp</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" style="text-align:center" value="{{ $user->whatsapp }}">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h3 class="text-center">PREDIO</h3>
                                <hr>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirPred">Dirección del Predio</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="dirPred" id="dirPred" style="text-align:center" value="{{ $user->dir_predio }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="numCat">Número Catastral</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="numCat" id="numCat" style="text-align:center" value="{{ $user->numCatastral }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="hectareas">Hectareas <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" required name="hectareas" id="hectareas" min="0" style="text-align:center" value="{{ $user->hect }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirPred">Cedula Catastral</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="cedCatastral" id="cedCatastral" style="text-align:center" value="{{ $user->cedCatastral }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="mt2">mt2 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" required min="0" name="mt2" id="mt2" style="text-align:center" value="{{ $user->metros }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="aConst">Area Construida <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" required name="aConst" id="aConst" min="0" style="text-align:center" value="{{ $user->area }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="dirPred">Matricula Inmobiliaria</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" name="matInmobiliaria" id="matInmobiliaria" style="text-align:center" value="{{ $user->matInmobiliaria }}">
                                        </div>
                                    </div>
                                    <div class="form-group">&nbsp;</div>
                                    <br>
                                </div>
                                <br>
                                <h3 class="text-center">AVALUOS</h3>
                                <hr>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2023">2023 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2023" id="a2023" style="text-align:center" value="{{ $user->a2023 }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2021">2021 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2021" id="a2021" style="text-align:center" value="{{ $user->a2021 }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2019">2019 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2019" id="a2019" style="text-align:center" value="{{ $user->a2019 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 align-self-center">
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2022">2022 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2022" id="a2022" style="text-align:center" value="{{ $user->a2022 }}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2020">2020 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2020" id="a2020" style="text-align:center" value="{{ $user->a2020 }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label text-right col-md-4" for="a2018">2018 <span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                            <input type="number" class="form-control" min="1" required name="a2018" id="a2018" style="text-align:center" value="{{ $user->a2018 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="btn btn-primary">Actualizar Contribuyente</button>
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