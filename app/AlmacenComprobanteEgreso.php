<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;
use App\Model\Admin\Dependencia;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class AlmacenComprobanteEgreso extends Model
{
    protected $fillable = ['fecha', 'dependencia_id', 'responsable_id', 'owner_id', 'ccc', 'ccd', 'status', 'observacion'];
    protected $casts = [
        'status' => 'array',
        'observacion' => 'array'
    ];

    public function dependencia(){
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    public function puc_credito(){
        return $this->belongsTo(PucAlcaldia::class, 'ccc');
    }

    public function responsable(){
        return $this->belongsTo(Persona::class, 'responsable_id');
    }

    public function salidas_pivot() {
        return $this->hasMany(AlmacenArticuloSalida::class, 'almacen_comprobante_egreso_id');
    }

    public function salidas() {
        return $this->belongsToMany(AlmacenArticulo::class, 'almacen_articulo_salidas')->withPivot('cantidad', 'id');
    }

    public function setCcdAttribute($value)
    {
        $this->attributes['ccd'] = json_encode($value);
    }

    public function getHistoricoAttribute(){
        return count($this->status) < 2 ? FALSE : TRUE;
    }

    public function getindexAttribute(){
        $salidas = AlmacenComprobanteEgreso::where('id', '<=', $this->id)->get();
        if($salidas->count() > 0){
            return $salidas->filter(function($g){ return $g->salidas_pivot->count() > 0; })->count() > 0 
            ? $salidas->filter(function($i){return $i->salidas_pivot->count() > 0;})->count()
            :0;
        }
    }

    public function getNombreAttribute(){
        return "Salida {$this->index}";
    }
}
