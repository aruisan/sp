<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CapitaniaPuertoController extends Controller
{
    private $dir_view = 'estadistica.capitania_puerto';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
