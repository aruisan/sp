<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PucAlcaldia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "puc_alcaldia";

}
