<?php

namespace App\Http\Controllers\Administrativo\Cdp;

use App\BPin;
use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
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
use App\Traits\FirebaseNotificationTraits;


use Session;
class CdpController extends Controller
{
    use FirebaseNotificationTraits;

    private $fechaFija;

    public function __construct()
    {
        $this->fechaFija = '2023-07-12';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $vigencia_id = $id;
        $roles = auth()->user()->roles;
        $dep = auth()->user()->dependencia->id;
        foreach ($roles as $role){
            $rol= $role->id;
        }
        if ($rol == 2)
        {
            //ROL DE SECRETARIA
            $cdpTarea = Cdp::where('vigencia_id', $id)->where('secretaria_e', '0')->get();
            $cdProcess = Cdp::where('vigencia_id', $id)->where('secretaria_e', '3')->where('jefe_e','0')->get();
            $cdps = Cdp::where('vigencia_id', $id)->where(function ($query) {$query->where('jefe_e','3')->orWhere('jefe_e','2');})->get();

        }elseif ($rol == 3)
        {
            //ROL DE JEFE
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
            $cdpTarea = Cdp::where('vigencia_id', $vigencia_id)->where('secretaria_e', '3')->where('alcalde_e','0')->get();
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
        } else {
            foreach ($cdps as $cdp){
                if (isset($rubros)) unset($rubros);
                if (isset($fuentes)) unset($fuentes);

                if ($cdp->tipo == "Funcionamiento"){
                    foreach($cdp->rubrosCdpValor as $rubroCdpValue){
                        $rubros[] = $rubroCdpValue->fontsRubro->rubro->cod.' - '.$rubroCdpValue->fontsRubro->rubro->name;
                        if(isset($rubroCdpValue->fontsRubro)){
                            $fuentes[] = $rubroCdpValue->fontsRubro->sourceFunding->code.' - '.$rubroCdpValue->fontsRubro->sourceFunding->description;
                        }
                    }
                    $cdp->rubros = $rubros;
                    $cdp->fuentes = $fuentes;
                } else{
                    foreach($cdp->bpinsCdpValor as $bpinsCDP){
                        if(isset($bpinsCDP->depRubroFont->fontRubro)){
                            $rubros[] = $bpinsCDP->depRubroFont->fontRubro->rubro->cod.' - '.$bpinsCDP->depRubroFont->fontRubro->rubro->name;
                            $fuentes[] = $bpinsCDP->depRubroFont->fontRubro->sourceFunding->code.' - '.$bpinsCDP->depRubroFont->fontRubro->sourceFunding->description;
                        }
                    }
                    $cdp->rubros = $rubros;
                    $cdp->fuentes = $fuentes;
                }
            }
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

        if ($cdp->tipo == "Funcionamiento"){
            $cdp->saldo = 0;
            $cdp->jefe_e = '2';
            $cdp->save();

            $rubrosCdp = RubrosCdpValor::where('cdp_id', $id)->get();
            foreach ($rubrosCdp as $rubroCdp){
                $fontR = FontsRubro::findOrFail($rubroCdp->fontsRubro->id);
                $fontR->valor_disp = $fontR->valor_disp + $rubroCdp->valor;
                $fontR->save();

                if($rubroCdp->fontsDep_id){
                    $depRubFontUpdate = DependenciaRubroFont::find($rubroCdp->fontsDep_id);
                    $depRubFontUpdate->saldo = $depRubFontUpdate->saldo + $rubroCdp->valor;
                    $depRubFontUpdate->save();
                }
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

                $actividadCdp->valor_disp = $actividadCdp->valor_disp - $actividadCdp->valor;
                $actividadCdp->save();

                if ($actividadCdp->dependencia_rubro_font_id != null){
                    $depRubroFont = DependenciaRubroFont::find($actividadCdp->dependencia_rubro_font_id);
                    $depRubroFont->saldo = $depRubroFont->saldo + $actividadCdp->valor;
                    $depRubroFont->save();

                    $bpinVigencia = bpinVigencias::where('bpin_id',$actividadCdp->actividad->id)
                        ->where('dep_rubro_id',$depRubroFont->id)->first();
                    $bpinVigencia->saldo = $bpinVigencia->saldo + $actividadCdp->valor;
                    $bpinVigencia->save();
                    
                } else{
                    $bpinVigencia = bpinVigencias::where('bpin_id',$actividadCdp->actividad->id)->first();
                    $bpinVigencia->saldo = $bpinVigencia->saldo + $actividadCdp->valor;
                    $bpinVigencia->save();

                    $depRubroFont = DependenciaRubroFont::find($bpinVigencia->dep_rubro_id);
                    $depRubroFont->saldo = $depRubroFont->saldo + $actividadCdp->valor;
                    $depRubroFont->save();
                }
            }

            Session::flash('error','El CDP ha sido anulado');
            return redirect('/administrativo/cdp/'.$vigen.'/'.$id);
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

        $request->fecha = $this->fechaFija;

        $cdp = new Cdp();
        $cdp->name = $request->name;
        $cdp->tipo = $request->tipo;
        $cdp->code = $count + 1;
        $cdp->valueControl = $request->valueControl;
        $cdp->valor = 0;
        $cdp->fecha = $request->fecha;
        //$cdp->fecha = '2023-03-27';
        $cdp->dependencia_id = $request->dependencia_id;
        $cdp->observacion = $request->observacion;
        $cdp->saldo = 0;
        $cdp->secretaria_e = $request->secretaria_e;
        $cdp->ff_secretaria_e = $request->fecha;
        //$cdp->ff_secretaria_e = '2023-03-16';
        $cdp->alcalde_e = '0';
        $cdp->vigencia_id = $request->vigencia_id;
        $cdp->created_at = $this->fechaFija.' 12:00:00';
        $cdp->secretaria_user_id = auth()->user()->id;
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
                            if ($fontDep->saldo > 0){
                                $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod,
                                    'depFont' => $fontDep->id, 'dependencia' => $fontDep->dependencias->name, 'codeFont' => $fuentes->sourceFunding->code,
                                    'descriptionFont' => $fuentes->sourceFunding->description];
                            }
                        }
                    }
                }
            }
        }

        if (!isset($infoRubro)) $infoRubro = [];

        //BPINS
        $bpinVigencia = bpinVigencias::where('vigencia_id',$vigencia_id)->get();
        $allBpins = BPin::all();
        $exist = false;
        foreach ($allBpins as $data){
            foreach ($bpinVigencia as $BVigen){
                if ($data->id == $BVigen->bpin_id) {
                    $exist = true;
                }
            }
            if ($exist) {
                $data->saldo = $BVigen->saldo;
                $bpins[] = $data;
                $exist = false;
            }
        }
        foreach ($bpins as $bpin){
            $bpin['rubro'] = "No";
            if (count($bpin->rubroFind) > 0) {
                foreach ($bpin->rubroFind as $rub){
                    if ($rub->vigencia_id == $V) $bpin['rubro'] = $rub;
                }
            }
        }

        $unicoBpins = $this->unique_multidim_array($bpins, 'cod_proyecto');

        foreach ($cdp->bpinsCdpValor as $data){
            $bpinVig = bpinVigencias::where('bpin_id', $data->actividad->id)->first();
            $data->actividad->rubro = $bpinVig->rubro;
        }

        $activateAnul = true;
        if ($cdp->cdpsRegistro->count() > 0){
            foreach ($cdp->cdpsRegistro as $registro){
                if ($registro->secretaria_e == 3){
                    $activateAnul = false;
                }
            }
        }



        return view('administrativo.cdp.show', compact('cdp','rubros','valores','rol',
            'infoRubro', 'conteo', 'bpins', 'user','unicoBpins','activateAnul','vigencia'));
    }

    public function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
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

        $fecha = $this->fechaFija;
        if ($rol == 2){

            //SE VALIDA QUE SE TENGA DINERO EN LA FUENTE PARA EL ENVIO DEL CDP

            if ($update->tipo == "Funcionamiento"){
                foreach ($update->rubrosCdp as $data){
                    foreach ($data->rubros->fontsRubro as $fuentesRubro){
                        foreach($fuentesRubro->dependenciaFont as $dep){
                            if($dep->dependencia_id == auth()->user()->dependencia_id){
                                if ($dep->saldo < $data->rubrosCdpValor->first()->valor){
                                    Session::flash('success','El CDP enviado tiene asignado un valor superior al
                                    disponible en el rubro.');
                                    return back();
                                }
                            }
                        }
                    }
                }
            }

            $update->valor = $valor;
            $update->secretaria_e = $estado;
            $update->alcalde_e = "0";
            $update->ff_alcalde_e = $fecha;
            $update->jefe_e = "0";
            $update->ff_secretaria_e = $fecha;
            $update->save();

            Session::flash('success','El CDP ha sido enviado exitosamente');

            $this->sendTokenMovil("Nuevo Cdp", "{$update->name}.", "Alcalde");
            return redirect('/administrativo/cdp/'.$update->vigencia_id);
        }
        if ($rol == 3) {
            if ($estado == 3) {
                if ($update->tipo == "Funcionamiento"){

                    //SE REALIZA LA VALIDACION DEL VALOR DEL CDP AL MOMENTO DE FINALIZARLO
                    foreach ($update->rubrosCdp as $data){
                        foreach ($data->rubros->fontsRubro as $fuentesRubro){
                            foreach($fuentesRubro->dependenciaFont as $dep){
                                if($dep->dependencia_id == $update->dependencia_id){
                                    if ($dep->saldo < $data->rubrosCdpValor->first()->valor){
                                        Session::flash('success','El CDP enviado tiene asignado un valor superior al
                                            disponible en el rubro.');
                                        return back();
                                    }
                                }
                            }
                        }
                    }

                    foreach ($update->rubrosCdpValor as $fuentes) {
                        if ($fuentes->fontsRubro->valor_disp >= $fuentes->valor) {
                            $depFont = DependenciaRubroFont::find($fuentes->fontsDep_id);
                            if ($depFont->saldo >= $fuentes->valor){
                                $update->secretaria_e = $estado;
                                if(auth()->user()->id == 39) {
                                    $update->alcalde_e = $estado;
                                    $update->ff_alcalde_e = $fecha;
                                }
                                $update->jefe_e = $estado;
                                $update->ff_jefe_e = $fecha;
                                $update->valor = $valor;
                                $update->saldo = $valor;

                                $this->actualizarValorRubro($id);

                                $update->save();

                                Session::flash('success', 'El CDP ha sido finalizado con exito');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id);
                            } else{
                                Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la dependencia.');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                            }
                        } else {
                            Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en el rubro');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                        }
                    }
                } else{
                    //VALIDACION DEL CDP CUANDO ES DE INVERSION
                    foreach ($update->bpinsCdpValor as $actividad) {
                        $validateVig = bpinVigencias::where('bpin_id',$actividad->actividad->id)
                            ->where('dep_rubro_id', $actividad->dependencia_rubro_font_id)->first();

                        if ($validateVig->saldo >= $actividad->valor){

                            //SE RESTA A BPIN VIGENCIA
                            $validateVig->saldo = $validateVig->saldo - $actividad->valor;
                            if ($validateVig->saldo < 0){
                                Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la bpinVigencia');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                            }
                            $validateVig->save();

                            $depRubroFont = DependenciaRubroFont::find($validateVig->dep_rubro_id);
                            $depRubroFont->saldo = $depRubroFont->saldo - $actividad->valor;
                            if ($depRubroFont->saldo < 0){
                                Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la dependenciaRubroFont');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                            }
                            $depRubroFont->save();

                        } else{
                            Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la actividad');
                            return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $id);
                        }
                    }

                    //SI LOS DESCUENTOS FUERON SATISFACTORIOS SE ACTUALIZA EL CDP A FINALIZADO
                    $update->jefe_e = $estado;
                    $update->ff_jefe_e = $fecha;
                    $update->valor = $valor;
                    $update->saldo = $valor;
                    $update->save();

                    Session::flash('success', 'El CDP ha sido finalizado con exito');
                    return redirect('/administrativo/cdp/' . $update->vigencia_id);
                }
            }
        }
        if ($rol == 5){
            if ($estado == 3) {
                $update->alcalde_e = $estado;
                $update->ff_alcalde_e = $fecha;
                $update->save();

                Session::flash('success','El CDP ha sido enviado al jefe exitosamente');
                $this->sendTokenMovil("Nuevo Cdp", "{$update->name}.", "Jefe");
                return redirect('/administrativo/cdp/'.$update->vigencia_id);
            }
        }
    }

    public function rechazar(Request $request, $id, $vigen)
    {
        $request->fecha = $this->fechaFija;
        if ($request->rol == "3"){
            $update = Cdp::findOrFail($id);
            $update->jefe_e = "1";
            $update->secretaria_e = "0";
            //$update->ff_jefe_e = $fecha;
            $update->ff_jefe_e = $request->fecha;
            $update->alcalde_e = "0";
            //$update->ff_alcalde_e = $fecha;
            $update->ff_alcalde_e = $request->fecha;
            $update->motivo = $request->motivo;
            $update->save();

            Session::flash('error','El CDP ha sido rechazado');
            return redirect('/administrativo/cdp/'.$vigen);

        } else if ($request->rol == "5"){
            $update = Cdp::findOrFail($id);
            $update->alcalde_e = "1";
            $update->secretaria_e = "0";
            //$update->ff_alcalde_e = $fecha;
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

            $fontRubro = FontsRubro::findOrFail($fuentes->fontsRubro->id);
            $fontRubro->valor_disp = $fontRubro->valor_disp - $fuentes->valor;
            $fontRubro->save();

            $depFont = DependenciaRubroFont::find($fuentes->fontsDep_id);
            $depFont->saldo = $depFont->saldo - $fuentes->valor;
            $depFont->save();
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

            $vigencia = Vigencia::findOrFail($vigen);
            $fecha = Carbon::createFromTimeString($cdp->created_at);

            //codigo de rubros

            foreach($cdp->rubrosCdp as $rubro){
                $infoRubro[] = ['id_rubro' => $rubro->id ,'id' => '', 'codigo' => $rubro->rubros->cod, 'name' => $rubro->rubros->name, 'value' => $rubro->rubrosCdpValor->first()->valor];
            }

            if (!isset($infoRubro)) $infoRubro = [];


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

        if (!isset($infoRubro)) $infoRubro = [];

        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $pdf = PDF::loadView('administrativo.cdp.pdfBorrador', compact('cdp','rubros','valores','rol','infoRubro', 'vigencia', 'dias', 'meses', 'fecha'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();

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
        $bpindCdp = BpinCdpValor::where('cdp_id', $id)->get();
        foreach ($bpindCdp as $data) $data->delete();

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

    public function check(Request $request){
        $flag = true;
        $rol = auth()->user()->roles->first()->id;

        for ($i = 0; $i < $request->countCDPs; $i++){
            $input = "checkInput".$i;
            if ($request->$input != null){
                $flag = false;
                //HAY CDP POR APROBAR
                $update = Cdp::findOrFail($request->$input);
                if ($rol == 5){
                    $update->alcalde_e = '3';
                    $update->secretaria_e = '3';
                    $update->jefe_e = '0';
                    $update->ff_alcalde_e = Carbon::today();
                    $update->save();
                } elseif ($rol == 3) {
                    if ($update->tipo == "Funcionamiento"){
                        //SE REALIZA LA VALIDACION DEL VALOR DEL CDP AL MOMENTO DE FINALIZARLO
                        foreach ($update->rubrosCdp as $data){
                            foreach ($data->rubros->fontsRubro as $fuentesRubro){
                                foreach($fuentesRubro->dependenciaFont as $dep){
                                    if($dep->dependencia_id == $update->dependencia_id){
                                        if ($dep->saldo < $update->valor){
                                            Session::flash('success','El CDP enviado tiene asignado un valor superior al
                                            disponible en el rubro.');
                                            return back();
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($update->rubrosCdpValor as $fuentes) {
                            if ($fuentes->fontsRubro->valor_disp >= $fuentes->valor) {
                                $depFont = DependenciaRubroFont::find($fuentes->fontsDep_id);
                                if ($depFont->saldo >= $fuentes->valor){
                                    $update->jefe_e = '3';
                                    $update->ff_jefe_e = Carbon::today();
                                    $update->saldo = $update->valor;

                                    $this->actualizarValorRubro($request->$input);

                                    $update->save();
                                } else {
                                    Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la dependencia');
                                    return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $request->$input);
                                }

                            } else {
                                Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en el rubro');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $request->$input);
                            }
                        }
                    } else{

                        //VALIDACION DEL CDP CUANDO ES DE INVERSION
                        foreach ($update->bpinsCdpValor as $actividad) {
                            $validateVig = bpinVigencias::where('bpin_id',$actividad->actividad->id)
                                ->where('dep_rubro_id', $actividad->dependencia_rubro_font_id)->first();

                            if ($validateVig->saldo >= $actividad->valor){

                                //SE RESTA A BPIN VIGENCIA
                                $validateVig->saldo = $validateVig->saldo - $actividad->valor;
                                if ($validateVig->saldo < 0){
                                    Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la bpinVigencia');
                                    return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $request->$input);
                                }
                                $validateVig->save();

                                $depRubroFont = DependenciaRubroFont::find($validateVig->dep_rubro_id);
                                $depRubroFont->saldo = $depRubroFont->saldo - $actividad->valor;
                                if ($depRubroFont->saldo < 0){
                                    Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la dependenciaRubroFont');
                                    return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $request->$input);
                                }
                                $depRubroFont->save();

                            } else{
                                Session::flash('error', 'El CDP no puede tener un valor superior al valor disponible en la actividad');
                                return redirect('/administrativo/cdp/' . $update->vigencia_id . '/' . $request->$input);
                            }
                        }

                        //SI LOS DESCUENTOS FUERON SATISFACTORIOS SE ACTUALIZA EL CDP A FINALIZADO
                        $update->jefe_e = '3';
                        $update->ff_jefe_e = Carbon::today();
                        $update->saldo = $update->valor;
                        $update->save();
                    }
                }
            }

        }
        if ($flag == true){
            Session::flash('warning','No se seleccionaron CDPs para aprobar');
            return back();
        } else{
            Session::flash('sucess','CDPs aprobados exitosamente');
            return back();
        }
    }

    public function cdpActividad(Request $request, $cdp, $vigencia){
        if (array_sum($request->valUsedActividad) == 0){
            Session::flash('warning','El CDP no se puede enviar en $0');
            return back();
        } else {
            for ($i = 0; $i < count($request->codActividad); $i++) {
                if ( $request->valUsedActividad[$i] > 0){
                    $bpinCdpValor = new BpinCdpValor();
                    $bpinCdpValor->valor = $request->valUsedActividad[$i];
                    $bpinCdpValor->valor_disp = $request->valUsedActividad[$i];
                    $bpinCdpValor->cdp_id = $cdp;
                    $bpinCdpValor->cod_actividad = $request->codActividad[$i];
                    $bpinCdpValor->dependencia_rubro_font_id = $request->depRubro_id[$i];
                    $bpinCdpValor->save();
                }
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
    }

    public function findActividades(Request $request){
        $actividadesFind = BPin::where('cod_proyecto', $request->proyecto)->get();
        foreach ($actividadesFind as $actividad){
            if (count($actividad->rubroFind) > 0){
                $bpinVigencias = bpinVigencias::where('bpin_id', $actividad->id)->get();
                foreach ($bpinVigencias as $bpinVigencia){
                    $depRF = DependenciaRubroFont::find($bpinVigencia->dep_rubro_id);
                    if ($bpinVigencia->vigencia_id == $request->vigencia_id and $bpinVigencia->saldo > 0){
                        if ($depRF->dependencia_id == auth()->user()->dependencia->id){
                            $actividades[] = collect(['cod_actividad' => $actividad->cod_actividad, 'nombre' => $actividad->actividad,
                                'dineroDisp' =>$bpinVigencia->saldo, 'depRubro_id' => $bpinVigencia->dep_rubro_id,
                                'font' => $depRF->fontRubro->sourceFunding->code.' - '.$depRF->fontRubro->sourceFunding->description,
                                'dependencia'  => $depRF->dependencias->name]);
                        }
                    }
                }
            }
        }
        return $actividades;
    }

    public function changeObject($id,Request $request){
        $cdp = Cdp::find($id);
        $cdp->name = $request->objeto;
        $cdp->save();
        return $cdp;
    }
}
