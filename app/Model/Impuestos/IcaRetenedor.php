<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IcaRetenedor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_ica_retenedor';
}
