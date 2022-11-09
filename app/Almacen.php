<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Admin\Dependencia;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = ['codigo', 'cantidad', 'nombre_articulo', 'referencia', 'ncomin_ingreso', 'fecha_ingreso', 'valor_unitario', 'ncomin_egreso', 
                            'fecha_egreso', 'dependencia_id', 'owner_id'];

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function dependencia(){
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    } 

    public function getTotalAttribute(){
        return $this->cantidad*$this->valor_unitario;
    }

    public function getNCominIngresoFechaAttribute(){
        return "Comp {$this->ncomin_ingreso} <br> {$this->fecha_ingreso}";
    }

    public function getNCominEgresoFechaAttribute(){
        return "Comp  {$this->ncomin_egreso} <br> {$this->fecha_egreso}";
    }
}
