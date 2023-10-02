<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Comunicado extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'imp_comunicados';

    public function destinatario(){
        return $this->belongsTo('App\User','destinatario_id');
    }

    public function remitente(){
        return $this->belongsTo('App\User','remitente_id');
    }
}
