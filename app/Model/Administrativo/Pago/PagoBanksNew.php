<?php

namespace App\Model\Administrativo\Pago;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PagoBanksNew extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function data_puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','rubros_puc_id');
    }

    public function pago(){
        return $this->belongsTo('App\Model\Administrativo\Pago\Pagos', 'pagos_id');
    }

    public function persona()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }

}
