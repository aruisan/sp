<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PacInformeIngresoEgreso extends Model
{
    protected $fillable = ['codigo', 'nombre', 'tipo', 'inicial'];

    public function getVMesAttribute(){
        return $this->inicial/12;
    }
}
