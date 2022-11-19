<?php

namespace App\Http\Controllers\Administrativo\Cdp;

use App\BPin;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Administrativo\Registro\CdpsRegistro;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Administrativo\Cdp\RubrosCdp;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Model\Hacienda\Presupuesto\Font;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Register;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;
Use App\Traits\ConteoTraits;


use Session;
class CdpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $vigencia_id = $id;
        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }
        if ($rol == 2)
        {
            $cdpTarea = Cdp::where('vigencia_id', $vigencia_id)->where('secretaria_e', '0')->orWhere('jefe_e','1')->get();
            $cdProcess = Cdp::where('vigencia_id', $vigencia_id)->where('secretaria_e', '3')->where('jefe_e','0')->get();
            $cdps = Cdp::where('vigencia_id', $id)
                ->where(function ($query) {
                    $query->where('jefe_e','3')
                        ->orWhere('jefe_e','2');
                })->get();


        }elseif ($rol == 3)
        {
            $cdpTarea = Cdp::where('vigencia_id', $vigencia_id)->where('jefe_e','0')->where('alcalde_e','3')->get();
            $cdProcess = null;
            $cdps = Cdp::where('vigencia_id', $id)
                ->where(function ($query) {
                    $query->where('jefe_e','3')
                        ->orWhere('jefe_e','2');
                })->get();
        }
        elseif ($rol == 5)
        {
            //ROL DE ALCALDE
            $cdpTarea = Cdp::where('vigencia_id', $vigencia_id)->where('alcalde_e','0')->get();
            $cdProcess = null;
            $cdps = Cdp::where('vigencia_id', $id)
                ->where(function ($query) {
                    $query->where('jefe_e','3')
                        ->orWhere('jefe_e','2');
                })->get();
        }
        else
        {
            $cdpTarea = null;
            $cdps = null;
            $cdProcess = null;
        }
        //this change fix the problem with the count
        if ($cdpTarea == null){
            $cdpTarea = [];
        }
        if ($cdps == null){
            $cdps = [];
        }
        if ($cdProcess == null){
            $cdProcess = [];
        }
        return view('administrativo.cdp.index', compact('cdps','rol', 'cdpTarea','vigencia_id','cdProcess'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vigencia = $id;
        $rubros = Rubro::all();
        $dependencia = auth()->user()->dependencia_id;
        return view('administrativo.cdp.create', compact('dependencia','rubros','id', 'vigencia'));
    }

    public function anular($id, $vigen){
        $cdp = Cdp::findOrFail($id);
        $cdpsRegistro = CdpsRegistro::where('cdp_id','=',$id)->get();
        if (count($cdpsRegistro) > 0){
            Session::flash('warning', 'Tiene Registros Relacionados al CDP. Elimine los Registros Para Poder Anular el CDP');
            return redirect('/administrativo/cdp/'.$vigen.'/'.$id);
        }else{
            if ($cdp->tipo == "Funcionamiento"){
                $cdp->saldo = 0;
                $cdp->jefe_e = '2';
                $cdp->save();

                $rubrosCdp = RubrosCdpValor::where('cdp_id', $id)->get();
                foreach ($rubrosCdp as $rubroCdp){
                    $fontR = FontsRubro::findOrFail($rubroCdp->fontsRubro->id);
                    $fontR->valor_disp = $fontR->valor_disp + $rubroCdp->valor;
                    $fontR->save();
                }

                Session::flash('error','El CDP ha sido anulado');
                return redirect('/administrativo/cdp/'.$vigen.'/'.$id);
            } else{
                //ANULAR EL CDP DE INVERSION
                $cdp->saldo = 0;
                $cdp->jefe_e = '2';
                $cdp->save();

                $actividadesCdp = BpinCdpValor::where('cdp_id', $id)->get();
                foreach ($actividadesCdp as $actividadCdp){
                    $actividad = BPin::findOrFail($actividadCdp->actividad->id);
                    $actividad->saldo = $actividad->saldo + $actividadCdp->valor;
                    $actividad->save();
                }

                Session::flash('error','El CDP ha sido anulado');
                return redirect('/administrativo/cdp/'.$vigen.'/'.$id);
            }

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $countCdps = Cdp::where('vigencia_id', $request->vigencia_id)->orderBy('id')->get()->last();

        if ($countCdps == null) $count = 0;
        else $count = $countCdps->code;

        $cdp = new Cdp();
        $cdp->name = $request->name;
        $cdp->tipo = $request->tipo;
        $cdp->code = $count + 1;
        $cdp->valueControl = $request->valueControl;
        $cdp->valor = 0;
        $cdp->fecha = $request->fecha;
        $cdp->dependencia_id = $request->dependencia_id;
        $cdp->observacion = $request->observacion;
        $cdp->saldo = 0;
        $cdp->secretaria_e = $request->secretaria_e;
        $cdp->ff_secretaria_e = $request->fecha;
        $cdp->alcalde_e = '0';
        $cdp->vigencia_id = $request->vigencia_id;
        $cdp->save();

        Session::flash('success','El CDP se ha creado exitosamente');
        return redirect('/administrativo/cdp/'.$request->vigencia_id.'/'.$cdp->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cdp  $cdp
     * @return \Illuminate\Http\Response
     */
    public function show($vigencia,$id)
    {
        $roles = auth()->user()->roles;
        $user = auth()->user();
        foreach ($roles as $role) $rol= $role->id;
        $cdp = Cdp::findOrFail($id);
        $all_rubros = Rubro::where('vigencia_id',$vigencia)->get();
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                if ($rubro->tipo == "Funcionamiento") $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $vigens = Vigencia::findOrFail($vigencia);
        $V = $vigens->id;
        $vigencia_id = $V;

        $conteoTraits = new ConteoTraits;
        $conteo = $conteoTraits->conteoCdps($vigens, $cdp->id);

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if ($data->id < '324') {
            } elseif (count($rubro) > 0) {
                if($rubro[0]->fontsRubro and $rubro[0]->tipo == "Funcionamiento"){
                    //SE VALIDA QUE EL RUBRO TENGA DINERO DISPONIBLE
                    foreach ($rubro[0]->fontsRubro as $fuentes){
                        foreach ($fuentes->dependenciaFont as $fontDep){
                            if (auth()->user()->dependencia_id == $fontDep->dependencia_id) $valDisp[] = $fontDep->saldo;
                        }
                    }
                    if (isset($valDisp) and array_sum($valDisp) > 0){
                        $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
                        unset($valDisp);
                    }
                }
            }
        }

        if (!isset($infoRubro)) $infoRubro = [];

        //BPINS
        $bpins = BPin::where('rubro_id','!=',0)->where('vigencia_id',$vigencia_id)->get();
        foreach ($bpins as $bpin){
            if ($bpin->rubro_id != 0) $bpin['rubro'] = $bpin->rubro->name;
            else $bpin['rubro'] = "No";
        }

        //dd($cdp->rubrosCdp[0]->rubros->fontsRubro[0]->sourceFunding->description);

        return view('administrativo.cdp.show', compact('cdp','rubros','valores','rol','infoRubro', 'conteo', 'bpins', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cdp  $cdp
     * @return \Illuminate\Http\Response
     */
    public function edit($vigen, $cdp)
    {
        $idcdp = Cdp::find($cdp);
        $rubros = Rubro::all();
        return view('administrativo.cdp.edit', compact('idcdp','rubros','vigen'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cdp  $cdp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idcdp, $vigen)
    {
        $store= Cdp::findOrFail($idcdp);
        $store->name = $request->name;
        $store->observacion = $request->observacion;
        $store->save();

        Session::flash('success','El CDP '.$request->name.' se edito exitosamente.');
        return  redirect('../administrativo/cdp/'.$vigen);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cdp  $cdp
     * @return \Illuminate\Http\Response
     */
    public function destroy($vigen, $id)
    {
        $borrar = Cdp::find($id);
        $borrar->delete();

        Session::flash('error','CDP borrado correctamente');
        return redirect('../administrativo/cdp/'.$vigen);
    }

    public function updateEstado($id,$rol,$fecha,$valor,$estado)
    {
        $update = Cdp::findOrFail($id);
        if ($rol == 2){
            $update->secretaria_e = $estado;
            $update->alcalde_e = "0";
            $update->ff_alcalde_e = $fecha;
            $update->jefe_e = "0";
            $update->ff_secretaria_e = $fecha;
            $update->save();

            Session::flash('success','El CDP ha sido enviado exitosamente');
            return redirect('/administrativo/cdp/'.$update->vigencia_id);
        }
        if ($rol == 3) {
            if ($estado == 3) {
                if ($update->tipo == "Funcionamiento"){
                    foreach ($update->rubrosCdpValor as $fuentes) {
                        if ($fuentes->fontsRubro->valor_disp >= $fuentes->valor) {
                            $update->jefe_e = $estado;
                            $update->ff_jefe_e = $fecha;
                            $update->valor = $valor;
                            $update->saldo = $valor;

                            $this->actualizarValorRubro($id);

                            $update->save();

                            Session::flash('success', 'El CDP ha sido finalizado con exito');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id);
                        } else {
                            Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en el rubro');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                        }
                    }
                } else{
                    //VALIDACION DEL CDP CUANDO ES DE INVERSION
                    foreach ($update->bpinsCdpValor as $actividad) {
                        if ($actividad->actividad->saldo >= $actividad->valor){
                            $update->jefe_e = $estado;
                            $update->ff_jefe_e = $fecha;
                            $update->valor = $valor;
                            $update->saldo = $valor;

                            $this->actualizarValorActividad($id);

                            $update->save();

                            Session::flash('success', 'El CDP ha sido finalizado con exito');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id);
                        } else{
                            Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la actividad');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                        }
                    }
                }
            }
        }
        if ($rol == 5){
            if ($estado == 3) {
                $update->alcalde_e = $estado;
                $update->ff_alcalde_e = $fecha;
                $update->save();

                Session::flash('success','El CDP ha sido enviado al jefe exitosamente');
                return redirect('/administrativo/cdp/'.$update->vigencia_id);
            }
        }
    }

    public function rechazar(Request $request, $id, $vigen)
    {
        if ($request->rol == "3"){
            $update = Cdp::findOrFail($id);
            $update->jefe_e = "1";
            $update->secretaria_e = "0";
            $update->ff_jefe_e = $request->fecha;
            $update->alcalde_e = "0";
            $update->ff_alcalde_e = $request->fecha;
            $update->motivo = $request->motivo;
            $update->save();

            Session::flash('error','El CDP ha sido rechazado');
            return redirect('/administrativo/cdp/'.$vigen);

        } else if ($request->rol == "5"){
            $update = Cdp::findOrFail($id);
            $update->alcalde_e = "1";
            $update->secretaria_e = "0";
            $update->ff_alcalde_e = $request->fecha;
            $update->motivo = $request->motivo;
            $update->save();

            Session::flash('error','El CDP ha sido rechazado');
            return redirect('/administrativo/cdp/'.$vigen);
        }
    }

    public function actualizarValorRubro($id)
    {
        $cdp = Cdp::findOrFail($id);
        foreach ($cdp->rubrosCdpValor as $fuentes){
            $valor = $fuentes->valor;
            $total = $fuentes->fontsRubro->valor_disp - $valor;
            $fontRubro = FontsRubro::findOrFail($fuentes->fontsRubro->id);
            $fontRubro->valor_disp = $total;
            $fontRubro->save();
        }
    }

    public function actualizarValorActividad($id)
    {
        $cdp = Cdp::findOrFail($id);
        foreach ($cdp->bpinsCdpValor as $actividad){
            $valor = $actividad->valor;
            $total = $actividad->actividad->saldo - $valor;
            $bpin = BPin::findOrFail($actividad->actividad->id);
            $bpin->saldo = $total;
            $bpin->save();
        }
    }

    public function pdf($id, $vigen)
    {
        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }
        $cdp = Cdp::findOrFail($id);
        if ($cdp->secretaria_e == 3 and $cdp->jefe_e == 3 ){
            $all_rubros = Rubro::all();
            foreach ($all_rubros as $rubro){
                if ($rubro->fontsRubro->sum('valor_disp') != 0){
                    $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                    $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                    $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
                }
            }

            //codigo de rubros

            $V = $vigen;
            $vigencia_id = $V;
            $vigencia = Vigencia::find($vigencia_id);

            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            $registers = Register::where('level_id', $ultimoLevel->id)->get();
            $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
            $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
            $rubroz = Rubro::where('vigencia_id', $vigencia_id)->get();

            global $lastLevel;
            $lastLevel = $ultimoLevel->id;
            $lastLevel2 = $ultimoLevel2->level_id;
            foreach ($registers2 as $register2) {
                global $codigoLast;
                if ($register2->register_id == null) {
                    $codigoEnd = $register2->code;
                } elseif ($codigoLast > 0) {
                    if ($lastLevel2 == $register2->level_id) {
                        $codigo = $register2->code;
                        $codigoEnd = "$codigoLast$codigo";
                        foreach ($registers as $register) {
                            if ($register2->id == $register->register_id) {
                                $register_id = $register->code_padre->registers->id;
                                $code = $register->code_padre->registers->code . $register->code;
                                $ultimo = $register->code_padre->registers->level->level;
                                while ($ultimo > 1) {
                                    $registro = Register::findOrFail($register_id);
                                    $register_id = $registro->code_padre->registers->id;
                                    $code = $registro->code_padre->registers->code . $code;

                                    $ultimo = $registro->code_padre->registers->level->level;
                                }
                                if ($register->level_id == $lastLevel) {
                                    foreach ($rubroz as $rub) {
                                        if ($register->id == $rub->register_id) {
                                            $newCod = "$code$rub->cod";
                                            $infoRubro[] = collect(['id_rubro' => $rub->id, 'id' => '', 'codigo' => $newCod, 'name' => $rub->name, 'code' => $rub->code, 'last_code' => $code, 'register' => $register->name]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else {
                    $codigo = $register2->code;
                    $newRegisters = Register::findOrFail($register2->register_id);
                    $codigoNew = $newRegisters->code;
                    $codigoEnd = "$codigoNew$codigo";
                    $codigoLast = $codigoEnd;
                }
            }

            $fecha = Carbon::createFromTimeString($cdp->created_at);


            $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

            $pdf = PDF::loadView('administrativo.cdp.pdf', compact('cdp','rubros','valores','rol','infoRubro', 'vigencia', 'dias', 'meses', 'fecha'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            return $pdf->stream();
        } else {
            Session::flash('error','El CDP no ha sido finalizado, debe finalizarse para poder generar el PDF');
            return back();
        }
    }

    public function pdfBorrador($id, $vigen)
    {
        $roles = auth()->user()->roles;
        foreach ($roles as $role) $rol= $role->id;
        $cdp = Cdp::findOrFail($id);

        $all_rubros = Rubro::all();
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $vigens = Vigencia::findOrFail($vigen);
        $vigencia = $vigens;
        $V = $vigens->id;
        $vigencia_id = $V;

        $conteoTraits = new ConteoTraits;
        $conteo = $conteoTraits->conteoCdps($vigens, $cdp->id);

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if ($data->id < '324') {
            } elseif (count($rubro) > 0) {
                if($rubro[0]->fontsRubro and $rubro[0]->tipo == "Funcionamiento"){
                    //SE VALIDA QUE EL RUBRO TENGA DINERO DISPONIBLE
                    foreach ($rubro[0]->fontsRubro as $fuentes){
                        foreach ($fuentes->dependenciaFont as $fontDep){
                            if (auth()->user()->dependencia_id == $fontDep->dependencia_id) $valDisp[] = $fontDep->saldo;
                        }
                    }
                    if (isset($valDisp) and array_sum($valDisp) > 0){
                        $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
                        unset($valDisp);
                    }
                }
            }
        }

        $fecha = Carbon::createFromTimeString($cdp->created_at);


        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $pdf = PDF::loadView('administrativo.cdp.pdfBorrador', compact('cdp','rubros','valores','rol','infoRubro', 'vigencia', 'dias', 'meses', 'fecha'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();

    }

    public function cdpActividad(Request $request, $cdp, $vigencia){
        for ($i = 0; $i < count($request->codActividad); $i++) {
            $bpinCdpValor = new BpinCdpValor();
            $bpinCdpValor->valor = $request->valUsedActividad[$i];
            $bpinCdpValor->valor_disp = $request->valUsedActividad[$i];
            $bpinCdpValor->cdp_id = $cdp;
            $bpinCdpValor->cod_actividad = $request->codActividad[$i];
            $bpinCdpValor->save();
        }

        $CDP = Cdp::findOrFail($cdp);
        $CDP->valor = array_sum($request->valUsedActividad);
        $CDP->saldo = array_sum($request->valUsedActividad);
        $CDP->secretaria_e = '3';
        $CDP->jefe_e = "0";
        $CDP->ff_secretaria_e = Carbon::today();
        $CDP->save();

        Session::flash('success','El CDP ha sido enviado exitosamente');
        return redirect('/administrativo/cdp/'.$vigencia);
    }

    public function DeleteInv($id){
        $bpindCdp = BpinCdpValor::where('cdp_id', $id)->first();
        $bpindCdp->delete();

        $CDP = Cdp::findOrFail($id);
        $CDP->delete();

        Session::flash('success','El CDP ha sido eliminado');
        return redirect('/administrativo/cdp/'.$CDP->vigencia_id);
    }

    public function restaurarInv($id){
        $CDP = Cdp::findOrFail($id);
        $bpindCdp = BpinCdpValor::where('cdp_id', $id)->first();
        $bpindCdp->delete();

        $CDP->valor = 0;
        $CDP->ff_secretaria_e = null;
        $CDP->alcalde_e = '0';
        $CDP->ff_alcalde_e = null;
        $CDP->motivo = null;
        $CDP->saldo = 0;
        $CDP->save();

        Session::flash('success','El CDP ha sido reiniciado, seleccione nuevamente el proyecto a asignar');
        return redirect('/administrativo/cdp/'.$CDP->vigencia_id.'/'.$id);
    }
}
