<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Data\InformeContable as InformeContableMensual;
use App\Model\Data\InformeContableData as InformeContableMensualData;
use Session, PDF;
use Carbon\Carbon;
use App\Helpers\FechaHelper;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class GeneralController extends Controller
{
    public function pdf($age, $elemento, $tipo, $vista){

        if($tipo == 'mensual'){
            $mes_i = $elemento; 
            $mes_f = $elemento;
            $icm = ["${age}-{$elemento}-01"];
        }else{
            $number = $tipo == 'trimestre' ? 3 : 6;
            $range = $tipo == 'anual' 
                    ? range(1, count(FechaHelper::meses($age))) 
                    : range(($elemento * $number) +1, ($elemento * $number) +$number);
            $icm = [];
            foreach($range as $m):
                array_push($icm, "${age}-{$m}-01");
            endforeach;

            $mes_i = $range[0]; 
            $mes_f = end($range);
        }

        //dd(InformeContableMensual::whereIn('fecha', $icm)->get());

        $informes_id = InformeContableMensual::whereIn('fecha', $icm)->pluck('id')->toArray();

        $pucs_active = [1,2,3,4,5,3110010101, 3110020101];
        $pa_ids = PucAlcaldia::whereIn('code', $pucs_active)->pluck('id')->toArray();
        $activo = collect();
        $pasivo = collect();
        $patrimonio = collect();
        $ingresos = collect();
        $gastos = collect();
        $activo_h = collect();
        $pasivo_h = collect();
        $patrimonio_h = collect();
        $ingresos_h = collect();
        $gastos_h = collect();
        $puc_opcional_1 = collect();
        $puc_opcional_2 = collect(); 

        foreach($informes_id as $k => $inf_id):
            $pucs_data = InformeContableMensualData::where('informe_contable_mensual_id', $inf_id)->whereIn('puc_alcaldia_id', $pa_ids)->get();
            foreach($pucs_data as $pd){
                if($pd->puc_alcaldia->code == 1):
                    $activo->push($pd);
                    foreach($pd->hijos as $h): 
                        $activo_h->push($h);
                    endforeach;
                elseif($pd->puc_alcaldia->code == 2):
                    $pasivo->push($pd);
                    foreach($pd->hijos as $h): 
                        $pasivo_h->push($h);
                    endforeach;
                elseif($pd->puc_alcaldia->code == 3):
                    $patrimonio->push($pd);
                    foreach($pd->hijos as $h): 
                        $patrimonio_h->push($h);
                    endforeach;
                elseif($pd->puc_alcaldia->code == 4):
                    $ingresos->push($pd);
                    foreach($pd->hijos as $h): 
                        $ingresos_h->push($h);
                    endforeach;
                elseif($pd->puc_alcaldia->code == 5):
                    $gastos->push($pd);
                    foreach($pd->hijos as $h): 
                        $gastos_h->push($h);
                    endforeach;
                elseif($pd->puc_alcaldia->code == 3110010101):
                    $puc_opcional_1->push($pd);
                elseif($pd->puc_alcaldia->code == 3110020101):
                    $puc_opcional_2->push($pd);
                endif;
            }
        endforeach;

        $m_inicial = FechaHelper::meses($age)[$mes_i-1];
        $d_actual = date('d');
        $d_final = date("t", strtotime("2023-".$mes_f."-01"));
        if($age == date('Y') && date('m') == $mes_f && $d_actual < $d_final ){
            $d_final = $d_actual;
        }
        $m_final = FechaHelper::meses($age)[$mes_f-1];

        //dd([$mes_i, $mes_f, $m_inicial, $d_final, $m_final, "2023-".$mes_f."-01", $d_actual, $elemento]);

        $titulo = "Balance General Periodo 1 de {$m_inicial} al {$d_final} de {$m_final} del año {$age}";


        $iguales_ingresos_gastos = $ingresos->sum('s_final') - $gastos->sum('s_final');

        $puc_opcional = $iguales_ingresos_gastos >= 0 
                        ? $puc_opcional_1
                        : $puc_opcional_2;

        if($vista == "vista"):
            return view('administrativo.contabilidad.balances.general', compact('titulo', 'age', 'elemento', 'tipo', 'activo', 'pasivo', 'patrimonio', 'ingresos', 'gastos', 'puc_opcional', 'activo_h', 'pasivo_h', 'patrimonio_h', 'ingresos_h', 'gastos_h', 'puc_opcional', 'iguales_ingresos_gastos'));   
        elseif($vista == "mostrar-pdf"):
            return view('administrativo.contabilidad.balances.mostrar-general', compact('age', 'elemento', 'tipo'));
        else:
            $pdf = PDF::loadView('administrativo.contabilidad.balances.general-pdf', compact('titulo', 'age', 'elemento', 'tipo', 'activo', 'pasivo', 'patrimonio', 'ingresos', 'gastos', 'puc_opcional', 'activo_h', 'pasivo_h', 'patrimonio_h', 'ingresos_h', 'gastos_h', 'puc_opcional', 'iguales_ingresos_gastos'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        endif;
    }
}
