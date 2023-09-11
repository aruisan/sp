<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Egresos;

use App\bpinVigencias;
use App\Http\Controllers\Controller;
use App\BPin;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Session;

class IndexController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:presupuesto-list');
    }

    public function asignaRubroProyecto(Request $request)
    {
        $bpinFind = BPin::where('cod_actividad', $request->actividadCode)->first();

        $bpinSave = new bpinVigencias();
        $bpinSave->bpin_id = $bpinFind->id;
        $bpinSave->dep_rubro_id = $request->depRubroID;
        $bpinSave->vigencia_id = $request->vigencia_id;
        $bpinSave->propios = $request->valueAsignarRubro;
        $bpinSave->saldo = $request->valueAsignarRubro;
        $bpinSave->save();

        Session::flash('success','Se ha asignado exitosamente la actividad al rubro.');
        return redirect('presupuesto/');

    }

    public function newPrepLoad(){
        if (auth()->user()->roles->first()->id == 4) return redirect('/impuestos');

        $today = Carbon::today();
        $añoActual = $today->year;
        $mesActual = $today->month;
        $prepSaved = PresupuestoSnap::where('mes', $mesActual)->where('año', $añoActual)->where('tipo','EGRESOS')->first();
        $lastDay = Carbon::now()->subDay()->toDateString();
        $actuallyDay = Carbon::now()->toDateString();
        $bpins = BPin::all();


        if (!$prepSaved) {
            Artisan::call("schedule:run");
            $V = "Vacio";

            return view('hacienda.presupuesto.indexCuipoFastCharge', compact( 'prepSaved',
                'añoActual', 'mesActual','V','bpins'));
        } else{
            $V = $prepSaved->vigencia_id;
            $dataPrepSaved = PresupuestoSnapData::where('pre_snap_id', $prepSaved->id)->first();
            $fechaData = Carbon::parse($dataPrepSaved->created_at)->subHours(5);
            $codeCon = CodeContractuales::all();

            foreach ($bpins as $bpin){
                $bpin['rubro'] = "No";
                if (count($bpin->rubroFind) > 0) {
                    foreach ($bpin->rubroFind as $rub){
                        if ($rub->vigencia_id == $V){
                            $bpin['rubro'] = $rub->dep_rubro_id;
                        }
                    }
                }
            }

            return view('hacienda.presupuesto.indexCuipoFastCharge', compact( 'prepSaved',
                'añoActual', 'mesActual','V','codeCon','lastDay','actuallyDay','bpins','fechaData'));
        }
    }

    public function getPrepSaved(Request $request){

        $prepSaved = PresupuestoSnapData::where('pre_snap_id', $request->prepSaved['id'])->get();
        foreach ($prepSaved as $item){

            $rubro = Rubro::where('vigencia_id', $request->prepSaved['vigencia_id'])->where('cod', $item->rubro)->first();
            if ($rubro) $item->rubroLink = '<a href="presupuesto/rubro/'.$rubro->id.'">'.$item->rubro.'</a>';
            else $item->rubroLink = $item->rubro;

            if ($item->name_act != ""){
                $bpin = BPin::where('cod_actividad', $item->code_act)->first();
                if ($bpin) $item->code_act = '<a href="presupuesto/actividad/'.$bpin->id.'/'.$request->prepSaved['vigencia_id'].'">'.$item->code_act.'</a>';
            }

            $item->p_inicial = '$'.number_format($item->p_inicial, 0);
            $item->adicion = '$'.number_format($item->adicion, 0);
            $item->reduccion = '$'.number_format($item->reduccion, 0);
            $item->credito = '$'.number_format($item->credito, 0);
            $item->ccredito = '$'.number_format($item->ccredito, 0);
            $item->p_def = '$'.number_format($item->p_def, 0);
            $item->cdps = '$'.number_format($item->cdps, 0);
            $item->rps = '$'.number_format($item->rps, 0);
            $item->saldo_disp = '$'.number_format($item->saldo_disp, 0);
            $item->saldo_cdps = '$'.number_format($item->saldo_cdps, 0);
            $item->ops = '$'.number_format($item->ops, 0);
            $item->pagos = '$'.number_format($item->pagos, 0);
            $item->cuentas_pagar = '$'.number_format($item->cuentas_pagar, 0);
            $item->reservas = '$'.number_format($item->reservas, 0);
        }
        return $prepSaved;

    }

    public function refreshPrepSaved(){
        Artisan::call("schedule:run");

        return "OK";
    }
}
