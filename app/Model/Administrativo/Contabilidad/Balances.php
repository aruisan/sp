<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Balances extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'balances';

    public function data(){
        return $this->hasMany('App\Model\Administrativo\Contabilidad\BalanceData','balance_id');
    }

}
