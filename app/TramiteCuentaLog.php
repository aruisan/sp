<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TramiteCuentaLog extends Model
{
    protected $table = "tramites_cuentas_logs";
    public $timestamps = false;

    public function tramiteCuenta(){
        return $this->belongsTo(TramiteCuenta::class, 'tramite_cuenta_id');
    }
}
