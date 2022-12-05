<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CosoArchivo extends Model
{
    protected $fillable = ['coso_individuo_id','ruta'];

    public function individuo(){
        return $this->belongsTo(CosoIndividuo::class);
    }

    public function getUrlAttribute(){
    	return \Storage::url($this->ruta);
    }
}
