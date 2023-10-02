<?php

namespace App\Model\Planeacion\Pdd;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Periodo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
