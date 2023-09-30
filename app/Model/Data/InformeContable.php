<?php

namespace App\Model\Data;

use Illuminate\Database\Eloquent\Model;

class InformeContable extends Model
{
    protected $connection = 'mysql_data';
    protected $table = "informe_contable_mensuals";
    protected $fillable = ['fecha', 'finalizar'];

    public function datos(){
        return $this->hasMany(InformeContableData::class, 'informe_contable_mensual_id');
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

