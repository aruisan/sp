<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmpresaEnergiaController extends Controller
{
    private $dir_view = 'estadistica.empresa_energia';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
