<?php

namespace App;

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigGeneral extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}
