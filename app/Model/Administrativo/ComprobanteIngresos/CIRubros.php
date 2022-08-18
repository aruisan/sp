<?php

namespace App\Model\Administrativo\ComprobanteIngresos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class CIRubros extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ci_rubros';

    public function ComprobanteIng(){
        return $this->hasOne('App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos','comprobante_ingreso_id','id');
    }

    public function rubros(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\Rubro','id','rubro_id');
    }

    public function fontsRubro(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\FontsRubro','id');
    }

}
