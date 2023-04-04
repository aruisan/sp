<?php

namespace App\Http\Controllers\Impuestos\Pagos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Hacienda\Presupuesto\Vigencia;
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
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            //$result[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) {
                if ($cuenta->code == '1110050122' or $cuenta->code == '1110050123') $result[] = $cuenta;
            }
        }

        if ($modulo == "PRED"){
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','PREDIAL')->get();
        } else {
            $pagosPendientes = Pagos::where('user_id', $user->id)->where('estado','Generado')->where('modulo','!=','PREDIAL')->get();
            $pagosBorrador = Pagos::where('user_id', $user->id)->where('estado','Borrador')->where('modulo','!=','PREDIAL')->get();
            $pagosHistoricos = Pagos::where('user_id', $user->id)->where('estado','Pagado')->where('modulo','!=','PREDIAL')->get();
        }

        return view('impuestos.pagos.index', compact('pagosPendientes', 'pagosHistoricos','pagosBorrador','modulo','result'));
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
            $pago->confirmed = "TRUE";
            $pago->save();

            //SE DEBE ELABORAR EL COMPROBANTE CONTABLE
            $this->makeCC($pago->id);

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
                    $morePropie = PredialContribuyentes::where('numCatastral', $contri->numCatastral)->get();
                    if (count($morePropie) > 1){
                        foreach ($morePropie as $propie) {
                            if ($propie->id != $contri->id) $contri->contribuyente = $contri->contribuyente.' - '.$propie->contribuyente;
                        }
                    }

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

    /**
     * Confirm Pay
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmPay(Request $request){
        return $this->makeCC($request->payId);
    }

    public function makeCC($id){
        $pago = Pagos::find($id);

        $añoActual = Carbon::now()->year;
        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 1)->where('estado', '0')->first();
        $countCI = ComprobanteIngresos::where('vigencia_id', $vigens->id)->orderBy('id')->get()->last();
        if ($countCI == null)  $count = 0;
        else $count = $countCI->code;

        //SE ELABORA EL COMPROBANTE CONTABLE
        $comprobante = new ComprobanteIngresos();
        $comprobante->code = $count + 1;
        $comprobante->valor = $pago->valor;
        $comprobante->iva = 0;
        $comprobante->val_total = $pago->valor;
        $comprobante->estado = '3';
        $comprobante->ff = $pago->fechaPago;
        $comprobante->tipoCI = 'Comprobante de Ingresos';
        $comprobante->user_id = $pago->user_id;
        $comprobante->vigencia_id = $vigens->id;
        $comprobante->responsable_id = Auth::user()->id;
        $comprobante->persona_id = $pago->user_id;
        if ($pago->modulo == 'PREDIAL') $comprobante->concepto = "IMPUESTO PREDIAL ".$pago->fechaPago." #".$pago->id;
        elseif ($pago->modulo == 'ICA-Contribuyente') $comprobante->concepto = "IMPUESTO ICA CONTRIBUYENTE ".$pago->fechaPago." #".$pago->id;
        $comprobante->save();

        //BANCO DEL COMPROBANTE CONTABLE
        $comprobanteMov = new ComprobanteIngresosMov();
        $comprobanteMov->comp_id = $comprobante->id;
        $comprobanteMov->fechaComp = $pago->fechaPago;
        $comprobanteMov->cuenta_banco = $pago->puc_alcaldia_id;
        $comprobanteMov->debito = $pago->valor;
        $comprobanteMov->credito = 0;
        $comprobanteMov->save();

        if ($pago->modulo == 'PREDIAL'){

            $predial = Predial::find($pago->entity_id);
            $liquidacion = $predial->liquidacion;
            $totImpPredial = 0;
            $totImpAdi = 0;
            $totDesc = 0;
            foreach ($liquidacion as $item){
                if($item->año == 2023) $totDesc = $totDesc + intval($item->int_mora);
                elseif($item->año == 2022) $totDesc = $totDesc + intval($item->int_mora) / 2;
                elseif($item->año == 2021) $totDesc = $totDesc + 0;
                elseif($item->año == 2020) $totDesc = $totDesc + 0;
                elseif($item->año == 2019)  $totDesc = $totDesc + intval($item->int_mora) * 0.03;
                else $totDesc = $totDesc + intval($item->int_mora);

                if($item->año == 2023) {
                    //DESCUENTO DEL 50% PARA EL 2023
                    $desc = $item->imp_predial / 2;
                    $totImpPredial = $totImpPredial + $desc;
                } else $totImpPredial = $totImpPredial + $item->imp_predial;

                if($item->año == 2023) {
                    //DESCUENTO DEL 50% PARA EL 2023
                    $descBomb = $item->tasa_bomberil / 2;
                    $totImpAdi = $totImpAdi + $descBomb;
                } else $totImpAdi = $totImpAdi + $item->tasa_bomberil;
            }

            //PUCs DEL COMPROBANTE CONTABLE
                //Predial unificado - vigencia actual
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 103;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $totImpPredial;
            $comprobanteMov->save();

                //Sobretasa ambiental CORALINA
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1074;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $totImpAdi;
            $comprobanteMov->save();

                //Intereses tributarios
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1072;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $totDesc;
            $comprobanteMov->save();

            //RUBROS DEL COMPROBANTE CONTABLE
                //1.1.01.01.200.02 Impuesto predial unificado- rural vigencia actual 1.2.1.0.00 Ingresos Corrientes de Libre Destinación
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 854;
            $comprobanteMov->debito = $totImpPredial;
            $comprobanteMov->save();

                //1.1.01.01.014.02 Sobretasa ambiental - Corporaciones Autónomas Regionales - Rural 1.2.3.1.01 Sobretasa ambiental
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 853;
            $comprobanteMov->debito = $totImpAdi;
            $comprobanteMov->save();

                //1.1.02.03.002	Intereses de mora (Triburarios) 1.2.1.0.00 Ingresos Corrientes de Libre Destinación
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 871;
            $comprobanteMov->debito = $totDesc;
            $comprobanteMov->save();

        } elseif ($pago->modulo == 'ICA-Contribuyente'){
            $ica = IcaContri::find($pago->entity_id);

            //PUCs DEL COMPROBANTE CONTABLE
                //IMPUESTO INDUSTRIA Y COMERCIO
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1068;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $ica->totImpIndyCom;
            $comprobanteMov->save();

                //AVISOS Y TABLEROS
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1069;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $ica->impAviyTableros;
            $comprobanteMov->save();

                //SOBRETASA BOMBERIL
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1070;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $ica->sobretasaBomberil;
            $comprobanteMov->save();

                //INTERESES TRIBUTARIOS (INTERESES DE MORA)
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->cuenta_puc_id = 1072;
            $comprobanteMov->debito = 0;
            $comprobanteMov->credito = $ica->interesesMora;
            $comprobanteMov->save();

            //RUBROS DEL COMPROBANTE CONTABLE
                //1.1.01.02.200.01 Impuesto de Industria y comercio - sobre actividades comerciales 1.2.1.0.00 Ingresos Corrientes de Libre Destinación
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 856;
            $comprobanteMov->debito = $ica->totImpIndyCom;
            $comprobanteMov->save();

                // 1.1.01.02.201 Impuesto complementario de avisos y tableros 1.2.1.0.00 Ingresos Corrientes de Libre Destinación
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 858;
            $comprobanteMov->debito = $ica->impAviyTableros;
            $comprobanteMov->save();

                // 1.1.01.02.212 Impuesto bomberil 1.2.3.1.15 Sobretasa bomberil
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 861;
            $comprobanteMov->debito = $ica->sobretasaBomberil;
            $comprobanteMov->save();

            // 1.1.02.03.002 Intereses de mora (Tributarios) 1.2.1.0.00 Ingresos Corrientes de Libre Destinación
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $pago->fechaPago;
            $comprobanteMov->rubro_font_ingresos_id = 871;
            $comprobanteMov->debito = $ica->interesesMora;
            $comprobanteMov->save();
        }

        $pago->confirmed = "TRUE";
        $pago->save();

        return 'OK';

    }
}
