<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NominaEmpleado extends Model
{
    const TIPOS_CARGO = [
        'Libre Nombramiento',
        'Carrera',
        'Periodo',
        'Trabajador oficial'
    ];
    const TIPOS_CUENTA_BANCARIA = [
        'Cuenta corriente',
        'Cuenta de ahorros'     
    ];

    public function getEdadAttribute(){
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function movimientos(){
        return $this->hasMany(NominaEmpleadoNomina::class, 'nomina_empleado_id');
    }

    public function getDescuentosAttribute(){
        $descuentos = collect();

        foreach($this->movimientos as $movimiento):
            if($movimiento->descuentos->count() > 0):
                foreach($movimiento->descuentos as $descuento):  
                    if($descuento->n_cuotas_faltantes > 0): 
                        $descuentos->push($descuento);
                    endif;
                endforeach;
            endif;
        endforeach;
        return $descuentos;
    }
}
