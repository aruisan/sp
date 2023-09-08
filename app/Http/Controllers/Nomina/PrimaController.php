<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NominaEmpleado;
use App\Nomina;
use App\NominaEmpleadoNomina;
use Session, PDF;

class primaController extends Controller
{
    private $view = "nomina.prima";

    public function listar($tipo){
        $nominas = Nomina::where('tipo', $tipo)->get()->filter(function($e){
           return $e->tiene_prima;
        });

        return view("{$this->view}.index_{$tipo}", compact('nominas', 'tipo'));
    }

    public function show(Nomina $nomina){
        $movimientos =  $nomina->empleados_nominas->map(function($e){
            return [
                'nombre' => $e->empleado->nombre,
                'num_dc' => $e->empleado->num_dc,
                'cargo' => $e->empleado->cargo,
                'sueldo_basico' => $e->sueldo,
                'prima' => $e->prima,
            ];
        });
        return view("{$this->view}.show", compact('nomina', 'movimientos'));
    }
}
