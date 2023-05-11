<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenArticuloSalida extends Model
{
    protected $fillable = ['almacen_comprobante_egreso_id', 'almacen_articulo_id', 'cantidad', 'status', 'observacion'];
    protected $casts = [
        'status' => 'array',
        'observacion' => 'array'
    ];

    public function egreso() {
        return $this->belongsTo(AlmacenComprobanteEgreso::class, 'almacen_comprobante_egreso_id');
    }

    public function articulo() {
        return $this->belongsTo(AlmacenArticulo::class, 'almacen_articulo_id');
    }
}
