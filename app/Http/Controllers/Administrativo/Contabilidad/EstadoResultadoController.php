<?php

namespace App\Http\Controllers\Administrativo\Contabilidad;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Data\InformeContable as InformeContableMensual;
use App\Model\Data\InformeContableData as InformeContableMensualData;
use Session, PDF;
use Carbon\Carbon;
use App\Helpers\FechaHelper;

class EstadoResultadoController extends Controller
{
    public function vista($age, $mes, $tipo){
        //dd(file_get_contents("/var/www/s_p/public/assets/bootstrap/css/bootstrap.min.css") );
        $fecha = "2023-{$mes}-01";
        $informe = InformeContableMensual::where('fecha', $fecha)->first();
        $pucs = InformeContableMensualData::where('informe_contable_mensual_id', $informe->id)->get();
        $ingresos = $pucs->filter(function($p){ return $p->puc_alcaldia->code == 4;})->first(); 
        $gastos = $pucs->filter(function($p){ return $p->puc_alcaldia->code == 5;})->first(); 

        $meses = FechaHelper::meses(2023);
        if($tipo == "vista"):
            return view('administrativo.contabilidad.estado-resultados', compact('ingresos', 'gastos', 'age', 'mes', 'meses'));   
        elseif($tipo == "mostrar_pdf"):
            return view('administrativo.contabilidad.mostrar-estado-resultados', compact('age', 'mes', 'meses'));
        else:
            //dd($pucs->filter(function($p){ return $p->puc_alcaldia->code <= 3 && $p->puc_alcaldia->padre_id == 0; })->values()[2]->puc_alcaldia);
            $pdf = PDF::loadView('administrativo.contabilidad.estado-resultados-pdf', compact('ingresos', 'gastos', 'age', 'mes', 'meses'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        endif;
    }
}
