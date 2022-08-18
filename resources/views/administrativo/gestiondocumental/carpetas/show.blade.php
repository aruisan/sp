@extends('layouts.dashboard')
@section('titulo')
    Crear Carpeta
@stop
@section('sidebar')
    {{-- <li> <a class="btn btn-primary" href="{{ asset('/dashboard/boletines') }}"><span class="hide-menu">Boletines</span></a></li> --}}
@stop
@section('content')

<div class="col-xs-12 col-sm-12 col-md-12 formularioBoletin">


        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="text-center">Carpeta {{$carpeta->nombre}}</h2>
            </div>
        </div>
        
<div class="row"  style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #efb827; ">
        
    <ul class="nav nav-pills" >
            <li class="nav-item">
                <a class="nav-link regresar" href="{{route('carpetas.listar', $carpeta->tipo)}}" >{{$carpeta->tipo}}</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link " data-toggle="pill" href="#ver">Carpeta {{$carpeta->nombre}}</a>
            </li>
    </ul>
            
    <div class="row mb-3" >
        <div class="col-md-8">
            <table class="table">
                <tbody>
                    <tr>
                        <td><b class="text-danger">Nombre:</b></td>
                        <td>{{$carpeta->nombre}}</td>
                    </tr>
                    <tr>
                        <td><b class="text-danger">Ubicación:</b></td>
                        <td>{{$carpeta->ubicacion_fisica}}</td>
                    </tr>
                    <tr>
                        <td><b class="text-danger">Tipo:</b></td>
                        <td>{{$carpeta->tipo}}</td>
                    </tr>
                    <tr>
                        <td><b class="text-danger">Cuantia:</b></td>
                        <td>{{$carpeta->cuantia}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row ">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-info btn-lg my-3" data-toggle="modal" data-target="#modal_nuevo_documento">Nuevo Documento</button>
        <table class="table">
            <thead>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th><i class="fa fa-eraser" aria-hidden="true"></i></th>
            </thead>
            <tbody>
                @foreach($carpeta->documentos as $documento)
                    <tr>
                        <td>
                            <a href="{{Storage::url($documento->resource->ruta)}}" target="blank" >
                                {{$documento->name}}
                            </a>
                        </td>
                        <td>{{$documento->estado_string}}</td>
                        <td>{{$documento->created_at}}</td>
                        <td>
                            <a href="{{route('carpeta.archivo.delete', $documento->id)}}" class="btn btm-link">
                                <i class="fa fa-eraser" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Modal -->
<div id="modal_nuevo_documento" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Nuevo {{$carpeta->tipo}}</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(array('route' => ['carpeta.archivo.store', $carpeta->id],'method'=>'POST','enctype'=>'multipart/form-data')) !!}
            <div class="row">
               <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Nombre: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <small class="form-text text-muted">Nombre que se desee asignar al archivo</small>
                </div>
            

                 <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Fecha del Documento: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        <input type="date" name="ff_document" class="form-control" required>
                    </div>
                    <small class="form-text text-muted">Fecha que tiene asiganada el documento a subir</small>
                </div>
            </div>



            <div class="row">
                 <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Tercero: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <select class="form-control" name="tercero_id">
                            @foreach($terceros as $tercero)
                                <option value="{{$tercero->id}}">{{$tercero->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="form-text text-muted">Relacionar persona</small>
                </div>
            

              <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Número de Documento: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-hashtag" aria-hidden="true"></i></span>
                        <input type="text" name="number_doc" class="form-control" required>
                    </div>
                    <small class="form-text text-muted">Número de Documento</small>
                </div>
            </div>


            <div class="row">
                 <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Fecha de Vencimiento del Documento: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                        <input type="date" name="ff_vence" class="form-control" required>
                    </div>
                    <small class="form-text text-muted">Fecha de vencimiento del documento</small>
                </div>
          
               <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Consecutivo: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-hashtag" aria-hidden="true"></i></span>
                        <input type="text" name="cc_id" class="form-control" required>
                    </div>
                    <small class="form-text text-muted">Consecutivo del Archivo</small>
                </div>
            </div>


            <div class="row">
                 <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Estado actual del documento: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                        <select class="form-control" name="estado">
                                <option value="0">Pendiente</option>
                                <option value="1">Aprobado</option>
                                <option value="2">Rechazado</option>
                                <option value="3">Archivado</option>
                        </select>
                    </div>
                    <small class="form-text text-muted">Seleccionar el estado en el que se encuentra el documento</small>
                </div>
          
          
                <div class="form-group col-xs-11 col-sm-11 col-md-6 col-lg-6"> 
                    <label>Subir Archivo: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>
                        <input type="file" name="archivo" accept="application/pdf" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-6 col-sm-12 col-md-12 col-lg-12 text-center">
                <button class="btn btn-primary btn-raised btn-lg" >Guardar</button>
            </div>
        {!! Form::close() !!}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('css')
    <style type="text/css">
        .my-3{
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .mb-3{
            margin-bottom: 30px;
        }
    </style>
@endsection

@section('js')
    <script type="text/javascript">
    </script>
@endsection
