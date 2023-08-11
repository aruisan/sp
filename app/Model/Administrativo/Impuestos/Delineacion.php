<?php

namespace App\Model\Administrativo\Impuestos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Delineacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'imp_delineacion';

    public function vecinos(){
        return $this->hasMany('App\Model\Administrativo\Impuestos\DelineacionVecinos','delineacion_id');
    }

    public function titulares(){
        return $this->hasMany('App\Model\Administrativo\Impuestos\DelineacionTitulares','delineacion_id');
    }

    public function user(){
        return $this->belongsTo('App\User','id');
    }
}
