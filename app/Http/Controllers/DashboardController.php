<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class DashboardController extends Controller
{
    public function  index()
    {
        if(!is_null(Auth::user()->route_autenticacion)){
            return redirect()->route(Auth::user()->route_autenticacion);
        }

    	if(Auth::user()->type_id > 4){
            if (auth()->user()->roles[0]->id == 9){
                return redirect('/nomina/empleados');
            } else{
                return redirect()->route('presupuesto.index');
            }
    	}else{
            if (auth()->user()->roles[0]->id == 4) {
                return redirect('/impuestos');
            } elseif (auth()->user()->roles[0]->id == 6){
                return redirect('/administrativo/impuestos/muellaje');
            } elseif (auth()->user()->id == 54){
                return redirect('/administrativo/impuestos/admin');
            } elseif (auth()->user()->roles[0]->id == 7){
                return redirect('/administrativo/contabilidad/libros');
            } elseif (auth()->user()->roles[0]->id == 8){
                return redirect('/administrativo/bancos');
            } else {
                return redirect()->route('notificaciones.index');
            }
    	}
    }
}
