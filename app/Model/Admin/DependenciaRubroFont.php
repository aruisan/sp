<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DependenciaRubroFont extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function dependencias(){
        return $this->hasMany('App\Model\Admin\Dependencia','dependencia_id');
    }

    public function fontRubro(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\FontsRubro', 'id','rubro_font_id');
    }

    public function vigencia(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\Vigencia', 'vigencia_id');
    }
}
