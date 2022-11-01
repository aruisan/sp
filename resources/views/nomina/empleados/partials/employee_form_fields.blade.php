<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_doc_number">
            Número de documento
        </label>
        <input 
            type="text" 
            value="{{isset($employee)? $employee->num_dc : "" }}" 
            class="form-control short-input" 
            id="employee_doc_number" 
            name="employee_doc_number"
        />
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
        <input 
            type="text" 
            value="{{isset($employee)? $employee->nombre : "" }}" 
            class="form-control short-input" 
            id="employee_name"
             name="employee_name"
        />
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
        <input 
            type="text" 
            value="{{isset($employee)? $employee->email : "" }}" 
            class="form-control short-input" 
            id="employee_email" 
            name="employee_email"
        />
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
        <input 
            type="text" 
            value="{{isset($employee)? $employee->direccion : "" }}" 
            class="form-control short-input" 
            id="employee_address" 
            name="employee_address"
        />
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
        <input 
            type="text" 
            value="{{isset($employee)? $employee->fecha_nacimiento : "" }}" 
            class="form-control short-input" 
            id="employee_birth_date" 
            name="employee_birth_date"
        />
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
        <input 
            type="text" 
            value="{{isset($employee)? $employee->telefono : "" }}" 
            class="form-control short-input" 
            id="employee_phone" 
            name="employee_phone"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_position">
            Cargo
        </label>
        <input 
            type="text" 
            value="{{isset($employee)? $employee->cargo : "" }}" 
            class="form-control short-input" 
            id="employee_position" 
            name="employee_position"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_position_code">
            Código del cargo
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->codigo_cargo : "" }}" 
            class="form-control short-input" 
            id="employee_position_code" 
            name="employee_position_code"
            />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 form-group">
        <label for="employee_position_type">Tipo de cargo</label>
        <select 
            class="form-control" 
            id="employee_position_type" 
            name="employee_position_type"
            value="{{isset($employee)? $employee->tipo_cargo : "" }}"
        >
            <option>{{ App\NominaEmpleado::TIPO_CARGO_1 }}</option>
            <option>{{ App\NominaEmpleado::TIPO_CARGO_2 }}</option>
            <option>{{ App\NominaEmpleado::TIPO_CARGO_3 }}</option>
            <option>{{ App\NominaEmpleado::TIPO_CARGO_4 }}</option>
        </select>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_degree">
            Grado
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->grado : "" }}" 
            class="form-control short-input" 
            id="employee_degree" 
            name="employee_degree"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_apto_administrative_num">
            Apto administrativo número
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->apto_administrativo_numero : "" }}" 
            class="form-control short-input" 
            id="employee_apto_administrative_num" 
            name="employee_apto_administrative_num"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">        
    </div>
    <div class='col-sm-6 col-md-4 input-group'>
        <label for="apto_adnimistrative_date">
            Apto administrativo fecha
        </label>
        <div class="form-group">
            <input type="date" id="apto_adnimistrative_date" name="apto_adnimistrative_date"
                value="{{isset($employee)? $employee->apto_administrativo_fecha : ""}}"
                min="2018-01-01" max="2050-12-31"
            >
        </div>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_apto_administrative_file">
            Apto administrativo archivo
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->apto_administrativo_archivo : "" }}" 
            class="form-control short-input" 
            id="employee_apto_administrative_file" 
            name="employee_apto_administrative_file"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_eps">
            EPS
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->eps : "" }}" 
            class="form-control short-input" 
            id="employee_eps" 
            name="employee_eps"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 input-group">
        <label for="employee_pension_fund">
            Fondo de pensiones
        </label>
        <input
            type="text" 
            value="{{isset($employee)? $employee->fondo_pensiones : "" }}" 
            class="form-control short-input" 
            id="employee_pension_fund" 
            name="employee_pension_fund"
        />
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-4">
    </div>
    <div class="col-sm-6 col-md-4 form-group">
        <label for="bank_account_type">Tipo de cuenta bancaria</label>
        <select 
            class="form-control" 
            id="bank_account_type" 
            name="bank_account_type" 
            value="{{isset($employee)? $employee->tipo_cuenta_bancaria : "" }}"
        >
            <option>{{ App\NominaEmpleado::TIPO_CUENTA_BANCARIA_1 }}</option>
            <option>{{ App\NominaEmpleado::TIPO_CUENTA_BANCARIA_2 }}</option>
        </select>
    </div>
    <div class="col-sm-3 col-md-4">
    </div>
</div>
<div class="divider" style="height: 15px">

</div>