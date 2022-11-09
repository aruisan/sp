<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;

class AlmacenFactura extends Model
{
    protected $fillable= [
        'numero_factura',
        'comprobante_ingreso',
        'comprobante_egreso',
        'ff_ingreso',
        'ff_egreso',
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
        return $this->hasMany(AlmacenFacturaArticulo::class, 'almacen_factura_id');
    }
}
