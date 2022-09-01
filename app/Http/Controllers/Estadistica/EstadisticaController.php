<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class EstadisticaController extends Controller
{
    private $dir_view = 'estadistica';

    public function index(){
        return view("{$this->dir_view}.index");//+a.index
    }
}
