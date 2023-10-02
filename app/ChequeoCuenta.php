<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class ChequeoCuenta extends Model
{
    public function tramiteCuenta(){
        return $this->belongsTo(TramiteCuenta::class, 'tramite_cuenta_id');
    }

    public function requisitoChequeo(){
        return $this->belongsTo(RequisitoChequeo::class, 'requisito_chequeo_id');
    }

    public function getvalidarChequeoAttribute(){
        if($this->estado == 'si'){
            return true;
        }else{
            return false;
        }
    }
}
