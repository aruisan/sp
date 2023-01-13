<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportEstadisticaPresupuesto extends Model
{
    protected $fillable = ['data'];
    
    protected $casts = [
        'data' => 'array',
    ];
}
