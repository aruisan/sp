@extends('layouts.dashboard')
@section('titulo')
Tramite de cuentas|Actualizar
@stop

@section('content')
    <div class="breadcrumb">
        <ul class="breadcrumb">
          <li class="text-capitalize"><a href="{{route('tramites-cuentas.index')}}" > Todos los tramites de cuentas</a></li>
          <li class="text-capitalize">Editar tramite de Cuentas</li>
        </ul>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-default widget col-md-12">
                <div class="panel-header">
                    <h3 class="text-capitalize text-center">formulario Editar Tramite de Cuenta</h3>
                </div>
                <div class="panel-body">
                    <form action="{{route('tramites-cuentas.update', $item->id)}}" method="POST">
                        {{method_field('put')}}
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group" >
                                    <label>Beneficiario  </label>
                                    <input type="hidden" name="beneficiario_id" value="{{old('beneficiario_id') ? old('beneficiario_id') : $item->beneficiario_id}}" id="persona2_id">
                                    <input type="text" name="beneficiario_num" value="{{old('beneficiario_num') ? old('beneficiario_num') : $item->beneficiario->num_dc}}" class="form-control @error('beneficiario_num') is-invalid @enderror" readonly placeholder="Número de Identificación" onclick="openModalRelacionarParticipantes('persona2_id','persona2_cc', 'persona2_name', 'persona2_tipo')" id="persona2_cc">
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group ">
                                    <label>Nombre Beneficiario</label>
                                    <input type="text" name="beneficiario_name" value="{{old('beneficiario_name') ? old('beneficiario_name') : $item->beneficiario->nombre}}" class="form-control @error('beneficiario_name') is-invalid @enderror" readonly placeholder="Nombre" id="persona2_name">
                                </div>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label>Tipo de contrato</label>
                                    <select name="tipo_contrato" class="form-control" id="tipo_contrato">
                                        <option value="Contrato de Obra" {{old('tipo_contrato') == 'Contrato de Obra' || $item->tipo_contrato == 'Contrato de Obra' ? 'selected':''}}>
                                            Contrato de Obra
                                        </option>
                                        <option value="Prestacion de Servicios" {{old('tipo_contrato') == 'Prestacion de Servicios' || $item->tipo_contrato == 'Prestacion de Servicios' ? 'selected':''}}>
                                            Prestación de Servicios
                                        </option>
                                        <option value="Convenios" {{old('tipo_contrato') == 'Convenios' || $item->tipo_contrato == 'Convenios' ? 'selected':''}}>
                                            Convenios
                                        </option>
                                        <option value="Resolucion" {{old('tipo_contrato') == 'Resolucion' || $item->tipo_contrato == 'Resolucion' ? 'selected':''}}>
                                            Resolución
                                        </option>
                                        <option value="Viaticos" {{old('tipo_contrato') == 'Viaticos' || $item->tipo_contrato == 'Viaticos' ? 'selected':''}}>
                                            Viáticos
                                        </option>
                                        <option value="Nomina" {{old('tipo_contrato') == 'Nomina' || $item->tipo_contrato == 'Nomina' ? 'selected':''}}>
                                            Nómina
                                        </option>
                                        <option value="Pago de Servicios" {{old('tipo_contrato') == 'Pago de Servicios' || $item->tipo_contrato == 'Pago de Servicios' ? 'selected':''}}>
                                            Pago de Servicios
                                        </option>
                                        <option value="Cuenta Ars" {{old('tipo_contrato') == 'Cuenta Ars' || $item->tipo_contrato == 'Cuenta Ars' ? 'selected':''}}>
                                            Cuenta Ars
                                        </option>
                                        <option value="Otros" {{old('tipo_contrato') == 'Otros' || $item->tipo_contrato == 'Otros' ? 'selected':''}}>
                                            Otros
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12" id="otros">
                                <div class="form-group">
                                    <label>Otro  </label>
                                    <input type="text" name="otro_tipo_contrato" id="input-otros" class="form-control" placeholder="otro tipo de Contrato" value="{{old('otro_tipo_contrato') ? old('otro_tipo_contrato') : $item->otro_tipo_contrato}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Número de contrato</label>
                                    <input type="text" name="n_contrato" class="form-control" placeholder="Numero de contrato" value="{{old('n_contrato') ? old('n_contrato') : $item->n_contrato}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Valor de contrato</label>
                                    <input type="text" name="v_contrato" class="form-control" placeholder="Valor de contrato" value="{{old('v_contrato') ? old('v_contrato') : $item->v_contrato}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label>Tipo de pago </label>
                                    <select name="tipo_pago" class="form-control">
                                        <option value="Pago Inicial" {{old('tipo_contrato') == 'Pago Inicial'  || $item->tipo_pago == 'Pago Inicial' ? 'selected':''}}>Pago Inicial</option>
                                        <option value="Pago Parcial" {{old('tipo_contrato') == 'Pago Parcial' || $item->tipo_pago == 'Pago Parcial'  ? 'selected':''}}>Pago Parcial</option>
                                        <option value="Pago Final" {{old('tipo_contrato') == 'Pago Final' || $item->tipo_pago == 'Pago Final'  ? 'selected':''}}>Pago Final</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Número de Pago</label>
                                    <input type="text" name="n_pago" class="form-control" placeholder="Numero de contrato" value="{{old('n_pago') ? old('n_pago') : $item->n_pago}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Valor de Pago</label>
                                    <input type="text" name="v_pago" class="form-control" placeholder="Valor de pago" value="{{old('v_pago') ? old('v_pago') : $item->v_pago }}">
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
