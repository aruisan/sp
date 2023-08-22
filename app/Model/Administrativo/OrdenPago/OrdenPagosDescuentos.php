<?php

namespace App\Model\Administrativo\OrdenPago;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\OrdenPago\OrdenPagos;

class OrdenPagosDescuentos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function descuento_retencion(){
        return $this->belongsTo('App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente','retencion_fuente_id');
    }

    public function descuento_mun(){
        return $this->belongsTo('App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales','desc_municipal_id');
    }

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','cuenta_puc_id');
    }

    public function pago(){
        return $this->belongsTo(OrdenPagos::class, 'orden_pagos_id');
    }
}
