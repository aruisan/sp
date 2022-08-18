<?php

namespace App\Http\Controllers\Impuestos\RIT;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\RitActividades;
use App\Model\Impuestos\RitEstablecimientos;
use App\Model\User;
use Illuminate\Http\Request;
use App\Model\Impuestos\RIT;

use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;

class RitController extends Controller
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
     * Show the form of creation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action = "Inscripción";
        return view('impuestos.rit.create', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->opciondeUso == "Inscripción"){
            $RIT = new RIT();
            $RIT->user_id = Auth::user()->id;
            //I. ENCABEZADO
            $RIT->opciondeUso = $request->opciondeUso;
            $RIT->claseContribuyente = $request->claseContribuyente;
            $RIT->nameRevFisc = $request->nameRevFisc;
            $RIT->idRevFisc = $request->idRevFisc;
            $RIT->TPRevFisc = $request->TPRevFisc;
            $RIT->emailRevFisc = $request->emailRevFisc;
            $RIT->movilRevFisc = $request->movilRevFisc;
            $RIT->nameCont = $request->nameCont;
            $RIT->idCont = $request->idCont;
            $RIT->TPCont = $request->TPCont;
            $RIT->emailCont = $request->emailCont;
            $RIT->movilCont = $request->movilCont;
            //II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR
            $RIT->tipoDocContri = $request->tipoDocContri;
            $RIT->numDocContri = $request->numDocContri;
            $RIT->DVDocContri = $request->DVDocContri;
            $RIT->natJuridiContri = $request->natJuridiContri;
            $RIT->tipSociedadContri = $request->tipSociedadContri;
            $RIT->tipEntidadContri = $request->tipEntidadContri;
            $RIT->claEntidadContri = $request->claEntidadContri;
            $RIT->apeynomContri = $request->apeynomContri;
            if ($request->avisos == "on") $RIT->avisos = true;
            else $RIT->avisos = false;
            $RIT->dirNotifContri = $request->dirNotifContri;
            $RIT->barrioContri = $request->barrioContri;
            $RIT->ciudadContri = $request->ciudadContri;
            $RIT->telContri = $request->telContri;
            $RIT->webPageContri = $request->webPageContri;
            $RIT->movilContri = $request->movilContri;
            $RIT->emailContri = $request->emailContri;
            //III. REPRESENTACIÓN LEGAL
            $RIT->nombreRepLegal = $request->nombreRepLegal;
            $RIT->TDRepLegal = $request->TDRepLegal;
            $RIT->IDNumRepLegal = $request->IDNumRepLegal;
            $RIT->CRRepLegal = $request->CRRepLegal;
            $RIT->emailRepLegal = $request->emailRepLegal;
            $RIT->telRepLegal = $request->telRepLegal;
            $RIT->nombreRepLegal2 = $request->nombreRepLegal2;
            $RIT->TDRepLegal2 = $request->TDRepLegal2;
            $RIT->IDNumRepLegal2 = $request->IDNumRepLegal2;
            $RIT->CRRepLegal2 = $request->CRRepLegal2;
            $RIT->emailRepLegal2 = $request->emailRepLegal2;
            $RIT->telRepLegal2 = $request->telRepLegal2;
            $RIT->radicacion = Carbon::today();
            $RIT->save();

            //TABLA IV. DATOS DE ESTABLECIMIENTOS DE COMERCIO UBICADOS EN PROVIDENCIA
            if (count($request->nombre) > 0){
                for ($i = 0; $i <= count($request->nombre) -1; $i++) {
                    $establecimientos = new RitEstablecimientos();
                    $establecimientos->rit_id = $RIT->id;
                    $establecimientos->nombre = $request->nombre[$i];
                    $establecimientos->matMercantil = $request->matMercantil[$i];
                    $establecimientos->telefono = $request->telefono[$i];
                    $establecimientos->fechaInicio = $request->fechaInicio[$i];
                    $establecimientos->direccion = $request->direccion[$i];
                    $establecimientos->barrio = $request->barrio[$i];
                    $establecimientos->fechaCancel = $request->fechaCancel[$i];
                    $establecimientos->save();
                }
            }

            //TABLA V. DATOS DE ACTIVIDADES ECONÓMICAS
            if (count($request->codActividad) > 0){
                for ($i = 0; $i <= count($request->codActividad) -1; $i++) {
                    $actividades = new RitActividades();
                    $actividades->rit_id = $RIT->id;
                    $actividades->codActividad = $request->codActividad[$i];
                    $actividades->codCIIU = $request->codCIIU[$i];
                    $actividades->descripción = $request->descripción[$i];
                    $actividades->baseGravable = $request->baseGravable[$i];
                    $actividades->save();
                }
            }
            Session::flash('success','RIT registrado exitosamente.');
        }
        if ($request->opciondeUso == "Actualización"){
            $RIT = RIT::find($request->rit_id);
            $RIT->opciondeUso = $request->opciondeUso;
            $RIT->claseContribuyente = $request->claseContribuyente;
            $RIT->nameRevFisc = $request->nameRevFisc;
            $RIT->idRevFisc = $request->idRevFisc;
            $RIT->TPRevFisc = $request->TPRevFisc;
            $RIT->emailRevFisc = $request->emailRevFisc;
            $RIT->movilRevFisc = $request->movilRevFisc;
            $RIT->nameCont = $request->nameCont;
            $RIT->idCont = $request->idCont;
            $RIT->TPCont = $request->TPCont;
            $RIT->emailCont = $request->emailCont;
            $RIT->movilCont = $request->movilCont;
            //II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR
            $RIT->tipoDocContri = $request->tipoDocContri;
            $RIT->numDocContri = $request->numDocContri;
            $RIT->DVDocContri = $request->DVDocContri;
            $RIT->natJuridiContri = $request->natJuridiContri;
            $RIT->tipSociedadContri = $request->tipSociedadContri;
            $RIT->tipEntidadContri = $request->tipEntidadContri;
            $RIT->claEntidadContri = $request->claEntidadContri;
            $RIT->apeynomContri = $request->apeynomContri;
            if ($request->avisos == "on") $RIT->avisos = true;
            else $RIT->avisos = false;
            $RIT->dirNotifContri = $request->dirNotifContri;
            $RIT->barrioContri = $request->barrioContri;
            $RIT->ciudadContri = $request->ciudadContri;
            $RIT->telContri = $request->telContri;
            $RIT->webPageContri = $request->webPageContri;
            $RIT->movilContri = $request->movilContri;
            $RIT->emailContri = $request->emailContri;
            //III. REPRESENTACIÓN LEGAL
            $RIT->nombreRepLegal = $request->nombreRepLegal;
            $RIT->TDRepLegal = $request->TDRepLegal;
            $RIT->IDNumRepLegal = $request->IDNumRepLegal;
            $RIT->CRRepLegal = $request->CRRepLegal;
            $RIT->emailRepLegal = $request->emailRepLegal;
            $RIT->telRepLegal = $request->telRepLegal;
            $RIT->nombreRepLegal2 = $request->nombreRepLegal2;
            $RIT->TDRepLegal2 = $request->TDRepLegal2;
            $RIT->IDNumRepLegal2 = $request->IDNumRepLegal2;
            $RIT->CRRepLegal2 = $request->CRRepLegal2;
            $RIT->emailRepLegal2 = $request->emailRepLegal2;
            $RIT->telRepLegal2 = $request->telRepLegal2;
            $RIT->radicacion = Carbon::today();
            $RIT->save();

            //TABLA IV. DATOS DE ESTABLECIMIENTOS DE COMERCIO UBICADOS EN PROVIDENCIA
            if ($request->nombre){
                for ($i = 0;$i <= count($request->nombre) -1; $i++) {
                    $establecimientos = new RitEstablecimientos();
                    $establecimientos->rit_id = $request->rit_id;
                    $establecimientos->nombre = $request->nombre[$i];
                    $establecimientos->matMercantil = $request->matMercantil[$i];
                    $establecimientos->telefono = $request->telefono[$i];
                    $establecimientos->fechaInicio = $request->fechaInicio[$i];
                    $establecimientos->direccion = $request->direccion[$i];
                    $establecimientos->barrio = $request->barrio[$i];
                    $establecimientos->fechaCancel = $request->fechaCancel[$i];
                    $establecimientos->save();
                }
            }

            //TABLA V. DATOS DE ACTIVIDADES ECONÓMICAS
            if ($request->codActividad){
                for ($i = 0; $i <= count($request->codActividad) -1 ; $i++) {
                    $actividades = new RitActividades();
                    $actividades->rit_id = $request->rit_id;
                    $actividades->codActividad = $request->codActividad[$i];
                    $actividades->codCIIU = $request->codCIIU[$i];
                    $actividades->descripción = $request->descripción[$i];
                    $actividades->baseGravable = $request->baseGravable[$i];
                    $actividades->save();
                }
            }
            Session::flash('success','RIT actualizado exitosamente.');

        }
        if ($request->opciondeUso == "Cancelación"){
            $RIT = RIT::find($request->rit_id);
            $RIT->opciondeUso = $request->opciondeUso;
            //TABLA VI. CANCELACIÓN
            $RIT->tipoCancelacion = $request->tipoCancelacion;
            $RIT->motivCancelacion = $request->motivCancelacion;
            //TABLA VII. FIRMAS Y FECHA DE RECEPCIÓN
            $RIT->radicacion = Carbon::today();
            $RIT->save();

            Session::flash('warning','RIT cancelado exitosamente.');
        }

        return redirect('/impuestos');
    }

    /**
     * Show the form of update the RIT.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRIT()
    {
        $action = "Actualización";
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $actividades = $rit->actividades;
        $establecimientos = $rit->establecimientos;
        return view('impuestos.rit.create', compact('action','rit','actividades','establecimientos'));
    }

    /**
     * Show the form of restore the RIT.
     *
     * @return \Illuminate\Http\Response
     */
    public function restoreRIT()
    {
        $action = "Restaurar";
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $actividades = $rit->actividades;
        $establecimientos = $rit->establecimientos;
        return view('impuestos.rit.create', compact('action','rit','actividades','establecimientos'));
    }

    /**
     * Delete establecimiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteEstablecimiento($id){

        $establecimiento = RitEstablecimientos::find($id);
        $establecimiento->delete();
        Session::flash('warning', 'Establecimiento de comercio eliminado correctamente.');
    }

    /**
     * Delete actividad.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteActividad($id){

        $actividad = RitActividades::find($id);
        $actividad->delete();
        Session::flash('warning', 'Actividad economica eliminada correctamente.');
    }
}
