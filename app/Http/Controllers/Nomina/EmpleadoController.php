<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;

class EmpleadoController extends Controller
{
    public function index(){
        $empleados =  NominaEmpleado::all();
        return view('nomina.empleado.index', compact('empleados'));
    }
}
