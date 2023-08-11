<div class="modal fade" role="dialog" id="modalRelacionarParticipante">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Buscar o Crear Dueño</h4>
              </div>
              {!! Form::Open(['url' => 'admin/find-create', 'method' => 'POST' , 'id' => 'formRelacionarParticipantes']) !!}
              <div class="modal-body" style="display: inline-block;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('Numero Documento', 'Numero Documento')}}
                                {{ Form::text('num_dc', NULL, ['class' => 'form-control', 'id'=>'identificadorRelacionParticipantes', 'placeholder' => 'Numero Documento']) }}
                            </div>
                        </div>          
                        <div class="col-md-4">
                            
                            <div class="form-group">
                                {{ Form::label('Nombre', 'Nombre')}}
                                {{ Form::text('nombre', Null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'id' => 'Nombre']) }}
                            </div>

                        </div>          
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('Email', 'Email')}}
                                {{ Form::text('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email', 'id' => 'Email']) }}            
                             </div>
                        </div>
                    </div>      
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('Tipo', 'Tipo')}}
                                {{ Form::select('tipo', ['NATURAL'=> 'NATURAL', 'JURIDICA' =>'JURIDICA'], 'NATURAL', ['class' => 'form-control', 'id' => 'Tipo'] ) }}            
                            </div>
                        </div>          
                        <div class="col-md-4">
                           <div class="form-group">
                                {{ Form::label('Direccion', 'Direccion')}}
                                {{ Form::text('direccion', NULL, ['class' => 'form-control', 'placeholder' => 'Direccion', 'id' => 'Direccion']) }}     
                            </div>

                        </div>          
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('Telefono', 'Telefono')}}
                                {{ Form::text('telefono', Null, ['class' => 'form-control', 'placeholder' => 'Telefono', 'id' => 'Telefono']) }}            
                            </div>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                <button type="button" onclick="submitRelacionarParticipante()" class="btn btn-primary">relacionar</button>
              </div>
              {!! Form::close()!!}
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->