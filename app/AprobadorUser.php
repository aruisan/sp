<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class AprobadorUser extends Model
{
    public function grupoAprobador(){
        return $this->belongsTo(GrupoAprobador::class, 'grupo_aprobador_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aprobadorCuentas(){
        return $this->hasMany(AprobadorCuenta::class, 'aprobado_user_id');
    }
}
