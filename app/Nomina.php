<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $fillable = ['salud', 'pension', 'riesgos', 'sena','icbf','caja_compensacion','cesantias','interes_cesantias','prima_navidad','vacaciones'];


    public function empleados_nominas(){
        return $this->hasMany(NominaEmpleadoNomina::class, 'nomina_id');
    }
}


