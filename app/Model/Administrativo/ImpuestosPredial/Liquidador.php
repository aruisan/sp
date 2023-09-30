<?php

namespace App\Model\Administrativo\ImpuestosPredial;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Liquidador extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
