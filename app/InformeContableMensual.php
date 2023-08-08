<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeContableMensual extends Model
{
    protected $fillable = ['fecha', 'finalizar'];

    public function datos(){
        return $this->hasMany(InformeContableMensualData::class, 'informe_contable_mensual_id');
    }

    public function getMesAttribute(){
        $fecha_array = explode('-', $this->fecha);
        return  intval($fecha_array[1]);
    }

    public function getYearAttribute(){
        $fecha_array = explode('-', $this->fecha);
        return  intval($fecha_array[0]);
    }
}
