<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\ChipContabilidadData;
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
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id','<>', 0)->orderBy('code', 'asc')->get()->filter(function($e){ return $e->level == 4; });
        $pucs_ = PucAlcaldia::where('hijo','0')->where('padre_id', 0)->get();
        $pucs = collect();
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        foreach($pucs_ as $puc):
            if(is_null($puc->contabilidad_data)):
                $m_debito = $puc['m_debito_trimestre'];
                $m_credito = $puc['m_credito_trimestre'];
                $s_final = $puc['naturaleza'] == "DEBITO" ? $puc['v_inicial'] + $m_debito - $m_credito : $puc['v_inicial'] + $m_credito - $m_debito;
                $corriente = $puc['estado_corriente'] ? $s_final : 0;
                $no_corriente = !$puc['estado_corriente'] ? $s_final : 0;
                $data = "<tr>
                            <td class='text-left'>D</td>
                            <td class='text-center'>{$puc['codigo_punto']}</td>
                            <td class='text-right' style='width=200px;'>$".number_format($puc['v_inicial'])."</td>
                            <td class='text-right' style='width=200px;'>{$puc['v_inicial']}</td>
                            <td class='text-right' style='width=200px;'>$".number_format($m_debito)."</td>
                            <td class='text-right' style='width=200px;'>$".number_format($m_credito)."</td>
                            <td class='text-right' style='width=200px;'>{$m_debito}</td>
                            <td class='text-right' style='width=200px;'>{$m_credito}</td>
                            <td class='text-right' style='width=200px;'>$".number_format($s_final)."</td>
                            <td class='text-right' style='width=200px;'>{$s_final}</td>
                            <td class='text-right' style='width=200px;'>$".number_format($corriente)."</td>
                            <td class='text-right' style='width=200px;'>$".number_format($no_corriente)."</td>
                            <td class='text-right' style='width=200px;'>{$corriente}</td>
                            <td class='text-right' style='width=200px;'>{$no_corriente}</td>
                            </tr>";
                
                $data .= $puc['format_hijos_contabilidad'];
    
                $new = new ChipContabilidadData;
                $new->puc_id = $puc->id;
                $new->data = $data;
                $new->save();
            else:
                $data = $puc->contabilidad_data->data;
            endif;
            $pucs->push($data);
        endforeach;

        return view('administrativo.contabilidad.informes.chip_contable',compact('añoActual', 'mesActual', 'diaActual', 'pucs'));
    }


    public function informe_contable_actualizacion(){
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id', 0)->get();
        $chips_contables_data = ChipContabilidadData::all();
        foreach($chips_contables_data as $cc):
            $cc->delete();
        endforeach;
        /*
        */
        //dd($pucs);
        return view('administrativo.contabilidad.informes.chip_contable_actualizacion',compact('pucs'));
    }

    public function informe_contable_ajax(PucAlcaldia $puc){
        //sleep(4);
        //return response()->json($puc);
        if(is_null($puc->contabilidad_data)):
            $m_debito = $puc['m_debito_trimestre'];
            $m_credito = $puc['m_credito_trimestre'];
            $s_final = $puc['naturaleza'] == "DEBITO" ? $puc['v_inicial'] + $m_debito - $m_credito : $puc['v_inicial'] + $m_credito - $m_debito;
            $corriente = $puc['estado_corriente'] ? $s_final : 0;
            $no_corriente = !$puc['estado_corriente'] ? $s_final : 0;
            $data = "<tr>
                        <td class='text-left'>D</td>
                        <td class='text-center'>{$puc['codigo_punto']}</td>
                        <td class='text-right' style='width=200px;'>$".number_format($puc['v_inicial'])."</td>
                        <td class='text-right' style='width=200px;'>{$puc['v_inicial']}</td>
                        <td class='text-right' style='width=200px;'>$".number_format($m_debito)."</td>
                        <td class='text-right' style='width=200px;'>$".number_format($m_credito)."</td>
                        <td class='text-right' style='width=200px;'>{$m_debito}</td>
                        <td class='text-right' style='width=200px;'>{$m_credito}</td>
                        <td class='text-right' style='width=200px;'>$".number_format($s_final)."</td>
                        <td class='text-right' style='width=200px;'>{$s_final}</td>
                        <td class='text-right' style='width=200px;'>$".number_format($corriente)."</td>
                        <td class='text-right' style='width=200px;'>$".number_format($no_corriente)."</td>
                        <td class='text-right' style='width=200px;'>{$corriente}</td>
                        <td class='text-right' style='width=200px;'>{$no_corriente}</td>
                        </tr>";
            
            $data .= $puc['format_hijos_contabilidad'];

            $new = new ChipContabilidadData;
            $new->puc_id = $puc->id;
            $new->data = $data;
            $new->save();
        else:
            $data = $puc->contabilidad_data->data;
        endif;
        
        return response()->json($data);
    }

    public function informe_contable_puc_ver(PucAlcaldia $puc) {
        return view('administrativo.contabilidad.informes.chip_contable_valores_puc',compact('puc'));
    }

    public function informe_contable_pucs() {
        $pucs = PucAlcaldia::get();
        return view('administrativo.contabilidad.informes.chip_contable_valores',compact('pucs'));
    }
}
