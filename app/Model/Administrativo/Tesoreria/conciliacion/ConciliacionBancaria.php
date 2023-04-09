<?php

namespace App\Model\Administrativo\Tesoreria\conciliacion;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\ComprobanteIngresoTemporal;
use App\ComprobanteIngresoTemporalConciliacion;

class ConciliacionBancaria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tesoreria_conciliacion_bancaria';

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','puc_id');
    }

    public function responsable()
    {
        return $this->belongsTo('App\User', 'responsable_id');
    }

    public function cuentas(){
        return $this->belongsTo('App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas','id');
    }

    public function cheques_mano(){
        return $this->hasMany('App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas','conciliacion_id');
    }

    public function cuentas_temporales() {
        return $this->hasMany(ComprobanteIngresoTemporalConciliacion::class, 'conciliacion_id');
    }
}
