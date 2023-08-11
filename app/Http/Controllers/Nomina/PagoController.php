<?php

namespace App\Http\Controllers\Nomina;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagoController extends Controller
{
    public function index(){
        $pagos =  NominaPago::all();
        return view('nomina.pagos.index', compact('pagos'));
    }
}
