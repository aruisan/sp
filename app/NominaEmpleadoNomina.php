<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominaEmpleadoNomina extends Model
{
    protected $fillable = [
        'nomina_empleado_id','dias_laborados','horas_extras','horas_extras_festivos', 'horas_extras_nocturnas', 'recargos_nocturnos','sueldo', 'bonificacion_direccion', 'bonificacion_servicios', 'bonificacion_recreacion',
         'prima_antiguedad', 'nomina_id', 'tiene_eps', 'retroactivo'
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

    public function getSueldoAttribute($value)
    {
        return $value == 0 ? $this->empleado->salario : $value;
    }

    public function getVDiaAttribute(){
        return $this->sueldo /30;
    }

    public function getVHoraAttribute(){
        return $this->v_dia/8;
    }

    public function getFspAttribute(){
        return $this->nomina->tipo == 'empleado' && $this->v_ibc >= $this->salario_minimo * 4 ? ceil(($this->v_ibc * 0.01)/100)*100 : 0;
    }

    public function getVDiasLaboradosAttribute(){
        return $this->nomina->tipo == 'empleado' ? $this->dias_laborados * $this->v_dia : 30 * $this->v_dia;
    }

    public function getVHorasExtrasAttribute(){
        return  $this->round_up($this->v_hora * 1.25 * $this->horas_extras, 100);
    }

    public function getVHorasExtrasFestivosAttribute(){
        return  $this->round_up($this->v_hora * 1.75 * $this->horas_extras_festivos, 100);
    }

    public function getVHorasExtrasNocturnasAttribute(){
        return  $this->round_up($this->v_hora * 1.35 * $this->horas_extras_nocturnas, 100);
    }

    public function getVRecargosNocturnosAttribute(){
        return $this->round_up($this->v_hora * 2 * $this->recargos_nocturnos, 100);
    }

    public function getVBonificacionServiciosAttribute(){
        return $this->round_up( $this->sueldo * ($this->bonificacion_servicios/100), 100);
    }

    public function getVBonificacionRecreacionAttribute(){
        return  $this->round_up($this->sueldo * ($this->bonificacion_recreacion/100), 100);
    }

    public function getVPrimaAntiguedadAttribute(){
        return  $this->round_up($this->sueldo * ($this->prima_antiguedad/100), 100);
    }

    public function getDevengadoAttribute(){
        return $this->round_up($this->TotalDevengado - $this->v_dias_laborados, 100);
    }

    public function getPrimaAttribute(){
        $meses_prima  = ['Junio', 'Diciembre'];
        $valor = 0;
        if(!is_null($this->empleado) && !is_null($this->nomina)){
            /*
            if(in_array($this->nomina->mes, $meses_prima) && $this->empleado->movimientos->count() > 0 && $this->nomina->tipo == 'empleado'){
                $movimientos = $this->empleado->movimientos->filter(function($e){ return !is_null($e->nomina); });
                $promedio_movimientos = $movimientos->sum('sueldo')/$movimientos->count();
                $valor = ($promedio_movimientos * 180) / 360;
            }
            */
            if(in_array($this->nomina->mes, $meses_prima)){
                return $this->empleado->v_prima; 
            }
        }
        return $this->round_up($valor, 100);           
    }

    public function getVIbcAttribute(){
        return $this->nomina->tipo == 'pensionado' ? $this->sueldo : $this->v_dias_laborados + $this->v_horas_extras + $this->v_horas_extras_festivos + $this->v_horas_extras_nocturnas 
        + $this->v_recargos_nocturnos + $this->v_bonificacion_servicios + $this->v_prima_antiguedad + $this->retroactivo + $this->prima;//810000
    }


    public function getTotalDevengadoAttribute(){
        return $this->v_ibc + $this->bonificacion_direccion + $this->v_bonificacion_recreacion + $this->total_vacaciones;
    }

    public function getPorcSaludAttribute(){
        $porc = 0.04;
        if($this->nomina->tipo == 'pensionado'){
            if($this->v_ibc <= $this->salario_minimo ){
                $porc = 0.04;
            }else if($this->v_ibc > $this->salario_minimo && $this->v_ibc <= $this->salario_minimo*2){
                $porc = 0.1;
            }else{
                $porc = 0.12;
            }
        }

        return $porc;
    }

    public function getVSaludAttribute() {
       
        return collect([
            "empleador" => $this->round_up($this->v_ibc * 0.085, 100),
            "empleado" => $this->round_up($this->v_ibc * $this->porc_salud , 100)
        ]);
    }

    public function getVSaludEmpleadorAttribute() {
        return $this->round_up($this->v_ibc * 0.085, 100);
    }

    public function getVPensionAttribute() {
        if($this->nomina->tipo == 'pensionado'):
            return collect([
                "empleador" => 0,
                "empleado" => 0
            ]);
        else:
            return collect([
                "empleador" => $this->round_up(strtolower($this->empleado->cargo) == "bombero" ? $this->v_ibc * 0.22 : $this->v_ibc * 0.12, 100),
                "empleado" => $this->round_up($this->v_ibc * 0.04, 100)
            ]);
        endif;
    }

    public function getVPensionEmpleadorAttribute() {
        if($this->nomina->tipo == 'pensionado'):
            return 0;
        else:
            return $this->round_up(strtolower($this->empleado->cargo) == "bombero" ? $this->v_ibc * 0.22 : $this->v_ibc * 0.12, 100);
        endif;
    }

    public function getVRiesgosAttribute(){
        $porc = [0,0.522, 1.044, 2.436, 4.350, 6.960];
        return [
            'valor' => $this->round_up($this->empleado->porc_riesgos > 0 ? $this->sueldo * $porc[$this->empleado->porc_riesgos - 1] : 0, 100),
            'porc' => $this->round_up($this->empleado->porc_riesgos > 0 ? $porc[$this->empleado->porc_riesgos - 1] : 0, 100)
        ];
    }

    public function getVCajaCompensacionAttribute(){
        return $this->round_up($this->v_ibc * 0.04, 100);
    }

    public function getVSenaAttribute(){
        return $this->round_up($this->v_ibc * 0.005, 100);
    }

    public function getVIcbfAttribute(){
        return $this->round_up($this->v_ibc * 0.03, 100);
    }

    public function getVEsapAttribute(){
        return $this->round_up($this->v_ibc * 0.005, 100);
    }

    public function getVMenAttribute(){
        return $this->round_up($this->v_ibc * 0.01, 100);
    }

    public function getTotalDeduccionAttribute() {
        $descuentos = $this->descuentos->count() > 0 ? array_sum($this->descuento_x_entidad) : 0;
        return ceil( $descuentos+ $this->v_salud['empleado'] + $this->v_pension['empleado'] + $this->fsp + $this->retencion_fuente  + $this->descuento_reintegro);
    }

    public function getNetoPagarAttribute() {
        return ceil($this->total_devengado - $this->total_deduccion);
    }

    public function getRetencionFuenteAttribute(){
        return $this->round_up($this->sueldo > 7000000 ? $this->v_ibc * 0.025 : 0, 100);
    }

    //atributos vacaciones
    public function getVDiarioVacacionesAttribute(){
        return $this->round_up((($this->vacaciones_basicas + $this->vacaciones_prima_servicios)/2)/30, 100);
    }
    
    public function getBSAttribute(){
        return $this->round_up($this->sueldo <= 1901879 ? $this->sueldo * 0.5 : $this->sueldo * 0.35, 100);
    }

    public function getPSAttribute(){
        return $this->round_up(($this->sueldo + $this->v_prima_antiguedad + ($this->b_s/12))/2, 100);
    }
    
    //pv
    public function getVPrimaVacacionesAttribute(){
        return $this->round_up(!is_null($this->ind_vac) ? ($this->sueldo + $this->v_prima_antiguedad + ($this->b_s/12)+($this->p_s/12))/30*15 : 0, 100);
    }

    //vac
    public function getVVacacionesAttribute(){
        return $this->round_up((($this->b_s/12)+($this->p_s/12))/30*$this->dias_vacaciones, 100);
    }

    //ind
    public function getVIndAttribute(){
        return $this->round_up(($this->sueldo + $this->v_prima_antiguedad + ($this->p_s/12)+($this->b_s/12))/30*$this->dias_vacaciones_laborados, 100);
    }

    public function getTotalVacacionesAttribute(){
        return $this->round_up($this->ind_vac == 'vacaciones' ? $this->v_vacaciones+$this->v_prima_vacaciones  : $this->v_prima_vacaciones+$this->v_ind , 100);
    }

    public function getDescuentoXEntidadAttribute(){
        $entidad_nombre = ['Popular', 'Bogota', 'Agrario', 'Coosepark', 'Davivienda', 'Judicial', 'Coocasa', 'Sindicato'];
        $entidad_id = [1940, 1854, 1855, 1858, 1856, 1866, 1859, 2129];
        $data = [0,0,0,0,0,0,0,0];
        foreach($this->descuentos as $descuento):
            if(in_array($descuento->tercero_id, $entidad_id)):
                $index = array_search($descuento->tercero_id, $entidad_id);
                $data[$index] = $descuento->valor;
            endif;
        endforeach;
        return $data;
    }

    public function round_up($v, $f){

        $a = ceil($v/$f);
        return $v != 0 ? $a *$f : 0;
    }
}
