<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Entidad extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['tipo','municipio','departamento','frase','frase_mov','escudo','slogan','imagen'];

  
}
