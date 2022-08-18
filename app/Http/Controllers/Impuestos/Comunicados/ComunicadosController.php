<?php

namespace App\Http\Controllers\Impuestos\Comunicados;

use App\Http\Controllers\Controller;
use App\Model\Impuestos\Comunicado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\User;
use Carbon\Carbon;
use Session;
use PDF;

class ComunicadosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $comunicados = Comunicado::select('id','estado','comunicado_title','enviado')->where('destinatario_id', $user->id)->orderBy('enviado','DESC')->get();

        return view('impuestos.comunicados.index', compact('comunicados'));
    }

    /**
     * Get comunicado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMessage(Request $request){
        $comunicado = Comunicado::find($request->id);
        if ($comunicado->estado == "Enviado"){
            $comunicado->estado = "Visto";
            $comunicado->visto = Carbon::now();
            $comunicado->save();
        }
        $remitente = User::find($comunicado->remitente_id);
        $comunicado->remitente = $remitente->name;
        $comunicado->enviado = Carbon::parse($comunicado->enviado)->format('d-m-Y h:m:s');
        return $comunicado;
    }
}
