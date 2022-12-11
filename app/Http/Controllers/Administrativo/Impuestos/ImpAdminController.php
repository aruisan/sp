<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Model\Impuestos\Comunicado;
use App\Model\Impuestos\Pagos;
use App\Model\Impuestos\PredialContribuyentes;
use App\Model\Impuestos\RIT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;

class ImpAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersPredial = PredialContribuyentes::all();
        $pagos = Pagos::all();
        $rits = RIT::all();
        $comunicados = Comunicado::all();
        return view('administrativo.impuestos.admin.index', compact('usersPredial','pagos','rits', 'comunicados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function makeComunicado()
    {
        $rits = RIT::all();
        return view('administrativo.impuestos.admin.createcomunicado', compact('rits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateComunicado(Request $request)
    {
        for ($i = 0; $i < count($request->users); $i++) {
            $comunicado = new Comunicado();
            $comunicado->estado = "Enviado";
            $comunicado->enviado = Carbon::today();
            $comunicado->comunicado_title = $request->titulo;
            $comunicado->comunicado_body = $request->mensaje;
            $comunicado->destinatario_id = $request->users[$i];
            $comunicado->remitente_id = auth()->user()->id;
            $comunicado->save();
        }

        Session::flash('success','Los comunicados han sido enviados exitosamente');
        return redirect('/administrativo/impuestos/comunicado/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showComunicado($id)
    {
        $comunicado = Comunicado::find($id);

        return view('administrativo.impuestos.admin.showcomunicado', compact('comunicado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
