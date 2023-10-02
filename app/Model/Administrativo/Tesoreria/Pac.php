<?php

namespace App\Model\Administrativo\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pac extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function meses(){
        return $this->hasMany('App\Model\Administrativo\Tesoreria\PacMeses','pac_id', 'id');
    }
}
