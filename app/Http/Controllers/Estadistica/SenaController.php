<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SenaController extends Controller
{
    private $dir_view = 'estadistica.sena';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
