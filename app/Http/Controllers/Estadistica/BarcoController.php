<?php

namespace App\Http\Controllers\Estadistica;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BarcoController extends Controller
{
    private $dir_view = 'estadistica.barcos';

    public function index(){
        return view("{$this->dir_view}.index");
    }

    public function store(Request $request){
        $travel = $request->travel;
        $boatName = $request->boatName;
        $transportationCompany = $request->transportationCompany;
        $date = $request->date;
        return view("{$this->dir_view}.store", compact('travel', 'boatName', 'transportationCompany', 'date'));
    }
}
