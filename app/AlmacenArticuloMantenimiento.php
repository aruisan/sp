<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;

class AlmacenArticuloMantenimiento extends Model
{
    protected $fillable = ["responsable_id", "almacen_articulo_id", "descripcion"];
    
    public function articulo(){
        return $this->belongsTo(AlmacenArticulo::class, 'almacen_articulo_id');
    } 

    public function responsable(){
        return $this->belongsTo(Persona::class, 'responsable_id');
    }
}
