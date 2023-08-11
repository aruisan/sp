<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotariaController extends Controller
{
    private $dir_view = 'estadistica.notaria';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
