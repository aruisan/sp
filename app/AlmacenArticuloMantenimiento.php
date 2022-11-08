<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenArticuloMantenimiento extends Model
{
    protected $fillable = ["responsable", "almacen_factura_articulo_id", "descripcion"];
    
    public function articulo(){
        return $this->belongsTo(AlmacenFacturaArticulo::class, 'almacen_factura_articulo_id');
    } 
}
