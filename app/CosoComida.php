<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CosoComida extends Model
{
    protected $fillable = ['coso_individuo_id','porcion_diaria','porciones'];

    protected $casts = [
        'porciones' => 'array',
    ];

    public function individuo(){
        return $this->belongsTo(CosoIndividuo::class);
    }


    public function setPorcionesAttribute($value)
    {
        $this->attributes['porciones'] = json_encode($value);
    }

}
