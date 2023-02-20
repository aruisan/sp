<?php

namespace App\Http\Controllers\Impuestos\ICA;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\Ciuu;
use App\Model\Impuestos\CodeMuni;
use App\Model\Impuestos\Exogena;
use App\Model\Impuestos\IcaContri;
use App\Model\Impuestos\IcaRetenedor;
use App\Model\Impuestos\Pagos;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\NaturalezaJuridicaTraits;
use Session;
use Carbon\Carbon;
use PDF;

class IcaController extends Controller
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
     * Show the form of contribuyente.
     *
     * @return \Illuminate\Http\Response
     */
    public function createContri()
    {
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $action = "Declaración";
        return view('impuestos.ica.contribuyente.create', compact('action','rit'));
    }

    /**
     * Show the form of agente retenedor.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRetenedor()
    {
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $action = "Declaración";
        return view('impuestos.ica.retenedor.create', compact('action','rit'));
    }

    /**
     * Show the form of exogena.
     *
     * @return \Illuminate\Http\Response
     */
    public function createExogena()
    {
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        if ($user->exogena->count() > 0){
            $action = "Actualización";
            $exogena = $user->exogena;
            foreach ($exogena as $item){
                $codeDept = CodeMuni::where('code_dept', $item->codeDpto)->get();
                $codeCiudad = CodeMuni::find($item->codeCiudad);
                $codeCiuu = Ciuu::find($item->ciuu_id);
                $item->codeDpto = $item->codeDpto .' - '. $codeDept[0]['name_dept'];
                $item->codeCiudad = $codeCiudad['code_ciudad'] .' - '. $codeCiudad['name_ciudad'];
                $item->ciuu_id = $codeCiuu['code_ciuu'] .' - '. $codeCiuu['description'];
            }
        } else{
            $action = "Creación";
            $exogena = [];
        }

        $ciuu = Ciuu::all();
        $codeMuni = CodeMuni::all();
        $deptos = $codeMuni->unique('name_dept');

        //modelos de las tablas de codigos y ciuu para colocarlas en el listado
        return view('impuestos.ica.exogena.create', compact('action','rit','ciuu','codeMuni','deptos','exogena'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeContri(Request $request)
    {
        if ($request->opciondeUso == "Declaración") {
            $ICA = new IcaContri();
            $ICA->user_id = Auth::user()->id;
            $ICA->añoGravable = $request->añoGravable;
            $ICA->numEstableLoc = $request->numEstableLoc;
            $ICA->numEstableNal = $request->numEstableNal;
            $ICA->totIngreOrd = $request->totIngreOrd;
            $ICA->menosIngreFuera = $request->menosIngreFuera;
            $ICA->totIngreOrdin = $request->totIngreOrd - $request->menosIngreFuera;
            $ICA->menosIngreDevol = $request->menosIngreDevol;
            $ICA->menosIngreExport = $request->menosIngreExport;
            $ICA->menosIngreOtrasActiv = $request->menosIngreOtrasActiv;
            $ICA->menosIngreActivExcentes = $request->menosIngreActivExcentes;
            $ICA->totIngreGravables = $ICA->totIngreOrdin - $request->menosIngreDevol - $request->menosIngreExport -
                $request->menosIngreOtrasActiv - $request->menosIngreActivExcentes;
            $ICA->codClasiMuni = $request->codClasiMuni;
            $ICA->tarifa = $request->tarifa;
            $ICA->impIndyCom = $ICA->totIngreGravables * $ICA->tarifa / 100;
            $ICA->codClasiMuni2 = $request->codClasiMuni2;
            $ICA->ingreGravados2 = $request->ingreGravados2;
            $ICA->tarifa2 = $request->tarifa2;
            $ICA->impIndyCom2 = $ICA->ingreGravados2 * $ICA->tarifa2 / 100;
            $ICA->codClasiMuni3 = $request->codClasiMuni3;
            $ICA->ingreGravados3 = $request->ingreGravados3;
            $ICA->tarifa3 = $request->tarifa3;
            $ICA->impIndyCom3 = $ICA->ingreGravados3 * $ICA->tarifa3 / 100;
            $ICA->codClasiMuni4 = $request->codClasiMuni4;
            $ICA->ingreGravados4 = $request->ingreGravados4;
            $ICA->tarifa4 = $request->tarifa4;
            $ICA->impIndyCom4 = $ICA->ingreGravados4 * $ICA->tarifa4 / 100;
            $ICA->codClasiMuni5 = $request->codClasiMuni5;
            $ICA->ingreGravados5 = $request->ingreGravados5;
            $ICA->totIngreGravado = $ICA->totIngreGravables + $ICA->ingreGravados2 + $ICA->ingreGravados3 +
                $ICA->ingreGravados4 + $ICA->ingreGravados5;
            $ICA->totImpuesto = $ICA->impIndyCom + $ICA->impIndyCom2 + $ICA->impIndyCom3 +
                $ICA->impIndyCom4 + $ICA->ingreGravados5;
            $ICA->genEnergiaCapacidad = $request->genEnergiaCapacidad;
            $ICA->impLey56 = $request->impLey56;
            $ICA->totImpIndyCom = $ICA->totImpuesto + $ICA->impLey56;
            $ICA->impAviyTableros = $ICA->totImpIndyCom * 15 / 100;
            $ICA->pagoUndComer = $request->pagoUndComer;
            $ICA->sobretasaBomberil = $request->sobretasaBomberil;
            $ICA->sobretasaSeguridad = $request->sobretasaSeguridad;
            $ICA->totImpCargo = $ICA->totImpIndyCom + $ICA->impAviyTableros + $ICA->pagoUndComer +
                $ICA->sobretasaBomberil + $ICA->sobretasaSeguridad;
            $ICA->menosValorExencion = $request->menosValorExencion;
            $ICA->menosRetenciones = $request->menosRetenciones;
            $ICA->menosAutorretenciones = $request->menosAutorretenciones;
            $ICA->menosAnticipoLiquidado = $request->menosAnticipoLiquidado;
            $ICA->anticipoAñoSiguiente = $request->anticipoAñoSiguiente;
            $ICA->SANCIONES = $request->SANCIONES;
            $ICA->cualOtra = $request->cualOtra;
            $ICA->sancionesVal = $request->sancionesVal;
            $ICA->menosSaldoaFavorPredio = $request->menosSaldoaFavorPredio;
            $ICA->totSaldoaCargo = $ICA->totImpCargo - $ICA->menosValorExencion - $ICA->menosRetenciones -
                $ICA->menosAutorretenciones - $ICA->menosAnticipoLiquidado + $ICA->anticipoAñoSiguiente +
                $ICA->sancionesVal - $ICA->menosSaldoaFavorPredio;
            $ICA->totSaldoaFavor = $request->totSaldoaFavor;
            $ICA->valoraPagar = $ICA->totSaldoaCargo;
            $ICA->valorDesc = $request->valorDesc;
            $ICA->interesesMora = $request->interesesMora;
            $ICA->totPagar = $ICA->valoraPagar - $ICA->valorDesc + $ICA->interesesMora;
            $ICA->presentacion = Carbon::today();
            $ICA->save();

            $ICAnumRef = IcaContri::find($ICA->id);
            if (strlen($ICAnumRef->id) < 6){
                $newValue = $ICAnumRef->id;
                for ($i = 0; $i < 6 - strlen($ICAnumRef->id); $i++) {
                    $newValue =  '0'.$newValue;
                }
            } else $newValue = $ICAnumRef->id;
            $ICAnumRef->numReferencia = Carbon::today()->format('Ymd').$newValue;
            $ICAnumRef->save();

            $pago = new Pagos();
            $pago->modulo = "ICA-Contribuyente";
            $pago->entity_id = $ICA->id;
            $pago->estado = "Borrador";
            $pago->valor = $ICA->totPagar;
            $pago->fechaCreacion = $ICA->presentacion;
            $pago->user_id = Auth::user()->id;
            $pago->save();

            Session::flash('success', 'Borrador del formulario declaración de contribuyente generado exitosamente.');

            return redirect('/impuestos/Pagos/'.$pago->id);
        } else {
            $ICA = IcaContri::find($request->ica_id);
            $ICA->user_id = Auth::user()->id;
            $ICA->añoGravable = $request->añoGravable;
            $ICA->numEstableNal = $request->numEstableNal;
            $ICA->totIngreOrd = $request->totIngreOrd;
            $ICA->menosIngreFuera = $request->menosIngreFuera;
            $ICA->totIngreOrdin = $request->totIngreOrd - $request->menosIngreFuera;
            $ICA->menosIngreDevol = $request->menosIngreDevol;
            $ICA->menosIngreExport = $request->menosIngreExport;
            $ICA->menosIngreOtrasActiv = $request->menosIngreOtrasActiv;
            $ICA->menosIngreActivExcentes = $request->menosIngreActivExcentes;
            $ICA->totIngreGravables = $ICA->totIngreOrdin - $request->menosIngreDevol - $request->menosIngreExport -
                $request->menosIngreOtrasActiv - $request->menosIngreActivExcentes;
            $ICA->codClasiMuni = $request->codClasiMuni;
            $ICA->tarifa = $request->tarifa;
            $ICA->impIndyCom = $ICA->totIngreGravables * $ICA->tarifa / 100;
            $ICA->codClasiMuni2 = $request->codClasiMuni2;
            $ICA->ingreGravados2 = $request->ingreGravados2;
            $ICA->tarifa2 = $request->tarifa2;
            $ICA->impIndyCom2 = $ICA->ingreGravados2 * $ICA->tarifa2 / 100;
            $ICA->codClasiMuni3 = $request->codClasiMuni3;
            $ICA->ingreGravados3 = $request->ingreGravados3;
            $ICA->tarifa3 = $request->tarifa3;
            $ICA->impIndyCom3 = $ICA->ingreGravados3 * $ICA->tarifa3 / 100;
            $ICA->codClasiMuni4 = $request->codClasiMuni4;
            $ICA->ingreGravados4 = $request->ingreGravados4;
            $ICA->tarifa4 = $request->tarifa4;
            $ICA->impIndyCom4 = $ICA->ingreGravados4 * $ICA->tarifa4 / 100;
            $ICA->codClasiMuni5 = $request->codClasiMuni5;
            $ICA->ingreGravados5 = $request->ingreGravados5;
            $ICA->totIngreGravado = $ICA->totIngreGravables + $ICA->ingreGravados2 + $ICA->ingreGravados3 +
                $ICA->ingreGravados4 + $ICA->ingreGravados5;
            $ICA->totImpuesto = $ICA->impIndyCom + $ICA->impIndyCom2 + $ICA->impIndyCom3 +
                $ICA->impIndyCom4 + $ICA->ingreGravados5;
            $ICA->genEnergiaCapacidad = $request->genEnergiaCapacidad;
            $ICA->impLey56 = $request->impLey56;
            $ICA->totImpIndyCom = $ICA->totImpuesto + $ICA->impLey56;
            $ICA->impAviyTableros = $ICA->totImpIndyCom * 15 / 100;
            $ICA->pagoUndComer = $request->pagoUndComer;
            $ICA->sobretasaBomberil = $request->sobretasaBomberil;
            $ICA->sobretasaSeguridad = $request->sobretasaSeguridad;
            $ICA->totImpCargo = $ICA->totImpIndyCom + $ICA->impAviyTableros + $ICA->pagoUndComer +
                $ICA->sobretasaBomberil + $ICA->sobretasaSeguridad;
            $ICA->menosValorExencion = $request->menosValorExencion;
            $ICA->menosRetenciones = $request->menosRetenciones;
            $ICA->menosAutorretenciones = $request->menosAutorretenciones;
            $ICA->menosAnticipoLiquidado = $request->menosAnticipoLiquidado;
            $ICA->anticipoAñoSiguiente = $request->anticipoAñoSiguiente;
            $ICA->SANCIONES = $request->SANCIONES;
            $ICA->cualOtra = $request->cualOtra;
            $ICA->sancionesVal = $request->sancionesVal;
            $ICA->menosSaldoaFavorPredio = $request->menosSaldoaFavorPredio;
            $ICA->totSaldoaCargo = $ICA->totImpCargo - $ICA->menosValorExencion - $ICA->menosRetenciones -
                $ICA->menosAutorretenciones - $ICA->menosAnticipoLiquidado + $ICA->anticipoAñoSiguiente +
                $ICA->sancionesVal - $ICA->menosSaldoaFavorPredio;
            $ICA->totSaldoaFavor = $request->totSaldoaFavor;
            $ICA->valoraPagar = $ICA->totSaldoaCargo;
            $ICA->valorDesc = $request->valorDesc;
            $ICA->interesesMora = $request->interesesMora;
            $ICA->totPagar = $ICA->valoraPagar - $ICA->valorDesc + $ICA->interesesMora;
            $ICA->presentacion = Carbon::today();
            $ICA->save();

            $pago = Pagos::where('entity_id', $ICA->id)->where('modulo','ICA-Contribuyente')->first();
            $pago->entity_id = $ICA->id;
            $pago->valor = $ICA->totPagar;
            $pago->fechaCreacion = $ICA->presentacion;


            //SE VALIDA SI ES UNA DECLARACION EN 0$ - SI ES EN 0$ SE FINALIZA EL PAGO INMEDIATAMENTE AL FIRMAR
            if ($ICA->totPagar > 0) $pago->estado = "Generado";
            else {
                $pago->estado = "Pagado";
                $pago->fechaPago = Carbon::today();
                $pago->user_pago_id = Auth::user()->id;
            }

            $pago->save();

            Session::flash('success', 'Formulario declaración de contribuyente firmado y emitido exitosamente.');

            return redirect('/impuestos/Pagos/ICA/');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRetenedor(Request $request)
    {
        if ($request->opciondeUso == "Declaración") {
            $retenedor = new IcaRetenedor();
            $retenedor->user_id = Auth::user()->id;
            $retenedor->periodo = $request->periodo;
            $retenedor->añoGravable = $request->añoGravable;
            $retenedor->opciondeUso = $request->opciondeUso;
            $retenedor->codAgente = $request->codAgente;
            $retenedor->contratosObra = $request->contratosObra;
            $retenedor->contratosPrestServ = $request->contratosPrestServ;
            $retenedor->compraBienes = $request->compraBienes;
            $retenedor->otrasActiv = $request->otrasActiv;
            $retenedor->practicadasPeriodosAnt = $request->practicadasPeriodosAnt;
            $retenedor->totRetenciones = $request->totRetenciones;
            $retenedor->devolucionExceso = $request->devolucionExceso;
            $retenedor->devolucionRetencion = $request->devolucionRetencion;
            $retenedor->totalRetencion = $request->totalRetencion;
            $retenedor->sancionExtemp = $request->sancionExtemp;
            $retenedor->sancionCorreccion = $request->sancionCorreccion;
            $retenedor->interesMoratorio = $request->interesMoratorio;
            $retenedor->pagoTotal = $request->pagoTotal;
            $retenedor->idSignatario = $request->idSignatario;
            $retenedor->nameSignatario = $request->nameSignatario;
            $retenedor->signatario = $request->signatario;
            $retenedor->tpRevFisc = $request->tpRevFisc;
            $retenedor->nameRevFisc = $request->nameRevFisc;
            $retenedor->presentacion = Carbon::today();
            $retenedor->save();

            $ICAnumRef = IcaRetenedor::find($retenedor->id);
            if (strlen($ICAnumRef->id) < 6){
                $newValue = $ICAnumRef->id;
                for ($i = 0; $i < 6 - strlen($ICAnumRef->id); $i++) {
                    $newValue =  '0'.$newValue;
                }
            } else $newValue = $ICAnumRef->id;
            $ICAnumRef->numReferencia = Carbon::today()->format('Ymd').$newValue;
            $ICAnumRef->save();

            $pago = new Pagos();
            $pago->modulo = "ICA-AgenteRetenedor";
            $pago->entity_id = $retenedor->id;
            $pago->estado = "Generado";
            $pago->valor = $retenedor->pagoTotal;
            $pago->fechaCreacion = $retenedor->presentacion;
            $pago->user_id = Auth::user()->id;
            $pago->save();

            Session::flash('success', 'Borrador del formulario declaración agente retenedor presentado exitosamente.');

            return redirect('/impuestos/Pagos/'.$pago->id);
        } else {
            $retenedor = IcaRetenedor::find($request->ica_id);
            $retenedor->user_id = Auth::user()->id;
            $retenedor->periodo = $request->periodo;
            $retenedor->añoGravable = $request->añoGravable;
            $retenedor->opciondeUso = $request->opciondeUso;
            $retenedor->codAgente = $request->codAgente;
            $retenedor->contratosObra = $request->contratosObra;
            $retenedor->contratosPrestServ = $request->contratosPrestServ;
            $retenedor->compraBienes = $request->compraBienes;
            $retenedor->otrasActiv = $request->otrasActiv;
            $retenedor->practicadasPeriodosAnt = $request->practicadasPeriodosAnt;
            $retenedor->totRetenciones = $request->totRetenciones;
            $retenedor->devolucionExceso = $request->devolucionExceso;
            $retenedor->devolucionRetencion = $request->devolucionRetencion;
            $retenedor->totalRetencion = $request->totalRetencion;
            $retenedor->sancionExtemp = $request->sancionExtemp;
            $retenedor->sancionCorreccion = $request->sancionCorreccion;
            $retenedor->interesMoratorio = $request->interesMoratorio;
            $retenedor->pagoTotal = $request->pagoTotal;
            $retenedor->idSignatario = $request->idSignatario;
            $retenedor->nameSignatario = $request->nameSignatario;
            $retenedor->signatario = $request->signatario;
            $retenedor->tpRevFisc = $request->tpRevFisc;
            $retenedor->nameRevFisc = $request->nameRevFisc;
            $retenedor->presentacion = Carbon::today();
            $retenedor->save();

            $pago = Pagos::where('entity_id', $retenedor->id)->where('modulo','ICA-AgenteRetenedor')->first();
            $pago->entity_id = $retenedor->id;
            $pago->valor = $request->pagoTotal;
            $pago->fechaCreacion = Carbon::today();
            $pago->save();

            Session::flash('success', 'Formulario declaración agente retenedor corregido exitosamente.');

            return redirect('/impuestos/Pagos/'.$pago->id);
        }
    }

    /**
     * Generate facture from the ICA Contribuyente.
     *
     * @return \Illuminate\Http\Response
     */
    public function facturaContri($id_ica){
        $ica = IcaContri::find($id_ica);
        $user = User::find($ica->user_id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $ica->presentacion = Carbon::parse($ica->presentacion)->format('d-m-Y');
        $pdf = PDF::loadView('impuestos.ica.contribuyente.pdf', compact('rit','ica'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    /**
     * Generate form from the ICA Contribuyente.
     *
     * @return \Illuminate\Http\Response
     */
    public function formContri($id_ica){
        $formulario = IcaContri::find($id_ica);
        $user = User::find($formulario->user_id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $formulario->presentacion = Carbon::parse($formulario->presentacion)->format('d-m-Y');
        $pdf = PDF::loadView('impuestos.ica.contribuyente.formulario', compact('rit','formulario'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    /**
     * Generate facture from the ICA Retenedor.
     *
     * @return \Illuminate\Http\Response
     */
    public function facturaRetenedor($id_ica){
        $ica = IcaRetenedor::find($id_ica);
        $user = User::find($ica->user_id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $ica->presentacion = Carbon::parse($ica->presentacion)->format('d-m-Y');
        $pdf = PDF::loadView('impuestos.ica.retenedor.pdf', compact('rit','ica'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    /**
     * Generate form from the ICA Retenedor.
     *
     * @return \Illuminate\Http\Response
     */
    public function formRetenedor($id_ica){
        $formulario = IcaRetenedor::find($id_ica);
        $user = User::find($formulario->user_id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $formulario->presentacion = Carbon::parse($formulario->presentacion)->format('d-m-Y');
        $pdf = PDF::loadView('impuestos.ica.retenedor.formulario', compact('rit','formulario'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }


    /*
    **
    * Show the form of update the ICA Contribuyente.
    *
    * @return \Illuminate\Http\Response
    */
    public function updateContri($id)
    {
        $action = "Corrección";
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $ica = IcaContri::find($id);
        $pago = Pagos::where('modulo','ICA-Contribuyente')->where('entity_id', $id)->first();
        if ($ica){
            return view('impuestos.ica.contribuyente.create', compact('action','rit','ica','pago'));
        } else {
            Session::flash('warning', 'Formulario de declaración de contribuyente no encontrado en el sistema.');
            return redirect('/impuestos/Pagos');
        }
    }


    /*
    **
    * Show the form of update the ICA Retenedor.
    *
    * @return \Illuminate\Http\Response
    */
    public function updateRetenedor($id)
    {
        $action = "Corrección";
        $user = User::find(Auth::user()->id);
        $rit = $user->rit;
        $ica = IcaRetenedor::find($id);
        if ($ica){
            return view('impuestos.ica.retenedor.create', compact('action','rit','ica'));
        } else {
            Session::flash('warning', 'Formulario de declaración de agente retenedor no encontrado en el sistema.');
            return redirect('/impuestos/Pagos');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeExogena(Request $request)
    {
        if ($request->opciondeUso == "Creación"){
            for ($i = 0; $i <= count($request->numIdeInform) - 1; $i++){
                $exogena = new Exogena();
                $exogena->user_id = Auth::user()->id;
                $exogena->año = $request->año;
                $exogena->numIdeInform = $request->numIdeInform[$i];
                $exogena->dv = $request->dv[$i];
                $exogena->primerApe = $request->primerApe[$i];
                $exogena->segApe = $request->segApe[$i];
                $exogena->primerNom = $request->primerNom[$i];
                $exogena->otrosNombres = $request->otrosNombres[$i];
                $exogena->razonSocial = $request->razonSocial[$i];
                $exogena->dir = $request->dir[$i];
                $exogena->tel = $request->tel[$i];
                $exogena->email = $request->email[$i];
                $exogena->codeDpto = $request->codeDpto[$i];
                $exogena->codeCiudad = $request->codeCiudad[$i];
                $exogena->ciuu_id = $request->ciuu_id[$i];
                $exogena->valorAcum = $request->valorAcum[$i];
                $exogena->tarifa = $request->tarifa[$i];
                $exogena->valorReten = $request->valorReten[$i];
                $exogena->valorRetenAsum = $request->valorRetenAsum[$i];
                $exogena->save();
            }
            Session::flash('success', 'Reporte de exogena almacenado exitosamente.');
            return redirect('/impuestos');

        } else {
            for ($i = 0; $i <= count($request->numIdeInform) - 1; $i++){
                $exogena = new Exogena();
                $exogena->user_id = Auth::user()->id;
                $exogena->año = $request->año;
                $exogena->numIdeInform = $request->numIdeInform[$i];
                $exogena->dv = $request->dv[$i];
                $exogena->primerApe = $request->primerApe[$i];
                $exogena->segApe = $request->segApe[$i];
                $exogena->primerNom = $request->primerNom[$i];
                $exogena->otrosNombres = $request->otrosNombres[$i];
                $exogena->razonSocial = $request->razonSocial[$i];
                $exogena->dir = $request->dir[$i];
                $exogena->tel = $request->tel[$i];
                $exogena->email = $request->email[$i];
                $exogena->codeDpto = $request->codeDpto[$i];
                $exogena->codeCiudad = $request->codeCiudad[$i];
                $exogena->ciuu_id = $request->ciuu_id[$i];
                $exogena->valorAcum = $request->valorAcum[$i];
                $exogena->tarifa = $request->tarifa[$i];
                $exogena->valorReten = $request->valorReten[$i];
                $exogena->valorRetenAsum = $request->valorRetenAsum[$i];
                $exogena->save();
            }

            Session::flash('success', 'Reporte de exogena actualizado exitosamente.');
            return redirect('/impuestos');
        }
    }

    /**
     * Delete exogena.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteExogena($id){
        $exogena = Exogena::find($id);
        $exogena->delete();
        Session::flash('warning', 'Persona eliminada correctamente.');
    }


    /**
     * Delete formulario contribuyente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteContri($idForm, $idPay){

        $pago = Pagos::find($idPay);
        $pago->delete();

        $ICA = IcaContri::find($idForm);
        $ICA->delete();

        Session::flash('warning', 'Formulario contribuyente eliminado exitosamente.');
        return redirect('/impuestos/Pagos');
    }

    /**
     * Delete formulario retenedor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteRetenedor($idForm, $idPay){

        $pago = Pagos::find($idPay);
        $pago->delete();

        $ICA = IcaRetenedor::find($idForm);
        $ICA->delete();

        Session::flash('warning', 'Formulario agente retenedor eliminado exitosamente.');
        return redirect('/impuestos/Pagos');
    }

}
