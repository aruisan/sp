<?php
namespace App;

use App\Model\Persona;
use Illuminate\Database\Eloquent\Model;

class TramiteCuenta extends Model
{

    //relaciones
    public function beneficiario(){
        return $this->belongsTo(Persona::class, 'beneficiario_id');
    }

    public function logs(){
        return $this->hasMany(TramiteCuentaLog::class, 'tramite_cuenta_id');
    }

    public function remitente(){
        return $this->belongsTo('App\User', 'remitente_id');
    }

    public function chequeosCuenta(){
        return $this->hasMany(ChequeoCuenta::class, 'tramite_cuenta_id');
    }

    public function AprobadoresCuenta(){
        return $this->hasMany(AprobadorCuenta::class, 'tramite_cuenta_id');
    }

    public function getFechaRecibidoAttribute(){
        return $this->created_at->format('Y-m-d');
    }
}
