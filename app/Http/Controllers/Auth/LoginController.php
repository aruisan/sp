<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Carbon\Carbon;
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
        if (auth()->user()->id == 3) {
            //VALIDACION PARA REDIRIGIR EL USUARIO A LOS CDPs DE LA VIGENCIA ACTUAL
            $añoActual = Carbon::now()->year;
            $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
            if ($vigens->count() == 0){
                $vigens = Vigencia::where('vigencia', $añoActual - 1)->where('tipo', 0)->where('estado', '0')->get();
            }

            $idVig = $vigens->first()->id;

            return '/administrativo/cdp/'.$idVig;
        } elseif (auth()->user()->roles[0]->id == 4) {
            //REDIRECCION DEL USUARIO DE IMPUESTOS
            return '/impuestos';
        } elseif (auth()->user()->roles[0]->id == 6){
            return '/administrativo/impuestos/muellaje';
        } elseif (auth()->user()->id == 54){
            return '/administrativo/impuestos/admin';
        } elseif (auth()->user()->roles[0]->id == 7){
            return '/administrativo/contabilidad/libros';
        } elseif (auth()->user()->roles[0]->id == 8){

            //VALIDACION PARA REDIRIGIR EL USUARIO A LOS PAGOS DE LA VIGENCIA ACTUAL

            $añoActual = Carbon::now()->year;
            $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->get();
            if ($vigens->count() == 0){
                $vigens = Vigencia::where('vigencia', $añoActual - 1)->where('tipo', 0)->where('estado', '0')->get();
            }

            $idVig = $vigens->first()->id;
            return '/administrativo/pagos/'.$idVig;
        }elseif (auth()->user()->roles[0]->id == 9){
            return '/nomina/empleados';
        }


        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

}
