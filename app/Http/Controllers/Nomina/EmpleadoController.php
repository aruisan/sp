<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\Traits\FileTraits;

class EmpleadoController extends Controller
{
    public function index(){
        $empleados =  NominaEmpleado::all();
        //dd($empleados);
        return view('nomina.empleados.index', compact('empleados'));
    }

    public function create(Request $request){
        $newEmployee = new NominaEmpleado();
        $newEmployee->num_dc = $request->employee_doc_number;
        $newEmployee->nombre = $request->employee_name;
        $newEmployee->email = $request->employee_email;
        $newEmployee->direccion = $request->employee_address;
        $newEmployee->fecha_nacimiento = $request->employee_birth_date;
        $newEmployee->telefono = $request->employee_phone;
        $newEmployee->cargo = $request->employee_position;
        $newEmployee->codigo_cargo = $request->employee_position_code;
        $newEmployee->tipo_cargo = $request->employee_position_type;
        $newEmployee->grado = $request->employee_degree;
        $newEmployee->apto_administrativo_numero = $request->employee_apto_administrative_num;
        $newEmployee->apto_administrativo_fecha = $request->employee_apto_adnimistrative_date;
        $newEmployee->apto_administrativo_archivo = $request->employee_apto_administrative_file;
        $newEmployee->eps = $request->employee_eps;
        $newEmployee->fondo_pensiones = $request->employee_pension_fund;
        $newEmployee->tipo_cuenta_bancaria = $request->employee_bank_account_type;
        $newEmployee->numero_cuenta_bancaria = $request->employee_bank_account_num;
        $newEmployee->banco_cuenta_bancaria = $request->employee_bank_account_bank;
        $newEmployee->certificado_cuenta_bancaria = $request->employee_bank_account_certificate;
        $newEmployee->save();
        $fileTraits = new FileTraits();
        $savedEmployeeAptoAdministrativeFile = $fileTraits->File($newEmployee->apto_administrativo_archivo, 'empleados/'.$newEmployee->id);
        $newEmployee->apto_administrativo_archivo = $savedEmployeeAptoAdministrativeFile;
        $savedEployeeBankAccountCertificate =  $fileTraits->File($newEmployee->certificado_cuenta_bancaria, 'empleados/'.$newEmployee->id);
        $newEmployee->certificado_cuenta_bancaria = $savedEployeeBankAccountCertificate;
        $newEmployee->save();
        return redirect()->route('nomina.empleados.index')
                ->with('empleados', NominaEmpleado::all());
    }

    public function edit($id){
        $employee = NominaEmpleado::find($id);
        return view('nomina.empleados.edit', compact('employee'));
    }

    public function update(Request $request, $id){
        $employee = NominaEmpleado::find($id);
        $employee->num_dc = $request->employee_doc_number;
        $employee->nombre = $request->employee_name;
        $employee->email = $request->employee_email;
        $employee->direccion = $request->employee_address;
        $employee->fecha_nacimiento = $request->employee_birth_date;
        $employee->telefono = $request->employee_phone;
        $employee->cargo = $request->employee_position;
        $employee->codigo_cargo = $request->employee_position_code;
        $employee->tipo_cargo = $request->employee_position_type;
        $employee->grado = $request->employee_degree;
        $employee->apto_administrativo_numero = $request->employee_apto_administrative_num;
        $employee->apto_administrativo_fecha = $request->employee_apto_adnimistrative_date;
        //$employee->apto_administrativo_archivo = $request->employee_apto_administrative_file;
        $employee->eps = $request->employee_eps;
        $employee->fondo_pensiones = $request->employee_pension_fund;
        $employee->tipo_cuenta_bancaria = $request->employee_bank_account_type;
        $employee->numero_cuenta_bancaria = $request->employee_bank_account_num;
        $employee->banco_cuenta_bancaria = $request->employee_bank_account_bank;
        //$employee->certificado_cuenta_bancaria = $request->employee_bank_account_certificate;
        $employee->save();
        return redirect()->route('nomina.empleados.index')
                ->with('empleados', NominaEmpleado::all());
    }
}
