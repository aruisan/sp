<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaModalidadEmpleado extends Model
{
    protected $fillable = ['nomina_empleado_nomina_id', 'nomina_modalidad_id'];

    public function nomina_modalidad(){
        return belongsTo(NominaModalidad::class, 'nomina_modalidad_id');
    }
}
