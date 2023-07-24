<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class ChipContabilidadValorInicial extends Model
{
    protected $fillable = ['puc_alcaldia_id', 'chip_contabilidad_data_id', 'm_credito', 'm_debito', 's_final', 'corriente', 'no_corriente', 'valor_inicial', 'padre_id'];

    public function puc_alcaldia(){
        return $this->belongsTo(PucAlcaldia::class, 'puc_alcaldia_id');
    }

    public function chip_trimestral(){
        return $this->belongsTo(ChipContabilidadData::class, 'informe_contable_mensual_id');
    }

    public function hijos(){
        return $this->hasMany(ChipContabilidadValorInicial::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(ChipContabilidadValorInicial::class, 'padre_id');
    }

    public function getFormatoHijosAttribute(){
        $grupo_puc = "";
        foreach($this->hijos->sortBy('puc_alcaldia.code') as $item):
            if(!is_null($item->puc_alcaldia)){
                 if($item->puc_alcaldia->level <= 4){
                    $grupo_puc .= $this->format_puc($item, $item->puc_alcaldia);
                }
            }
            $grupo_puc .= $item->formato_hijos;
        endforeach;
            
        return $grupo_puc;
    }

    public function format_puc($data, $puc){
        $codigo = is_null($puc) ? "no tiene" :  $puc->codigo_punto;


        return "<tr>
                    <td class='text-left'>D</td>
                    <td class='text-center'>{$codigo}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['valor_inicial'])."</td>
                    <td class='text-right' style='width=200px;'>{$data['valor_inicial']}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['m_debito'])."</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['m_credito'])."</td>
                    <td class='text-right' style='width=200px;'>{$data['m_debito']}</td>
                    <td class='text-right' style='width=200px;'>{$data['m_credito']}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['s_final'])."</td>
                    <td class='text-right' style='width=200px;'>{$data['s_final']}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['corriente'])."</td>
                    <td class='text-right' style='width=200px;'>$".number_format($data['no_corriente'])."</td>
                    <td class='text-right' style='width=200px;'>{$data['corriente']}</td>
                    <td class='text-right' style='width=200px;'>{$data['no_corriente']}</td>
                    </tr>";
    }
}
