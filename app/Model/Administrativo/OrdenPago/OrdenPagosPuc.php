<?php

namespace App\Model\Administrativo\OrdenPago;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\OrdenPago\OrdenPagos;

class OrdenPagosPuc extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function data_puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','rubros_puc_id');
    }

    public function ordenPago()
    {
        return $this->belongsTo(OrdenPagos::class, 'orden_pago_id');
    }

    public function getSumaPagosAttribute(){
        return is_null($this->ordenPago) ? 0 : $this->ordenPago->suma_pagos_aceptados;
    }



}
