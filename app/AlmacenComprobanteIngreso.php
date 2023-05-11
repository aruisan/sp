<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;

class AlmacenComprobanteIngreso extends Model
{
    protected $fillable= [
        'fecha',
        'factura',
        'fecha_factura',
        'contrato',
        'fecha_contrato',
        'owner_id',
        'proovedor_id'

    ];

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function proovedor(){
        return $this->belongsTo(Persona::class, 'proovedor_id');
    }

    public function articulos() {
        return $this->hasMany(AlmacenArticulo::class, 'almacen_comprobante_ingreso_id');
    }
}
