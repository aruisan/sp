<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;

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
        $newEmployee->edad = $request->employee_age;
        $newEmployee->email = $request->employee_email;
        $newEmployee->direccion = $request->employee_address;
        $newEmployee->fecha_nacimiento = $request->employee_birth_date;
        $newEmployee->telefono = $request->employee_phone;
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
        $employee->edad = $request->employee_age;
        $employee->email = $request->employee_email;
        $employee->direccion = $request->employee_address;
        $employee->fecha_nacimiento = $request->employee_birth_date;
        $employee->telefono = $request->employee_phone;
        $employee->save();
        return redirect()->route('nomina.empleados.index')
                ->with('empleados', NominaEmpleado::all());
    }
}
