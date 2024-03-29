<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
    	'nombre', 'num_dc', 'email', 'direccion', 'tipo', 'telefono', 
    ];

     public function predios(){
        return $this->belongsToMany('App\Model\Cobro\Predio');
    }

    public function concejal()
    {
        return $this->hasOne('App\Model\Administrativo\GestionDocumental\Concejal', 'dato_id');
    }

    public function puc_tercero()
    {
        return $this->hasOne('App\Model\Administrativo\Contabilidad\Puc');
    }


    public function setTipoTerceroAttribute($value)
    {
        $atributos = ['Empleado', 'Contribuyente', 'Contratista', 'Especial'];
        $this->attributes['tipo_tercero'] = $atributos[$value];
    }

    public function getTipoTerceroIndexAttribute()
    {
        $atributos = ['Empleado', 'Contribuyente', 'Contratista', 'Especial'];
        return array_search($this->tipo_tercero ,$atributos); 
    }
}