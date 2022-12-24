<?php

namespace App;

use App\Vigencia;
use Illuminate\Database\Eloquent\Model;

class bpinVigencias extends Model
{
    public function bpin(){
        return $this->belongsTo('App\BPin');
    }

    public function rubro(){
        return $this->belongsTo('App\Model\Admin\DependenciaRubroFont', 'dep_rubro_id');
    }

    public function vigencia(){
        return $this->belongsTo(Vigencia::class);
    }
}
