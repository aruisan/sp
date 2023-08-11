<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\CompContMov;
use App\ChipContabilidadData;
use \Carbon\Carbon;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;

class PucAlcaldia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "puc_alcaldia";

    public function contabilidad_data(){
        return $this->hasOne(ChipContabilidadData::class, 'puc_id');
    }

    public function conciliaciones() {
        return $this->hasMany(ConciliacionBancaria::class, 'puc_id');
    }

    public function hijos(){
        return $this->hasMany(PucAlcaldia::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(PucAlcaldia::class, 'padre_id');
    }

    //odenes de pago
    public function orden_pagos() {
        return $this->hasMany(OrdenPagosPuc::class, 'rubros_puc_id');
    }

    //pagos
    public function pagos_bank(){
        return $this->hasMany(PagoBanks::class, 'rubros_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }

    //comprobantes de ingreso son los de banco
    public function getComprobantesAttribute(){
        return ComprobanteIngresosMov::where('cuenta_banco', $this->id)->orwhere('cuenta_puc_id', $this->id)->whereYear('created_at', Carbon::today()->format('Y'))->get();
    }

    //retefuente
    public function retefuente_movimientos(){
        return $this->hasMany(CompContMov::class, 'cuenta_puc_id')->where('comp_cont_id', '>', 2);
    }

/*
    public function comprobantes_contables_movimientos(){
        return $this->hasMany(CompContMov::class, 'cuenta_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }
*/

    public function getLevelAttribute(){
        $count_string = strlen($this->code);
        if($count_string == 4){
            return 3;
        }elseif($count_string == 6){
            return 4;
        }elseif($count_string == 10){
            return 5;
        }else{
            return $count_string;
        }

    }

    public function getCodigoPuntoAttribute(){
        $secuencia = [1,2,4,6];
        $numero = '';
        $caracteres_array = $arr1 = str_split($this->code);
        foreach($caracteres_array as $index => $letra):
            if(in_array($index, $secuencia)){
                $numero .= ".";
            }
            $numero .= $letra;
        endforeach;

        return $numero;
    }

    public function getDebCredTrimestreAttribute(){
        $age =  Carbon::today()->format('Y');
        $totCred = 0;
        $totDeb = 0;
        $inicio = "2023-01-01";
        $final = "2023-03-31";
        if($this->pagos_bank->count() > 0):
            $totCred += $this->pagos_bank->count() > 0 ? $this->pagos_bank->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->filter(function($p){ return $p->pago->estado == 1;})->sum('valor') : 0;
        endif;

        if($this->comprobantes->count() > 0):
            $totDeb += $this->comprobantes->count() > 0 ? $this->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito') : 0;
            $totCred += $this->comprobantes->count() > 0 ? $this->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito') : 0;
        endif;

        if($this->orden_pagos->count() > 0):
            $totDeb += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_debito') : 0;
            $totCred += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_credito') : 0;
        endif;

        if($this->retefuente_movimientos->count() > 0):
            $totDeb += $this->retefuente_movimientos->count() > 0 ? $this->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito') : 0;
            $totCred += $this->retefuente_movimientos->count() > 0 ? $this->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito') : 0;
        endif;
        
        return ['debito' => $totDeb, 'credito' => $totCred];
    }

    public function getDebitoTrimestreAttribute(){
        return $this->deb_cred_trimestre['debito'];
    }

    public function getCreditoTrimestreAttribute(){
        return $this->deb_cred_trimestre['credito'];
    }

    public function getMDebitoTrimestreAttribute(){
        $suma = $this->debito_trimestre;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_debito_trimestre');
        endif;
            
        return $suma;
    }

    public function getMCreditoTrimestreAttribute(){
        $suma = $this->credito_trimestre;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_credito_trimestre');
        endif;
            
        return $suma;
    }

///////////////////////////////////////////////////////////////////////////
    public function getDebCredAttribute(){
        $age =  Carbon::today()->format('Y');
        $totCredAll = 0;
        $totCred = 0;
        $totDeb = 0;
        $inicio = "2023-01-01";
        $final = "2023-03-31";
        if($this->pagos_bank->count() > 0):
            $totCredAll = $this->pagos_bank->sum('valor');
            $totCred += $this->pagos_bank->filter(function($p){ return $p->pago->estado == 1;})->sum('valor');
        endif;

        if($this->comprobantes->count() > 0):
            $totDeb += $this->comprobantes->sum('debito');
            $totCred += $this->comprobantes->sum('credito');
        endif;

        if($this->orden_pagos->count() > 0):
            $totDeb += $this->orden_pagos->sum('valor_debito');
            $totCred += $this->orden_pagos->sum('valor_credito');
        endif;
        
        return ['debito' => $totDeb, 'credito' => $totCred];
    }

    public function getDebitoAttribute(){
        return $this->deb_cred['debito'];
    }

    public function getCreditoAttribute(){
        return $this->deb_cred['credito'];
    }

    public function getVHijosAttribute(){
        return $this->hijos->count() == 0 ? $this->saldo_inicial : $this->hijos->sum('saldo_inicial'); 
    }

    public function getMDebitoAttribute(){
        $suma = $this->debito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_debito');
        endif;
            
        return $suma;
    }

    public function getMCreditoAttribute(){
        $suma = $this->credito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_credito');
        endif;
            
        return $suma;
    }

    /*
    public function getSDebitoAttribute(){
        return $this->debito + $this->m_debito - $this->m_credito;
    }

    public function getSCreditoAttribute(){
        return $this->credito + $this->m_credito - $this->m_debito;
    }
    */

    public function getVInicialAttribute(){
        $suma = $this->saldo_inicial;
        if($this->hijos->count() > 0):
           // $suma += $this->hijos->sum('valor_inicial');
            $suma = $this->hijos->sum('v_inicial');
        endif;
            
        return $suma;
    }

    public function getFormatHijosInicialAttribute(){
        $grupo_puc = "";
        foreach($this->hijos as $item):
            $grupo_puc .= $this->format_puc($item, 'inicial');
            $grupo_puc .= $item->format_hijos_inicial;
        endforeach;
            
        return $grupo_puc;
    }

    public function getFormatHijosPruebaAttribute(){
        $grupo_puc = "";
        foreach($this->hijos as $item):
            $grupo_puc .= $this->format_puc($item, 'prueba');
            $grupo_puc .= $item->format_hijos_prueba;
        endforeach;
            
        return $grupo_puc;
    }

    public function getFormatHijosContabilidadAttribute(){

        $grupo_puc = "";
        foreach($this->hijos as $item):
            if($item->level <= 4):
                $grupo_puc .= $this->format_puc_contabilidad($item);
                $grupo_puc .= $item->format_hijos_contabilidad;
            endif;
        endforeach;
            
        return $grupo_puc;
    }


    public function format_puc_contabilidad($puc){
        $m_debito = $puc['m_debito_trimestre'];
        $m_credito = $puc['m_credito_trimestre'];
        $s_final = $puc['naturaleza'] == "DEBITO" ? $puc['v_inicial'] + $m_debito - $m_credito : $puc['v_inicial'] + $m_credito - $m_debito;
        $corriente = $puc['estado_corriente'] ? $s_final : 0;
        $no_corriente = !$puc['estado_corriente'] ? $s_final : 0;
        return "<tr>
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
    }


    public function format_puc($puc, $tipo){
        $debito = $puc['naturaleza'] == 'DEBITO' ? $puc['v_inicial']: 0;
        $credito = $puc['naturaleza'] == 'CREDITO' ? $puc['v_inicial']: 0;
       // $padre = is_null($puc['padre']) ? 'no tiene' : $puc['padre']['code'];
        //$hijos = count($puc['hijos']) == 0  ? 'no tiene' : $puc->hijos->pluck('id');
        
        $item =  "<tr>
                    <td class='text-left'>{$puc['code']}</td>
                    <td class='text-rigth'>{$puc['concepto']}</td>
                    <td class='text-right'>$".number_format($debito)."</td>
                    <td class='text-right'>$".number_format($credito)."</td>
                    <td class='text-right'>{$debito}</td>
                    <td class='text-right'>{$credito}</td>";
                    

        if($tipo == 'prueba'){
            $m_debito = $puc['m_debito'];
            $m_credito = $puc['m_credito'];
            $s_debito = $puc-> naturaleza == "DEBITO" ? $debito + $m_debito - $m_credito : 0;
            $s_credito = $puc-> naturaleza == "CREDITO" ?  $credito + $m_credito - $m_debito : 0;

            $item.= "
            <td class='text-right'>$".number_format($m_debito)."</td>
            <td class='text-right'>$".number_format($m_credito)."</td>
            <td class='text-right'>{$m_debito}</td>
            <td class='text-right'>{$m_credito}</td>
            <td class='text-right'>$".number_format($s_debito)."</td>
            <td class='text-right'>$".number_format($s_credito)."</td>
            <td class='text-right'>{$s_debito}</td>
            <td class='text-right'>{$s_credito}</td>
            ";
        }

        $item .= "</tr>";


        return $item;
                    /*
                    <td>{$padre}</td>
                    <td>{$puc['naturaleza']}</td>
                    <td>{$puc['saldo_inicial']}</td>
                    <td>{$hijos}</td>
                    */
    }


    public function validateBeforeMonths($age, $rubroPUC){
        $today = Carbon::today();//2023-04-11
        $lastDate = Carbon::parse($age." 23:59:59");//2023-2-1 23:59:59
        $fechaIni = Carbon::parse($today->year."-01-01");//2023-01-01
        //$fechaFin = Carbon::parse($today->year."-3-31");
        $fechaFin = $lastDate->subDays(1);//2023-31-1 23:59:59
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $mes = $fechaFin->month.'-'.$today->year;//1-2023

        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        //select * from pago_banks where rubros_puc_id == 28 and created_at >= 2023-01-01 and created_at <= 2023-31-1
        //trae todos los pagos de bancos hechos entre una fecha inicial que seria el primero de enero del año actual a la fecha final
        // que seria el dia que hagan el descargue de el informe
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    $total = $total - $pagoBank->valor;
                    $totDeb = $totDeb + 0;
                    $totCred = $totCred + $pagoBank->valor;
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
        //aca coge todos los movimientos de las cuenta de bancos poara generar los libros y al igual que pahgo de bancos tiene una fecha final y una fecha inicial
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    if ($compCont->cuenta_banco == $rubroPUC->id){
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    } else{
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    }
                }
            }
        }

        return $total;
    }

}
