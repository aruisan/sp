<?php

namespace App\Http\Controllers\Administrativo\Contabilidad\Balances;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\ChipContabilidadData;
use App\ChipContabilidadValorInicial;
use Illuminate\Http\Request;
use App\Model\Administrativo\Contabilidad\BalanceData;
use App\Model\Administrativo\Contabilidad\Balances;
use Carbon\Carbon;
use Session;

class ChipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function informe_contable($age, $trimestre){
        Session::put(auth()->id().'-mes-informe-chip-trimestre', $trimestre);
        Session::put(auth()->id().'-mes-informe-chip-age', $age);
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id','<>', 0)->orderBy('code', 'asc')->get()->filter(function($e){ return $e->level == 4; });
        $pucs_ = PucAlcaldia::where('hijo','0')->where('padre_id', 0)->get();
        $pucs = collect();
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;
        
        
        foreach($pucs_ as $puc):
            $contabilidad_data = ChipContabilidadData::where('puc_id', $puc->id)->where('age', $age)->where('trimestre', $trimestre)->first();
            if(is_null($contabilidad_data)):
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
                $new->trimestre = $trimestre;
                $new->age = $age;
                $new->puc_id = $puc->id;
                $new->data = $data;
                $new->save();
            else:
                $data = $contabilidad_data->data;
            endif;
            $pucs->push($data);
        endforeach;

        return view('administrativo.contabilidad.informes.chip_contable',compact('añoActual', 'mesActual', 'diaActual', 'pucs', 'age', 'trimestre', 'meses'));
    }


    public function informe_contable_actualizacion($age, $trimestre){
        Session::put(auth()->id().'-mes-informe-chip-trimestre', $trimestre);
        Session::put(auth()->id().'-mes-informe-chip-age', $age);
        $pucs = PucAlcaldia::where('hijo','0')->where('padre_id', 0)->get();
        $chips_contables_data = ChipContabilidadData::where('age', $age)->where('trimestre', $trimestre)->get();
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
            $new->trimestre = Session::get(auth()->id().'-mes-informe-chip-trimestre');
            $new->age = Session::get(auth()->id().'-mes-informe-chip-age');
            $new->puc_id = $puc->id;
            $new->data = $data;
            $new->save();
        else:
            $data = $puc->contabilidad_data->data;
        endif;
        
        return response()->json($data);
    }

    public function informe_contable_puc_ver(PucAlcaldia $puc) {
        //dd($puc->comprobantes_mensual);
        return view('administrativo.contabilidad.informes.chip_contable_valores_puc',compact('puc'));
    }

    public function informe_contable_pucs() {
        $pucs = PucAlcaldia::get();
        return view('administrativo.contabilidad.informes.chip_contable_valores',compact('pucs'));
    }


    public function pre_informe($age, $trimestre){
        
        Session::put(auth()->id().'-mes-informe-chip-trimestre', $trimestre);
        Session::put(auth()->id().'-mes-informe-chip-age', $age);

        $informe = ChipContabilidadData::where('age', $age)->where('trimestre', $trimestre)->first();
        $pucs = PucAlcaldia::get();
        if(is_null($informe)):
            $informe = ChipContabilidadData::create(['age' => $age, 'trimestre' => $trimestre]);
        endif;

        if($informe->finalizar){
            return redirect()->route('chip-informe', $informe->id);
        }
        //Session::flash('error','Actualmente no existe un PUC para poder ver los informes. Se recomienda crearlo.');
        return view('administrativo.contabilidad.informes.chip_pre',compact('informe', 'pucs'));
    }


    public function generar_informe(ChipContabilidadData $informe, PucAlcaldia $puc){
        $trimestres = [1,4,7,10];
        $trimestre = Session::get(auth()->id().'-mes-informe-chip-trimestre');
        $age = Session::get(auth()->id().'-mes-informe-chip-age');
        $mes_inicio = $trimestres[$trimestre];
        $mes_final = $mes_inicio+2;
        $trimestre_anterior = $trimestre == 0 ? 3 : $trimestre-1;
        $age_anterior = $trimestre == 0 ? $age-1 : $age;

        $pucs_count = PucAlcaldia::count();
        $puc_mensual = ChipContabilidadValorInicial::where('chip_contabilidad_data_id', $informe->id)->where('puc_alcaldia_id', $puc->id)->first();


        if($trimestre == 0 && $age == 2023){
            $v_inicial = $puc->v_inicial;
        }else{
            $informe_anterior = ChipContabilidadData::where('age', $age_anterior)->where('trimestre', $trimestre_anterior)->first();
            //dd([$informe_anterior, $fecha_anterior]);
            if(is_null($informe_anterior)){
                $informe->delete();
                return "no se ha generado el mes anterior";
            }
            $puc_informe_puc_anterior = ChipContabilidadValorInicial::where('chip_contabilidad_data_id', $informe_anterior->id)->where('puc_alcaldia_id', $puc->id)->first();
            $v_inicial = $puc_informe_puc_anterior->s_final;
        }
        $m_credito = 0;
        $m_debito = 0;

        foreach(range($mes_inicio, $mes_final) as $mes_): 
            $balance = Balances::where('mes', $mes_)->where('año', $age)->where('tipo', 'MENSUAL')->first();
            $puc_balances = BalanceData::where('cuenta_puc_id', $puc->id)->where('balance_id', $balance->id)->get();
            //$m_credito = $puc_balances->sum('credito') + array_sum(array_column($puc->almacen_salidas_credito(2023,$mes),'total')) +array_sum(array_column($puc->almacen_entradas_credito(2023,$mes),'total'));
            //$m_debito = $puc_balances->sum('debito') + array_sum(array_column($puc->almacen_salidas_debito(2023,$mes), 'total')) +array_sum(array_column($puc->almacen_entradas_debito(2023,$mes), 'total'));
            $m_credito += $puc_balances->sum('credito');
            $m_debito += $puc_balances->sum('debito');

            $a_credito = $puc->almacen_salidas_credito($age,$mes_)->sum('total') +$puc->almacen_entradas_credito($age,$mes_)->sum('total');
            $a_debito = $puc->almacen_salidas_debito($age,$mes_)->sum('total') +$puc->almacen_entradas_debito($age,$mes_)->sum('total');

        endforeach;
        
        $s_final = $puc->naturaleza == "DEBITO" ? $v_inicial + $m_debito - $m_credito : $v_inicial + $m_credito - $m_debito;
        $corriente = $puc->estado_corriente ? $s_final : 0;
        $no_corriente = !$puc->estado_corriente ? $s_final : 0;
        
        
        if(is_null($puc_mensual)):
            $puc_mensual = new ChipContabilidadValorInicial;
        endif;

        $puc_mensual->chip_contabilidad_data_id = $informe->id;
        $puc_mensual->puc_alcaldia_id = $puc->id;
        $puc_mensual->m_credito = $m_credito;
        $puc_mensual->m_debito = $m_debito;
        $puc_mensual->s_final = $s_final;
        $puc_mensual->corriente = $corriente;
        $puc_mensual->no_corriente = $no_corriente; 
        $puc_mensual->valor_inicial = $v_inicial; 
        $puc_mensual->a_debito = $a_debito; 
        $puc_mensual->a_credito = $a_credito; 
        $puc_mensual->save();

        if($puc_mensual->puc_alcaldia->hijos->count() > 0){
            $pucs_mensuales = ChipContabilidadValorInicial::where('chip_contabilidad_data_id', $informe->id)->whereIn('puc_alcaldia_id', $puc_mensual->puc_alcaldia->hijos->pluck('id')->toArray())->get();
            foreach($pucs_mensuales as $hijo):
                $hijo->padre_id = $puc_mensual->id;
                $hijo->save();
            endforeach;
        }

        $puc_mensual->a_credito = $puc_mensual->a_credito + $puc_mensual->hijos->sum('a_credito');
        $puc_mensual->a_debito = $puc_mensual->a_debito + $puc_mensual->hijos->sum('a_debito');  
        $puc_mensual->save();
        
        return response()->json(TRUE);
        //return response()->json($pucs_count == $informe->datos->count() ? TRUE : FALSE);
    }


    public function generar_informe_relaciones(ChipContabilidadData $informe){
        $pucs = PucAlcaldia::get();

        foreach($informe->datos->filter(function($d){ return is_null($d->padre_id); }) as $puc_mensual):
            if($puc_mensual->puc_alcaldia->hijos->count()):
                $hijos = $informe->datos->filter(function($e)use($puc_mensual){ return in_array($e->puc_alcaldia_id, $puc_mensual->puc_alcaldia->hijos->pluck('id')->toArray()); });
                foreach($hijos as $puc_data):
                    $puc_data->update(['padre_id' => $puc_mensual->id]); 
                endforeach;
            endif;
        endforeach;

        $informe->finalizar = TRUE;
        $informe->save();
        return response()->json(TRUE);
    }

    public function informe(ChipContabilidadData $informe){
        $meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio'];
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->take(3)->get();
        $pucs = $informe->datos->filter(function($p){ return is_null($p->padre); })->sortBy('puc_alcaldia.code');

        return view('administrativo.contabilidad.informes.chip',compact('informe', 'pucs'));
    }

    public function reload_informe(ChipContabilidadData $informe){
        $informe->finalizar = FALSE;
        $informe->save();
        $pucs = PucAlcaldia::get();
        return view('administrativo.contabilidad.informes.chip_pre',compact('informe', 'pucs'));
    }
}
