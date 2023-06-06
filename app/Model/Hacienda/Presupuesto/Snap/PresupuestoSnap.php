<?php

namespace App\Model\Hacienda\Presupuesto\Snap;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PresupuestoSnap extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'presupuesto_snap';
}
