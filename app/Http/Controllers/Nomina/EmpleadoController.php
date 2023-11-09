<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;
use App\Traits\StorageTraits;

class EmpleadoController extends Controller
{
    use StorageTraits;

    public function __construct()
    {
         $this->middleware('permission:listar-empleados', ['only' => ['index']]);
         $this->middleware('permission:crear-empleados', ['only' => ['create', 'store']]);
         $this->middleware('permission:editar-empleados', ['only' => ['edit','update']]);
         $this->middleware('permission:activar-empleados|activar-empleados', ['only' => ['status']]);
    }

    public function index(){
        $empleados =  $empleados = NominaEmpleado::where('tipo', 'empleado')->get();
        //dd($empleados);
        return view('nomina.empleados.index', compact('empleados'));
    }

    public function create(){
        return view('nomina.empleados.create');
    }

    public function store(Request $request){
        $newEmployee = new NominaEmpleado();
        $newEmployee->num_dc = $request->employee_doc_number;
        $newEmployee->nombre = $request->employee_name;
        $newEmployee->salario = $request->salario;
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
        $newEmployee->apto_administrativo_archivo = "";
        $newEmployee->eps = $request->employee_eps;
        $newEmployee->porc_riesgos = $request->porc_riesgos;
        $newEmployee->fondo_pensiones = $request->employee_pension_fund;
        $newEmployee->tipo_cuenta_bancaria = $request->employee_bank_account_type;
        $newEmployee->numero_cuenta_bancaria = $request->employee_bank_account_num;
        $newEmployee->banco_cuenta_bancaria = $request->employee_bank_account_bank;
        $newEmployee->certificado_cuenta_bancaria = "";
        $newEmployee->tipo = 'empleado';
        $newEmployee->save();

        $savedEmployeeAptoAdministrativeFile = $request->hasFile('employee_apto_administrative_file') 
                                             ? $this->uploadFile($request->employee_apto_administrative_file, 'empleados/'.$newEmployee->id)
                                             : '';
        $savedEployeeBankAccountCertificate =  $request->hasFile('employee_bank_account_certificate') 
                                            ? $this->uploadFile($request->employee_bank_account_certificate, 'empleados/'.$newEmployee->id)
                                            : '';


        $newEmployee->apto_administrativo_archivo = $savedEmployeeAptoAdministrativeFile;
        $newEmployee->certificado_cuenta_bancaria = $savedEployeeBankAccountCertificate;
        $newEmployee->save();

        return redirect()->route('nomina.empleados.index');
    }

    public function edit(NominaEmpleado $employee){
        dd($employee->movimientos->filter(function($m){ return !is_null($m->nomina); })->last()->prima_navidad);
        return view('nomina.empleados.edit', compact('employee'));
    }

    public function update(Request $request, NominaEmpleado $employee){

        $savedEmployeeAptoAdministrativeFile = $request->hasFile('employee_apto_administrative_file') 
                                             ? $this->uploadFile($request->employee_apto_administrative_file, 'empleados/'.$employee->id)
                                             : '';
        $savedEployeeBankAccountCertificate =  $request->hasFile('employee_bank_account_certificate') 
                                            ? $this->uploadFile($request->employee_bank_account_certificate, 'empleados/'.$employee->id)
                                            : '';

        $employee->num_dc = $request->employee_doc_number;
        $employee->nombre = $request->employee_name;
        $employee->email = $request->employee_email;
        $employee->salario = $request->salario;
        $employee->direccion = $request->employee_address;
        $employee->fecha_nacimiento = $request->employee_birth_date;
        $employee->telefono = $request->employee_phone;
        $employee->cargo = $request->employee_position;
        $employee->codigo_cargo = $request->employee_position_code;
        $employee->tipo_cargo = $request->employee_position_type;
        $employee->grado = $request->employee_degree;
        $employee->apto_administrativo_numero = $request->employee_apto_administrative_num;
        $employee->apto_administrativo_fecha = $request->employee_apto_adnimistrative_date;
        $employee->apto_administrativo_archivo = $savedEmployeeAptoAdministrativeFile;
        $employee->eps = $request->employee_eps;
        $employee->porc_riesgos = $request->porc_riesgos;
        $employee->fondo_pensiones = $request->employee_pension_fund;
        $employee->tipo_cuenta_bancaria = $request->employee_bank_account_type;
        $employee->numero_cuenta_bancaria = $request->employee_bank_account_num;
        $employee->banco_cuenta_bancaria = $request->employee_bank_account_bank;
        $employee->certificado_cuenta_bancaria = $savedEployeeBankAccountCertificate;
        $employee->save();
        return redirect()->route('nomina.empleados.index');
    }

    public function status(NominaEmpleado $employee){
        $employee->activo = $employee->activo ? False : True;
        $employee->save();

        return response()->json($employee->activo);
    }
}
