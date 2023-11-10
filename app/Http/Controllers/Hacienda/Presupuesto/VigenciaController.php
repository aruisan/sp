<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\BPin;
use App\Exports\InfMensualExport;
use App\Model\Admin\Dependencia;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Administrativo\Pago\PagoRubros;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\ComprobanteIngresos\CIRubros;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FileTraits;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;
use Session;


class VigenciaController extends Controller
{

    public function create($tipo)
    {
        return view('hacienda.presupuesto.vigencia.create', compact('tipo'));
    }

    public function store(Request $request){

        //dd(public_path());
        $user = User::all()->count();
        if($user == 0){
            Session::flash('error','Debe crear un usuario para anexarlo a la vigencia');
            return back();
        }

        $duple = Vigencia::where('tipo', $request->tipo)->where('vigencia', $request->vigencia)->get()->count();
        if($duple < 1){
            if($request->hasFile('file'))
            {
                $file = new FileTraits;
                $ruta = $file->File($request->file('file'), 'Presupuesto');
            }else{
                $ruta = "";
            }

            $vigencia = new Vigencia;
            $vigencia->vigencia = $request->vigencia;
            $vigencia->tipo = $request->tipo;
            $vigencia->ultimo = 0;
            $vigencia->presupuesto_inicial = $request->valor;
            $vigencia->ruta = $ruta;
            $vigencia->numero_decreto = $request->decreto;
            $vigencia->fecha = $request->fecha;
            $vigencia->user_id = 1;
            $vigencia->estado = '0';
            $vigencia->save();

            Session::flash('success','La Vigencia se ha creado exitosamente');
            return redirect('/presupuesto/rubro/create/'.$vigencia->id);
        }else{
            Session::flash('error','La Vigencia no se puede duplicar');
            return back();
        }

    }

    public function historico($mesActual, $id)
    {
        $vigencia = Vigencia::findOrFail($id);
        if ($vigencia->tipo == 0){
            //EGRESOS
            $prepSaved = PresupuestoSnap::where('mes', $mesActual)->where('vigencia_id', $id)->where('tipo','EGRESOS')->first();
            $fuentes = SourceFunding::all();
            $deps = Dependencia::all();
            $bpins = BPin::all();
            $today = Carbon::today();
            $añoActual = $today->year;

            if (!$prepSaved) {
                Artisan::call("schedule:run");
                $V = "Vacio";

                return view('hacienda.presupuesto.indexCuipoFastCharge', compact( 'prepSaved',
                    'añoActual', 'mesActual','V','bpins','fuentes','deps'));
            } else{

                $añoActual = $prepSaved->año;
                $lastDay = Carbon::now()->subDay()->toDateString();
                $actuallyDay = Carbon::now()->toDateString();
                $rol = auth()->user()->roles->first()->id;

                $presupuestos = Vigencia::where('vigencia', $añoActual)->get();
                foreach ($presupuestos as $prep){
                    if ($prep->tipo == 0){
                        $rubI = Rubro::where('vigencia_id', $prep->id)->orderBy('cod','ASC')->get();
                        foreach ($rubI as $rub){
                            foreach ($rub->fontsRubro as $fuente){
                                $dependencias = DependenciaRubroFont::where('rubro_font_id', $fuente->id)->get();
                                foreach ($dependencias as $dependencia){
                                    if ($dependencia->saldo > 0){
                                        $rubrosEgresos[] = collect(['id' => $dependencia->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                                            $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description, 'dep' => $dependencia->dependencias]);
                                        $rubrosEgresosAll[] = collect(['id' => $dependencia->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                                            $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description, 'dep' => $dependencia->dependencias]);
                                    } else{
                                        $rubrosEgresosAll[] = collect(['id' => $dependencia->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                                            $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description, 'dep' => $dependencia->dependencias]);
                                    }
                                }
                            }
                        }
                    }
                }

                $V = $prepSaved->vigencia_id;
                $vigencia = Vigencia::find($prepSaved->vigencia_id);
                $dataPrepSaved = PresupuestoSnapData::where('pre_snap_id', $prepSaved->id)->first();
                $fechaData = Carbon::parse($dataPrepSaved->created_at);
                $codeCon = CodeContractuales::all();

                //Rubros no asignados a alguna actividad
                $Rubros = Rubro::where('vigencia_id', $id)->where('tipo','Inversion')->get();
                foreach ($Rubros as $item){
                    foreach ($item->fontsRubro as $fontRubro){
                        foreach ($fontRubro->dependenciaFont as $dependencia){
                            dd($dependencia, $fontRubro);
                            $bpin = BPin::where('rubro_id', $item->id)->first();
                            if (!$bpin) $rubBPIN[] = collect(['depRubID' => $dependencia->id, 'cod' => $item->cod,
                                'name' => $item->name, 'dep' => $dependencia->dependencias->name,
                                'presupuesto_inicial' => $dependencia->saldo]);
                        }
                    }
                }

                dd($rubBPIN);

                if (!isset($rubBPIN)){
                    $rubBPIN[] = null;
                    unset($rubBPIN[0]);
                }

                foreach ($bpins as $bpin){
                    $bpin['rubro'] = "No";
                    if (count($bpin->rubroFind) > 0) {
                        foreach ($bpin->rubroFind as $rub){
                            if ($rub->vigencia_id == $V) $bpin['rubro'] = $rub->dep_rubro_id;
                        }
                    }
                }


                return view('hacienda.presupuesto.newHistorico', compact( 'prepSaved',
                    'añoActual', 'mesActual','V','codeCon','lastDay','actuallyDay','bpins','fechaData',
                    'vigencia','rol','rubrosEgresosAll','fuentes','deps','rubBPIN'));
            }
        } else {

            $V = $vigencia->id;
            $vigencia_id = $V;
            $añoActual = Carbon::now()->year;
            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            $registers = Register::where('level_id', $ultimoLevel->id)->get();
            $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
            $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
            $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
            $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
            $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
            $allRegisters = Register::orderByDesc('level_id')->get();
            $pagos = Pagos::all();
            $ordenPagos = OrdenPagos::all();

            $historico = Vigencia::where('vigencia', '!=', $añoActual)->get();
            foreach ($historico as $his) {
                if ($his->tipo == "0"){
                    $years[] = [ 'info' => $his->vigencia." - Egresos", 'id' => $his->id];
                }else{
                    $years[] = [ 'info' => $his->vigencia." - Ingresos", 'id' => $his->id];
                }
            }
            asort($years);

            global $lastLevel;
            $lastLevel = $ultimoLevel->id;
            $lastLevel2 = $ultimoLevel2->level_id;

            foreach ($fonts as $font){
                $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
            }

            foreach ($fontsRubros as $fontsRubro){
                if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
                    $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
                }
            }
            $tamFountsRubros = count($fuentesRubros);

            foreach ($registers2 as $register2) {
                if ($register2->level->vigencia_id == $vigencia_id) {
                    global $codigoLast;
                    if ($register2->register_id == null) {
                        $codigoEnd = $register2->code;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    } elseif ($codigoLast > 0) {
                        if ($lastLevel2 == $register2->level_id) {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
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
                                    $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                                    if ($register->level_id == $lastLevel) {
                                        foreach ($rubros as $rubro) {
                                            if ($register->id == $rubro->register_id) {
                                                $newCod = "$code$rubro->cod";
                                                $fR = $rubro->FontsRubro;
                                                //dd($newCod, $fR);
                                                for ($i = 0; $i < $tamFountsRubros; $i++) {
                                                    $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_vigencia_id')->get();
                                                    $numR = count($rubrosF);
                                                    $numF = count($fonts);
                                                    if ($numR == $numF) {
                                                        if ($fuentesRubros[$i]['rubro_id'] == $rubro->id) {
                                                            $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['font_vigencia_id']]);
                                                        }
                                                    } else {
                                                        foreach ($fonts as $font) {
                                                            if ($fuentesRubros[$i]['font_vigencia_id'] == $font->id) {
                                                                $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'font_vigencia_id' => $font->id]);
                                                            } else {
                                                                $findFont = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->where('font_vigencia_id', $font->id)->get();
                                                                $numFinds = count($findFont);
                                                                if ($numFinds >= 1) {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id + 1;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                } else {

                                                                    $saveRubroF = new FontsRubro();

                                                                    $saveRubroF->valor = 0;
                                                                    $saveRubroF->valor_disp = 0;
                                                                    $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                    $saveRubroF->font_vigencia_id = $font->id;

                                                                    $saveRubroF->save();

                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                                $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                                $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                                $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $codigo = $register2->code;
                            $codigoEnd = "$codigoLast$codigo";
                            $codigoLast = $codigoEnd;
                            $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                        }
                    } else {
                        $codigo = $register2->code;
                        $newRegisters = Register::findOrFail($register2->register_id);
                        $codigoNew = $newRegisters->code;
                        $codigoEnd = "$codigoNew$codigo";
                        $codigoLast = $codigoEnd;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    }
                }
            }
            //Sumas de los Valores
            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
                            if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresIniciales[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //SUMA DE VALOR DISPONIBLE DEL RUBRO - CDP

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor_disp');
                            $ArraytotalFR[] = $valFuent;
                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valorDisp); $i++) {
                            if ($valorDisp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valorDisp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

//ADICION
            foreach ($rubros as $R2){
                $ad = RubrosMov::where([['rubro_id', $R2->id],['movimiento', '=', '2']])->get();
                if ($ad->count() > 0){
                    $valoresAdd[] = collect(['id' => $R2->id, 'valor' => $ad->sum('valor')]) ;
                } else{
                    $valoresAdd[] = collect(['id' => $R2->id, 'valor' => 0]) ;
                }
            }


            //REDUCCIÓN
            foreach ($rubros as $R3){
                $red = RubrosMov::where([['rubro_id', $R3->id],['movimiento', '=', '3']])->get();
                if ($red->count() > 0){
                    $valoresRed[] = collect(['id' => $R3->id, 'valor' => $red->sum('valor')]) ;
                } else{
                    $valoresRed[] = collect(['id' => $R3->id, 'valor' => 0]) ;
                }
            }


            //CREDITO
            foreach ($rubros as $R4){
                $cred = RubrosMov::where([['rubro_id', $R4->id],['movimiento', '=', '1']])->get();
                if ($cred->count() > 0){
                    $valoresCred[] = collect(['id' => $R4->id, 'valor' => $cred->sum('valor')]) ;
                }else{
                    $valoresCred[] = collect(['id' => $R4->id, 'valor' => 0]) ;
                }
            }

            //CONTRACREDITO
            foreach ($rubros as $R5){
                foreach ($R5->fontsRubro as $FR){
                    foreach ($FR->rubrosMov as $movR) {
                        if ($movR->movimiento == 1){
                            $suma[] = $movR->valor;
                        }
                    }
                }
                if (isset($suma)){
                    $valoresCcred[] = collect(['id' => $R5->id, 'valor' => array_sum($suma)]);
                    unset($suma);
                } else{
                    $valoresCcred[] = collect(['id' => $R5->id, 'valor' => 0]);
                }
            }

            //CREDITO Y CONTRACREDITO

            for ($i=0;$i<sizeof($valoresCcred);$i++){
                if ($valoresCcred[$i]['id'] == $valoresCred[$i]['id']){
                    $valoresCyC[] = collect(['id' => $valoresCcred[$i]['id'], 'valorC' => $valoresCred[$i]['valor'], 'valorCC' => $valoresCcred[$i]['valor']]) ;
                }
            }

            //PRESUPUESTO DEFINITIVO

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                            foreach ($valoresAdd as $valAdd) {
                                if ($rubrosReg->id == $valAdd["id"]) {
                                    $valAdicion = $valAdd["valor"];
                                }
                            }
                            foreach ($valoresRed as $valRed) {
                                if ($rubrosReg->id == $valRed["id"]) {
                                    $valReduccion = $valRed["valor"];
                                }
                            }
                            foreach ($valoresCred as $valCred) {
                                if ($rubrosReg->id == $valCred["id"]) {
                                    $valCredito = $valCred["valor"];
                                }
                            }
                            foreach ($valoresCcred as $valCcred) {
                                if ($rubrosReg->id == $valCcred["id"]) {
                                    $valCcredito = $valCcred["valor"];
                                }
                            }
                            if (isset($valAdicion) and isset($valReduccion)) {
                                $ArraytotalFR[] = $valFuent + $valAdicion - $valReduccion + $valCredito - $valCcredito;
                            } else {
                                $ArraytotalFR[] = $valFuent + $valCredito - $valCcredito;
                            }

                        }
                        if (isset($ArraytotalFR)) {
                            $totalFR = array_sum($ArraytotalFR);
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalFR);
                        } else {
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresDisp); $i++) {
                            if ($valoresDisp[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresDisp[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            foreach ($codigos as $cod){
                if ($cod['valor']){
                    foreach ($valoresAdd as $valores1){
                        if ($cod['id_rubro'] == $valores1['id']){
                            $valAd1 = $valores1['valor'];
                        }
                    }
                    foreach ($valoresRed as $valores2){
                        if ($cod['id_rubro'] == $valores2['id']){
                            $valRed1 = $valores2['valor'];
                        }
                    }
                    foreach ($valoresCred as $valores3){
                        if ($cod['id_rubro'] == $valores3['id']){
                            $valCred1 = $valores3['valor'];
                        }
                    }
                    foreach ($valoresCcred as $valores4){
                        if ($cod['id_rubro'] == $valores4['id']){
                            $valCcred1 = $valores4['valor'];
                        }
                    }
                    if (isset($valAd1) and isset($valRed1)){
                        $AD = $cod['valor'] + $valAd1 - $valRed1 + $valCred1 - $valCcred1;
                    } else{
                        $AD = $cod['valor'] + $valCred1 - $valCcred1;
                    }
                    $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
                }
            }

            //ROL

            $roles = auth()->user()->roles;
            foreach ($roles as $role){
                $rol= $role->id;
            }

            //CODE CONTRACTUALES

            $codeCon = CodeContractuales::all();

            //TOTAL RECAUDO
            foreach ($Rubros as $rubro){
                $infoR = Rubro::findOrFail($rubro['id_rubro']);
                $totalRecaud[] = collect(['id' => $infoR->id, 'valor' => $infoR->compIng->sum('valor')]);
            }

            //TOTAL GENERAL VALOR RECAUDO

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg) {
                            foreach ($rubrosReg->compIng as $Rub){
                                $compInfo = CIRubros::where('id', $Rub->id)->get();
                                if ($compInfo->count() > 0 ){
                                    $ArraytotalRub[] = $Rub->valor;
                                }
                            }
                        }
                        if (isset($ArraytotalRub)) {
                            $totalRub = array_sum($ArraytotalRub);
                            $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => $totalRub, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalRub);
                        } else {
                            $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinRec); $i++) {
                            if ($valoresFinRec[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinRec[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinRec[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            //SALDO POR RECAUDAR
            foreach ($Rubros as $rubro){
                $infoR2 = Rubro::findOrFail($rubro['id_rubro']);
                $recaudado = $infoR->compIng->sum('valor');
                $valor = $infoR->fontsRubro->sum('valor');
                $saldoRecaudo[] = collect(['id' => $infoR2->id, 'valor' => $valor - $recaudado]);
            }

            //TOTAL GENERAL SALDO POR RECAUDAR

            foreach ($allRegisters as $allRegister){
                if ($allRegister->level->vigencia_id == $vigencia_id) {
                    if ($allRegister->level_id == $lastLevel) {
                        $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                        foreach ($rubrosRegs as $rubrosReg2) {
                            $recaudado2 = $rubrosReg2->compIng->sum('valor');
                            $valor2 = $rubrosReg2->fontsRubro->sum('valor');
                            $ArraytotalSald[] = $valor2 - $recaudado2;
                        }
                        if (isset($ArraytotalSald)) {
                            $totalSald = array_sum($ArraytotalSald);
                            $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => $totalSald, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($ArraytotalSald);
                        } else {
                            $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    } else {
                        for ($i = 0; $i < sizeof($valoresFinSald); $i++) {
                            if ($valoresFinSald[$i]['register_id'] == $allRegister->id) {
                                $suma[] = $valoresFinSald[$i]['valor'];
                            }
                        }
                        if (isset($suma)) {
                            $valSum = array_sum($suma);
                            $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                            unset($suma);
                        } else {
                            $valoresFinSald[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        }
                    }
                }
            }

            return view('hacienda.presupuesto.historicoIng', compact('codigos','V','fuentes','FRubros','fuentesRubros','valoresIniciales', 'Rubros','valorDisp','valoresAdd','valoresRed','valoresDisp','ArrayDispon','rol','valoresCred', 'valoresCcred','valoresCyC','ordenPagos','pagos','codeCon','totalRecaud','saldoRecaudo','valoresFinRec','valoresFinSald', 'vigencia','years'));
        }

    }

    public function historicoExcel($id)
    {
        $vigencia = Vigencia::findOrFail($id);

        //EGRESOS
        $V = $vigencia->id;
        $vigencia_id = $V;
        $añoActual = Carbon::now()->year;
        $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
        $mesActual = 12;
        $primerLevel = Level::where('vigencia_id', $vigencia_id)->get()->first();
        $registers = Register::where('level_id', $ultimoLevel->id)->get();
        $registers2 = Register::where('level_id', '<', $ultimoLevel->id)->get();
        $ultimoLevel2 = Register::where('level_id', '<', $ultimoLevel->id)->get()->last();
        $fonts = FontsVigencia::where('vigencia_id',$vigencia_id)->get();
        $rubros = Rubro::where('vigencia_id', $vigencia_id)->get();
        $fontsRubros = FontsRubro::orderBy('font_vigencia_id')->get();
        $allRegisters = Register::orderByDesc('level_id')->get();
        $ordenP = OrdenPagos::all();

        $historico = Vigencia::where('vigencia', '!=', $añoActual)->get();
        foreach ($historico as $his) {
            if ($his->tipo == "0"){
                $years[] = [ 'info' => $his->vigencia." - Egresos", 'id' => $his->id];
            }else{
                $years[] = [ 'info' => $his->vigencia." - Ingresos", 'id' => $his->id];
            }
        }
        asort($years);

        foreach ($ordenP as $ord){
            if ($ord->registros->cdpsRegistro[0]->cdp->vigencia_id == $V){
                $ordenPagos[] = collect(['id' => $ord->id, 'code' => $ord->code, 'nombre' => $ord->nombre, 'persona' => $ord->registros->persona->nombre, 'valor' => $ord->valor, 'estado' => $ord->estado]);
            }
        }
        if (!isset($ordenPagos)){
            $ordenPagos[] = null;
            unset($ordenPagos[0]);
        } else {
            foreach ($ordenPagos as $data){
                $pagoFind = Pagos::where('orden_pago_id',$data['id'])->get();
                if ($pagoFind->count() == 1){
                    $pagos[] = collect(['id' => $pagoFind[0]->id, 'code' =>$pagoFind[0]->code, 'nombre' => $data['nombre'], 'persona' => $pagoFind[0]->orden_pago->registros->persona->nombre, 'valor' => $pagoFind[0]->valor, 'estado' => $pagoFind[0]->estado]);
                } elseif($pagoFind->count() > 1){
                    foreach ($pagoFind as $info){
                        $pagos[] = collect(['id' => $info->id, 'code' => $info->code, 'nombre' => $data['nombre'], 'persona' => $info->orden_pago->registros->persona->nombre, 'valor' => $info->valor, 'estado' => $info->estado]);
                    }
                }
            }
        }
        if (!isset($pagos)){
            $pagos[] = null;
            unset($pagos[0]);
        }

        global $lastLevel;
        $lastLevel = $ultimoLevel->id;
        $lastLevel2 = $ultimoLevel2->level_id;

        foreach ($fonts as $font){
            $fuentes[] = collect(['id' => $font->font->id, 'name' => $font->font->name, 'code' => $font->font->code]);
        }

        foreach ($fontsRubros as $fontsRubro){
            if ($fontsRubro->fontVigencia->vigencia_id == $vigencia_id){
                $fuentesRubros[] = collect(['valor' => $fontsRubro->valor, 'rubro_id' => $fontsRubro->rubro_id, 'font_vigencia_id' => $fontsRubro->font_vigencia_id]);
            }
        }
        $tamFountsRubros = count($fuentesRubros);

        foreach ($registers2 as $register2) {
            if ($register2->level->vigencia_id == $vigencia_id) {
                global $codigoLast;
                if ($register2->register_id == null) {
                    $codigoEnd = $register2->code;
                    $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                } elseif ($codigoLast > 0) {
                    if ($lastLevel2 == $register2->level_id) {
                        $codigo = $register2->code;
                        $codigoEnd = "$codigoLast$codigo";
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
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
                                $codigos[] = collect(['id' => $register->id, 'codigo' => $code, 'name' => $register->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                                if ($register->level_id == $lastLevel) {
                                    foreach ($rubros as $rubro) {
                                        if ($register->id == $rubro->register_id) {
                                            $newCod = "$code$rubro->cod";
                                            $fR = $rubro->FontsRubro;
                                            //dd($newCod, $fR);
                                            for ($i = 0; $i < $tamFountsRubros; $i++) {
                                                $rubrosF = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->orderBy('font_vigencia_id')->get();
                                                $numR = count($rubrosF);
                                                $numF = count($fonts);
                                                if ($numR == $numF) {
                                                    if ($fuentesRubros[$i]['rubro_id'] == $rubro->id) {
                                                        $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'fount_id' => $fuentesRubros[$i]['font_vigencia_id']]);
                                                    }
                                                } else {
                                                    foreach ($fonts as $font) {
                                                        if ($fuentesRubros[$i]['font_vigencia_id'] == $font->id) {
                                                            $FRubros[] = collect(['valor' => $fuentesRubros[$i]['valor'], 'rubro_id' => $fuentesRubros[$i]['rubro_id'], 'font_vigencia_id' => $font->id]);
                                                        } else {
                                                            $findFont = FontsRubro::where('rubro_id', $fuentesRubros[$i]['rubro_id'])->where('font_vigencia_id', $font->id)->get();
                                                            $numFinds = count($findFont);
                                                            if ($numFinds >= 1) {

                                                                $saveRubroF = new FontsRubro();

                                                                $saveRubroF->valor = 0;
                                                                $saveRubroF->valor_disp = 0;
                                                                $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                $saveRubroF->font_vigencia_id = $font->id + 1;

                                                                $saveRubroF->save();

                                                                break;
                                                            } else {

                                                                $saveRubroF = new FontsRubro();

                                                                $saveRubroF->valor = 0;
                                                                $saveRubroF->valor_disp = 0;
                                                                $saveRubroF->rubro_id = $fuentesRubros[$i]['rubro_id'];
                                                                $saveRubroF->font_vigencia_id = $font->id;

                                                                $saveRubroF->save();

                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $valFuent = FontsRubro::where('rubro_id', $rubro->id)->sum('valor');
                                            $codigos[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id]);
                                            $valDisp = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                                            $Rubros[] = collect(['id_rubro' => $rubro->id, 'id' => '', 'codigo' => $newCod, 'name' => $rubro->name, 'code' => $rubro->code, 'V' => $V, 'valor' => $valFuent, 'register_id' => $register->register_id, 'valor_disp' => $valDisp]);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $codigo = $register2->code;
                        $codigoEnd = "$codigoLast$codigo";
                        $codigoLast = $codigoEnd;
                        $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                    }
                } else {
                    $codigo = $register2->code;
                    $newRegisters = Register::findOrFail($register2->register_id);
                    $codigoNew = $newRegisters->code;
                    $codigoEnd = "$codigoNew$codigo";
                    $codigoLast = $codigoEnd;
                    $codigos[] = collect(['id' => $register2->id, 'codigo' => $codigoEnd, 'name' => $register2->name, 'code' => '', 'V' => $V, 'valor' => '', 'id_rubro' => '', 'register_id' => $register2->register_id]);
                }
            }
        }

        //VALOR DEL PRESUPUESTO INICIAL

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                        $ArraytotalFR[] = $valFuent;
                    }
                    if (isset($ArraytotalFR)) {
                        $totalFR = array_sum($ArraytotalFR);
                        $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalFR);
                    } else {
                        $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresIniciales); $i++) {
                        if ($valoresIniciales[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresIniciales[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresIniciales[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //VALOR TOTAL DE LAS ADICIONES

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valAdd = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"2")->sum('valor');
                        $ArraytotalAdd[] = $valAdd;
                    }
                    if (isset($ArraytotalAdd)) {
                        $totalAdd = array_sum($ArraytotalAdd);
                        $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $totalAdd, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalAdd);
                    } else {
                        $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinAdd); $i++) {
                        if ($valoresFinAdd[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinAdd[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinAdd[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //VALOR TOTAL DE LAS REDUCCIONES

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valRed = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"3")->sum('valor');
                        $ArraytotalRed[] = $valRed;
                    }
                    if (isset($ArraytotalRed)) {
                        $totalRed = array_sum($ArraytotalRed);
                        $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $totalRed, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalRed);
                    } else {
                        $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinRed); $i++) {
                        if ($valoresFinRed[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinRed[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinRed[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        // VALOR TOTAL DE LOS CREDITOS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valCred = RubrosMov::where('rubro_id', $rubrosReg->id)->where('movimiento',"1")->sum('valor');
                        $ArraytotalCred[] = $valCred;
                    }
                    if (isset($ArraytotalCred)) {
                        $totalCred = array_sum($ArraytotalCred);
                        $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalCred);
                    } else {
                        $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinCred); $i++) {
                        if ($valoresFinCred[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinCred[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        // VALOR TOTAL DE LOS CONTRACREDITOS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        foreach ($rubrosReg->fontsRubro as $FR){
                            foreach ($FR->rubrosMov as $movR){
                                if ($movR->movimiento == 1){
                                    $ArraytotalCCred[] = $movR->valor;
                                }
                            }
                        }
                    }
                    if (isset($ArraytotalCCred)) {
                        $totalCCred = array_sum($ArraytotalCCred);
                        $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $totalCCred, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalCCred);
                    } else {
                        $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinCCred); $i++) {
                        if ($valoresFinCCred[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinCCred[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinCCred[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //VALOR TOTAL DE CDPS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        foreach ($rubrosReg->rubrosCdp as $Rcdp){
                            $cdpInfo = Cdp::where('id', $Rcdp->cdps->id)->where('jefe_e', '!=', 3)->get();
                            if ($cdpInfo->count() > 0 ){
                                $ArraytotalCdp[] = $Rcdp->cdps->valor;
                            }
                        }
                    }
                    if (isset($ArraytotalCdp)) {
                        $totalCdp = array_sum($ArraytotalCdp);
                        $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $totalCdp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalCdp);
                    } else {
                        $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinCdp); $i++) {
                        if ($valoresFinCdp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinCdp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinCdp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //VALOR TOTAL DE LOS REGISTROS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosFReg = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosFReg as $rubro) {
                        if ($rubro->cdpRegistroValor->count() == 0){
                            $ArraytotalReg[] =  0 ;
                        }elseif ($rubro->cdpRegistroValor->count() > 1){
                            foreach ($rubro->cdpRegistroValor as $cdpRV){
                                if ($cdpRV->registro->secretaria_e == "3"){
                                    $sumaValores[] = $cdpRV->registro->val_total;
                                }
                            }
                            $ArraytotalReg[] = array_sum($sumaValores);
                            unset($sumaValores);
                        }else{
                            $reg = $rubro->cdpRegistroValor->first();
                            $ArraytotalReg[] = $reg['valor'];
                        }
                    }
                    if (isset($ArraytotalReg)) {
                        $totalReg = array_sum($ArraytotalReg);
                        $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $totalReg, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalReg);
                    } else {
                        $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinReg); $i++) {
                        if ($valoresFinReg[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinReg[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinReg[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //SUMA DE VALOR DISPONIBLE DEL RUBRO - CDP

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor_disp');
                        $ArraytotalFR[] = $valFuent;
                    }
                    if (isset($ArraytotalFR)) {
                        $totalFR = array_sum($ArraytotalFR);
                        $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalFR);
                    } else {
                        $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valorDisp); $i++) {
                        if ($valorDisp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valorDisp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valorDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        foreach ($rubros as $R){
            if ($R->rubrosCdp->count() == 0){
                $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0 ]) ;
            }
            if ($R->rubrosCdp->count() > 1){
                foreach ($R->rubrosCdp as $R3){
                    if ($R3->cdps->jefe_e == "2"){
                        $suma2[] = 0;
                    } else{
                        $suma2[] = $R3->rubrosCdpValor->sum('valor');
                    }
                }
                $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => array_sum($suma2)]) ;
                unset($suma2);
            }else{
                foreach ($R->rubrosCdp as $R2){
                    if ($R2->cdps->jefe_e == "2"){
                        $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => 0]) ;
                    }else {
                        $valoresCdp[] = collect(['id' => $R->id, 'name' => $R->name, 'valor' => $R2->rubrosCdpValor->sum('valor')]) ;
                    }
                }
            }

        }

        //VALOR DE LOS REGISTROS DEL RUBRO
        foreach ($rubros as $rub){
            if ($rub->cdpRegistroValor->count() == 0){
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => 0 ]) ;
            }elseif ($rub->cdpRegistroValor->count() > 1){
                foreach ($rub->cdpRegistroValor as $cdpRV){
                    if ($cdpRV->registro->secretaria_e == "3"){
                        $sumaValores[] = $cdpRV->registro->val_total;
                    }
                }
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => array_sum($sumaValores)]);
                unset($sumaValores);
            }else{
                $reg = $rub->cdpRegistroValor->first();
                $valoresRubro[] = collect(['id' => $rub->id, 'name' => $rub->name, 'valor' => $reg['valor']]) ;
            }
        }

        //VALORES TOTALES SALDO CDP

        for ($i=0;$i<count($valoresFinCdp);$i++){
            $valorFcdp[] = collect(['id' => $valoresFinCdp[$i]['id'], 'valor' => $valoresFinCdp[$i]['valor'] - $valoresFinReg[$i]['valor']]);
        }


        //VALOR DISPONIBLE CDP - REGISTROS
        for ($i=0;$i<count($valoresCdp);$i++){
            $valorDcdp[] = collect(['id' => $valoresCdp[$i]['id'], 'valor' => $valoresCdp[$i]['valor'] - $valoresRubro[$i]['valor']]);
        }


        //ORDEN DE PAGO

        $OP = OrdenPagosRubros::all();
        if ($OP->count() != 0){
            foreach ($OP as $val){
                $valores[] = ['id' => $val->cdps_registro->rubro->id, 'val' => $val->valor];
            }
            foreach ($valores as $id){
                $ids[] = $id['id'];
            }
            $valores2 = array_unique($ids);
            foreach ($valores2 as $valUni){
                $keys = array_keys(array_column($valores, 'id'), $valUni);
                foreach ($keys as $key){
                    $values[] = $valores[$key]["val"];
                }
                $valoresF[] = ['id' => $valUni, 'valor' => array_sum($values)];
                unset($values);
            }
            foreach ($rubros as $rub){
                $validate = in_array($rub->id, $valores2);
                if ($validate == true ){
                    $data = array_keys(array_column($valoresF, 'id'), $rub->id);
                    $x[] = $valoresF[$data[0]];
                    $valOP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                    unset($x);
                } else {
                    $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }
        } else {
            foreach ($rubros as $rub){
                $valOP[] = collect(['id' => $rub->id, 'valor' => 0]);
            }
        }

        //TOTALES ORDEN DE PAGO

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubs as $rubro) {
                        foreach ($valOP as $data){
                            if ($data['id'] == $rubro->id and $data['valor'] != 0){
                                $ArrayTotalOp[] = $data['valor'];
                            }
                        }
                    }
                    if (isset($ArrayTotalOp)) {
                        $totalOp = array_sum($ArrayTotalOp);
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $totalOp, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArrayTotalOp);
                    } else {
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinOp); $i++) {
                        if ($valoresFinOp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinOp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinOp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //PAGOS

        $pagos2 = PagoRubros::all();
        if ($pagos2->count() != 0) {

            foreach ($pagos2 as $val) {
                $valores1[] = ['id' => $val->rubro->id, 'val' => $val->valor];
            }
            foreach ($valores1 as $id1) {
                $ides[] = $id1['id'];
            }
            $valores3 = array_unique($ides);
            foreach ($valores3 as $valUni) {
                $keys = array_keys(array_column($valores1, 'id'), $valUni);
                foreach ($keys as $key) {
                    $values1[] = $valores1[$key]["val"];
                }
                $valoresZ[] = ['id' => $valUni, 'valor' => array_sum($values1)];
                unset($values1);
            }

            foreach ($rubros as $rub) {
                $validate = in_array($rub->id, $valores3);
                if ($validate == true) {
                    $data = array_keys(array_column($valoresZ, 'id'), $rub->id);
                    $x[] = $valoresZ[$data[0]];
                    $valP[] = collect(['id' => $rub->id, 'valor' => $x[0]['valor']]);
                    unset($x);
                } else {
                    $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
                }
            }
        }else {
            foreach ($rubros as $rub) {
                $valP[] = collect(['id' => $rub->id, 'valor' => 0]);
            }
        }

        //TOTALES PAGOS

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubs as $rubro) {
                        foreach ($valP as $data){
                            if ($data['id'] == $rubro->id and $data['valor'] != 0){
                                $ArrayTotalP[] = $data['valor'];
                            }
                        }
                    }
                    if (isset($ArrayTotalP)) {
                        $totalP = array_sum($ArrayTotalP);
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $totalP, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArrayTotalP);
                    } else {
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresFinP); $i++) {
                        if ($valoresFinP[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresFinP[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresFinP[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }

        //ADICION
        foreach ($rubros as $R2){
            $ad = RubrosMov::where([['rubro_id', $R2->id],['movimiento', '=', '2']])->get();
            if ($ad->count() > 0){
                $valoresAdd[] = collect(['id' => $R2->id, 'valor' => $ad->sum('valor')]) ;
            } else{
                $valoresAdd[] = collect(['id' => $R2->id, 'valor' => 0]) ;
            }
        }


        //REDUCCIÓN
        foreach ($rubros as $R3){
            $red = RubrosMov::where([['rubro_id', $R3->id],['movimiento', '=', '3']])->get();
            if ($red->count() > 0){
                $valoresRed[] = collect(['id' => $R3->id, 'valor' => $red->sum('valor')]) ;
            } else{
                $valoresRed[] = collect(['id' => $R3->id, 'valor' => 0]) ;
            }
        }


        //CREDITO
        foreach ($rubros as $R4){
            $cred = RubrosMov::where([['rubro_id', $R4->id],['movimiento', '=', '1']])->get();
            if ($cred->count() > 0){
                $valoresCred[] = collect(['id' => $R4->id, 'valor' => $cred->sum('valor')]) ;
            }else{
                $valoresCred[] = collect(['id' => $R4->id, 'valor' => 0]) ;
            }
        }

        //CONTRACREDITO
        foreach ($rubros as $R5){
            foreach ($R5->fontsRubro as $FR){
                foreach ($FR->rubrosMov as $movR) {
                    if ($movR->movimiento == 1){
                        $suma[] = $movR->valor;
                    }
                }
            }
            if (isset($suma)){
                $valoresCcred[] = collect(['id' => $R5->id, 'valor' => array_sum($suma)]);
                unset($suma);
            } else{
                $valoresCcred[] = collect(['id' => $R5->id, 'valor' => 0]);
            }
        }

        //CREDITO Y CONTRACREDITO

        for ($i=0;$i<sizeof($valoresCcred);$i++){
            if ($valoresCcred[$i]['id'] == $valoresCred[$i]['id']){
                $valoresCyC[] = collect(['id' => $valoresCcred[$i]['id'], 'valorC' => $valoresCred[$i]['valor'], 'valorCC' => $valoresCcred[$i]['valor']]) ;
            }
        }

        //PRESUPUESTO DEFINITIVO

        foreach ($allRegisters as $allRegister){
            if ($allRegister->level->vigencia_id == $vigencia_id) {
                if ($allRegister->level_id == $lastLevel) {
                    $rubrosRegs = Rubro::where('register_id', $allRegister->id)->get();
                    foreach ($rubrosRegs as $rubrosReg) {
                        $valFuent = FontsRubro::where('rubro_id', $rubrosReg->id)->sum('valor');
                        foreach ($valoresAdd as $valAdd) {
                            if ($rubrosReg->id == $valAdd["id"]) {
                                $valAdicion = $valAdd["valor"];
                            }
                        }
                        foreach ($valoresRed as $valRed) {
                            if ($rubrosReg->id == $valRed["id"]) {
                                $valReduccion = $valRed["valor"];
                            }
                        }
                        foreach ($valoresCred as $valCred) {
                            if ($rubrosReg->id == $valCred["id"]) {
                                $valCredito = $valCred["valor"];
                            }
                        }
                        foreach ($valoresCcred as $valCcred) {
                            if ($rubrosReg->id == $valCcred["id"]) {
                                $valCcredito = $valCcred["valor"];
                            }
                        }
                        if (isset($valAdicion) and isset($valReduccion)) {
                            $ArraytotalFR[] = $valFuent + $valAdicion - $valReduccion + $valCredito - $valCcredito;
                        } else {
                            $ArraytotalFR[] = $valFuent + $valCredito - $valCcredito;
                        }

                    }
                    if (isset($ArraytotalFR)) {
                        $totalFR = array_sum($ArraytotalFR);
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $totalFR, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($ArraytotalFR);
                    } else {
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($valoresDisp); $i++) {
                        if ($valoresDisp[$i]['register_id'] == $allRegister->id) {
                            $suma[] = $valoresDisp[$i]['valor'];
                        }
                    }
                    if (isset($suma)) {
                        $valSum = array_sum($suma);
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => $valSum, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                        unset($suma);
                    } else {
                        $valoresDisp[] = collect(['id' => $allRegister->id, 'valor' => 0, 'level_id' => $allRegister->level_id, 'register_id' => $allRegister->register_id]);
                    }
                }
            }
        }


        foreach ($codigos as $cod){
            if ($cod['valor']){
                foreach ($valoresAdd as $valores1){
                    if ($cod['id_rubro'] == $valores1['id']){
                        $valAd1 = $valores1['valor'];
                    }
                }
                foreach ($valoresRed as $valores2){
                    if ($cod['id_rubro'] == $valores2['id']){
                        $valRed1 = $valores2['valor'];
                    }
                }
                foreach ($valoresCred as $valores3){
                    if ($cod['id_rubro'] == $valores3['id']){
                        $valCred1 = $valores3['valor'];
                    }
                }
                foreach ($valoresCcred as $valores4){
                    if ($cod['id_rubro'] == $valores4['id']){
                        $valCcred1 = $valores4['valor'];
                    }
                }
                if (isset($valAd1) and isset($valRed1)){
                    $AD = $cod['valor'] + $valAd1 - $valRed1 + $valCred1 - $valCcred1;
                } else{
                    $AD = $cod['valor'] + $valCred1 - $valCcred1;
                }
                $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => $AD]);
            } elseif($cod['valor'] == 0 and $cod['id_rubro'] != ""){
                $ArrayDispon[] = collect(['id' => $cod['id_rubro'], 'valor' => 0]);
            }
        }

        //SALDO DISPONIBLE

        foreach ($ArrayDispon as $valDisp){
            foreach ($valoresCdp as $valCdp){
                if ($valCdp['id'] == $valDisp['id']){
                    $valrest = $valCdp['valor'];
                }
            }
            $saldoDisp[] = collect(['id' => $valDisp['id'], 'valor' => $valDisp['valor'] - $valrest]);
        }

        //ROL

        $roles = auth()->user()->roles;
        foreach ($roles as $role){
            $rol= $role->id;
        }

        //CUENTAS POR PAGAR

        for ($i=0;$i<sizeof($valOP);$i++){
            $valueTot = $valOP[$i]['valor'] - $valP[$i]['valor'];
            $valCP[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valueTot]);
            unset($valueTot);
        }

        //TOTAL CUENTAS POR PAGAR

        for ($i=0;$i<sizeof($valoresFinOp);$i++){
            $valTot = $valoresFinOp[$i]['valor'] - $valoresFinP[$i]['valor'];
            $valoresFinC[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $valTot]);
            unset($valTot);
        }

        //RESERVAS

        for ($i=0;$i<sizeof($valoresRubro);$i++){
            $valTot = $valoresRubro[$i]['valor'] - $valOP[$i]['valor'];
            $valR[] = collect(['id' => $valOP[$i]['id'], 'valor' => $valTot]);
            unset($valTot);
        }

        //TOTAL RESERVAS

        for ($i=0;$i<sizeof($valoresFinReg);$i++){
            $totally = $valoresFinReg[$i]['valor'] - $valoresFinOp[$i]['valor'];
            $valoresFinRes[] = collect(['id' => $valoresFinOp[$i]['id'], 'valor' => $totally]);
            unset($totally);
        }

        //CODE CONTRACTUALES

        $codeCon = CodeContractuales::all();

        //CDP's

        $cdps= Cdp::where('vigencia_id', $V)->get();

        //REGISTROS
        $allReg = Registro::all();
        foreach ($allReg as $reg){
            if ($reg->cdpsRegistro[0]->cdp->vigencia_id == $V){
                $registros[] = collect(['id' => $reg->id, 'code' => $reg->code, 'objeto' => $reg->objeto, 'nombre' => $reg->persona->nombre, 'valor' => $reg->valor, 'estado' => $reg->secretaria_e]);
            }
        }
        if (!isset($registros)){
            $registros[] = null;
            unset($registros[0]);
        }
        if (!isset($cdps)){
            $cdps[] = null;
            unset($cdps[0]);
        }

        $año = $vigencia->vigencia;
        $mesFileName = "12-".$año;
        $carbon_fi = $año.'-01-01';
        $carbon_ff = $año.'-12-31';

        return Excel::download(new InfMensualExport($mesFileName,$año, $codigos, $valoresIniciales, $valoresFinAdd, $valoresAdd, $valoresFinRed, $valoresRed, $valoresFinCred, $valoresCred,
            $valoresFinCCred, $valoresCcred, $valoresDisp, $ArrayDispon, $valoresFinCdp, $valoresCdp, $valoresFinReg, $valoresRubro, $valorDisp, $saldoDisp, $valorFcdp,$valorDcdp,
            $valoresFinOp, $valOP, $valoresFinP, $valP, $valoresFinC, $valCP, $valoresFinRes, $valR, $carbon_fi, $carbon_ff),
            'Informe Mensual Egresos '.$mesFileName.'.xlsx');
    }

}