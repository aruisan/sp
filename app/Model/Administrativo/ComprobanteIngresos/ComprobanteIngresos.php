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

    public function fontRubro(){
        return $this->belongsTo('App\Model\Hacienda\Presupuesto\FontsRubro','rubro_font_ingresos_id');

    }

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia', 'puc_alcaldia_id');
    }
}
