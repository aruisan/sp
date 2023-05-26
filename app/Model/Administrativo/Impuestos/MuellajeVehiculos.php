<?php

namespace App\Model\Administrativo\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MuellajeVehiculos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_muellaje_vehiculos';

    public function impuesto(){
        return $this->belongsTo('App\Model\Administrativo\Impuestos\Muellaje','imp_id');
    }
}
