<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type_id', 'dependencia_id', 'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function dependencia()
    {
        return $this->belongsTo('App\Model\Admin\Dependencia');
    }    
    public function type()
    {
        return $this->belongsTo('App\Model\Cobro\Type');
    }    

    public function user_boss()
    {
        return $this->hasOne('App\Model\Cobro\UserBoss');
    }

    public function boss_users()
    {
        return $this->hasMany('App\Model\Cobro\UserBoss', 'boss_id');
    }

    public function rol()
    {
        return $this->hasOne('App\Model\Admin\ModelHasRol', 'model_id');
    }

    public function isCobrocoactivo(){

        if ($this->type != null) {
             switch ($this->type->id) {
                case '1':
                        $value = true;
                    break;    
                case '2':
                        $value = true;
                    break;   
                case '3':
                        $value = true;
                    break;          
                default:
                        $value = false;
                    break;

             }

            return $value;
        }
        // else{
        //     return false;
        // }
    }    

    public function validar_cargo($cargo){
        return in_array($cargo, auth()->user()->getRoleNames()->toArray());
    }
}
