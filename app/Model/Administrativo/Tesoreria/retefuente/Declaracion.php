<?php

namespace App\Model\Administrativo\Tesoreria\retefuente;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Declaracion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
