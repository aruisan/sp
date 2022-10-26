<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_doc_number">
            Número de documento
        </label>
        <input type="text" value="{{isset($employee)? $employee->num_dc : "" }}" class="form-control short-input" id="employee_doc_number" name="employee_doc_number"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_name">
            Nombre
        </label>
        <input type="text" value="{{isset($employee)? $employee->nombre : "" }}" class="form-control short-input" id="employee_name" name="employee_name"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_age">
            Edad
        </label>
        <input type="text" value="{{isset($employee)? $employee->edad : "" }}" class="form-control short-input" id="employee_age" name="employee_age"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_email">
            Email
        </label>
        <input type="text" value="{{isset($employee)? $employee->email : "" }}" class="form-control short-input" id="employee_email" name="employee_email"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_address">
            Dirección
        </label>
        <input type="text" value="{{isset($employee)? $employee->direccion : "" }}" class="form-control short-input" id="employee_address" name="employee_address"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_birth_date">
            Fecha de nacimiento
        </label>
        <input type="text" value="{{isset($employee)? $employee->fecha_nacimiento : "" }}" class="form-control short-input" id="employee_birth_date" name="employee_birth_date"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_phone">
            Teléfono
        </label>
        <input type="text" value="{{isset($employee)? $employee->telefono : "" }}" class="form-control short-input" id="employee_phone" name="employee_phone"/>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="divider" style="height: 15px">

</div>