<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, Auditable 
{
    use \OwenIt\Auditing\Auditable;
    use Notifiable;
    use HasRoles;
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type_id','dependencia_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function type()
    {
        return $this->belongsTo('App\Model\Cobro\Type');
    }  

    public function dependencia()
    {
        return $this->belongsTo('App\Model\Admin\Dependencia');
    }

    public function rit()
    {
        return $this->hasOne('App\Model\Impuestos\RIT');
    }

    public function user_boss()
    {
        return $this->hasOne('App\Model\Cobro\UserBoss');
    }

    public function boss_users()
    {
        return $this->hasMany('App\Model\Cobro\UserBoss', 'boss_id');
    }

    public function CIngresos()
    {
        return $this->belongsTo('App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos');
    }

    public function pagos()
    {
        return $this->hasMany('App\Model\Impuestos\Pagos','user_id');
    }

    public function exogena()
    {
        return $this->hasMany('App\Model\Impuestos\Exogena','user_id');
    }
}
