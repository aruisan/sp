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
/*
    public function getFormatHijosAttribute(){
        $item = "";
        //$last = PlantillaCuipoEgresos::latest('id')->first();
        //dd($last->id);
        //$plantilla = $this;
        $grupo_plantillas = "";
        foreach($this->hijos as $item):
            $grupo_plantillas .= $this->format_plantilla($item);
            foreach($item->rubros as $rubro):
                $grupo_plantillas .= $this->format_rubro(
                    $rubro->fontsRubro,
                    $rubro->name, 
                    $item->code);
                foreach($rubro->fontsRubro as $r_f_r):
                    $grupo_plantillas  .= $this->format_f_r(
                        $rubro->fontsRubro,
                        $rubro->name, 
                        $item->code);
                endforeach;
            endforeach;
            $grupo_plantillas .= $item->format_hijos;
        endforeach;
            
        return $grupo_plantillas;
    }
//cod_bpin | cod_actividad | nom_actividad | plantilla_code | rubro_nombre | p inicial | adicion 
//| reduccion | credito | p definitivo | registros | saldo_disponible | saldo_cdp | ordenes_pago 
    public function format_f_R($bpin, $rubro_name, $plantilla_code){
        return "<tr><td></td><td></td><td>{$plantilla['code']}</td><td>{$plantilla['name']}</td></tr>";
    }
    
    public function format_plantilla($plantilla){
        $bg = $plantilla['hijo'] ? '' : 'bg-success';
        return "<tr class='{$bg}'><td></td><td></td><td>{$plantilla['code']}</td><td>{$plantilla['name']}</td></tr>";
    }

    */
}
