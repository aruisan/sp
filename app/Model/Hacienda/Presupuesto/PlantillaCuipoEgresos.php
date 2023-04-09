<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;

class PlantillaCuipoEgresos extends Model
{
    protected $table = 'plantilla_cuipos_egresos';
    protected $appends = ['format_hijos'];

    public function padre(){
        return $this->belongsTo(PlantillaCuipoEgresos::class, 'padre_id');
    }

    public function hijos(){
        return $this->hasMany(PlantillaCuipoEgresos::class, 'padre_id');
    }

    public function rubros(){
        return $this->hasMany(Rubro::class, 'plantilla_cuipos_id');
    }

    public function getFormatHijosAttribute(){
        $item = "";
        //$last = PlantillaCuipoEgresos::latest('id')->first();
        //dd($last->id);
        //$plantilla = $this;
        $grupo_plantillas = "";
        foreach($this->hijos as $item):
            $grupo_plantillas .= $this->format_plantilla($item);
            foreach($item->rubros as $rubro):
                $grupo_plantillas .= $rubro->format;
            endforeach;
            $grupo_plantillas .= $item->format_hijos;
        endforeach;
            
        return $grupo_plantillas;
    }
    
    public function format_plantilla($plantilla){
        $tr  = "<tr>
                    <td>{$plantilla->code}</td>
                    <td>{$plantilla->name}</td>
                    <td></td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_p_inicial')}</td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_movimiento_adicion_suma')}</td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_movimiento_reduccion_suma')}</td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_movimiento_credito_suma')}</td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_movimiento_contra_credito_suma')}</td>
                    <td>{$plantilla->rubros->sum('rubro_fuente_p_definitivo')}</td>
                </tr>";
    }

    
}
