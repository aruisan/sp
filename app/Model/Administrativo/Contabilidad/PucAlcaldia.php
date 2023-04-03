<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PucAlcaldia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "puc_alcaldia";

    public function hijos(){
        return $this->hasMany(PucAlcaldia::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(PucAlcaldia::class, 'padre_id');
    }


    public function getVHijosAttribute(){
        return $this->hijos->count() == 0 ? $this->saldo_inicial : $this->hijos->sum('saldo_inicial'); 
    }

    public function getVInicialAttribute(){
        $suma = $this->saldo_inicial;
        if($this->hijos->count() > 0):
            $suma += $this->hijos->sum('valor_inicial');
            $suma += $this->hijos->sum('v_inicial');
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
                </tr>";
                    /*
                    <td>{$puc['naturaleza']}</td>
                    <td>{$puc['saldo_inicial']}</td>
                    <td>{$padre}</td>
                    <td>{$hijos}</td>
                    */
    }

}
