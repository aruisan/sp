<?php

namespace App\Http\Controllers\Impuestos\RIT;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\Ciuu;
use App\Model\Impuestos\RitActividades;
use App\Model\Impuestos\RitEstablecimientos;
use App\Model\User;
use App\Resource;
use App\Traits\ResourceTraits;
use Illuminate\Http\Request;
use App\Model\Impuestos\RIT;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $ciius = Ciuu::all();
        return view('impuestos.rit.create', compact('action','ciius'));
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
            $RIT->otrasClasesContribuyente = $request->otrasClasesContribuyente;
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

            if ($request->hasFile('fileRUT')){
                $file = new ResourceTraits;
                $RUT = $file->resource($request->fileRUT, 'public/RIT');
                $RIT->rut_resource_id = $RUT;
            }

            if($request->hasFile('fileCC')) {
                $file = new ResourceTraits;
                $CC = $file->resource($request->fileCC, 'public/RIT');
                $RIT->cc_resource_id = $CC;
            }
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
            if (count($request->codCIIU) > 0){
                for ($i = 0; $i <= count($request->codCIIU) -1; $i++) {
                    $actividades = new RitActividades();
                    $actividades->rit_id = $RIT->id;
                    $actividades->codCIIU = $request->codCIIU[$i];
                    $actividades->save();
                }
            }
            Session::flash('success','RIT registrado exitosamente.');
        }
        if ($request->opciondeUso == "Actualización"){
            $RIT = RIT::find($request->rit_id);
            $RIT->opciondeUso = $request->opciondeUso;
            $RIT->claseContribuyente = $request->claseContribuyente;
            $RIT->otrasClasesContribuyente = $request->otrasClasesContribuyente;
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

            if ($request->hasFile('fileRUT')){
                if ($RIT->ResourceRUT){
                    $fileOld = Resource::find($RIT->ResourceRUT)->first();
                    Storage::delete($fileOld->ruta);
                    $fileOld->delete();
                }

                $file = new ResourceTraits;
                $RUT = $file->resource($request->fileRUT, 'public/RIT');
                $RIT->rut_resource_id = $RUT;
            }

            if($request->hasFile('fileCC')) {
                if ($RIT->ResourceCC){
                    $fileOld = Resource::find($RIT->ResourceCC)->first();
                    Storage::delete($fileOld->ruta);
                    $fileOld->delete();
                }

                $file = new ResourceTraits;
                $CC = $file->resource($request->fileCC, 'public/RIT');
                $RIT->cc_resource_id = $CC;
            }
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
            if ($request->codCIIU){
                for ($i = 0; $i <= count($request->codCIIU) -1 ; $i++) {
                    $actividades = new RitActividades();
                    $actividades->rit_id = $request->rit_id;
                    $actividades->codCIIU = $request->codCIIU[$i];
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
        $ciius = Ciuu::all();
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        if ($rit->ResourceRUT) $rit->rutaFileRUT = $rit->ResourceRUT->ruta;
        else $rit->rutaFileRUT = null;
        if ($rit->ResourceCC)  $rit->rutaFileCC = $rit->ResourceCC->ruta;
        else $rit->rutaFileCC = null;
        $actividades = $rit->actividades;
        foreach ($actividades as $actividad){
            $ciuu = Ciuu::find($actividad->codCIIU);
            $actividad['code'] = $ciuu->code_ciuu;
            $actividad['description'] = $ciuu->description;
        }
        $establecimientos = $rit->establecimientos;
        return view('impuestos.rit.create', compact('action','rit','actividades','establecimientos','ciius'));
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
