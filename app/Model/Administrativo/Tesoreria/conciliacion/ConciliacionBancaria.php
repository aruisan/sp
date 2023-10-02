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

    protected $appends = ['saldo_libros'];

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

    public function getDataAttribute(){
        $libro_debito = 0;
        $libro_credito = 0;
        $banco_debito = 0;
        $banco_credito = 0;
        $banco_diferencia = 0;
        $banco_credito_anterior = 0;
        $banco_diferencia_anterior = 0;

        $data_cheque_mano = $this->cheques_mano;
        $data_mano_select = $this->cheques_mano->filter(function($c){ return $c->aprobado == "ON";});
        $data_mano_no_select = $this->cheques_mano->filter(function($c){ return $c->aprobado == "OFF";});
        $data_cobro_select =  $this->cuentas_temporales->filter(function($e){ return $e->check;});
        $data_cobro_no_select =  $this->cuentas_temporales->filter(function($e){ return !$e->check;});

        foreach($data_cheque_mano as $a):
            $libro_credito += $a->credito;
            $libro_debito += $a->debito;
        endforeach;

        foreach($data_mano_select as $a):
            $banco_credito += $a->credito;
            $banco_debito += $a->debito;
        endforeach;

        foreach($data_mano_no_select as $a):
            $banco_diferencia += $a->credito;
        endforeach;

        foreach($data_cobro_select as $a):
            $banco_credito_anterior += $a->comprobante_ingreso_temporal->valor;
        endforeach;

        foreach($data_cobro_no_select as $a):
            $banco_diferencia_anterior += $a->comprobante_ingreso_temporal->valor;
        endforeach;

        return [
                'libro_debito' => $libro_debito,
                'libro_credito' => $libro_credito,
                'banco_debito' => $banco_debito,
                'banco_credito' => $banco_credito,
                'banco_diferencia' => $banco_diferencia,
                'banco_credito_anterior' => $banco_credito_anterior,
                'banco_diferencia_anterior' => $banco_diferencia_anterior
            ];
    }

    public function getDiferenciaAttribute() {
        return $this->data['$banco_diferencia'] + $this->data['$banco_diferencia_anterior'];
    } 

    public function getSaldoLibrosAttribute(){
       // return $this->conciliacion_anterior;
       // return [!is_null($this->conciliacion_anterior)];
       return !is_null($this->conciliacion_anterior) ? $this->conciliacion_anterior->saldo_siguiente : $this->puc->validateBeforeMonths(\Carbon\Carbon::today()->format('Y').'-'.$this->mes."-01", $this->puc);
    }
    
    public function getSaldoSiguienteAttribute(){
        return $this->saldo_libros + $this->data['libro_debito'] - $this->data['libro_credito'];
        //return $this->saldo_libros + $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('debito') - $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('credito');
    }
    
    public function getSaldoFinalAttribute(){
        return $this->saldo_inicial + $this->data['banco_debito'] - $this->data['banco_credito'] - $this->data['banco_credito_anterior'];
       // return $this->saldo_inicial + $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('debito') - $this->cheques_mano->filter(function($e){ return $e->aprobado == 'ON'; })->sum('credito') + $this->cuentas_temporales->filter(function($e){return $e->check;})->sum('comprobante_ingreso_temporal.valor');
    }
    
    public function getSaldoInicialAttribute(){
        return !is_null($this->conciliacion_anterior) ? $this->conciliacion_anterior->subTotBancoFinal : $this->subTotBancoInicial;
    } 
}
