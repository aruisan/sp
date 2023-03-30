<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaModalidad extends Model
{
    protected $table = 'nomina_modalidades';

    protected $fillable = ['nomina_id', 'modalidad'];

    public function nomina(){
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function movimientos(){
        return $this->belongsToMany(NominaEmpleadoNomina::class, 'nomina_modalidad_empleados');
    }
}
