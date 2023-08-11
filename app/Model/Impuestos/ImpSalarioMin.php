<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ImpSalarioMin extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_salario_min';
}
