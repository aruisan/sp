<?php

namespace App\Http\Controllers\Administrativo\Registro;

use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Persona;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Registro\CdpsRegistro;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\Contractuall\Contractual;
use App\Traits\FileTraits;
use Illuminate\Http\Response;

use Session;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Carbon\Carbon;

class RegistrosController extends Controller
{
    private $photos_path;
 
    public function __construct()
    {
        $this->photos_path = public_path('uploads\Registros');
    }
 
    /**
     * Display all of the images.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $vigencia = $id;
        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }
        $regT = Registro::where('secretaria_e','0')->orderBy('id','DESC')->get();
        foreach ($regT as $dataT){
            if ($dataT->cdpsRegistro[0]->cdp->vigencia_id == $vigencia){
                $registros[] = collect(['id' => $dataT->id, 'code' => $dataT->code , 'objeto' => $dataT->objeto, 'nombre' => $dataT->persona->nombre, 'valor' => $dataT->val_total, 'saldo' => $dataT->saldo, 'estado' => $dataT->secretaria_e]);
            }
        }
        $regH = Registro::where('secretaria_e','!=','0')->orderBy('id','DESC')->get();
        foreach ($regH as $data){
            if ($data->cdpsRegistro[0]->cdp->vigencia_id == $vigencia){
                $registrosHistorico[] = collect(['id' => $data->id, 'code' => $data->code, 'objeto' => $data->objeto, 'nombre' => $data->persona->nombre, 'valor' => $data->val_total, 'saldo' => $data->saldo, 'estado' => $data->secretaria_e]);
            }
        }
        if (!isset($registros)){
            $registros[] = null;
            unset($registros[0]);
        }
        if (!isset($registrosHistorico)){
            $registrosHistorico[] = null;
            unset($registrosHistorico[0]);
        }

        return  view('administrativo.registros.index', compact('registros','rol', 'registrosHistorico','vigencia'))->with('i', ($request->input('page', 1) - 1) * 5);

    }
 
    /**
     * Show the form for creating uploading new images.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $personas = Persona::all();
        $roles = auth()->user()->roles;
        foreach ($roles as $role){$rol= $role->id;}
        $cdp = Cdp::all()->where('jefe_e','3')->where('vigencia_id',$id)->count();
        $registros = Registro::all();
        $registrocount = 0;
        foreach ($registros as $registro){
            if ($registro->cdpsRegistro[0]->cdp->vigencia_id == $id){
                $registrocount = $registrocount +1;
            }
        }
        if($cdp > 0){
            $cdps = Cdp::all()->where('jefe_e','3')->where('saldo','>','0')->where('vigencia_id',$id);
            return view('administrativo.registros.create', compact('rol','personas','cdps', 'id','registrocount'));
        }else{
            Session::flash('error','Actualmente no existen CDPs disponibles para crear registros.');
            return redirect('/administrativo/registros/'.$id);
        }
    }
 
    /**
     * Saving images uploaded through XHR Request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('file'))
        {
            $file = new FileTraits;
            $ruta = $file->File($request->file('file'), 'Registros');
        }else{
            $ruta = "";
        }

        $registro = new Registro();

        $registro->code = $request->numReg + 1;
        $registro->objeto = $request->objeto;
        $registro->ff_expedicion = $request->fecha;
        $registro->ruta = $ruta;
        $registro->valor = "0";
        $registro->saldo = "0";
        $registro->val_total = "0";
        $registro->iva = "0";
        $registro->persona_id = $request->persona_id;
        if ($request->tipo_doc_text == null){
            $registro->tipo_doc = $request->tipo_doc;
        } elseif($request->tipo_doc == "Otro" and $request->tipo_doc_text != null) {
            $registro->tipo_doc = $request->tipo_doc_text;
        } else {
            $registro->tipo_doc = $request->tipo_doc;
        }
        $registro->num_doc = $request->num_tipo_doc;
        $registro->ff_doc = $request->fecha_tipo_doc;
        $registro->secretaria_e = $request->secretaria_e;
        $registro->ff_secretaria_e = $request->fecha;
        $registro->save();

        $fuenteRubroId = $request->fuente_id;
        $rubroId = $request->rubro_id;
        $cdpId = $request->cdp_id_s;
        $valorRubro = $request->valorFuenteUsar;
        $rubrosCdpId = $request->rubros_cdp_id;
        $rubrosCdpValorId = $request->rubros_cdp_valor_id;
        $registro_id = $registro->id;
        $cdps = $request->cdp_id;

        if ($cdps != null) {
            $count = count($cdps);

            for($i = 0; $i < $count; $i++){

                $cdpsRegistro = new CdpsRegistro();
                $cdpsRegistro->registro_id = $registro_id;
                $cdpsRegistro->cdp_id = $cdps[$i];
                $cdpsRegistro->valor = 0;
                $cdpsRegistro->save();
            }
        }

        if ($valorRubro != null){

            $countV = count($valorRubro);

            for($i = 0; $i < $countV; $i++){

                if ($rubrosCdpValorId[$i]){
                    $this->updateV($rubrosCdpValorId[$i], $valorRubro[$i]);
                }else{
                    $cdpsRegistroValor = new CdpsRegistroValor();
                    $cdpsRegistroValor->valor = $valorRubro[$i];
                    $cdpsRegistroValor->valor_disp = $valorRubro[$i];
                    $cdpsRegistroValor->fontsRubro_id = $fuenteRubroId[$i];
                    $cdpsRegistroValor->registro_id = $registro_id;
                    $cdpsRegistroValor->cdp_id = $cdpId[$i];
                    $cdpsRegistroValor->rubro_id = $rubroId[$i];
                    $cdpsRegistroValor->cdps_registro_id = $rubrosCdpId[$i];
                    $cdpsRegistroValor->save();
                }
            }

        }

        Session::flash('success','El registro se ha creado exitosamente');
        return redirect('/administrativo/registros/'.$request->vigencia);
    }

    public function destroy($id)
    {
        $destroy = Registro::find($id);

        if($destroy->ruta == ""){

            $cdpReg = CdpsRegistro::where('registro_id', $id)->get();
            $vigencia =  $cdpReg[0]->cdp->vigencia_id;
            foreach ($cdpReg as $data){
                $data->delete();
            }
            $destroy->delete();
            Session::flash('error','Registro borrado correctamente');
            return redirect('/administrativo/registros/'.$vigencia);

        }else{

            $file_path = $this->photos_path.'\ '.$destroy->ruta;
            $file_path = preg_replace('[\s+]',"", $file_path);

            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $cdpReg = CdpsRegistro::where('registro_id', $id)->get();
            $vigencia =  $cdpReg[0]->cdp->vigencia_id;
            foreach ($cdpReg as $data){
                $data->delete();
            }
            $destroy->delete();
            Session::flash('error','Registro borrado correctamente');
            return redirect('/administrativo/registros/'.$vigencia);
        }

    }

    public function edit($id)
    {
        $personas = Persona::all();
        $registro = Registro::findOrFail($id);
        $vigencia = $registro->cdpsRegistro[0]->cdp->vigencia_id;
        $cdps = Cdp::all();
        $contratos = Contractual::all();
        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }
        return view('administrativo.registros.edit', compact('registro','cdps','contratos','rol','personas','vigencia'));
    }

    public function show($id)
    {
        $registro = Registro::findOrFail($id);
        $vigencia = $registro->cdpsRegistro[0]->cdp->vigencia_id;
        $roles = auth()->user()->roles;
        $cdps = Cdp::where('saldo','>',0)->where('vigencia_id',$vigencia)->get();
        foreach ($roles as $role){
            $rol= $role->id;
        }
        $ordenesPago = $registro->ordenPagos;
        return view('administrativo.registros.show', compact('registro','rol','cdps','vigencia','ordenesPago'));
    }

    public function update(Request $request, $id)
    {
        $update = Registro::findOrFail($id);
        $vigencia = $update->cdpsRegistro[0]->cdp->vigencia_id;
        $update->objeto = $request->objeto;
        $update->iva = $request->iva;
        $update->persona_id = $request->persona_id;
        if ($request->tipo_doc_text == null){
            $update->tipo_doc = $request->tipo_doc;
        } elseif($request->tipo_doc == "Otro" and $request->tipo_doc_text != null) {
            $update->tipo_doc = $request->tipo_doc_text;
        } else {
            $update->tipo_doc = $request->tipo_doc;
        }
        $update->save();

        Session::flash('success','El registro se ha actualizado exitosamente');
        return redirect('/administrativo/registros/'.$vigencia);
    }

    public function updateEstado($id,$fecha,$valor,$estado,$valTot)
    {
        $update = Registro::findOrFail($id);

        //Validación del valor total frente a el valor disponible de los CDP's

        foreach ($update->cdpsRegistro as $cdps){
            $d[] =$cdps->cdp->saldo;
        }
        $valD = array_sum($d);
        if ($valD >= $valTot){
            $update->secretaria_e = $estado;
            $update->ff_secretaria_e = $fecha;
            $update->valor = $valor;
            $update->saldo = $valTot;
            $update->val_total = $valTot;
            $update->save();

            $cdpsRegistroValor = CdpsRegistroValor::where('registro_id', $id)->get();
            foreach ($cdpsRegistroValor as $value){
                $cdp = Cdp::findOrFail($value->cdp_id);
                $cdp->saldo = $cdp->saldo - $value->valor;
                $cdp->save();
                foreach ($cdp->rubrosCdp as $RCDP){
                    $RCDP->rubrosCdpValor->first()->valor_disp = $RCDP->rubrosCdpValor->first()->valor_disp - $value->valor;
                    $RCDP->rubrosCdpValor->first()->save();
                }
            }

            Session::flash('success','Secretaria, su registro ha sido finalizado exitosamente.');
            return redirect('/administrativo/registros/show/'.$id);
        } else{
            Session::flash('error','Secretaria, esta sobrepasando el valor disponible de los CDPs, verifique las sumas asignadas y el valor del iva.');
            return back();
        }
    }

    public function rechazar(Request $request, $id,$rol,$estado)
    {
        if ($rol == 3){
            if ($estado == 1){
                $update = Registro::findOrFail($id);
                $update->observacion = $request->observacion;
                $update->jcontratacion_e = $estado;
                $update->secretaria_e = "0";
                $update->save();

                Session::flash('error','El registro ha sido rechazado');
                return redirect('/administrativo/registros');
            }
        }
        if ($rol == 4){
            if ($estado == 1){
                $update = Registro::findOrFail($id);
                $update->observacion = $request->observacion;
                $update->jpresupuesto_e = $estado;
                $update->jcontratacion_e = "0";
                $update->save();

                Session::flash('error','El registro ha sido rechazado');
                return redirect('/administrativo/registros');
            }
        }
    }

    public function anular($id){
        $registro = Registro::findOrFail($id);
        foreach ($registro->cdpRegistroValor as $valCDPR){
            $valor = $valCDPR->valor;
            $cdp_id = $valCDPR->cdp_id;
            $registro_id = $valCDPR->registro_id;

            $registro = Registro::findOrFail($registro_id);
            $registro->secretaria_e = "2";
            $registro->saldo = 0;
            $registro->save();

            $cdp = Cdp::findOrFail($cdp_id);
            $cdp->saldo = $cdp->saldo + $valor;
            $cdp->save();

            $rubrosCdpValor = RubrosCdpValor::where('cdp_id',$cdp_id)->first();
            $rubrosCdpValor->valor_disp = $rubrosCdpValor->valor_disp + $valor;
            $rubrosCdpValor->save();
        }

        Session::flash('error','El Registro ha sido anulado');
        return redirect('/administrativo/registros/show/'.$id);
    }

    public function pdf($id, $vigen){
        $registro = Registro::findOrFail($id);
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
                                $registro2 = Register::findOrFail($register_id);
                                $register_id = $registro2->code_padre->registers->id;
                                $code = $registro2->code_padre->registers->code . $code;

                                $ultimo = $registro2->code_padre->registers->level->level;
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

        $rubroNameCDP = $registro->cdpsRegistro->first()->cdp->rubrosCdp->first()->rubros->name;

        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $fecha = Carbon::createFromTimeString($registro->created_at);

        $pdf = \PDF::loadView('administrativo.registros.pdf', compact('registro', 'vigencia', 'dias', 'meses', 'fecha','infoRubro','rubroNameCDP'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            return $pdf->stream();
    }
}

