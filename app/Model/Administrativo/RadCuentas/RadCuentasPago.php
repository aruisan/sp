<?php

namespace App\Model\Administrativo\RadCuentas;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class RadCuentasPago extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'rad_cuentas_pago';

    public function descuentos()
    {
        return $this->hasMany('App\Model\Administrativo\RadCuentas\RadCuentasPagoDesc','rad_cuenta_pago_id','id');
    }

}
