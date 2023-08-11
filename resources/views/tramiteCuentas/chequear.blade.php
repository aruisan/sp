
@extends('layouts.dashboard')
@section('titulo')
Chequeartramites de cuentas 
@stop

@section('content')
 <div class="breadcrumb">
        <ul class="breadcrumb">
          <li class="text-capitalize"><a href="{{route('tramites-cuentas.index')}}" > Todos los tramites de cuentas</a></li>
          <li class="text-capitalize">Chequeando Tramite de Cuenta No  de contrato{{$tramiteCuenta->n_contrato}} </li>
        </ul>
    </div>
   <div class="container-fluid">
    <div class="row">
        <div class="panel panel-default widget col-md-12">
            <div class="panel-body">
                <form action="{{route('chequeo-cuenta.store')}}" method="post">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" align="100%">
                                <thead>
                                    <th class="text-center col-md-2">DOCUMENTOS</th>
                                    <th class="text-center"><i class="fa fa-check-square" aria-hidden="true"></i></th>
                                    <th class="text-center">MOTIVO DE DEVOLUCION</th>
                                    <th class="text-center">OBSERVACIONES</th>
                                </thead>    
                                <tbody>
                                    @foreach ($tramiteCuenta->chequeosCuenta as  $item )
                                        <tr>
                                            <td>{{$item->requisitoChequeo->nombre}}</td>
                                            <td><input type="checkbox" name="estado[]" class="form-check-input" {{!$item->validar_chequeo ?: 'checked=""'}} value="{{$item->id}}"></td>
                                            <td><input type="hidden" name="id[]" value="{{$item->id}}"><input class="form-control" type="text" name="devolucion[]" value="{{$item->devolucion}}"></td>
                                            <td><input class="form-control" type="text" name="observacion[]" value="{{$item->observacion}}"></td>
                                        </tr>
                                     @endforeach         
                                </tbody>
                            </table>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                </form>
            </div>
        </div>
    </div>

   </div>
@stop
