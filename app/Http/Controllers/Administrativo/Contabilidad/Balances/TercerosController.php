<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Carbon\Carbon;

class TercerosController extends Controller
{
    public function index(){
        return view('administrativo.contabilidad.balances.terceros');
    }
}
