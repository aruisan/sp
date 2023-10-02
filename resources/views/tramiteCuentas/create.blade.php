@extends('layouts.dashboard')
@section('titulo')
Tramite de cuentas|crear
@stop

@section('content')
    <div class="breadcrumb">
        <ul class="breadcrumb">
          <li class="text-capitalize"><a href="{{route('tramites-cuentas.index')}}" > Todos los tramites de cuentas</a></li>
          <li class="text-capitalize">Nuevo tramite de Cuentas</li>
        </ul>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-default widget col-md-12">
                <div class="panel-header">
                    <h3 class="text-capitalize text-center">formulario creación Tramite de Cuenta</h3>
                </div>
                <div class="panel-body">
                    <form action="{{route('tramites-cuentas.store')}}" method="POST">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group" >
                                    <label>Beneficiario  </label>
                                    <select name="beneficiario_id" class="form-control select">
                                        @foreach($personas as $persona)
                                            <option value="{{$persona->id}}">{{$persona->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label>Tipo de pago </label>
                                    <select name="tipo_pago" class="form-control">
                                        <option value="Pago Inicial" {{old('tipo_contrato') == 'Pago Inicial' ? 'selected':''}}>Pago Inicial</option>
                                        <option value="Pago Parcial" {{old('tipo_contrato') == 'Pago Parcial' ? 'selected':''}}>Pago Parcial</option>
                                        <option value="Pago Final" {{old('tipo_contrato') == 'Pago Final' ? 'selected':''}}>Pago Final</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-xs-12">
                                <div class="form-group">
                                    <label>Tipo de contrato</label>
                                    <select name="tipo_contrato" class="form-control" id="tipo_contrato">
                                        <option value="Contrato de Obra" {{old('tipo_contrato') == 'Contrato de Obra' ? 'selected':''}}>Contrato de Obra</option>
                                        <option value="Prestacion de Servicios" {{old('tipo_contrato') == 'Prestacion de Servicios' ? 'selected':''}}>Prestación de Servicios</option>
                                        <option value="Convenios" {{old('tipo_contrato') == 'Convenios' ? 'selected':''}}>Convenios</option>
                                        <option value="Resolucion" {{old('tipo_contrato') == 'Resolucion' ? 'selected':''}}>Resolución</option>
                                        <option value="Viaticos" {{old('tipo_contrato') == 'Viaticos' ? 'selected':''}}>Viáticos</option>
                                        <option value="Nomina" {{old('tipo_contrato') == 'Nomina' ? 'selected':''}}>Nómina</option>
                                        <option value="Pago de Servicios" {{old('tipo_contrato') == 'Pago de Servicios' ? 'selected':''}}>Pago de Servicios</option>
                                        <option value="Cuenta Ars" {{old('tipo_contrato') == 'Cuenta Ars' ? 'selected':''}}>Cuenta Ars</option>
                                        <option value="Otros" {{old('tipo_contrato') == 'Otros' ? 'selected':''}}>Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12" id="otros">
                                <div class="form-group">
                                    <label>Otro  </label>
                                    <input type="text" name="otro_tipo_contrato" id="input-otros" class="form-control" placeholder="otro tipo de Contrato" value="{{old('otro_tipo_contrato')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label>contrato</label>
                                    <input type="text" name="n_contrato" class="form-control" placeholder="Número del contrato" value="{{old('n_contrato')}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Valor de contrato</label>
                                    <input type="text" name="v_contrato" class="form-control" placeholder="Valor de contrato" value="{{old('v_contrato')}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Número de Pago</label>
                                    <input type="text" name="n_pago" class="form-control" placeholder="Numero de pago" value="{{old('n_pago')}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Valor de Pago</label>
                                    <input type="text" name="v_pago" class="form-control" placeholder="Valor de pago" value="{{old('v_pago')}}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                 <button type="submit" class="btn btn-primary" id="submitCreateProcessJuridico">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript" src="{{asset('js/relacionarParticipantes.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        otros();
        $('.select').select2();
    });

    $('#tipo_contrato').on('change', function(){
        otros();
    });

    function otros(){
        if($('#tipo_contrato').val() == 'Otros'){
            $('#otros').show();
        }else{
            $('#otros').hide();
            $('#input-otros').val('');
        }
    }
</script>
@stop
