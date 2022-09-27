<?php

namespace App\Model\Administrativo\Cdp;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BpinCdpValor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bpin_cdp_valors';

    public function actividad(){
        return $this->hasOne('App\Bpin','cod_actividad','cod_actividad');
    }

    public function cdp(){
        return $this->hasOne('App\Model\Administrativo\Cdp\Cdp','id','cdp_id');
    }
}
