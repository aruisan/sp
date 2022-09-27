<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RIT extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_rit';

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function actividades(){
        return $this->hasMany('App\Model\Impuestos\RitActividades','rit_id');
    }

    public function establecimientos(){
        return $this->hasMany('App\Model\Impuestos\RitEstablecimientos','rit_id');
    }

    public function ResourceRUT(){
        return $this->belongsTo('App\Resource','rut_resource_id');
    }

    public function ResourceCC(){
        return $this->belongsTo('App\Resource','cc_resource_id');
    }
}
