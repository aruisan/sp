<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaEmpleadoDescuentos extends Model
{
    protected $fillable = ['nombre', 'valor', 'nomina_empleado_nomina_id', 'tercero_id'];
    protected $appends = ['cop'];

    public function tercero(){
        return $this->belongsTo(\App\Model\Persona::class, 'tercero_id');
    }

    public function getCopAttribute(){
        return "<li><b>{$this->tercero->nombre}:</b>$".number_format($this->valor, 0, ',', '.')."</li>";
    }
}
