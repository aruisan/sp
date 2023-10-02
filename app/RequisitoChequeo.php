<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class RequisitoChequeo extends Model
{
    public function chequeCuentas(){
        return $this->hasMany(ChequeoCuenta::class, 'requisito_chequeo_id');
    }
}
