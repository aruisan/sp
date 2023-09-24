<?php

namespace App\Model\Administrativo\RadCuentas;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class RadCuentas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'rad_cuentas';
    public function registro()
    {
        return $this->hasOne('App\Model\Administrativo\Registro\Registro','id','registro_id');
    }
    public function persona()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }
    public function pago()
    {
        return $this->hasOne('App\Model\Administrativo\RadCuentas\RadCuentasPago','rad_cuenta_id');
    }
    public function interventor()
    {
        return $this->hasOne('App\Model\Persona','id','interventor_id');
    }
    public function supervisor()
    {
        return $this->hasOne('App\Model\Persona','id','supervisor_id');
    }
    public function elaborador()
    {
        return $this->hasOne('App\User','id','user_id');
    }
    public function adds()
    {
        return $this->hasMany('App\Model\Administrativo\RadCuentas\RadCuentasAdd','rad_cuenta_id');
    }
    public function anexos()
    {
        return $this->hasMany('App\Model\Administrativo\RadCuentas\RadCuentasAnex','rad_cuenta_id');
    }
}
