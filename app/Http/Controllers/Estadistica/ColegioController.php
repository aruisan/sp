<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ColegioController extends Controller
{
    private $dir_view = 'estadistica.colegios';

    public function index(){
        return view("{$this->dir_view}.index", compact('data'));
    }
}
