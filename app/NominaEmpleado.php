<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NominaEmpleado extends Model
{
    const TIPO_CARGO_1 = 'Libre Nombramiento';
    const TIPO_CARGO_2 = 'Carrera';
    const TIPO_CARGO_3 = 'Periodo';
    const TIPO_CARGO_4 = 'Trabajador oficial';
    const TIPO_CUENTA_BANCARIA_1 = 'Cuenta corriente';
    const TIPO_CUENTA_BANCARIA_2 = 'Cuenta de ahorros';    

    public function getEdadAttribute(){
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
}