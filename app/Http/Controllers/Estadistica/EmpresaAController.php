<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmpresaAController extends Controller
{
    private $dir_view = 'estadistica.empresa_aaa';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
