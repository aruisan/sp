<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservaVueloController extends Controller
{
    private $dir_view = 'estadistica.vuelos';

    public function index(){
        return view("{$this->dir_view}.index");
    }

    public function store(Request $request){
        $vuelo = $request->n_vuelo;
        $airline = $request->airline;
        $date = $request->date;
        return view("{$this->dir_view}.store", compact('vuelo', 'airline', 'date'));
    }
}
