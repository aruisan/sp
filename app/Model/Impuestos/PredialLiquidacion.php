<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PredialLiquidacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_predial_liquidacion';

    public function predial(){
        return $this->belongsTo('App\Model\Impuestos\Predial','imp_predial_id');
    }
}
