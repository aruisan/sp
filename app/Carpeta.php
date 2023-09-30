<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Administrativo\GestionDocumental\Documents;

class Carpeta extends Model
{
    protected $fillable = ['nombre', 'tipo', 'ubicacion_fisica', 'cuantia', 'owner_id'];

    public function documentos(){
        return $this->hasMany(Documents::class, 'carpeta_id');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }
}
