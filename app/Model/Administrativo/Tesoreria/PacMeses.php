<?php

namespace App\Model\Administrativo\Tesoreria;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PacMeses extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
