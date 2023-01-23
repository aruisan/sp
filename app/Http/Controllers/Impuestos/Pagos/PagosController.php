<?php

namespace App\Http\Controllers\Impuestos\Pagos;
use App\Model\Impuestos\IcaRetenedor;
use App\Model\Impuestos\Predial;
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

        if ($modulo == "PRED"){
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','PREDIAL')->get();
        } else {
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','!=','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','!=','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','!=','PREDIAL')->get();
        }

        return view('impuestos.pagos.index', compact('pagosPendientes', 'pagosHistoricos','pagosBorrador','modulo'));
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
            $pago->save();

            Session::flash('success', 'Pago aplicado exitosamente.');
            return redirect('/impuestos/Pagos');

        }
    }
}
