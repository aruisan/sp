<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HospitalController extends Controller
{
    private $dir_view = 'estadistica.hospital';

    public function index(){
        return view("{$this->dir_view}.index");
    }
}
