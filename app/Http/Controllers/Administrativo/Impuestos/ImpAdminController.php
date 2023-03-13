<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Impuestos\Comunicado;
use App\Model\Impuestos\ImpSalarioMin;
use App\Model\Impuestos\ImpUVT;
use App\Model\Impuestos\Pagos;
use App\Model\Impuestos\Predial;
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
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) {
                if ($cuenta->code == '1110050122' or $cuenta->code == '1110900067'){
                    $result[] = $cuenta;
                }
            }
        }
        $año = Carbon::today()->year;
        $uvts = ImpUVT::all();
        $smls = ImpSalarioMin::all();
        $pagosFinalizados = Pagos::where('estado','Pagado')->where('download', 1)->where('modulo','PREDIAL')->whereBetween('fechaPago',array($año.'-01-01', $año.'-12-31'))->with('user')->get();
        foreach ($pagosFinalizados as $pago){
            $pago->impPred = Predial::find($pago->entity_id);
            $pago->contribuyente = PredialContribuyentes::find($pago->impPred->imp_pred_contri_id);
        }
        return view('administrativo.impuestos.admin.index', compact('usersPredial','pagos','rits', 'comunicados','result'
        ,'pagosFinalizados','uvts','smls'));
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
    public function editUser($id)
    {
        $user = PredialContribuyentes::find($id);

        return view('administrativo.impuestos.admin.userpredial.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $user = PredialContribuyentes::find($id);
        $user->email = $request->correo;
        $user->dir_predio = $request->dirPred;
        $user->otra_red= $request->otraRed;
        $user->dir_notificacion = $request->dirNoti;
        $user->municipio = $request->municipio;
        $user->whatsapp = $request->whatsapp;
        $user->facebook = $request->facebook;
        $user->cedCatastral = $request->cedCatastral;
        $user->matInmobiliaria = $request->matInmobiliaria;
        $user->save();

        Session::flash('success','El usuario '.$user->contribuyente.' se ha actualziado exitosamente');
        return redirect('/administrativo/impuestos/admin');
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
