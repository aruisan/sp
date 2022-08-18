<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompCont extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function tipo(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\TipoComp','tipo_comp_id');
    }
}
