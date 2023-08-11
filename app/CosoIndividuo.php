<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CosoIndividuo extends Model
{
    protected $fillable = ['date_at','ficha_ingreso','nombre','tipo','peso','talla','sexo','color','Observacion','marcas'];

    public function archivos(){
        return $this->hasMany(CosoArchivo::class);
    }

    public function comidas(){
        return $this->hasMany(CosoComida::class);
    }

    public function veterinarios(){
        return $this->hasMany(CosoVeterinario::class);
    }
}
