<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaEmpleadoNomina extends Model
{
    protected $fillable = [
        'nomina_empleado_id','dias_laborados','horas_extras','horas_extras_festivos', 'horas_extras_nocturnas', 'recargos_nocturnos','sueldo', 'bonificacion_direccion', 'bonificacion_servicios', 'bonificacion_recreacion',
         'prima_antiguedad', 'nomina_id', 'tiene_eps'
    ];

    protected $appends = ['v_dias_laborados'];

    private $salario_minimo = 1160000;

    public function nomina(){
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function descuentos(){
        return $this->hasMany(NominaEmpleadoDescuentos::class, 'nomina_empleado_nomina_id');
    }

    public function empleado(){
        return $this->belongsTo(NominaEmpleado::class, 'nomina_empleado_id');
    }

    public function getFspAttribute(){
        return $this->nomina->tipo == 'empleado' && $this->v_ibc >= $this->salario_minimo * 4 ? $this->v_ibc * 0.01 : 0;
    }

    public function getVDiasLaboradosAttribute(){
        $v_dias = $this->sueldo/30;
        return $this->nomina->tipo == 'empleado' ? $this->dias_laborados * $v_dias : 30 * $v_dias;
    }

    public function getVHorasExtrasAttribute(){
        $v_horas = $this->sueldo/240;
        return  $v_horas * 1.25 * $this->horas_extras;
    }

    public function getVHorasExtrasFestivosAttribute(){
        $v_horas = $this->sueldo/240;
        return  $v_horas * 1.75 * $this->horas_extras_festivos;
    }

    public function getVHorasExtrasNocturnasAttribute(){
        $v_horas = $this->sueldo/240;
        return  $v_horas * 1.35 * $this->horas_extras_nocturnas;
    }

    public function getVRecargosNocturnosAttribute(){
        $v_horas = $this->sueldo/240;
        return $v_horas * 2 * $this->recargos_nocturnos;
    }

    public function getVBonificacionServiciosAttribute(){
        return  $this->sueldo * ($this->bonificacion_servicios/100);
    }

    public function getVBonificacionRecreacionAttribute(){
        return  $this->sueldo * ($this->bonificacion_recreacion/100);
    }

    public function getVPrimaAntiguedadAttribute(){
        return  $this->sueldo * ($this->prima_antiguedad/100);
    }

    public function getVRetroactivoAttribute(){
        return 0;
    }

    public function getDevengadoAttribute(){
        return $this->TotalDevengado - $this->v_dias_laborados;
    }

    public function getVIbcAttribute(){
        return $this->v_dias_laborados + $this->v_horas_extras + $this->v_horas_extras_festivos + $this->v_horas_extras_nocturnas 
        + $this->v_recargos_nocturnos + $this->v_prima_antiguedad + $this->v_bonificacion_servicios + $this->v_retroactivo;
    }


    public function getTotalDevengadoAttribute(){
        return $this->v_ibc + $this->bonificacion_direccion + $this->v_bonificacion_recreacion;
    }

    public function getVSaludAttribute() {
        $porc = 0.04;
        if($this->nomina->tipo == 'pensionado'){
            if($this->ibc <= $this->salario_minimo ){
                $porc = 0.04;
            }else if($this->ibc > $this->salario_minimo && $this->ibc <= ($this->salario_minimo*2)){
                $porc = 0.1;
            }else{
                $porc = 0.12;
            }
        }
        return collect([
            "empleador" => $this->v_ibc * 0.085,
            "empleado" => $this->v_ibc * $porc
        ]);
    }

    public function getVSaludEmpleadorAttribute() {
        return $this->v_ibc * 0.085;
    }

    public function getVPensionAttribute() {
        if($this->nomina->tipo == 'pensionado'):
            return collect([
                "empleador" => 0,
                "empleado" => 0
            ]);
        else:
            return collect([
                "empleador" => strtolower($this->empleado->cargo) == "bombero" ? $this->v_ibc * 0.22 : $this->v_ibc * 0.12,
                "empleado" => $this->v_ibc * 0.04
            ]);
        endif;
    }

    public function getVPensionEmpleadorAttribute() {
        if($this->nomina->tipo == 'pensionado'):
            return 0;
        else:
            return strtolower($this->empleado->cargo) == "bombero" ? $this->v_ibc * 0.22 : $this->v_ibc * 0.12;
        endif;
    }

    public function getVRiesgosAttribute(){
        $porc = [0,0.522, 1.044, 2.436, 4.350, 6.960];
        return 0;
    }

    public function getVCajaCompensacionAttribute(){
        return $this->v_ibc * 0.04;
    }

    public function getVSenaAttribute(){
        return $this->v_ibc * 0.005;
    }

    public function getVIcbfAttribute(){
        return $this->v_ibc * 0.03;
    }

    public function getVEsapAttribute(){
        return $this->v_ibc * 0.005;
    }

    public function getVMenAttribute(){
        return $this->v_ibc * 0.01;
    }

    public function getTotalDeduccionAttribute() {
        return $this->descuentos->sum('valor') + $this->v_salud['empleado'] + $this->v_pension['empleado'] + $this->fsp;
    }

    public function getNetoPagarAttribute() {
        return $this->total_devengado - $this->total_deduccion;
    }
}
