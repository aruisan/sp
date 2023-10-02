<?php

namespace App\Model\Administrativo\RadCuentas;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class RadCuentasOp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'rad_cuentas_op';

    public function ordenPago()
    {
        return $this->hasOne('App\Model\Administrativo\OrdenPago\OrdenPagos','id','orden_pago_id');
    }

    public function radicacion()
    {
        return $this->hasOne('App\Model\Administrativo\RadCuentas\RadCuentas','id','rad_cuenta_id');
    }
}
