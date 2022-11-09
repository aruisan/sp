<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LudotecaController extends Controller
{
    private $dir_view = 'estadistica.ludoteca';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
