<?php

namespace App\Model\Administrativo\ComprobanteIngresos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class ComprobanteIngresosMov extends Model implements Auditable
{
    protected $table = 'comprobante_ingresos_movs';

    use \OwenIt\Auditing\Auditable;

    public function comprobante(){
        return $this->belongsTo('App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos', 'comp_id');
    }

    public function users(){
        return $this->hasMany('App\User','user_id');
    }

    public function fontRubro(){
        return $this->belongsTo('App\Model\Hacienda\Presupuesto\FontsRubro','rubro_font_ingresos_id');
    }

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia', 'cuenta_puc_id');
    }

    public function banco(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia', 'cuenta_banco');
    }
}
