<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pagos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_pagos';

    public function user(){
        return $this->belongsTo('App\User','id');
    }

    public function Resource(){
        return $this->belongsTo('App\Resource','resource_id');
    }
}
