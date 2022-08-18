<?php

namespace App\Model\Administrativo\ComprobanteIngresos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class ComprobanteIngresos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function vigencia(){
        return $this->belongsTo('App\Model\Hacienda\Presupuesto\Vigencia', 'vigencia_id');
    }

    public function users(){
        return $this->hasMany('App\User','user_id');
    }

    public function rubros(){
        return $this->hasMany('App\Model\Administrativo\ComprobanteIngresos\CIRubros','comprobante_ingreso_id');

    }
}
