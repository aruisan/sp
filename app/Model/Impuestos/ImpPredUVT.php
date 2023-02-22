<?php

namespace App\Model\Impuestos;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ImpPredUVT extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'imp_predial_uvt';
}
