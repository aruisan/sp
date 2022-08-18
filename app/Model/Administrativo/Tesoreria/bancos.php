<?php

namespace App\Model\Administrativo\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class bancos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function data_puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\RubrosPuc','rubros_puc_id');
    }
}
