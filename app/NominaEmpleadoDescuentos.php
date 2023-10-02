<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Persona;

class NominaEmpleadoDescuentos extends Model
{
    protected $fillable = ['nombre', 'valor', 'nomina_empleado_nomina_id', 'tercero_id', 'padre_id', 'n_cuotas', 'valor_total'];
    protected $appends = ['cop'];

    public function tercero(){
        return $this->belongsTo(Persona::class, 'tercero_id');
    }

    public function hijos(){
        return $this->hasMany(NominaEmpleadoDescuentos::class, 'padre_id', );
    }

    public function padre(){
        return $this->belongsTo(NominaEmpleadoDescuentos::class, 'padre_id', );
    }



    //funciones

    public function getNCuotasFaltantesAttribute(){
        return $this->n_cuotas - ($this->hijos->count()+1); 
    }

    public function getSaldoAttribute(){
        return $this->n_cuotas > 1 ? $this->valor_total - ($this->hijos->sum('valor')+$this->valor) : $this->valor_total - $this->valor;
    }

    public function getValorCuotaSugeridaAttribute(){
        if(is_null($this->padre)):
            return $this->hijos->count() > 0 ? ceil($this->saldo/$this->n_cuotas_faltantes) : 0;
        else:
            return $this->padre->valor_cuota_sugerida; 
        endif;
    }

    public function getCopAttribute(){
        return "<li><b>{$this->tercero->nombre}:</b> ".$this->valor."</li>";
    }
}
