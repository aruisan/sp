<?php

namespace App\Model\Administrativo\Tesoreria\conciliacion;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\ComprobanteIngresoTemporal;
use App\ComprobanteIngresoTemporalConciliacion;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas;

class ConciliacionBancaria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tesoreria_conciliacion_bancaria';

    public function puc(){
        return $this->belongsTo('App\Model\Administrativo\Contabilidad\PucAlcaldia','puc_id');
    }

    public function responsable()
    {
        return $this->belongsTo('App\User', 'responsable_id');
    }

    public function cuentas(){
        return $this->belongsTo('App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancariaCuentas','id');
    }

    public function cheques_mano(){
        return $this->hasMany(ConciliacionBancariaCuentas::class,'conciliacion_id');
    }

    public function cuentas_temporales() {
        return $this->hasMany(ComprobanteIngresoTemporalConciliacion::class, 'conciliacion_id');
    }

    public function conciliaciones_anteriores(){
        return $this->hasMany(ConciliacionBancaria::class, 'puc_id', 'puc_id')->where('mes', '<', $this->mes);
    }

    public function conciliacion_anterior(){
        return $this->hasOne(ConciliacionBancaria::class, 'puc_id', 'puc_id')->where('mes', $this->mes -1);
    }

    public function getSaldoLibrosAttribute(){
        return !is_null($this->conciliacion_anterior) ? $this->conciliacion_anterior->saldo_siguiente : $this->puc->validateBeforeMonths(\Carbon\Carbon::today()->format('Y').'-'.$this->mes."-01", $this->puc);
    }
    
    public function getSaldoSiguienteAttribute(){
        return $this->saldo_libros + $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('debito') - $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('credito');
    }
    
    public function getSaldoFinalAttribute(){
      // return 0;
        return $this->saldo_inicial + $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('debito') - $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('credito') + $this->cuentas_temporales->filter(function($e){return $e->check;})->sum('comprobante_ingreso_temporal.valor');
    }
    
    public function getSaldoInicialAttribute(){
        return !is_null($this->conciliacion_anterior) ? $this->conciliacion_anterior->subTotBancoFinal : $this->subTotBancoInicial;
    } 
}
