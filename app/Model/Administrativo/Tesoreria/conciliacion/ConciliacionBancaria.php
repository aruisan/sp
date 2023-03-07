<?php

namespace App\Model\Administrativo\Tesoreria\conciliacion;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConciliacionBancaria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tesoreria_conciliacion_bancaria';

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','puc_id');
    }

    public function responsable()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }

    public function cuentas(){
        return $this->belongsTo('App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas','id');
    }
}