@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Bpin</div>
                <div class="card-body">
                   <form  method="post" action="{{route('bpin.store')}}">
                    @csrf
                    <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Fecha</label>
                            <input type="date" value="{{date('Y-m-d')}}" class="form-control col-sm-7" readonly >
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Secretaria Solicitante</label>
                            <input type="text"  class="form-control col-sm-7" value="{{auth()->user()->dependencia->nombre}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Nombre de Secretario</label>
                            <input type="text"  class="form-control col-sm-7" value="{{auth()->user()->name}}" readonly>
                        </div>
                    </div>
                    <hr class="mt-2 mb-5">
                    <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Valor del Bpin</label>
                            <input type="text" name="valor" class="form-control col-sm-7">
                        </div>
                    </div>
                     <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Codigo del Bpin</label>
                            <input type="text" name="codigo" class="form-control col-sm-7">
                        </div>
                    </div>
                     <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Nombre del Proyecto</label>
                            <input type="text" name="proyecto" class="form-control col-sm-7">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group my-2 col-md-12">
                            <label for="" class="input-group-text input_text_label col-sm-5">Selecciona un rubro</label>
                            <select name="rubro_id" class="form-control input_text col-sm-7">
                                @foreach($rubros as $item)
                                    <option value="{{$item->id}}">{{$item->puc->categoria}} - {{$item->puc->codigo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
    
    </script>
@endsection
