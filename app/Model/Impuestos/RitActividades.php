<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;

class RitActividades extends Model
{
    protected $table = 'imp_rit_activ';

    public function rit(){
        return $this->belongsTo('App\Model\Impuestos\RIT','id');
    }
}
