<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\CompContMov;
use \Carbon\Carbon;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;

class PucAlcaldia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "puc_alcaldia";

    public function conciliaciones() {
        return $this->hasMany(ConciliacionBancaria::class, 'puc_id');
    }

    public function hijos(){
        return $this->hasMany(PucAlcaldia::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(PucAlcaldia::class, 'padre_id');
    }

    public function orden_pagos() {
        return $this->hasMany(OrdenPagosPuc::class, 'rubros_puc_id');
    }

    public function pagos_bank(){
        return $this->hasMany(PagoBanks::class, 'rubros_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }

    public function getComprobantesAttribute(){
        return ComprobanteIngresosMov::where('cuenta_banco', $this->id)->orwhere('cuenta_puc_id', $this->id)->whereYear('created_at', Carbon::today()->format('Y'))->get();
    }
/*
    public function comprobantes_contables_movimientos(){
        return $this->hasMany(CompContMov::class, 'cuenta_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }
*/
    public function getDebCredAttribute(){
        $age =  Carbon::today()->format('Y');
        $totCredAll = 0;
        $totCred = 0;
        $totDeb = 0;
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

    public function getVDebitoAttribute(){
        $suma = $this->debito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('v_debito');
        endif;
            
        return $suma;
    }

    public function getVCreditoAttribute(){
        $suma = $this->credito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('v_credito');
        endif;
            
        return $suma;
    }

    public function getVInicialAttribute(){
        $suma = $this->saldo_inicial;
        if($this->hijos->count() > 0):
           // $suma += $this->hijos->sum('valor_inicial');
            $suma = $this->hijos->sum('v_inicial');
        endif;
            
        return $suma;
    }

    public function getFormatHijosAttribute(){
        $grupo_puc = "";
        foreach($this->hijos as $item):
            $grupo_puc .= $this->format_puc($item);
            $grupo_puc .= $item->format_hijos;
        endforeach;
            
        return $grupo_puc;
    }


    public function format_puc($puc){
        $debito = $puc['naturaleza'] == 'DEBITO' ? $puc['v_inicial']: 0;
        $credito = $puc['naturaleza'] == 'CREDITO' ? $puc['v_inicial']: 0;
        $padre = is_null($puc['padre']) ? 'no tiene' : $puc['padre']['code'];
        $hijos = count($puc['hijos']) == 0  ? 'no tiene' : $puc->hijos->pluck('id');
        return "<tr>
                    <td class='text-left'>{$puc['code']}</td>
                    <td class='text-rigth'>{$puc['concepto']}</td>
                    <td class='text-right'>$".number_format($debito)."</td>
                    <td class='text-right'>$".number_format($credito)."</td>
                    <td class='text-right'>{$debito}</td>
                    <td class='text-right'>{$credito}</td>
                    <td class='text-right'>{$puc['v_debito']}</td>
                    <td class='text-right'>{$puc['v_credito']}</td>
                    </tr>";
                    /*
                    <td>{$padre}</td>
                    <td>{$puc['naturaleza']}</td>
                    <td>{$puc['saldo_inicial']}</td>
                    <td>{$hijos}</td>
                    */
    }

}
