<?php

namespace App\Model\Administrativo\GestionDocumental;

use App\Carpeta;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Documents extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'documents';
    protected $fillable = ['ff_document', 'ff_salida', 'ff_primerdbte', 'ff_segundodbte', 'ff_aprobacion', 'ff_sancion', 'ff_vence', 'cc_id', 'name', 'respuesta', 'number_doc', 'estado', 'resource_id', 'user_id', 'tercero_id'];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function carpeta(){
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function persona(){
        return $this->belongsTo('App\Model\Persona', 'persona_id');
    }

    public function resource()
    {
        return $this->belongsTo('App\Resource', 'resource_id');
    }

    public function comision()
    {
        return $this->belongsTo('App\Model\Administrativo\GestionDocumental\Comisiones','comision_id');
    }

    public function concejalesPonentes()
    {
        return $this->belongsTo('App\Model\Administrativo\GestionDocumental\Concejal','ponente_id');
    }

    public function Concejales()
    {
        return $this->belongsTo('App\Model\Administrativo\GestionDocumental\Concejal','concejal_id');
    }

    public function tercero(){
        return $this->belongsTo('App\Model\Persona', 'tercero_id');
    }

    public function getEstadoStringAttribute()
    {
        $arrayEstado = [ 'Pendiente', 'Aprobado', 'Rechazado', 'Archivado'];
        return $arrayEstado[$this->estado];
    }
}
