<?php

namespace App\Http\Controllers\Impuestos\Pagos;
use App\Model\Impuestos\IcaRetenedor;
use App\Model\Impuestos\Predial;
use App\Traits\NaturalezaJuridicaTraits;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\IcaContri;
use App\Model\Impuestos\Pagos;
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
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->get();
        $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->get();
        $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->get();
        return view('impuestos.pagos.index', compact('pagosPendientes', 'pagosHistoricos','pagosBorrador'));
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
}
