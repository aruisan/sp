<?php

use Illuminate\Database\Eloquent\Model;

class GrupoAprobador extends Model
{
    public function aprobadorUsers(){
        return $this->hasMany(AprobadorUser::class, 'grupo_aprobador_id');
    }
}
