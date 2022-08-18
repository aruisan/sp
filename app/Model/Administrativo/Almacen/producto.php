<?php

namespace App\Model\Administrativo\Almacen;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class producto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function data_puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\RubrosPuc','rubros_puc_id');
    }

    public function inventario(){
        return $this->hasMany('App\Model\Administrativo\Almacen\inventario','producto_id');
    }

    public function mueble(){
        return $this->hasMany('App\Model\Administrativo\Almacen\muebles','producto_id');
    }
}
