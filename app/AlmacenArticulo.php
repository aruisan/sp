<?php

namespace App;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use Illuminate\Database\Eloquent\Model;

class AlmacenArticulo extends Model
{
    protected $fillable = ['codigo', 'cantidad', 'nombre_articulo', 'referencia',  'valor_unitario',  'estado', 'tipo', 'almacen_comprobante_ingreso_id','ccd','marca', 'presentacion', 'vida_util'];
    protected $appends = ['stock'];

    public function comprobante_ingreso(){
        return $this->belongsTo(AlmacenComprobanteIngreso::class, 'almacen_comprobante_ingreso_id');
    }

    public function comprobante_egresos(){
        return $this->belongsToMany(AlmacenComprobanteEgreso::class, 'almacen_articulo_salidas')->withPivot('cantidad');
    }

    public function articulos_salida(){
        return $this->hasMany(AlmacenArticuloSalida::class, 'almacen_articulo_id');
    }

    public function mantenimientos(){
        return $this->hasMany(AlmacenArticuloMantenimiento::class, 'almacen_articulo_id');
    }
    
    public function getTotalAttribute(){
        return $this->cantidad * $this->valor_unitario;
    }

    public function getStockAttribute(){
        return  $this->cantidad - $this->articulos_salida->sum('cantidad');
    }

    public function puc_ccd(){
        return $this->belongsTo(PucAlcaldia::class, 'ccd');
    }

    public function fechas_depreciacion(){
        $hoy = \Carbon\Carbon::now();
        $fecha_final = $this->created_at->addYear($this->vida_util);
        $dias_vida_util_total = $this->created_at->diffInDays($fecha_final);
        $dias_vida_util_restantes = $hoy->diffInDays($fecha_final);
        $dias_vida_util = $dias_vida_util_total -$dias_vida_util_restantes;
        return collect([
            'hoy' => $hoy,
            'fecha_final' => $fecha_final,
            'dias_vida_util_total' => $dias_vida_util_total,
            'dias_vida_util' => $dias_vida_util,
            'dias_vida_util_restantes' => $dias_vida_util_restantes
        ]);
    }

    public function getDepreciacionAttribute(){
        $result = 0;
        if($this->vida_util > 0):
            $valor_unitario_depreciacion_x_dia = $this->valor_unitario/$this->fechas_depreciacion()['dias_vida_util_total'];
            $result = $valor_unitario_depreciacion_x_dia * $this->fechas_depreciacion()['dias_vida_util'];
        endif;

        return round($result);
    }


}
