<?php

namespace App\Model\Administrativo\Tesoreria\retefuente;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TesoreriaRetefuenteConta extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','cuenta_puc_id');
    }

    public function persona()
    {
        return $this->hasOne('App\Model\Persona','id','persona_id');
    }
}
