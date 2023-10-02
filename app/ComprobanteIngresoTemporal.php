<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComprobanteIngresoTemporal extends Model
{
    protected $table = "comprobante_ingreso_temporales";

    public function comprobante_conciliacion() {
        return $this->hasMany(ComprobanteIngresoTemporalConciliacion::class, 'comprobante_ingreso_temporal_id');
    }

    public function getCheckAttribute(){
        $r = 0;
        if($this->comprobante_conciliacion->count() > 0){
            $r = $this->comprobante_conciliacion->filter(function($e){ return $e->check;})->count() > 0 ? 1 : 0;
        }
        return $r;
    }
}
