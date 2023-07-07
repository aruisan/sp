<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Carbon\Carbon;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class InicialController extends Controller
{
    public function index($mes){

        Session::put('mes-informe-inicial', $mes);
        //dd();
        $meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio'];
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->get();
        //$pucs = PucAlcaldia::get();
        //dd($pucs->map(function($e){ return $e->orden_pagos_mensual->count();}));
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        //dd($pucs);

        return view('administrativo.contabilidad.balances.inicial',compact('añoActual', 'mesActual', 'diaActual', 'pucs', 'meses'));

        //ini_set('max_execution_time',600);
       // ini_set('memory_limit',"4096");

        //return Excel::download(new ReporteBalanceInicialExcExport($añoActual, $pucs, $mesActual, $diaActual),
        //    'Balance Inicial'.$añoActual.'-'.$mesActual.'-'.$diaActual.'.xlsx');
    }
}
