<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeContableMensual extends Model
{
    protected $fillable = ['fecha', 'finalizar'];

    public function datos(){
        return $this->hasMany(InformeContableMensualData::class, 'informe_contable_mensual_id');
    }
}
