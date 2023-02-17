<?php

namespace App\Http\Controllers\Impuestos\Pagos;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Impuestos\IcaRetenedor;
use App\Model\Impuestos\PazySalvo;
use App\Model\Impuestos\Predial;
use App\Model\Impuestos\PredialContribuyentes;
use App\Model\Impuestos\PredialLiquidacion;
use App\Traits\NaturalezaJuridicaTraits;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\IcaContri;
use App\Model\Impuestos\Pagos;
use App\Traits\ResourceTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\User;
use Carbon\Carbon;
use Session;
use PDF;

class PagosController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($modulo)
    {
        $user = User::find(Auth::user()->id);
        $bancos = PucAlcaldia::where('id', '>=', 9)->where('id', '<=', 50)->get();

        if ($modulo == "PRED"){
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','PREDIAL')->get();
        } else {
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','!=','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','!=','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','!=','PREDIAL')->get();
        }

        return view('impuestos.pagos.index', compact('pagosPendientes', 'pagosHistoricos','pagosBorrador','modulo','bancos'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Impuestos\Pagos $pago_id
     * @return \Illuminate\Http\Response
     */
    public function show($pago_id)
    {
        $pago = Pagos::find($pago_id);
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        if ($pago->modulo == "ICA-Contribuyente") {
            $formulario = IcaContri::find($pago->entity_id);

            return view('impuestos.pagos.show', compact('pago', 'formulario','user','rit'));
        } elseif ($pago->modulo == "PREDIAL"){
            $formulario = Predial::find($pago->entity_id);
            $formulario->liquid = $formulario->liquidacion;
            $formulario->presentacion = Carbon::parse($pago->fechaCreacion)->format('d-m-Y');

            return view('impuestos.pagos.show', compact('pago', 'formulario','user','rit'));

        } elseif ($pago->modulo == "ICA-AgenteRetenedor"){
            $formulario = IcaRetenedor::find($pago->entity_id);

            return view('impuestos.pagos.show', compact('pago', 'formulario','user','rit'));
        } else {
            Session::flash('warning', 'No se detecta el modulo de pago.');
            return redirect()->back();
        }

    }

    /**
     * Update pay with the new date and state.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request){
        $pago = Pagos::find($request->pago_id);
        $pago->estado = "Generado";
        $pago->fechaCreacion = Carbon::today();
        $pago->save();

        Session::flash('success', 'Formulario '.$pago->modulo.' declaración de contribuyente presentado exitosamente.');
        return redirect('/impuestos/Pagos');
    }


    /**
     * Update pay with the new date and state.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Constancia(Request $request){
        if (!$request->hasFile('constanciaPago')){
            Session::flash('warning', 'Hay algun error en el archivo, intente de nuevo por favor.');
            return redirect('/impuestos/Pagos');
        } else {

            $file = new ResourceTraits;
            $resource = $file->resource($request->constanciaPago, 'public/Impuestos/ConstanciaPagos');

            $pago = Pagos::find($request->regId);
            $pago->estado = "Pagado";
            $pago->fechaPago = Carbon::today();
            $pago->resource_id = $resource;
            $pago->user_pago_id = Auth::user()->id;
            $pago->puc_alcaldia_id = $request->cuenta;
            $pago->save();

            if ($pago->modulo == 'PREDIAL') $modulo = "PRED";
            else $modulo = "ICA";

            Session::flash('success', 'Pago aplicado exitosamente.');

            return redirect('/impuestos/Pagos/'.$modulo);

        }
    }

    /**
     * Update pay with the new date and state from users admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ConstanciaAdmin(Request $request){
        if (!$request->hasFile('constanciaPago')){
            Session::flash('warning', 'Hay algun error en el archivo, intente de nuevo por favor.');
            return redirect('/administrativo/impuestos/admin');
        } else {
            $file = new ResourceTraits;
            $resource = $file->resource($request->constanciaPago, 'public/Impuestos/ConstanciaPagos');

            $pago = Pagos::find($request->regId);
            $pago->estado = "Pagado";
            $pago->fechaPago = Carbon::today();
            $pago->resource_id = $resource;
            $pago->user_pago_id = Auth::user()->id;
            $pago->puc_alcaldia_id = $request->cuenta;
            $pago->save();

            Session::flash('success', 'Pago aplicado exitosamente.');
            return redirect('/administrativo/impuestos/admin');
        }
    }

    /**
     * Validate if the pay can be downloaded.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validatePagoDownload(Request $request){
        $pago = Pagos::find($request->payId);
        if ($pago->download > 0) return 'OK';
        else return 'FALSE';
    }

    /**
     * Delete Pay
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deletePago(Request $request){
        $pago = Pagos::find($request->payId);
        if ($pago->modulo == 'PREDIAL'){
            //SE ELIMINA LA LIQUIDACIÓN HECHA
            $impLiquid = PredialLiquidacion::where('imp_predial_id', $pago->entity_id)->get();
            foreach ($impLiquid as $imp) $imp->delete();

            //SE ELIMINA EL FORMULARIO
            $imp = Predial::find($pago->entity_id);
            $imp->delete();

            //SE ELIMINA EL PAGO
            $pago->delete();

            return 'OK';

        } elseif($pago->modulo == 'ICA-AgenteRetenedor'){

            //SE ELIMINA EL FORMULARIO
            $imp = IcaRetenedor::find($pago->entity_id);
            $imp->delete();

            //SE ELIMINA EL PAGO
            $pago->delete();

            return 'OK';


        }elseif($pago->modulo == 'ICA-Contribuyente'){

            //SE ELIMINA EL FORMULARIO
            $imp = IcaContri::find($pago->entity_id);
            $imp->delete();

            //SE ELIMINA EL PAGO
            $pago->delete();

            return 'OK';

        }
    }

    /**
     * Download Cert.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function certDownload($id){
        $impPago = Pagos::find($id);
        if ($impPago){
            if ($impPago->modulo == "PREDIAL"){
                if ($impPago->download > 0){
                    $pred = Predial::find($impPago->entity_id);
                    $contri = PredialContribuyentes::find($pred->imp_pred_contri_id);

                    $declaFecha = Carbon::parse($impPago->fechaCreacion);
                    $fechaDeclaracion = Carbon::createFromTimeString($declaFecha);

                    $hoy = Carbon::today();
                    $fecha = Carbon::createFromTimeString($hoy);

                    $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

                    $pazysalvo = new PazySalvo();
                    $pazysalvo->modulo = $impPago->modulo;
                    $pazysalvo->pago_id = $id;
                    $pazysalvo->entity_id = $impPago->entity_id;
                    $pazysalvo->contri_id = $pred->imp_pred_contri_id;
                    $pazysalvo->valor = $impPago->valor;
                    $pazysalvo->fecha = $hoy;
                    $pazysalvo->user_id = Auth::user()->id;
                    $pazysalvo->save();

                    if (strlen($pazysalvo->id) < 6){
                        $pazysalvo->numForm = $pazysalvo->id;
                        for ($i = 0; $i < 6 - strlen($pazysalvo->id); $i++) {
                            $pazysalvo->numForm =  '0'. $pazysalvo->numForm;
                        }
                    } else  $pazysalvo->numForm = $pazysalvo->id;

                    $impPago->download = 0;
                    $impPago->save();

                    if (strlen($impPago->id) < 12){
                        $impPago->numForm = $impPago->id;
                        for ($i = 0; $i < 12 - strlen($impPago->id); $i++) {
                            $impPago->numForm =  '0'.$impPago->numForm;
                        }
                    } else $impPago->numForm = $impPago->id;



                    $pdf = \PDF::loadView('impuestos.pagos.pazysalvo', compact('dias','meses','fechaDeclaracion', 'fecha',
                    'impPago','pred','contri','pazysalvo'))
                        ->setOptions(['images' => true,'isRemoteEnabled' => true]);
                    return $pdf->stream();
                } else{
                    Session::flash('warning','Ya ha sido generado el paz y salvo de ese predio.');
                    return back();
                }
            } else{
                Session::flash('warning','No se puede generar paz y salvo. Contacte con el administrador.');
                return back();
            }
        }else {
            Session::flash('warning','El pago no se encuentra en el sistema.');
            return back();
        }
    }
}
