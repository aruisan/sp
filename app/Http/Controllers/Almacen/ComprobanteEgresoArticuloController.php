<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\AlmacenComprobanteEgreso;
use App\Model\Admin\Dependencia;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\User;
use Session;


class ComprobanteEgresoController extends Controller
{
    public function autorizar_dependencia(AlmacenArticuloSalida $articulo_salida){

        $users = User::get()
                    ->filter(function($u){ return count($u->getRoleNames()->toArray()) > 0;})
                    ->filter(function($e){return in_array('Secretaria', $e->getRoleNames()->toArray()); });

        if($users > 0){
            $articulo_salida->cantidad = $request->cantidad;
            $articulo_salida->status = json_encode(array_push($articulo_salida->status, $request->estado));
            $articulo_salida->observacion = json_encode(array_push($articulo_salida->obsevacion, $request->estado ? '' : $request->observacion));
            $articulo_salida->save();
        }else{
            Session::flash('error','No existen usuarios con el rol secretaria.');
        }

        return back();
    }

    public function autorizar_secretaria(AlmacenArticuloSalida $articulo_salida){
        $users = User::get()
        ->filter(function($u){ return count($u->getRoleNames()->toArray()) > 0;})
        ->filter(function($e){return in_array('Almacenista', $e->getRoleNames()->toArray()); });

        if($users > 0){
            $articulo_salida->status = json_encode(array_push($articulo_salida->status, $request->estado));
            $articulo_salida->observacion = json_encode(array_push($articulo_salida->obsevacion, $request->estado ? '' : $request->observacion));
            $articulo_salida->save();
        }else{
            Session::flash('error','No existen usuarios con el rol Almacenista.');
        }
        return back();
    }

    public function autorizar_almacenista(AlmacenArticuloSalida $articulo_salida){
        $articulo_salida->status = json_encode(array_push($articulo_salida->status, $request->estado));
        $articulo_salida->observacion = json_encode(array_push($articulo_salida->obsevacion, $request->estado ? '' : $request->observacion));
        $articulo_salida->save();

        return back();
    }
}
