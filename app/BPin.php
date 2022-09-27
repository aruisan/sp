<?php

namespace App;

use App\Vigencia;
use Illuminate\Database\Eloquent\Model;

class BPin extends Model
{
    protected $fillable = ['confinanciado', 'entidad', 'secretaria', 'dependencia', 'cod_sector', 'nombre_sector','cod_proyecto' 
    ,'nombre_proyecto' ,'actividad','metas' , 'cod_actividad', 'actividad', 'fecha_radicado', 'inicial', 'final','propios' ,'sgp', 'cod_producto' 
    ,'nombre_producto' , 'cod_indicador' ,'nombre_indicador', 'vigencia_id' 
    ];


    public function rubro(){
        return $this->belongsTo('App\Model\Hacienda\Presupuesto\Rubro', 'rubro_id');
    }

    public function vigencia(){
        return $this->belongsTo(Vigencia::class);
    }

    public function getsecreAttribute(){
        return Dependencia::where('nombre', $this->secretaria)->first();
    }
}

