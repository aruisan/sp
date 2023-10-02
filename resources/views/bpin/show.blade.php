@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">BPins </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mt-3 mb-3">
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="modal" data-target="#myModal">Nueva Actividad</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#menu1">Adición</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#menu2">Reducción</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#menu2">Decreto</a>
                        </li>
                    </ul>
                    
                    <a href="{{route('bpin.create')}}" class="btn btn-primary">Nuevo Actividad</a>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th>
                                    Codigo Actividad
                                </th>
                                <th>
                                    Nombre Actividad
                                </th>
                            </thead>
                            <tbody>
                                @foreach($bpins as $item)
                                    <tr>
                                        <td>
                                            {{$item->cod_proyecto}}
                                        </td>
                                         <td>
                                            {{$item->nombre_proyecto}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Nueva Actividad</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form method="post" action="{{route('bpin.create')}}">
             <div class="row">
                <input type="hidden">
                <div class="input-group my-2 col-md-12">
                    <label for="" class="input-group-text input_text_label col-sm-5">codigo de Actividad</label>
                    <input type="text" name="cod_actividad" class="form-control col-sm-7">
                </div>
                 <div class="input-group my-2 col-md-12">
                    <label for="" class="input-group-text input_text_label col-sm-5">Nombre de Actividad</label>
                    <input type="text" name="nombre_actividad" class="form-control col-sm-7">
                </div>
                 <div class="input-group my-2 col-md-12">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </div
        </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
    <script>
    
    </script>
@endsection
