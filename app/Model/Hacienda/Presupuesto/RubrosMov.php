<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Admin\DependenciaRubroFont;

class RubrosMov extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rubros_mov';

    public function Resource(){
        return $this->belongsTo('App\Resource','resource_id');
    }

    public function ResourcesMov(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\ResourcesMov','mov_id','id');
    }

    public function Vigencia(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\Vigencia','id','font_vigencia_id');
    }
}
