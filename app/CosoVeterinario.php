<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CosoVeterinario extends Model
{
    protected $fillable = ['nombre_veterinario','tarjeta_profesional','cedula','celular','coso_individuo_id'];

    public function individuo(){
        return $this->belongsTo(CosoIndividuo::class);
    }

    public function medicinas(){
        return $this->hasMany(CosoMedicina::class);
    }
}
