<?php

namespace App\Model\Administrativo\OrdenPago;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\Pago\Pagos;

class OrdenPagos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function registros()
    {
        return $this->hasOne('App\Model\Administrativo\Registro\Registro','id','registros_id');
    }

    public function fechas(){
        return $this->hasMany('App\Model\Administrativo\OrdenPago\OrdenPagosFechas','orden_pagos_id');
    }

    public function descuentos(){
        return $this->hasMany('App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos','orden_pagos_id');
    }

    public function pucs(){
        return $this->hasMany('App\Model\Administrativo\OrdenPago\OrdenPagosPuc','orden_pago_id');
    }

    public function pago(){
        return $this->hasOne('App\Model\Administrativo\Pago\Pagos','orden_pago_id');
    }

    public function rubros(){
        return$this->hasMany('App\Model\Administrativo\OrdenPago\OrdenPagosRubros','orden_pagos_id');
    }

    public function pagos(){
        return $this->hasMany(Pagos::class,'orden_pago_id');
    }

    public function getSumaPagosAceptadosAttribute(){
        $inicio = "2023-01-01";
        $final = "2023-03-31";
        return $this->pagos->count() > 0 ? $this->pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->filter(function($p){
            return $p->estado == 1;
        })->sum('valor') : 0;
    }
}
