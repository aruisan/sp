<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\bpinVigencias;
use App\Http\Controllers\Controller;
use App\Model\Admin\DependenciaRubroFont;
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

class TrasladosController extends Controller
{

    public function index($año){
        $traslados = RubrosMov::where('valor','>',0)->whereBetween('created_at',array($año.'-01-01', $año.'-12-31'))->get();
        $rol = auth()->user()->roles->first()->id;

        return view('hacienda.presupuesto.traslados.index', compact('traslados','año', 'rol'));
    }

    public function create($año){
        $presupuestos = Vigencia::where('vigencia', $año)->get();
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

        $añoActual = Carbon::today()->year;
        $prepSaved = PresupuestoSnap::where('año', $añoActual)->where('tipo','EGRESOS')->first();
        $bpins = bpinVigencias::where('vigencia_id', $prepSaved->vigencia_id)->where('saldo','>',0)->get();
        $bpinsAll = bpinVigencias::where('vigencia_id', $prepSaved->vigencia_id)->get();

        foreach ($bpinsAll as $data){
            if (!isset($data->rubro->fontRubro->sourceFunding)){
                $fontRubro = FontsRubro::find($data->rubro->rubro_font_id);
                $data->rubro->fontRubr = $fontRubro;
                dd($data, $data->rubro, $data->rubro->fontRubr);
            }
        }

        return view('hacienda.presupuesto.traslados.create', compact('año','presupuestos',
            'rubrosEgresos','bpins', 'rubrosEgresosAll', 'bpinsAll'));
    }

    public function depCred(Request $request){
        $dependencia = DependenciaRubroFont::find($request->id);
        if ($dependencia){
            if ($dependencia->saldo > 0) return [$dependencia->saldo, $dependencia->fontRubro->rubro->cod.' - '.$dependencia->fontRubro->rubro->name.' - '.$dependencia->fontRubro->sourceFunding->code.' - '.$dependencia->fontRubro->sourceFunding->description.' - '.$dependencia->dependencias->name];
            else return "SIN SALDO";
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
