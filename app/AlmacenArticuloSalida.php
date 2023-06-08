<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenArticuloSalida extends Model
{
    protected $fillable = ['almacen_comprobante_egreso_id', 'almacen_articulo_id', 'cantidad'];
    

    public function egreso() {
        return $this->belongsTo(AlmacenComprobanteEgreso::class, 'almacen_comprobante_egreso_id');
    }

    public function articulo() {
        return $this->belongsTo(AlmacenArticulo::class, 'almacen_articulo_id');
    }

    public function getTotalAttribute(){
        return $this->articulo->valor_unitario * $this->cantidad;
    }
}
