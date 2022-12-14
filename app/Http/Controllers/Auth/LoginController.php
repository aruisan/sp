<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    protected $loginPath = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectPath()
    {
        //REDIRECCION DEL USUARIO DE IMPUESTOS
        if (auth()->user()->roles[0]->id == 4) {
            return '/impuestos';
        } elseif (auth()->user()->roles[0]->id == 6){
            return '/administrativo/impuestos/muellaje';
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

}
