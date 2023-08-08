<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InformeContableMensual;
use App\InformeContableMensualData;
use Session, PDF;
use Carbon\Carbon;
use App\Helpers\FechaHelper;

class GeneralController extends Controller
{
    public function pdf($age, $mes, $tipo){
        //dd(file_get_contents("/var/www/s_p/public/assets/bootstrap/css/bootstrap.min.css") );
        $fecha = "2023-{$mes}-01";
        $informe = InformeContableMensual::where('fecha', $fecha)->first();
        $pucs = InformeContableMensualData::where('informe_contable_mensual_id', $informe->id)->get();
        $activo = $pucs->filter(function($p){ return $p->puc_alcaldia->code == 1;})->first(); 
        $pasivo = $pucs->filter(function($p){ return $p->puc_alcaldia->code == 2;})->first(); 
        $patrimonio = $pucs->filter(function($p){ return $p->puc_alcaldia->code == 3;})->first(); 

        $meses = FechaHelper::meses(2023);
        if($tipo == "vista"):
            return view('administrativo.contabilidad.balances.general', compact('activo', 'pasivo', 'patrimonio', 'age', 'mes', 'meses'));   
        else:
            //dd($pucs->filter(function($p){ return $p->puc_alcaldia->code <= 3 && $p->puc_alcaldia->padre_id == 0; })->values()[2]->puc_alcaldia);
            $pdf = PDF::loadView('administrativo.contabilidad.balances.general-pdf', compact('activo', 'pasivo', 'patrimonio'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        endif;
    }
}
