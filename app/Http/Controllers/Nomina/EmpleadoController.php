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
        $newEmployee->num_dc = $request->doc_number;
        $newEmployee->email = $request->employe_email;
        $newEmployee->direccion = $request->address;
        $newEmployee->fecha_nacimiento = $request->birth_date;
        $newEmployee->telefono = $request->phone;
        $newEmployee->save();
        return redirect()->route('nomina.empleados.index')
                ->with('empleados', NominaEmpleado::all());
    }
}
