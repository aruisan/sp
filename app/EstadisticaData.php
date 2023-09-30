<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadisticaData extends Model
{
    protected $casts = [
        'data' => 'array'
    ];
}
