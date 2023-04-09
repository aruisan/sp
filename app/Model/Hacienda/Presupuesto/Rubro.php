<?php

namespace App\Model\Hacienda\Presupuesto;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Rubro extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function dependencia(){
		return $this->hasOne('App\Model\Dependencia','id', 'dependencia_id');
	}

	public function vigencia(){
		return $this->belongsTo('App\Model\Hacienda\Presupuesto\Vigencia', 'vigencia_id');
	}

	public function register() {
	  return $this->hasOne('App\Model\Hacienda\Presupuesto\Register', 'id', 'register_id');
	  //va la clase que lo relaciona el id de la tabla y la llave foranea
	}

	public function fontsRubro(){
		return $this->hasMany('App\Model\Hacienda\Presupuesto\FontsRubro','rubro_id');
	}

    public function subProyecto() {
        return $this->hasOne('App\Model\Planeacion\Pdd\SubProyecto', 'id', 'subproyecto_id');
    }

    public function rubrosCdp(){
        return $this->hasMany('App\Model\Administrativo\Cdp\RubrosCdp','rubro_id');
    }

    public function cdpRegistroValor(){
        return $this->hasMany('App\Model\Administrativo\Registro\CdpsRegistroValor','rubro_id');
    }

    public function rubrosMov(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\RubrosMov','rubro_id');
    }

    public function pago(){
        return $this->hasMany('App\Model\Administrativo\Pago\PagoRubros','rubro_id');
    }

    public function codeCo(){
        return $this->hasOne('App\Model\Hacienda\Presupuesto\Informes\CodeContractuales', 'id', 'code_contractuales_id');
    }

    public function compIng(){
        return $this->hasMany('App\Model\Administrativo\ComprobanteIngresos\CIRubros','rubro_id');
    }

    public function pac(){
        return $this->hasOne('App\Model\Administrativo\Tesoreria\Pac','rubro_id', 'id');
    }

    public function cpcs(){
        return $this->hasMany('App\Model\Hacienda\Presupuesto\CpcsRubro','rubro_id');
    }

    public function bpin(){
        return $this->hasOne('App\BPin','id', 'rubro_id');
    }

    public function plantilla_cuipo(){
        return $this->belongsTo(PlantillaCuipoEgresos::class, 'plantilla_cuipos_id');
    }

    /////////////////////////////////////////////////////////////////

    public function getRubroFuentePInicialAttribute(){
        return $this->fontsRubro->sum('valor');    
    }

    public function getRubroFuenteMovimientoCreditoSumaAttribute(){
        return $this->fontsRubro->sum('movimiento_credito_suma');
    }

    public function getRubroFuenteMovimientoContraCreditoSumaAttribute(){
        return $this->fontsRubro->sum('movimiento_contra_credito_suma');
    }

    public function getRubroFuenteMovimientoAdicionSumaAttribute(){
        return $this->fontsRubro->sum('movimiento_contra_credito_suma');
    }

    public function getRubroFuenteMovimientoReduccionSumaAttribute(){
        return $this->fontsRubro->sum('movimiento_reduccion_suma');
    }

    public function getRubroFuentePDefinitivoAttribute(){
        return $this->fontsRubro->sum('p_definitivo');
    }

    

    public function getFormatAttribute(){
        $tr  = "<tr>
                    <td>{$this->plantilla_cuipo->code}-r{$this->id}</td>
                    <td>{$this->name}</td>
                    <td></td>
                    <td>{$this->rubro_fuente_p_inicial}</td>
                    <td>{$this->rubro_fuente_movimiento_adicion_suma}</td>
                    <td>{$this->rubro_fuente_movimiento_reduccion_suma}</td>
                    <td>{$this->rubro_fuente_movimiento_credito_suma}</td>
                    <td>{$this->rubro_fuente_movimiento_contra_credito_suma}</td>
                    <td>{$this->rubro_fuente_p_definitivo}</td>
                </tr>";


              /*
               $tr  = "<tr>
                    <td>{$this->cod}</td>
                    <td>{$this->name}</td>
                    <td>{$this->fontsRubro->sum('valor')}</td>
                    <td>cero</td>
                    <td>cero</td>";
                 // <td>{$this->fontsRubro->sum('movimiento_contra_credito_suma')}</td>
                 //   <td>{$this->fontsRubro->sum('movimiento_credito_suma')}</td>
        $tr .=   "<td>{$this->fontsRubro->sum('movimiento_adicion_suma')}</td>
                    <td>{$this->fontsRubro->sum('movimiento_reduccion_suma')}</td>
                    <td>{$this->fontsRubro->sum('movimiento_suma')}</td>
                    <td>{$this->fontsRubro->sum('p_definitivo')}</td>
                </tr>";
              */  

        foreach($this->fontsRubro as $fr ):
            $tr .= $fr->format;
        endforeach;

        return $tr;            
    }

}
