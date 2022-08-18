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
}
