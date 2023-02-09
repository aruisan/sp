<?php

namespace App\Http\Controllers\Impuestos\Predial;

use App\Http\Controllers\Controller;
use App\Model\Administrativo\ImpuestosPredial\Liquidador;
use App\Model\Impuestos\Pagos;
use App\Model\Impuestos\Predial;
use App\Model\Impuestos\PredialCalendario;
use App\Model\Impuestos\PredialContribuyentes;
use App\Model\Impuestos\PredialLiquidacion;
use App\Model\User;
use App\Traits\NaturalezaJuridicaTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use PDF;
use DB;

class PredialController extends Controller
{
    use NaturalezaJuridicaTraits;
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
     * Show the form of predial.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::find(Auth::user()->id);
        $action = "Creación";
        $contribuyente = PredialContribuyentes::where('email',$user->email)->get();
        $predios = $contribuyente;
        $añoActual = Carbon::today()->format('Y');
        if (count($contribuyente) == 0){
            Session::flash('warning', 'No se encuentra información del usuario almacenada en el sistema - Contacte con un funcionario.');
            return back();
        } else{
            foreach ($contribuyente as $contri){
                $impHechos = Predial::where('imp_pred_contri_id', $contri->id)->get();
                foreach ($impHechos as $impHecho){
                    if (Carbon::parse($impHecho->fechaPago)->format('Y') == $añoActual) $hechos[] = $contri->id;
                }
            }

            if (isset($hechos)){
                if (count($hechos) >= count($contribuyente)){
                    if (count($contribuyente) == 1){
                        Session::flash('warning', 'Ya ha realizado el impuesto predial de su predio por este año.');
                        return back();
                    } else{
                        Session::flash('warning', 'Ya ha realizado el impuesto predial de sus predios por este año.');
                        return back();
                    }
                } else{
                    foreach ($predios as $validate){
                        $clave = array_search($validate->id, $hechos);
                        if ($clave === FALSE) $validatePred[] = collect($validate);
                    }
                }
            }
            if (isset($validatePred)) $predios = $validatePred;
            $contribuyente = $contribuyente[0];

            return view('impuestos.predial.create', compact('action','contribuyente','predios'));
        }
    }


    /**
     * Get imp calendar
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getImpCalendar(Request $request){
        $fecha = $request->date;
        $calendario = PredialCalendario::all();
        foreach ($calendario as $item){
            if ($fecha >= $item->f_inicio and $fecha <= $item->f_final){
                return $item->valor;
            }
        }
        return 0;
    }


    /**
     * Get predio info
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPredio(Request $request){
        $predio = PredialContribuyentes::find($request->id);
        if ($predio) {
            $hoy = Carbon::today()->format('Y-m-d');
            if ($predio->a2018 > 0) $debe = 2018;
            elseif ($predio->a2019 > 0) $debe = 2019;
            elseif ($predio->a2020 > 0) $debe = 2020;
            elseif ($predio->a2021 > 0) $debe = 2021;
            elseif ($predio->a2022 > 0) $debe = 2022;
            elseif ($predio->a2023 > 0) $debe = 2023;
            else {
                Session::flash('warning', 'Ese predio se encuentra sin deuda.');
                return back();
            }
            $predio->deudaYear = $debe;
            $predio->hoy = $hoy;
            return $predio;
        } else return 0;
    }


    /**
     * Get value liquidador
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function liquidar(Request $request){

        $fechaPago = $request->fecha_pago;
        $añoVencimiento = $request->añoVencimiento;
        $subTotal = $request->subTotal;
        $mesPago = date('m', strtotime($fechaPago));
        $añoPago = date('Y', strtotime($fechaPago));
        $diaPago = date('d', strtotime($fechaPago));
        $añoActual = date('Y');

        if ($añoActual != $añoVencimiento){
            $liquidador = Liquidador::whereBetween('vencimiento',array($añoVencimiento.'-08-01', $fechaPago))->orderBy('id','DESC')->get();
            foreach ($liquidador as $item){
                $diasMes = date('d', strtotime($item->vencimiento));
                $porcent = $subTotal * floatval($item->valor) / 100;
                $interesMoraMeses[] = $porcent * $diasMes / 365;
            }

            $liquidadorLastMes = Liquidador::where('año', $añoPago)->where('mes',$mesPago)->get();
            if (count($liquidadorLastMes) > 0){
                $porcent = $subTotal * floatval($liquidadorLastMes[0]->valor) / 100;
                $interesMoraMeses[] = $porcent * $diaPago / 365;
            } else $interesMoraMeses[] = 0;

            return array_sum($interesMoraMeses);
        } else {
            return 0;
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
        $predial = new Predial();
        $predial->cedula = $request->cedula;
        $predial->matricula = $request->matricula;
        $predial->tasaInt = $request->tasaInt;
        $predial->tarifaMil = $request->tarifaMil;
        $predial->tarifaBomb = $request->tarifaBomb;
        $predial->fechaPago = $request->fechaPago;
        $predial->tasaDesc = $request->tasaDesc;
        $predial->año = $request->año;
        $predial->añoInicio = $request->añoInicio;
        $predial->user_id  = Auth::user()->id;
        $predial->imp_pred_contri_id = $request->predio;

        //TOTALES IMPUESTO
        $predial->tot_imp = 0;
        $predial->desc_imp = 0;
        $predial->tot_pago = $request->totalPago;
        $predial->save();

        $añoPago = Carbon::parse($request->fechaPago)->format('Y');

        for ($i = 0; $i < $añoPago - $predial->año +1; $i++) {

            //VALORES LIQUIDACION

            $liquidacion = new PredialLiquidacion();
            $añoCiclo = $predial->año + $i;

            $añoReq = "año".$añoCiclo;
            $liquidacion->año = $request->$añoReq;

            $fechaVenReq = Carbon::parse($añoCiclo."-08-01")->format('Y-m-d');
            $liquidacion->fecha_venc = $fechaVenReq;

            $avaluoReq = "a".$añoCiclo;
            $liquidacion->avaluo = $request->$avaluoReq;

            $subTotalReq = "subTotal".$añoCiclo;
            $liquidacion->sub_total = $request->$subTotalReq;

            $interesMoraReq = "interesMora".$añoCiclo;
            $liquidacion->int_mora = $request->$interesMoraReq;

            $totalReq = "total".$añoCiclo;
            $liquidacion->tot_año = $request->$totalReq;

            $impPredialReq = "impPredial".$añoCiclo;
            $liquidacion->imp_predial = $request->$impPredialReq;

            $tasaBomberilReq = "tasaBomberil".$añoCiclo;
            $liquidacion->tasa_bomberil = $request->$tasaBomberilReq;

            $liquidacion->tasa_ambiental = 0;
            $liquidacion->int_ambiental = 0;
            $liquidacion->imp_predial_id  = $predial->id;

            $liquidacion->save();
        }

        $pago = new Pagos();
        $pago->modulo = "PREDIAL";
        $pago->entity_id = $predial->id;
        $pago->estado = "Generado";
        $pago->valor = $predial->tot_pago;
        $pago->fechaCreacion = Carbon::today();
        $pago->user_id = Auth::user()->id;
        $pago->save();

        Session::flash('success', 'Impuesto predial liquidado exitosamente.');

        return redirect('/impuestos/Pagos/PRED');

    }

    /**
     * Generate facture from the PREDIAL.
     *
     * @return \Illuminate\Http\Response
     */
    public function factura($id_predial){
        $predial = Predial::find($id_predial);
        $infoContri = PredialContribuyentes::find($predial->imp_pred_contri_id);
        $predial->numCatas = $infoContri->numCatastral;
        $user = User::find($predial->user_id);
        $pago = Pagos::where('modulo','PREDIAL')->where('entity_id',$id_predial)->get();
        $liquidacion = $predial->liquidacion;
        $predial->presentacion = Carbon::parse($pago[0]->fechaCreacion)->format('d-m-Y');
        $contribuyente = PredialContribuyentes::where('email',$user->email)->get();
        $contribuyente = $contribuyente[0];
        $totImpPredial = 0;
        $totImpAdi = 0;
        $totIntPred = 0;
        foreach ($liquidacion as $item){
            $totImpPredial = $totImpPredial + $item->imp_predial;
            $totImpAdi = $totImpAdi + $item->tasa_bomberil;
            $totIntPred = $totIntPred + $item->int_mora;
        }
        if (strlen($predial->tot_pago) < 12){
            $newValue = $predial->tot_pago;
            for ($i = 0; $i < 12 - strlen($predial->tot_pago); $i++) {
                $newValue =  '0'.$newValue;
            }
        } else $newValue = $predial->tot_pago;

        if (strlen($pago[0]->id) < 12){
            $numFacturaCodebar = $pago[0]->id;
            for ($i = 0; $i < 12 - strlen($pago[0]->id); $i++) {
                $numFacturaCodebar =  '0'.$numFacturaCodebar;
            }
        } else $numFacturaCodebar = $pago[0]->id;

        $pdf = PDF::loadView('impuestos.predial.pdf', compact('contribuyente','predial','liquidacion','pago', 'totImpPredial',
        'totImpAdi', 'totIntPred', 'newValue','numFacturaCodebar'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }
}
