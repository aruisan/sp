<?php

namespace App;

use App\ComprobanteIngresoTemporal;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use Illuminate\Database\Eloquent\Model;

class ComprobanteIngresoTemporalConciliacion extends Model
{
    protected $table = "comprobante_ingreso_temporal_conciliaciones";
    protected $fillable = ['conciliacion_id', 'comprobante_ingreso_temporal_id', 'check'];

    public function conciliacion() {
        return $this->belonsgTo(ConciliacionBancaria::class, 'conciliacion_id');
    }

    public function comprobante_ingreso_temporal() {
        return $this->belongsTo(ComprobanteIngresoTemporal::class, 'comprobante_ingreso_temporal_id');
    }
}
