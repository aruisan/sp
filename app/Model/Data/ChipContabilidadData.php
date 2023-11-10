<?php

namespace App\Model\Data;

use Illuminate\Database\Eloquent\Model;

class ChipContabilidadData extends Model
{
    protected $fillable = ['age', 'trimestre', 'finalizar'];
    protected $connection = 'mysql_data';

    public function datos(){
        return $this->hasMany(ChipContabilidadValorInicial::class, 'chip_contabilidad_data_id');
    }
}
