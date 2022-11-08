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
}
