<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Egresos;

use App\bpinVigencias;
use App\Http\Controllers\Controller;
use App\BPin;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Informes\CodeContractuales;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;

class ActividadController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:presupuesto-list');
    }

    public function show($id, $vigencia){

        $bpin = BPin::find($id);

        dd($bpin, $vigencia);

        return view('hacienda.presupuesto.actividad.show', compact('bpin'));
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
}
