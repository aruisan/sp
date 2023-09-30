<?php

namespace App\Model\Administrativo\Tesoreria\descuentos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TesoreriaDescuentos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tesoreria_descuentos';
}
