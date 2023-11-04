<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\bpinVigencias;
use App\Http\Controllers\Api\Presupuesto\CdpController;
use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Hacienda\Presupuesto\Nomina\PrepNomina;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ResourceTraits;
use App\Resource;
use Illuminate\Support\Facades\Storage;

use Session;

class PrepNominaController extends Controller
{

    public function index($año){
        $traslados = RubrosMov::where('valor','>',0)->whereBetween('created_at',array($año.'-01-01', $año.'-12-31'))->get();
        $rol = auth()->user()->roles->first()->id;

        return view('hacienda.presupuesto.traslados.index', compact('traslados','año', 'rol'));
    }

    public function create($año){
        $presupuesto = Vigencia::where('vigencia', $año)->where('tipo',0)->first();

        return view('hacienda.presupuesto.nomina.create', compact('año','presupuesto'));
    }

    public function findNomina(Request $request){
        $nomina = PrepNomina::where('mes', $request->mes)->where('tipo', $request->tipo)->where('año', $request->año)
            ->where('estado', 0)->first();
        if ($nomina) return 1;
        else return 0;
    }

    public function makeNomina(Request $request){
        $nomina = PrepNomina::where('mes', $request->mes)->where('tipo', $request->tipo)->where('año', $request->año)
            ->where('estado', 0)->first();
        $presupuesto = Vigencia::where('vigencia', $request->año)->where('tipo',0)->first();

        $countCdps = Cdp::where('vigencia_id', $presupuesto->id)->orderBy('id')->get()->last();

        if ($countCdps == null) $count = 0;
        else $count = $countCdps->code;

        $valores = [1,2,3,4,5];

        //ELABORACION AUTOMATICA DE CDPs
        $cdp = new Cdp();
        $cdp->code = $countCdps->code + 1;
        $cdp->name = "Pago Nomina de ".$request->tipo." mes de ".$this->mounth($request->mes);
        $cdp->fecha = $_ENV['FECHA_CDPS_RPS'];
        $cdp->tipo = 'Funcionamiento';
        $cdp->valueControl = array_sum($valores);
        $cdp->valor = array_sum($valores);
        $cdp->dependencia_id = 15;
        $cdp->saldo = 0;
        $cdp->secretaria_e = 3;
        $cdp->ff_secretaria_e = $_ENV['FECHA_CDPS_RPS'];
        $cdp->alcalde_e = 3;
        $cdp->ff_alcalde_e = $_ENV['FECHA_CDPS_RPS'];
        $cdp->jefe_e = 3;
        $cdp->ff_jefe_e = $_ENV['FECHA_CDPS_RPS'];
        $cdp->vigencia_id = $presupuesto->id;
        $cdp->created_at = $_ENV['FECHA_CDPS_RPS'].' 12:00:00';
        $cdp->secretaria_user_id = 4;
        //$cdp->save();
        dd($cdp);
    }

    public function mounth($mes){
        switch ($mes){
            case 1:
                return "ENERO";
            case 2:
                return "FEBRERO";
            case 3:
                return "MARZO";
            case 4:
                return "ABRIL";
            case 5:
                return "MAYO";
            case 6:
                return "JUNIO";
            case 7:
                return "JULIO";
            case 8:
                return "AGOSTO";
            case 9:
                return "SEPTIEMBRE";
            case 10:
                return "OCTUBRE";
            case 11:
                return "NOVIEMBRE";
            case 12:
                return "DICIEMBRE";
        }
    }


    public function actividadCred(Request $request){
        $dependencia = bpinVigencias::find($request->id);
        if ($dependencia){
            if ($dependencia->saldo > 0) return [$dependencia->saldo, $dependencia->bpin->cod_actividad.' - '.$dependencia->bpin->actividad.' - '.$dependencia->rubro->dependencias->name.' - '.$dependencia->rubro->fontRubro->sourceFunding->code.' - '.$dependencia->rubro->fontRubro->sourceFunding->description];
            else return "SIN SALDO";
        }
    }

    public function store(Request $request){
        $año = Carbon::today()->year;
        //TRASLADO DE FUNCIONAMIENTO
        if ($request->tipTras == '2'){
            $depCC = DependenciaRubroFont::find($request->fontRubEgr);
            if ($depCC->saldo >= $request->dineroCC){
                if ($depCC->fontRubro->valor_disp >= $request->dineroCC){

                    //SE DESCUENTA EL DINERO A LA DEPENDENCIA
                    $depCC->saldo = $depCC->saldo - $request->dineroCC;

                    //SE DESCUENTA EL DINERO A LA FUENTE DEL RUBRO
                    $depCC->fontRubro->valor_disp = $depCC->fontRubro->valor_disp - $request->dineroCC;

                    $depC = DependenciaRubroFont::find($request->fontRubCred);

                    //SE ADICIONA EL DINERO A LA DEPENDENCIA
                    $depC->saldo = $depC->saldo + $request->dineroCC;

                    //SE ADICIONA EL DINERO A LA FUENTE DEL RUBRO
                    $depC->fontRubro->valor_disp = $depC->fontRubro->valor_disp + $request->dineroCC;

                    $rubroMov = new RubrosMov();
                    $rubroMov->valor = $request->dineroCC;
                    $rubroMov->fonts_rubro_id = $depC->fontRubro->id;
                    $rubroMov->font_vigencia_id = $depC->vigencia_id;
                    $rubroMov->rubro_id = $depC->fontRubro->rubro->id;
                    $rubroMov->resource_id = 0;
                    $rubroMov->movimiento = '1';
                    $rubroMov->dep_rubro_font_cred_id = $depC->id;
                    $rubroMov->dep_rubro_font_cc_id = $depCC->id;
                    $rubroMov->save();

                    $file = new ResourceTraits;
                    $file->resourceMov($request->fileRes, 'public/AdicionyRed', $rubroMov->id);

                    $depCC->save();
                    $depCC->fontRubro->save();
                    $depC->save();
                    $depC->fontRubro->save();

                    Session::flash('success','El traslado se ha realizado correctamente');
                    return redirect('presupuesto/traslados/'.$año);
                } else{
                    Session::flash('warning','El traslado no puede tener un valor superior al disponible en la fuente');
                    return back();
                }
            } else {
                Session::flash('warning','El traslado no puede tener un valor superior al disponible en el rubro');
                return back();
            }
        } elseif ($request->tipTras == '1'){
            //TRASLADO DE INVERSION
            $bpinCC = bpinVigencias::find($request->activCC);
            if ($bpinCC->saldo >= $request->dineroCC){
                if ($bpinCC->rubro->saldo >= $request->dineroCC){
                    //SE DESCUENTA EL DINERO A BPIN VIGENCIA
                    $bpinCC->saldo = $bpinCC->saldo - $request->dineroCC;

                    //SE DESCUENTA EL DINERO A LA DEPENDENCIA RUBRO FONT
                    $bpinCC->rubro->saldo = $bpinCC->rubro->saldo - $request->dineroCC;

                    $activC = bpinVigencias::find($request->actividadCred);

                    //SE ADICIONA EL DINERO A BPIN VIGENCIA
                    $activC->saldo = $activC->saldo + $request->dineroCC;

                    //SE ADICIONA EL DINERO A LA DEPENDENCIA RUBRO FONT
                    $activC->rubro->saldo = $activC->rubro->saldo + $request->dineroCC;

                    $rubroMov = new RubrosMov();
                    $rubroMov->valor = $request->dineroCC;
                    $rubroMov->fonts_rubro_id = $activC->rubro->fontRubro->id;
                    $rubroMov->font_vigencia_id = $activC->rubro->vigencia_id;
                    $rubroMov->rubro_id = $activC->rubro->fontRubro->rubro->id;
                    $rubroMov->resource_id = 0;
                    $rubroMov->movimiento = '1';
                    $rubroMov->dep_rubro_font_cred_id = $activC->rubro->id;
                    $rubroMov->dep_rubro_font_cc_id = $bpinCC->rubro->id;
                    $rubroMov->save();

                    $file = new ResourceTraits;
                    $file->resourceMov($request->fileRes, 'public/AdicionyRed', $rubroMov->id);

                    $bpinCC->save();
                    $bpinCC->rubro->save();
                    $activC->save();
                    $activC->rubro->save();

                    Session::flash('success','El traslado se ha realizado correctamente');
                    return redirect('presupuesto/traslados/'.$año);
                } else {
                    Session::flash('warning','El traslado no puede tener un valor superior al disponible en la dependencia de la actividad');
                    return back();
                }
            } else{
                Session::flash('warning','El traslado no puede tener un valor superior al disponible a la actividad');
                return back();
            }
        } else{
            Session::flash('warning','Debe seleccionar si el traslado es de inversion o de funcionamiento');
            return back();
        }
    }

    public function show($id){
        $rubroMov = RubrosMov::find($id);
        $año = Carbon::parse($rubroMov->created_at)->year;

        if ($rubroMov->movimiento == '1'){
            $depCred = DependenciaRubroFont::find($rubroMov->dep_rubro_font_cred_id);
            $depCC = DependenciaRubroFont::find($rubroMov->dep_rubro_font_cc_id);

            //TRASLADOS DE INVERSION
            $bpinVigCred = bpinVigencias::where('dep_rubro_id', $depCred->id)->first();
            if ($bpinVigCred)$depCred->bpinVig =  $bpinVigCred;
            $bpinVigCC = bpinVigencias::where('dep_rubro_id', $depCC->id)->first();
            if ($bpinVigCC)$depCC->bpinVig =  $bpinVigCC;

        } elseif($rubroMov->dep_rubro_font_id) {
            $depCred = DependenciaRubroFont::find($rubroMov->dep_rubro_font_id);
            $depCC = [];
        } else{
            $depCred = FontsRubro::find($rubroMov->fonts_rubro_id);
            $depCC = [];
        }

        if ($rubroMov->resource_id == 0){
            foreach ($rubroMov->ResourcesMov as $resource) $files[] = collect(['idResource' => $resource->id , 'ruta' => $resource->ruta, 'mov' => $rubroMov->movimiento,
                'fecha' => Carbon::parse($rubroMov->created_at)->format('d-m-Y')]);
        } else $files[] = collect(['idResource' => $rubroMov->resource_id , 'ruta' => $rubroMov->Resource->ruta, 'mov' => $rubroMov->movimiento,
            'fecha' => Carbon::parse($rubroMov->created_at)->format('d-m-Y')]);

        return view('hacienda.presupuesto.traslados.show', compact('año', 'rubroMov','depCred',
        'depCC', 'files'));
    }
}
