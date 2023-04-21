<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class ChipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function informe_contable(){
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id','<>', 0)->orderBy('code', 'asc')->get()->filter(function($e){ return $e->level == 4; });
        //dd($pucs);
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        return view('administrativo.contabilidad.informes.chip_contable',compact('añoActual', 'mesActual', 'diaActual', 'pucs'));
    }
}
