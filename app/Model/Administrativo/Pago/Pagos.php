<?php

namespace App\Model\Administrativo\Pago;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pagos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function persona()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }

    public function orden_pago(){
        return $this->belongsTo('App\Model\Administrativo\OrdenPago\OrdenPagos');
    }

    public function banks(){
        return $this->hasOne('App\Model\Administrativo\Pago\PagoBanks','id');
    }

    public function rubros(){
        return $this->hasMany('App\Model\Administrativo\Pago\PagoRubros','pago_id');
    }
}
