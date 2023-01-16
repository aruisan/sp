<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaEmpleadoDescuentos extends Model
{
    protected $fillable = ['nombre', 'valor', 'nomina_empleado_nomina_id', 'tercero_id'];
}-
