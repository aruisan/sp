<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Predial extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_predial';

    public function liquidacion(){
        return $this->hasMany('App\Model\Impuestos\PredialLiquidacion','imp_predial_id');
    }
}
