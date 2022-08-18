<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Vigencia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function rubros(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\Rubro','vigencia_id');
    }

    public function fonts(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\FontsVigencia','vigencia_id');
    }

    public function levels(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\Level','vigencia_id');
    }

    public function cdps(){
        return $this->hasMany('App\Model\Administrativo\Cdp\Cdp','vigencia_id');
    }

    public function CIngresos(){
        return $this->hasMany('App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos','vigencia_id');
    }

}
