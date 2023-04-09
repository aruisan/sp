<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Admin\DependenciaRubroFont;

class FontsRubro extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fonts_rubro';

    public function fontVigencia(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\FontsVigencia','id','font_vigencia_id');
    }

    public function rubrosCdpValor(){
        return $this->hasMany('App\Model\Administrativo\Cdp\RubrosCdpValor','fontsRubro_id');
    }

    public function cdpRegistrosValor(){
        return $this->hasMany('App\Model\Administrativo\Registro\CdpsRegistroValor','fontsRubro_id');
    }

    public function rubrosMov(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\RubrosMov','fonts_rubro_id');
    }

    public function rubro(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\Rubro','id','rubro_id');
    }

    public function sourceFunding(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\SourceFunding','id','source_fundings_id');
    }

    public function dependenciaFont(){
        return $this->hasMany('App\Model\Admin\DependenciaRubroFont', 'rubro_font_id','id');
    }

    public function dependencia_rubros_font(){
        return $this->hasMany('App\Model\Admin\DependenciaRubroFont', 'rubro_font_id');
    }

    public function compIng(){
        return $this->hasMany('App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov','rubro_font_ingresos_id');
    }

    public function getPInicialAttribute(){
        return $this->movimiento_adicion_suma + $this->movimiento_credito_suma - $this->movimiento_reduccion_suma - $this->movimiento_contra_credito_suma;
    }

    public function getMovimientoCreditoSumaAttribute(){
        //return $this->rubrosMov->filter(function($m){ return $m->movimiento == 1 && $m->dep_rubro_font_cred_id == $this->id;})->sum('valor');
        return $this->hasMany('App\Model\Hacienda\Presupuesto\RubrosMov','dep_rubro_font_cred_id')->sum('valor');
    }

    public function getMovimientoContraCreditoSumaAttribute(){
        //return $this->rubrosMov->filter(function($m){ return $m->movimiento == 1 && $m->dep_rubro_font_cc_id == $this->id;})->sum('valor');
        return $this->hasMany('App\Model\Hacienda\Presupuesto\RubrosMov','dep_rubro_font_cc_id')->sum('valor');
    }

    public function getMovimientoAdicionSumaAttribute(){
        return $this->rubrosMov->filter(function($m){ return $m->movimiento == 2;})->sum('valor');
    }

    public function getMovimientoReduccionSumaAttribute(){
        return $this->rubrosMov->filter(function($m){ return $m->movimiento == 3;})->sum('valor');
    }

    public function getPDefinitivoAttribute(){
        return $this->valor + $this->p_inicial;
    }


    public function getFormatAttribute(){
        $dependencia = $this->dependencia_rubros_font->count() > 0 ? $this->dependencia_rubros_font->first->dependencia->dependencia->name : 'no tiene';
        return "<tr>
                    <td>{$this->rubro->plantilla_cuipo->code}-fr{$this->id}</td>
                    <td>{$this->rubro->name}</td>
                    <td>{$dependencia}</td>
                    <td>{$this->valor}</td>
                    <td>{$this->movimiento_adicion_suma}</td>
                    <td>{$this->movimiento_reduccion_suma}</td>
                    <td>{$this->movimiento_credito_suma}</td>
                    <td>{$this->movimiento_contra_credito_suma}</td>
                    <td>{$this->p_definitivo}</td>
                </tr>";
    }
}