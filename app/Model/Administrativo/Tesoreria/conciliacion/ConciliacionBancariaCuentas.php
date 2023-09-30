<?php

namespace App\Model\Administrativo\Tesoreria\conciliacion;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConciliacionBancariaCuentas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tesoreria_conciliacion_bancaria_cuentas';

    public function conciliacion()
    {
        return $this->hasMany('App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria','id','conciliacion_id');
    }

    public function getTotalAttribute(){
        return $this->debito > 0 ? $this->debito : 0 - $this->credito;
    }
}
