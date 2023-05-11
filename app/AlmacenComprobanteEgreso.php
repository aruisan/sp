<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;
use App\Model\Admin\Dependencia;

class AlmacenComprobanteEgreso extends Model
{
    protected $fillable = ['fecha', 'dependencia_id', 'responsable_id', 'owner_id', 'ccc', 'ccd'];

    public function dependencia(){
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    public function responsable(){
        return $this->belongsTo(Persona::class, 'responsable_id');
    }

    public function salidas_pivot() {
        return $this->hasMany(AlmacenArticuloSalida::class, 'almacen_comprobante_egreso_id');
    }

    public function salidas() {
        return $this->belongsToMany(AlmacenArticulo::class, 'almacen_articulo_salidas')->withPivot('cantidad', 'id', 'status', 'Observacion');
    }

    public function setCccAttribute($value)
    {
        $this->attributes['ccc'] = json_encode($value);
    }

    public function setCcdAttribute($value)
    {
        $this->attributes['ccd'] = json_encode($value);
    }
}
