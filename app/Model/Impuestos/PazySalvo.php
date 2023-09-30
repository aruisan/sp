<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PazySalvo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_pazysalvo';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function contribuyente(){
        return $this->belongsTo('App\Model\Impuestos\IcaContri','contri_id');
    }

    public function pago(){
        return $this->belongsTo('App\Model\Impuestos\Pagos','pago_id');
    }
}
