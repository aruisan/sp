@extends('layouts.dashboard')
@section('titulo')
   Crear Entidad
@stop
@section('sidebar')
  {{-- @include('admin.modulos.cuerpo.aside') --}}
   {{-- @include('admin.permisos.cuerpo.aside') --}}
@stop
@section('content')

<div class="col-12 ">


        <div class="row">
            <div class="col-9 margin-tb">
                    <h2 class="text-center">Ingreso Datos Entidad</h2>
            </div>
        </div>
      
{!! Form::open(array('route' => 'entidades.store','method'=>'POST')) !!}
   

            <div class="row inputCenter" style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #3d7e9a; ">
            
        <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">     

   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Entidad:</strong>
                         
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    <select class="form-control" name="estado">
                                        <option value="Alcaldía">Alcaldía</option>
                                        <option value="Concejo">Concejo</option>
                                        
                                    </select>
                                </div>
                    </div>
                </div>

   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Nombre de Funcionario:</strong>
                             <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <input type="text" name="alcalde" class="form-control" required>
                            </div>
                    </div>
                </div>

        



            </div>
        </div>

    <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">     

   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Cargo de Funcionario:</strong>
                         
                                <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <input type="text" name="alcalde" class="form-control" required>
                            </div>

                    </div>
                </div>

   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Año de Inicio de vigencia:</strong>
                             <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                <input type="number" name="alcalde" class="form-control" required>
                            </div>
                    </div>
                </div>

        



            </div>
        </div>

                    
        <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">     


           <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                        <strong>Municipio:</strong>
                        <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    <select class="form-control" name="estado">
                                        <option value=""></option>
                                        <option value=""></option>
                                        
                                    </select>
                                </div>
                    </div>
                </div>

   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Departamento:</strong>
                         
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    <select class="form-control" name="estado">
                                        <option value=""></option>
                                        <option value=""></option>
                                        
                                    </select>
                                </div>
                    </div>
                </div>
                   

            </div>
        </div>

                    
        <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">     

            <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                                <strong>Frase Principal:</strong>
                                 {!! Form::textArea('frase', null, array('placeholder' => 'Escriba la frase principal','class' => 'form-control')) !!}
                              
                    </div>
                </div>


                   <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                        <strong>Frase en movimiento:</strong>
                        {!! Form::textArea('frase_mov', null, array('placeholder' => 'Escriba la frase en movimiento','class' => 'form-control')) !!}
                    </div>
                </div>

            </div>
        </div>

    <div class="row">
       <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Subir Archivo del Escudo del Municipio: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>
                <input type="file" name="fileActa" accept="application/pdf" class="form-control" required>
            </div>
            <small class="form-text text-muted">Archivo Correspondiente al Escudo del Municipio</small>
        </div>

         <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Subir Archivo del Escudo del Departamento: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>
                <input type="file" name="fileActa" accept="application/pdf" class="form-control" required>
            </div>
            <small class="form-text text-muted">Archivo correspondiente al Escudo del Departamento</small>
        </div>
    </div>


</div>
<div class="row">

<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Subir Archivo Imagen Principal: </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>
                <input type="file" name="fileActa" accept="application/pdf" class="form-control" required>
            </div>
            <small class="form-text text-muted">Archivo correspondiente a imagen principal</small>
        </div>

    <div class="col-xs-12 col-sm-12 col-md-6 text-center">
        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
         </div>



    </div>

    <br><br>

</div>
{!! Form::close() !!}

@endsection
