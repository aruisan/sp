{!! Form::Open(['route' => $route, 'method' => $method]) !!}
<div class="col-12 formularioTerceros">
            <div class="row justify-content-center">
                <br>
                    <div class="breadcrumb text-center">
                        <center> 
                        <h2>Nuevo Tercero</h2></center></div>
                        <br>
                    </div>
        
        
            <div class="row inputCenter">
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Nombre', 'Nombre')}}
                            {{ Form::text('nombre', $persona->nombre, ['class' => 'form-control', 'placeholder' => 'Nombre']) }}
                        </div>
                    </div>

                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4"> 
                        <div class="form-group">
                            {{ Form::label('Tipo de Documento', 'Tipo de Documento')}}
                            {{ Form::select('tipo_doc', ['NIT'=> 'NIT', 'CC' =>'CC', 'CE' =>'CE'], $persona->tipo_cc, ['class' => 'form-control', 'placeholder' => 'Selecciona Tipo de Documento'] )}}
                        </div>
                    </div>
                    
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4"> 
                        <div class="form-group">
                            {{ Form::label('Numero Documento', 'Numero Documento')}}
                            {{ Form::text('num_dc', $persona->num_dc, ['class' => 'form-control', 'placeholder' => 'Numero Documento']) }}
                        </div>
                    </div>
            </div>  

            <div class="row inputCenter">
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Regimen', 'Regimen')}}
                            {{ Form::select('regimen', ['Ordinario'=> 'Ordinario', 'Simple Tribulación' =>'Simple Tribulación','Especial' => 'Especial'], $persona->regimen, ['class' => 'form-control', 'placeholder' => 'Selecciona Tipo de Regimen'] )}}
                        </div>
                    </div>

                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4"> 
                        <div class="form-group">
                            {{ Form::label('Responsabilidad Iva', 'Responsabilidad Iva')}}
                            {{ Form::select('responsabilidad_iva', ['Si'=> 'Si', 'No' =>'No'], $persona->responsabilidad_iva, ['class' => 'form-control', 'placeholder' => 'Es Responsable del IVA'] )}}
                        </div>
                    </div>
            </div>    

            <div class="row inputCenter" >
            
                 
              
            
                <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                    <div class="form-group">
                    {{ Form::label('Responsable de Renta', 'Responsable de Renta')}}
                    {{ Form::select('responsabilidad_renta', [
                            'Responsable Impuesto Renta' => 'Responsable Impuesto Renta', 
                            'Gran Contribuyente' => 'Gran Contribuyente', 
                            'Simple Tributacion' => 'Simple Tributacion', 
                            'Entidad del Estado'=> 'Entidad del Estado', 
                            'Entidad Sin Animo De Lucro' =>'Entidad Sin Animo De Lucro', 
                            'Institucion Educativa Publica' => 'Institucion Educativa Publica',
                            'No declarante impuesto de Renta' => 'No declarante impuesto de Renta'
                        ], 
                        $persona->regimen, 
                        ['class' => 'form-control', 'onchange' => 'var obj= document.getElementById("regimen_text");if(this.value=="Otro"){obj.style.display="inline";}else{obj.style.display="none";};', 'id' => 'regimen']) }}
                    </div>
                </div> 

                <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                    <div class="form-group">
                    {{ Form::label('Tipo', 'Tipo')}}
                    {{ Form::select('tipo', ['NATURAL'=> 'NATURAL', 'JURIDICA' =>'JURIDICA'], $persona->tipo, ['class' => 'form-control','placeholder' => 'Selecciona Tipo de Persona', 'id' => 'tipo_persona']) }}            
                    </div>
                </div>


                <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group" id = 'regimen_porcentaje'>
                            {{ Form::label('%', '%')}}
                            {{ Form::text('regimen_porcentaje', is_null($persona->regimen_porcentaje) ? 10 : $persona->regimen_porcentaje, ['class' => 'form-control', 'placeholder' => '%', 'id' => 'input_porcentaje']) }}
                        </div>
                </div>
            </div>



            
            

            <div class="inputCenter" >
                <div class="row">
                   
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Email', 'Email')}}
                            {{ Form::text('email', $persona->email, ['class' => 'form-control', 'placeholder' => 'Email']) }}            
                        </div>
                    </div>
        
            
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Direccion', 'Direccion')}}
                            {{ Form::text('direccion', $persona->direccion, ['class' => 'form-control', 'placeholder' => 'Direccion']) }}            
                        </div>
                    </div>
                    
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Telefono', 'Telefono')}}
                            {{ Form::text('telefono', $persona->telefono, ['class' => 'form-control', 'placeholder' => 'Telefono']) }}            
                        </div>
                    </div>
            </div>
        </div>
     <div class="inputCenter" >
        <div class="row">
                
               <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                    <div class="form-group">
                        {{ Form::label('Ciudad', 'Ciudad')}}
                        {{ Form::text('ciudad', $persona->ciudad, ['class' => 'form-control', 'placeholder' => 'Ciudad']) }}
                    </div>
                </div>

                <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                    <div class="form-group">
                    {{ Form::label('Clase', 'Clase')}}
                    {{ Form::select('tipo_tercero', [0 => 'Funcionario', 1 =>'Contribuyente', 2 => 'Proveedor', 3 => 'Libranza'], $persona->tipo_tercero_index, ['class' => 'form-control','placeholder' => 'Selecciona Tipo de Persona', 'id' => 'tipo_persona']) }}            
                    </div>
                </div>

           
        </div>
   </div>
   <div class="inputCenter" >
                <div class="row">
                   
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('No. Cuenta Bancaria', 'No. Cuenta Bancaria')}}
                            {{ Form::text('numero_cuenta_bancaria', $persona->numero_cuenta_bancaria, ['class' => 'form-control', 'placeholder' => 'No. Cuenta Bancaria']) }} 
                        </div>
                    </div>
        
            
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Tipo de cuenta Bancaria', 'Tipo de cuenta Bancaria')}}
                            {{ Form::select('tipo_cuenta_bancaria', ['Ahorros'=> 'Ahorros', 'Corriente' =>'Corriente'], $persona->tipo_cuenta_bancaria, ['class' => 'form-control','placeholder' => 'Selecciona Tipo de Cuenta bancaria']) }}                    
                        </div>
                    </div>
                    
                    <div class="col-xs-11 col-sm-11 col-md-4 col-lg-4">  
                        <div class="form-group">
                            {{ Form::label('Entidad bancaria', 'Entidad Bancaria')}}
                            {{ Form::select('banco_cuenta_bancaria', [
                                'BANCO DE BOGOTA' => 'BANCO DE BOGOTA',
                                'BANCO AGRARIO' => 'BANCO AGRARIO', 
                                'BANCO DAVIVIENDA' => 'BANCO DAVIVIENDA',
                                'BANDO POPULAR' => 'BANDO POPULAR',
                                'BANCO BANCOLOMBIA' => 'BANCO BANCOLOMBIA',
                                'BANCO OCCIDENTE' => 'BANCO OCCIDENTE',
                                'BANCO AVVILLAS' => 'BANCO AVVILLAS',
                                'BANCO VBVA' => 'BANCO VBVA',
                                'BANCO CAJA SOCIAL' => 'BANCO CAJA SOCIAL', 
                                'BANCO FALABELLA' => 'BANCO FALABELLA',
                                'BANCO SUDAMERIS' => 'BANCO SUDAMERIS',
                                'BANCO PICHINCHA' => 'BANCO PICHINCHA',
                                'BANCO CITIBANK' => 'BANCO CITIBANK',
                                'BANCO SANTANDER' => 'BANCO SANTANDER'
                            ], $persona->banco_cuenta_bancaria, ['class' => 'form-control','placeholder' => 'Selecciona Tipo de Cuenta bancaria']) }}                    
                        </div>
                    </div>
            </div>
        </div>

       
            <div class="form-group text-center">
                <input type="submit" value="Guardar" class="btn-danger  btn-lg" >
            </div>
    </div>
{!! Form::close()!!}

