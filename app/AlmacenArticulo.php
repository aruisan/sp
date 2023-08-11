<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenArticulo extends Model
{
    protected $fillable = ['codigo', 'cantidad', 'nombre_articulo', 'referencia',  'valor_unitario',  'estado', 'tipo', 'almacen_comprobante_ingreso_id'];
    protected $appends = ['stock'];

    public function comprobante_ingreso(){
        return $this->belongsTo(AlmacenComprobanteIngreso::class, 'almacen_comprobante_ingreso_id');
    }

    public function comprobante_egresos(){
        return $this->belongsToMany(AlmacenComprobanteEgreso::class, 'almacen_articulo_salidas')->withPivot('cantidad');
    }

    public function mantenimientos(){
        return $this->hasMany(AlmacenArticuloMantenimiento::class, 'almacen_articulo_id');
    }
    
    
    public function getTotalAttribute(){
        return $this->cantidad * $this->valor_unitario;
    }

    public function getStockAttribute(){
        return  $this->cantidad - $this->hasMany(AlmacenArticuloSalida::class, 'almacen_articulo_id')->sum('cantidad');
    }
}
