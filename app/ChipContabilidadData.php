<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChipContabilidadData extends Model
{
    protected $fillable = ['age', 'trimestre', 'finalizar'];

    public function datos(){
        return $this->hasMany(ChipContabilidadValorInicial::class, 'chip_contabilidad_data_id');
    }
}
