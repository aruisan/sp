<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class PersonalizarController extends Controller
{
    private $personificacion_id =[];

    public function start(User $usuario){
        array_push($this->personificacion_id, auth()->id());
        session(['old_personificado_id' => $this->personificacion_id]);
        auth()->loginUsingId($usuario->id);
        return redirect(route('dashboard'))->with('success', "Te has personificado como {$usuario->name}.");
    }

    public function stop(){
        auth()->loginUsingId(session('old_personificado_id')[0]);
        session()->forget('old_personificado_id');
        return redirect(route('dashboard'))->with('success', "Te has logueado con tu cuenta.");
    }

    public function users(){
        $users = User::get();
        return view('personalizacion.index', compact('users'));
    }
}
