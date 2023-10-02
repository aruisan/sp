@extends('layouts.dashboard')
@section('titulo')
    Crear Funcionarios
@stop
@section('sidebar')
  {{-- @include('admin.funcionarios.cuerpo.aside') --}}
@stop
@section('content')

<div class="col-12 formularioFuncionarios">


    <div class="breadcrumb text-center">
            <strong>
                <h4><b>Creación nuevo Funcionario</b></h4>
            </strong>
        </div>
            <ul class="nav nav-pills">
                <li class="nav-item ">
                    <a class="nav-link "  href="{{route('funcionarios.index')}}">Lista Funcionarios</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" data-toggle="pill" href="#crear">Crear Funcionario</a>
                </li>
             
            </ul>
     
            <div class="tab-content" style="background-color: white">
                <div id="lista" class="tab-pane active"><br>


        {!! Form::open(array('route' => 'funcionarios.store','method'=>'POST')) !!}
        <div  id="data col-10">

{{-- style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #6c0e03; " --}}
            <div class="row inputCenter" >
            
                <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">  
                    <div class="form-group">
                        <strong>Nombre:</strong>
                       
                        {!! Form::text('name', null, array('placeholder' => 'Digite el Nombre','class' => 'form-control ')) !!}
                   
                    </div>

                </div>

                <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
                    <div class="form-group">
                        <strong>Correo:</strong>
                        {!! Form::text('email', null, array('placeholder' => 'Correo ','class' => 'form-control ')) !!}
                    </div>
                </div>

            </div>

            <div class="row inputCenter">
       
                    <div class="col-xs-11 col-sm-11 col-md-6">
                        <div class="form-group">
                            {{ Form::label('Dependencia', 'Dependencia')}}
                            {!! Form::select('dependencia_id', $dependencias,[], array('class' => 'form-control')) !!}
                        </div>
                    </div> 

                <div class="col-xs-11 col-sm-11 col-md-6">
                    <div class="form-group">
                        {{ Form::label('Rol', 'Rol')}}
                        {!! Form::select('roles', $roles,[], array('class' => 'form-control')) !!}
                    </div>
                </div>
                

            

            </div>

   <div class="row inputCenter">
       
                <div class=" col-xs-11 col-sm-11 col-md-6 col-lg-6">
                    <div class="form-group">
                        {{ Form::label('Tipo', 'Tipo')}}
                        {{ Form::select('type_id', $tipos , null, ['id'=>'type','class' => 'form-control', 'placeholder' =>'Selecciona Tipo de usuario', '@change' => 'getJefes()']) }}            
                    </div>
                </div>
              
                <div class=" col-xs-11 col-sm-11 col-md-6 col-lg-6">
                    <div class="form-group" style="display: none;" id="divJefes">
                        {{ Form::label('Jefe', 'Jefe')}}
                        <select class="form-control" name="jefe" v-model="selected">
                            <option v-for="dato in datos" v-bind:value="dato.id">
                            @{{dato.name}}
                            </option>             
                        </select>          
                    </div>
                </div>

            </div>

        <div class="row" style=" margin-top: 20px;    padding-top: 20px;    border-top: 3px solid #6c0e03;    margin-right: 15px;
    margin-left: 15px;">
           
            <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
                <div class="form-group">
                    <strong>Password:</strong>
                    {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                </div>
            </div>


            <div class="col-xs-11 col-sm-11 col-md-6 col-lg-6">
                <div class="form-group">
                    <strong>Confirm Password:</strong>
                    {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                </div>
            </div>

       

                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                </div>
        
         </div>

    </div>
</div>

    </div>
</div>
{!! Form::close() !!}


@endsection

@section('js')
    <script type="text/javascript">

        new Vue({
            el: '#data',
            jefes: function(){
                this.getJefes();
            },
            data:{
                selected: "",
                datos: []
            },
            methods:{
                getJefes: function(){
                    var data = $('#type').val();
                        if(data == 1 || data == 5 || data == 6)
                        {
                            $('#divJefes').hide();
                        }else{
                            $('#divJefes').show();
                        }
                    var url = '/admin/funcionarios/jefes/'+data;
                    axios.get(url)
                         .then(response => {
                            this.datos = response.data;
                    });
                }

            }
        });
    </script>
@stop