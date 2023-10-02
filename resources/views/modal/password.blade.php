<div id="cambiarPasword" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Cambiar Contraseña de Cuenta de Usuario</h4>
          </div>
          {!! Form::Open(['route' => 'user-password', 'method' => 'POST', 'class' => 'form-valide']) !!}
            <div class="modal-body text-center">
                {{ Form::label('passwordActual', 'Contraseña Actual:')}}
                {{ Form::password('passwordActual', null, ['class' => 'form-control', 'placeholder' => 'password Actual']) }}
                <br><br>
                {{ Form::label('password', 'Nueva Contraseña:')}}
                {{ Form::password('password', null, ['class' => 'form-control', 'placeholder' => 'nuevo password']) }}
                <br><br>
                {{ Form::label('passwordC', 'Confirmar Contraseña:')}}
                {{ Form::password('passwordC', null, ['class' => 'form-control', 'placeholder' => 'confirmar nuevo password']) }}

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Cambiar Password</button>
            </div>
        {!! Form::close()!!}
      </div>	
    </div>
</div>