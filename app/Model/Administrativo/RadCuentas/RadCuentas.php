<?php

namespace App\Model\Administrativo\RadCuentas;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;
use Session;

class RadCuentas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function registros()
    {
        return $this->hasOne('App\Model\Administrativo\Registro\Registro','id','registro_id');
    }
}
