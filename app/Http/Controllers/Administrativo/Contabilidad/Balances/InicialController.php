<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Carbon\Carbon;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class InicialController extends Controller
{
    public function index(){
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->get();
        //dd($pucs);
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        return view('administrativo.contabilidad.balances.inicial',compact('añoActual', 'mesActual', 'diaActual', 'pucs'));

        //ini_set('max_execution_time',600);
       // ini_set('memory_limit',"4096");

        //return Excel::download(new ReporteBalanceInicialExcExport($añoActual, $pucs, $mesActual, $diaActual),
        //    'Balance Inicial'.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');
    }
}
