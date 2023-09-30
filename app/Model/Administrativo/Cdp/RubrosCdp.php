<?php

namespace App\Model\Administrativo\Cdp;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RubrosCdp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rubros_cdp';

    public function rubros(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\Rubro','id','rubro_id');
    }

    public function cdps(){
        return $this->hasOne('App\Model\Administrativo\Cdp\Cdp','id','cdp_id');
    }

    public function rubrosCdpValor(){
        return $this->hasMany('App\Model\Administrativo\Cdp\RubrosCdpValor','rubrosCdp_id');
    }

    public function depRubroFont(){
        return $this->hasOne('App\Model\Admin\DependenciaRubroFont','id','dep_rubro_font_id');
    }
}
