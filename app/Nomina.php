<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $fillable = ['salud', 'pension', 'riesgos', 'sena','icbf','caja_compensacion','cesantias','interes_cesantias','prima_navidad','vacaciones', 'mes', 'tipo'];


    public function empleados_nominas(){
        return $this->hasMany(NominaEmpleadoNomina::class, 'nomina_id');
    }

    public function getBasicoAttribute(){
        return $this->empleados_nominas->sum('v_dias_laborados');
    }

    public function getExtrasRecargosAttribute(){
        return $this->empleados_nominas->sum('v_horas_extras') + $this->empleados_nominas->sum('v_horas_extras_festivos') 
        + $this->empleados_nominas->sum('v_horas_extras_nocturnas') +$this->empleados_nominas->sum('v_recargos_nocturnos');
    } 

    
    public function getBonificacionServiciosAttribute(){
        return $this->empleados_nominas->sum('v_bonificacion_servicios');
    }

    public function getBonificacionDireccionAttribute(){
        return $this->empleados_nominas->sum('bonificacion_direccion');
    }

    public function getBonificacionRecreacionAttribute(){
        return $this->empleados_nominas->sum('v_bonificacion_recreacion');
    }

    public function getPrimaAntiguedadAttribute(){
        return $this->empleados_nominas->sum('v_prima_antiguedad');
    }
/*
*/
    public function getNIcbfAttribute(){
        return $this->empleados_nominas->sum('v_icbf');
    }
    public function getNSenaAttribute(){
        return $this->empleados_nominas->sum('v_sena');
    }
    public function getNPensionAttribute(){
        return $this->empleados_nominas->sum('v_pension_empleador');
    }
    public function getNSaludAttribute(){
        return $this->empleados_nominas->sum('v_salud_empleador');
    }
    
    public function getNCajaCompensacionAttribute(){
        return $this->empleados_nominas->sum('v_caja_compensacion');
    }
    public function getNRiesgosAttribute(){
        return $this->empleados_nominas->sum('v_riesgos');
    }

    public function getEsapAttribute(){
        return $this->empleados_nominas->sum('v_esap');
    }
    
    public function getMenAttribute(){
        return $this->empleados_nominas->sum('v_men');
    }
    
    public function getTotalDeduccionAttribute(){
        return $this->empleados_nominas->sum('total_deduccion');
    }
 

    public function getBancosAttribute(){
        $data = [
            1854 => 0,//bogota
            1940 => 0,//popular
            1856 => 0,//davivienda
            1855 => 0, //agrario
            1858 => 0,//cooserpark
            1859 => 0,//coocasa
            1866 => 0,//juzgado
            1 => 0,
            2 => 0
        ];
        foreach($this->empleados_nominas as $movimiento):
            foreach($movimiento->descuentos as $descuento):
                $data[1] = $data[1]+1; 
                if(array_key_exists($descuento->tercero_id, $data)){
                    $data[2] = $data[2]+1; 
                    $data[$descuento->tercero_id] =  $data[$descuento->tercero_id] + $descuento->valor;
                }
            endforeach;
        endforeach;


        return $data;
    }
}


