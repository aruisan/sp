<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenArticuloSalida extends Model
{
    protected $fillable = ['almacen_comprobante_egreso_id', 'almacen_articulo_id', 'cantidad'];
}
