<?php

namespace App\Model\Administrativo\Almacen;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class muebles extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;

    public function producto(){
        return $this->belongsTo('App\Model\Administrativo\Almacen\producto','producto_id');
    }

}
