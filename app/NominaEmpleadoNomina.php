<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaEmpleadoNomina extends Model
{
    protected $fillable = ['nomina_empleado_id','dias_laborados','horas_extras','recargos_nocturnos','sueldo'];

    public function nomina(){
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function descuentos(){
        return $this->hasMany(NominaEmpleadoDescuentos::class, 'nomina_empleado_nomina_id');
    }

}
