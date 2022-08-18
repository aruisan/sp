<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;

class RitEstablecimientos extends Model
{
    protected $table = 'imp_rit_estable';

    public function rit(){
        return $this->belongsTo('App\Model\Impuestos\RIT','id');
    }
}
