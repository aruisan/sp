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

    public function index($vigencia_id){
        $vigencia = Vigencia::find($vigencia_id);

        return back();
    }

    public function show($id, $vigencia_id){

        $bpin = BPin::find($id);
        $vigencia = Vigencia::find($vigencia_id);

        return view('hacienda.presupuesto.actividad.show', compact('bpin','vigencia'));
    }

    public function certProyecto($code){

        $proyectos = BPin::where('cod_proyecto', $code)->get();
        if (count($proyectos) > 0){
            $proyecto = $proyectos->first();
            $hoy = Carbon::now();

            $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

            $pdf = \PDF::loadView('hacienda.presupuesto.actividad.pdf', compact('proyecto','hoy','dias','meses'))
                ->setOptions(['images' => true,'isRemoteEnabled' => true]);
            return $pdf->stream();

        } else {
            Session::flash('warning','El proyecto no se encuentra registrado en el sistema.');
            return back();
        }
    }

}
