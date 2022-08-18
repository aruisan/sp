<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Exogena extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'imp_exogena';

    public function user(){
        return $this->belongsTo('App\User','id');
    }
}
