<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CosoMedicina extends Model
{
    protected $fillable = ['medicamento','dosis_diaria','hora','termino','aplica','coso_veterinario_id'];

    public function veterinario(){
        return $this->belongsTo(CosoVeterinario::class);
    }
    
}
