<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NominaEmpleado extends Model
{
    public function getEdadAttribute(){
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
}