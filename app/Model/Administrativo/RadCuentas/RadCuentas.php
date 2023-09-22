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

    public function registros()
    {
        return $this->hasOne('App\Model\Administrativo\Registro\Registro','id','registro_id');
    }

    public function persona()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }
}
