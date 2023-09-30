<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ImportEstadisticaPresupuesto;

class ProyectoController extends Controller
{
    private $dir_view = 'estadistica.proyectos';

    public function index(){
        //dd('aca');
        $data = ImportEstadisticaPresupuesto::find(2);
        return view("{$this->dir_view}.index", compact('data'));
    }

    
}
