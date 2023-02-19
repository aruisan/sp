<?php

namespace App\Model\Administrativo\Tesoreria\retefuente;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TesoreriaRetefuentePago extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function contas(){
        return $this->hasMany('App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuenteConta','retefuente_id');
    }

    public function formularios(){
        return $this->hasMany('App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuenteForm','retefuente_id');
    }

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','cuenta_puc_id');
    }

    public function compcontable(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\CompCont','comp_conta_id');
    }

    public function egreso(){
        return $this->belongsTo('App\Model\Administrativo\Pago\Pagos','comp_egreso_id');
    }
}
