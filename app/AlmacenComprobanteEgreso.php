<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;
use App\Model\Admin\Dependencia;

class AlmacenComprobanteEgreso extends Model
{
    protected $fillable = ['fecha', 'dependencia_id', 'responsable_id', 'owner_id'];

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
        return $this->belongsToMany(AlmacenArticulo::class, 'almacen_articulo_salidas')->withPivot('cantidad');
    }

    public function puc_ccd(){
        return $this->belongsTo(PucAlcaldia::class, 'ccd');
    }

    public function puc_ccc(){
        return $this->belongsTo(PucAlcaldia::class, 'ccc');
    }
}
