<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IcaContri extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_ica_contri';

    public function user(){
        return $this->belongsTo('App\User');
    }
}
