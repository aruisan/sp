<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ResourcesMov extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'resources_mov';

    public function Movimiento(){
        return $this->belongsTo('App\Model\Hacienda\Presupuesto\RubrosMov','mov_id');
    }

}
