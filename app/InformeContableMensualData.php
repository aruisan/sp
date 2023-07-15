<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class InformeContableMensualData extends Model
{
    protected $fillable = ['puc_alcaldia_id', 'informe_contable_mensual_id', 'm_credito', 'm_debito', 's_credito', 's_debito', 'i_credito', 'i_debito', 'padre_id'];

    public function puc_alcaldia(){
        return $this->belongsTo(PucAlcaldia::class, 'puc_alcaldia_id');
    }

    public function informe_contable_mensual(){
        return $this->belongsTo(InformeContableMensual::class, 'informe_contable_mensual_id');
    }

    public function hijos(){
        return $this->hasMany(InformeContableMensualData::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(InformeContableMensualData::class, 'padre_id');
    }

    public function getFormatHijosPruebaAttribute(){
        $grupo_puc = "";
        foreach($this->hijos->sortBy('puc_alcaldia.code') as $item):
            $grupo_puc .= $this->format_puc($item, $item->puc_alcaldia);
            $grupo_puc .= $item->format_hijos_prueba;
        endforeach;
            
        return $grupo_puc;
    }

    public function format_puc($data, $puc){
        $item =  "<tr>
                    <td class='text-left'>{$puc['code']}</td>
                    <td class='text-rigth'>{$puc['concepto']}</td>
                    <td class='text-right'>$".number_format($data['i_debito'])."</td>
                    <td class='text-right'>$".number_format($data['i_credito'])."</td>
                    <td class='text-right'>{$data['i_debito']}</td>
                    <td class='text-right'>{$data['i_credito']}</td>
                    <td class='text-right'>$".number_format($data['m_debito'])."</td>
                    <td class='text-right'>$".number_format($data['m_credito'])."</td>
                    <td class='text-right'>{$data['m_debito']}</td>
                    <td class='text-right'>{$data['m_credito']}</td>
                    <td class='text-right'>$".number_format($data['s_debito'])."</td>
                    <td class='text-right'>$".number_format($data['s_credito'])."</td>
                    <td class='text-right'>{$data['s_debito']}</td>
                    <td class='text-right'>{$data['s_credito']}</td>
                </tr>";

        return $item;
    }
}
