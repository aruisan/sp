<?php

namespace App\Model\Administrativo\Impuestos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Muellaje extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'imp_muellaje';

    public function user(){
        return $this->belongsTo('App\User','id');
    }

    public function vehiculosRelation(){
        return $this->hasMany('App\Model\Administrativo\Impuestos\MuellajeVehiculos','imp_id');
    }
}
