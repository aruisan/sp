<?php

namespace App\Model\Administrativo\RadCuentas;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class RadCuentasAdd extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'rad_cuentas_add';

    public function registro()
    {
        return $this->hasOne('App\Model\Administrativo\Registro\Registro','id','registro_id');
    }

    public function radicacion()
    {
        return $this->hasOne('App\Model\Administrativo\RadCuentas\RadCuentas','id','rad_cuenta_id');
    }
}
