<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenFacturaArticulo extends Model
{
    protected $fillable = ['codigo', 'cantidad', 'nombre_articulo', 'referencia',  'valor_unitario', 'ccd', 'ccc', 'estado', 'dependencia_id', 'almacen_factura_id'];
    
    public function dependencia() {
        return $this->belongsTo(Dependencia::class);
    } 

    public function factura(){
        return $this->belongsTo(AlmacenFactura::class, 'almacen_factura_id');
    }
   ////////////////////// 
    public function owner(){
        return $this->hasMany(AlmacenArticuloOwner::class, 'owner_id')->last();
    }
    
    public function getTotalAttribute(){
        return $this->cantidad * $this->valor_unitario;
    }
}
