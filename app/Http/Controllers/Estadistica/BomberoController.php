<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BomberoController extends Controller
{
    private $dir_view = 'estadistica.bomberos';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
