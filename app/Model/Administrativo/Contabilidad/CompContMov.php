<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompContMov extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function comprobante(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\CompCont','comp_cont_id');
    }

    public function data_puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\RubrosPuc','rubros_puc_id');
    }
}
