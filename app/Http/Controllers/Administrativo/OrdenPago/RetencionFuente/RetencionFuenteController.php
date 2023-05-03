<?php

namespace App\Http\Controllers\Administrativo\OrdenPago\RetencionFuente;

use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Cdp\RubrosCdp;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\Contabilidad\CompCont;
use App\Model\Administrativo\Contabilidad\CompContMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Registro\CdpsRegistro;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuenteConta;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuenteForm;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\Terceros;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Persona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use PDF;

class RetencionFuenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = RetencionFuente::all();
        return view('administrativo.contabilidad.retencionfuente.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrativo.contabilidad.retencionfuente.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reteF = new RetencionFuente();
        $reteF->concepto = $request->concept;
        $reteF->uvt = $request->uvt;
        $reteF->base = $request->base;
        $reteF->tarifa = $request->tarifa;
        $reteF->codigo = $request->codigo;
        $reteF->cuenta = $request->cuenta;
        $reteF->save();

        Session::flash('success','La retención en la fuente '.$request->concept.' se ha almacenado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function show(RetencionFuente $retencionFuente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $retens = RetencionFuente::findOrFail($id);
        return view('administrativo.contabilidad.retencionfuente.edit', compact('retens'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reteF = RetencionFuente::findOrFail($id);
        $reteF->concepto = $request->concept;
        $reteF->uvt = $request->uvt;
        $reteF->base = $request->base;
        $reteF->tarifa = $request->tarifa;
        $reteF->codigo = $request->codigo;
        $reteF->cuenta = $request->cuenta;
        $reteF->save();

        Session::flash('success','La retención en la fuente '.$request->concept.' se ha actualizado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RetencionFuente  $retencionFuente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $retenF = RetencionFuente::findOrFail($id);
        $retenF->delete();

        Session::flash('error','La retención en la fuente se ha eliminado exitosamente');
        return redirect('/administrativo/contabilidad/retefuente');
    }

    /**
     * Display a form to create the declaracion.
     *
     * @return \Illuminate\Http\Response
     */
    public function declaracion()
    {
        return view('administrativo.contabilidad.retencionfuente.declaracion');
    }

    /**
     * Display a form to create the declaracion.
     *
     * @return \Illuminate\Http\Response
     */
    public function certificado()
    {
        $personas = Persona::all();
        return view('administrativo.contabilidad.retencionfuente.certificado', compact('personas'));
    }

    public function pagoRetefuente($vigencia_id, $mes){

        $pagosVigRealizados = TesoreriaRetefuentePago::where('vigencia_id', $vigencia_id)->get();
        $canMake = true;
        if (count($pagosVigRealizados) > 0){
            foreach ($pagosVigRealizados as $pago){
                if ($pago->mes == $mes){
                    $canMake = false;
                    break;
                }
            }
        }

        $vigencia = Vigencia::find($vigencia_id);

        $cuentaPUC = PucAlcaldia::where('padre_id',660)->get();
        foreach ($cuentaPUC as $cuenta){

            //CUENTA CORRESPONDIENTE AL DEBITO
            if ($cuenta->code == '243603') $idPadreDeb = 868; //honorarios ->  honorarios
            elseif ($cuenta->code == '243605') $idPadreDeb = 1029;
            elseif ($cuenta->code == '243606') $idPadreDeb = 1048;
            else $idPadreDeb = 869;
            $padreDeb = PucAlcaldia::find($idPadreDeb);
            $hijosDeb = PucAlcaldia::where('padre_id', $idPadreDeb)->get();

            $hijos = PucAlcaldia::where('padre_id', $cuenta->id)->get();
            foreach ($hijos as $hijo){
                $retefuenteCode = RetencionFuente::where('codigo', $hijo->code)->first();
                if ($retefuenteCode){
                    $descuentosOP = OrdenPagosDescuentos::where('retencion_fuente_id', $retefuenteCode->id)->get();
                    foreach ($descuentosOP as $descuento){
                        $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')->first();
                        if ($ordenPago){
                            if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $vigencia_id){
                                $mesOP = Carbon::parse($ordenPago->created_at)->month;
                                //SE VALIDA QUE LA ORDEN DE PAGO HAYA SIDO CREADA EN EL MISMO MES DE BUSQUEDA
                                if ($mesOP == $mes){
                                    //SE RECORRE EL PUC PARA OBTENER LOS VALORES DE LA OP
                                    foreach ($ordenPago->pucs as $puc){
                                        //SE RECORRE EL PADRE CORRESPONDIENTE AL DEBITO PARA SABER SI UN HIJO CORRESPONDE
                                        if (count($hijosDeb) > 0){
                                            foreach ($hijosDeb as $hDeb){
                                                if ($hDeb->id == $puc->rubros_puc_id ){
                                                    //dd($hDeb, $puc);
                                                    if ($puc->valor_debito == 0) $valueOP = $ordenPago->valor;
                                                    else $valueOP =$puc->valor_debito;
                                                    $tableValues[] = collect(['code' => $retefuenteCode->codigo, 'concepto' => $retefuenteCode->concepto,
                                                        'valorDesc' => $descuento->valor, 'cc' => $ordenPago->registros->persona->num_dc,
                                                        'nameTer' => $ordenPago->registros->persona->nombre, 'codeDeb' => $hDeb->code,
                                                        'conceptoDeb' => $hDeb->concepto, 'valorDeb' => $valueOP,
                                                        'idTercero' => $ordenPago->registros->persona->id]);
                                                    $valueCred[] = $valueOP;
                                                    $valueDeb[] = $descuento->valor;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //VALIDACION CUANDO EN LA CONTABILIZACION ESTA EL PAGO DE LA DIAN
                $contaOP = OrdenPagosPuc::where('rubros_puc_id', $hijo->id)->get();
                if (count($contaOP) > 0){
                    foreach ($contaOP as $contabilizacion){
                        $ordenPago = OrdenPagos::where('id', $contabilizacion->orden_pago_id)->where('estado', '1')->first();
                        if ($ordenPago){
                            if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $vigencia_id){
                                $mesOP = Carbon::parse($ordenPago->created_at)->month;
                                //SE VALIDA QUE LA ORDEN DE PAGO HAYA SIDO CREADA EN EL MISMO MES DE BUSQUEDA
                                if ($mesOP == $mes){
                                    $debito = OrdenPagosPuc::where('orden_pago_id', $contabilizacion->orden_pago_id)->where('valor_credito',0)->first();
                                    $cuentaDeb = PucAlcaldia::find($debito->rubros_puc_id);

                                    $tableValues[] = collect(['code' => $hijo->code, 'concepto' => $hijo->concepto,
                                        'valorDesc' => $contabilizacion->valor_credito, 'cc' => $ordenPago->registros->persona->num_dc,
                                        'nameTer' => $ordenPago->registros->persona->nombre, 'codeDeb' => $cuentaDeb->code,
                                        'conceptoDeb' => $cuentaDeb->concepto, 'valorDeb' => $debito->valor_debito,
                                        'idTercero' => $ordenPago->registros->persona->id]);
                                    $valueCred[] = $debito->valor_debito;
                                    $valueDeb[] = $contabilizacion->valor_credito;
                                }
                            }
                        }
                    }
                }
            }

            //SE VALIDA SI HAY VALORES PARA AGREGARLE AL PADRE, SI NO HAY EL PADRE POR ENDE ESTA VACIO
            if (isset($tableValues)){
                //SE INGRESA EL PADRE
                $tableRT[] = collect(['code' => $cuenta->code, 'concepto' => $cuenta->concepto,
                    'valorDesc' => array_sum($valueDeb), 'cc' => '', 'nameTer' => '',
                    'codeDeb' => $padreDeb->code, 'conceptoDeb' => $padreDeb->concepto, 'valorDeb' => array_sum($valueCred)]);

                $form[] = collect(['concepto' => $cuenta->concepto, 'base' => array_sum($valueCred), 'reten' => array_sum($valueDeb)]);
                $pago[] = array_sum($valueDeb);


                //SE INGRESAN LOS HIJOS
                foreach ($tableValues as $data) $tableRT[] = collect($data);

                //SE LIMPIAN LOS ARRAY
                if (isset($valueDeb))unset($valueDeb);
                if (isset($valueCred))unset($valueCred);
                if (isset($tableValues))unset($tableValues);
            } else {
                $form[] = collect(['concepto' => $cuenta->concepto, 'base' => 0, 'reten' => 0]);
            }
        }

        if (isset($tableRT)){
            $total = array_sum($pago);
            $bancos = PucAlcaldia::where('id', '>=', 9)->where('id', '<=', 50)->get();
            $multaC = PucAlcaldia::find(1039);
            $multaD = PucAlcaldia::find(1040);
            $mesID = $mes;

            $days = cal_days_in_month(CAL_GREGORIAN, $mes, $vigencia->vigencia);

            switch ($mes){
                case 1:
                    $mes = "Enero";
                    break;
                case 2:
                    $mes = "Febrero";
                    break;
                case 3:
                    $mes = "Marzo";
                    break;
                case 4:
                    $mes = "Abril";
                    break;
                case 5:
                    $mes = "Mayo";
                    break;
                case 6:
                    $mes = "Junio";
                    break;
                case 7:
                    $mes = "Julio";
                    break;
                case 8:
                    $mes = "Agosto";
                    break;
                case 9:
                    $mes = "Septiembre";
                    break;
                case 10:
                    $mes = "Octubre";
                    break;
                case 11:
                    $mes = "Noviembre";
                    break;
                case 12:
                    $mes = "Diciembre";
                    break;
            }
/*
            dd([
                'total' => $total,
                'tableRT' => $tableRT,
                'bancos' => $bancos,
                'form' => $form,
                'multaC' => $multaC,
                'multaD' => $multaD,
                'mesID' => $mesID
            ]);
*/
            return view('administrativo.tesoreria.retefuente.pagos.pago', compact('tableRT','form',
                'total','bancos', 'vigencia_id','mes','days','vigencia','multaC','multaD','mesID','canMake'));
        } else {
            Session::flash('error','Para el mes escogido no hay ordenes de pago finalizadas. Seleccione un mes distinto.');
            return back();
        }

    }

    public function makePagoRetefuente($vigencia_id, $mes, Request $request){

        //SE VALIDA QUE SE ENCUENTRE CREADO EN EL SISTEMA EL RUBRO DE LAS MULTAS.
        $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', 754)->first();

        if ($rubro) {
            foreach ($rubro->fontsRubro as $fuente) $saldo[] = $fuente->dependenciaFont->sum('saldo');

            if ($request->debMulta != 0 and $request->credMulta != 0) {
                if ($request->debMulta == $request->credMulta) $multas = true;
                else {
                    Session::flash('error', 'El valor de debito y credito en las multas deben ser iguales.');
                    return back();
                }
            } elseif ($request->debMulta == 0 and $request->credMulta == 0) $multas = false;
            else {
                Session::flash('error', 'Se tiene que enviar el valor de la multa de debito y credito con valores diferentes a cero pero iguales o valores en cero cuando no aplica multa.');
                return back();
            }

            $vigencia = Vigencia::find($vigencia_id);

            if ($multas) {
                if (isset($saldo)){
                    if (array_sum($saldo) >= intval($request->valorPago)){

                        $depRubroFonts = DependenciaRubroFont::where('dependencia_id',15)->where('vigencia_id',$request->vigencia_id)
                            ->where('rubro_font_id', $rubro->fontsRubro->first()->id)->first();

                        //SE CREA EL CDP YA FINALIZADO
                        $cdp = $this->createCDP($request, $depRubroFonts, $rubro);

                        //SE CREA EL REGISTRO YA FINALIZADO
                        $registro = $this->createRegistro($request, $cdp, $vigencia);

                    } else {
                        Session::flash('warning', 'El rubro de SANCIONES ADMINISTRATIVAS no tiene fondos para realizar el pago.');
                        return back();
                    }
                } else{
                    Session::flash('warning', 'El rubro de SANCIONES ADMINISTRATIVAS no tiene fondos para realizar el pago.');
                    return back();
                }
            }

            //SE REALIZA LA BUSQUEDA DEL CODIGO CORRESPONDIENTE A LA NUEVA ORDEN DE PAGO
            $ordenPagoFind = OrdenPagos::orderBy('code','DESC')->first();
            $añoOPago = Carbon::parse($ordenPagoFind->created_at)->format('Y');
            if ($añoOPago == $vigencia->vigencia) $numOP = $ordenPagoFind->code + 1;
            else $numOP = 1;

            $ordenPago = new OrdenPagos();
            $ordenPago->code = $numOP;
            $ordenPago->nombre = $request->conceptoOP;
            $ordenPago->valor = intval($request->valorPago);
            $ordenPago->saldo = intval($request->valorPago);
            $ordenPago->iva = 0;
            $ordenPago->estado = '1';
            if ($multas) $ordenPago->registros_id = $registro['Registro']->id;
            $ordenPago->user_id = auth()->user()->id;
            $ordenPago->save();

            $ordenPagoRubros = new OrdenPagosRubros();
            $ordenPagoRubros->orden_pagos_id  = $ordenPago->id;
            $ordenPagoRubros->valor  = $ordenPago->valor;
            $ordenPagoRubros->saldo  = $ordenPago->saldo;
            $ordenPagoRubros->save();

            if ($multas){
                for ($i = 0; $i < sizeof($request->codeForm); $i++) {
                    $puc = PucAlcaldia::where('code', $request->codeForm[$i])->first();

                    $oPP = new OrdenPagosPuc();
                    $oPP->rubros_puc_id = $puc->id;
                    $oPP->orden_pago_id = $ordenPago->id;
                    $oPP->valor_debito = $request->debitoForm[$i];
                    $oPP->valor_credito = 0;
                    $oPP->save();
                }

                $oPP = new OrdenPagosPuc();
                $oPP->rubros_puc_id = 1039;
                $oPP->orden_pago_id = $ordenPago->id;
                $oPP->valor_debito = $request->debMulta;
                $oPP->valor_credito = 0;
                $oPP->save();

                $oPP = new OrdenPagosPuc();
                $oPP->rubros_puc_id = 1040;
                $oPP->orden_pago_id = $ordenPago->id;
                $oPP->valor_debito = 0;
                $oPP->valor_credito = $request->credMulta;
                $oPP->save();
            }

            $pago = new TesoreriaRetefuentePago();
            $pago->vigencia_id = $vigencia_id;
            $pago->mes = $mes;
            $pago->valor = $request->valorPago;
            $pago->orden_pago_id = $ordenPago->id;
            $pago->save();

            //SAVE FORMS
            for ($i = 0; $i < sizeof($request->concepto); $i++) {
                $form = new TesoreriaRetefuenteForm();
                $form->retefuente_id = $pago->id;
                $form->concepto = $request->concepto[$i];
                $form->base = $request->base[$i];
                $form->retencion = $request->reten[$i];
                $form->save();
            }

            //SAVE CONTABILIZACION
            for ($i = 0; $i < sizeof($request->codeForm); $i++) {
                $puc = PucAlcaldia::where('code', $request->codeForm[$i])->first();

                $conta = new TesoreriaRetefuenteConta();
                $conta->retefuente_id = $pago->id;
                $conta->cuenta_puc_id = $puc->id;
                $conta->persona_id = $request->terceroForm[$i];
                $conta->concepto = $request->conceptoForm[$i];
                $conta->debito = $request->debitoForm[$i];
                $conta->credito = 0;
                $conta->save();
            }

            //SAVE CONTABILIZACION MULTAS
            if ($multas) {
                $conta = new TesoreriaRetefuenteConta();
                $conta->retefuente_id = $pago->id;
                $conta->cuenta_puc_id = 1040;
                $conta->concepto = 'Multas Y Sanciones';
                $conta->debito = $request->debMulta;
                $conta->credito = 0;
                $conta->save();

                $conta = new TesoreriaRetefuenteConta();
                $conta->retefuente_id = $pago->id;
                $conta->cuenta_puc_id = 1039;
                $conta->concepto = 'Multas Y Sanciones';
                $conta->debito = 0;
                $conta->credito = $request->credMulta;
                $conta->save();
            }


            //SE CREA EL COMPROBANTE CONTABLE
            $compContable = new CompCont();
            $compContable->fecha = Carbon::today();
            $compContable->code = 1;
            $compContable->descripcion = "DECLARACION DE RETENCION EN LA FUENTE MES " . $mes . " - " . $vigencia->vigencia;
            $compContable->tipo_comp_id = 1;
            $compContable->save();

            //VALORES DEBITO DEL COMPROBANTE CONTABLE
            for ($i = 0; $i < sizeof($request->codeForm); $i++) {
                $puc = PucAlcaldia::where('code', $request->codeForm[$i])->first();

                $compContableMov = new CompContMov();
                $compContableMov->debito = $request->debitoForm[$i];
                $compContableMov->credito = 0;
                $compContableMov->comp_cont_id = $compContable->id;
                $compContableMov->cuenta_puc_id = $puc->id;
                $compContableMov->persona_id = $request->terceroForm[$i];
                $compContableMov->save();
            }

            //VALORES CREDITO DEL COMPROBANTE CONTABLE
            $compContableMov = new CompContMov();
            $compContableMov->debito = 0;
            $compContableMov->credito = $request->valorPago;
            $compContableMov->comp_cont_id = $compContable->id;
            $compContableMov->persona_id = 75;
            $compContableMov->save();

            //SE RELACIONA EL COMPROBANTE CONTABLE A EL PAGO DE LA RETENCION
            $pago->comp_conta_id = $compContable->id;
            $pago->save();

            Session::flash('success', 'Pago de retención en la fuente generado en el sistema exitosamente.');
            return redirect('administrativo/tesoreria/retefuente/pago/' . $vigencia_id);

        } else{
            Session::flash('warning', 'No se detecta el rubro de SANCIONES ADMINISTRATIVAS en el sistema.');
            return back();
        }
    }

    public function pagosRetefuente($vigencia_id){

        $pagos = TesoreriaRetefuentePago::where('vigencia_id', $vigencia_id)->get();
        $vigencia = Vigencia::find($vigencia_id);

        return view('administrativo.tesoreria.retefuente.pagos.index', compact('pagos','vigencia_id','vigencia'));
    }

    public function showpago($id){

        $pago = TesoreriaRetefuentePago::find($id);
        $banks = PagoBanks::where('pagos_id', $pago->comp_egreso_id)->get();
        $vigencia = Vigencia::find($pago->vigencia_id);
        $ordenPago = OrdenPagos::find($pago->orden_pago_id);

        $days = cal_days_in_month(CAL_GREGORIAN, $pago->mes, $vigencia->vigencia);

        switch ($pago->mes){
            case 1:
                $mes = "Enero";
                break;
            case 2:
                $mes = "Febrero";
                break;
            case 3:
                $mes = "Marzo";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Mayo";
                break;
            case 6:
                $mes = "Junio";
                break;
            case 7:
                $mes = "Julio";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Septiembre";
                break;
            case 10:
                $mes = "Octubre";
                break;
            case 11:
                $mes = "Noviembre";
                break;
            case 12:
                $mes = "Diciembre";
                break;
        }

        return view('administrativo.tesoreria.retefuente.pagos.show', compact('pago','vigencia'
        ,'mes','days', 'banks','ordenPago'));
    }

    public function pdfPago($id){
        $pago = TesoreriaRetefuentePago::find($id);
        $vigencia = Vigencia::find($pago->vigencia_id);

        $days = cal_days_in_month(CAL_GREGORIAN, $pago->mes, $vigencia->vigencia);

        switch ($pago->mes){
            case 1:
                $mes = "Enero";
                break;
            case 2:
                $mes = "Febrero";
                break;
            case 3:
                $mes = "Marzo";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Mayo";
                break;
            case 6:
                $mes = "Junio";
                break;
            case 7:
                $mes = "Julio";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Septiembre";
                break;
            case 10:
                $mes = "Octubre";
                break;
            case 11:
                $mes = "Noviembre";
                break;
            case 12:
                $mes = "Diciembre";
                break;
        }

        $fecha = Carbon::createFromTimeString($pago->created_at);
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $pdf = PDF::loadView('administrativo.tesoreria.retefuente.pagos.pdf', compact('pago',
            'vigencia','days','mes','fecha','dias','meses'))->setOptions(['images' => true,'isRemoteEnabled' => true]);

        return $pdf->stream();
    }

    public function createCDP($request, $depRubroFonts, $rubro){
        $countCdps = Cdp::where('vigencia_id', $request->vigencia_id)->orderBy('id')->get()->last();

        if ($countCdps == null) $count = 0;
        else $count = $countCdps->code;

        $cdp = new Cdp();
        $cdp->name = $request->conceptoOP;
        $cdp->tipo = 'Funcionamiento';
        $cdp->code = $count + 1;
        $cdp->valueControl = intval($request->valorPago);
        $cdp->valor = intval($request->valorPago);
        $cdp->fecha =  Carbon::today()->format('Y-m-d');
        $cdp->dependencia_id = 15;
        $cdp->observacion = $request->conceptoOP;
        $cdp->saldo = 0;
        $cdp->secretaria_e = '3';
        $cdp->ff_secretaria_e =  Carbon::today()->format('Y-m-d');
        $cdp->alcalde_e = '3';
        $cdp->ff_alcalde_e = Carbon::today()->format('Y-m-d');
        $cdp->jefe_e = '3';
        $cdp->ff_jefe_e = Carbon::today()->format('Y-m-d');
        $cdp->vigencia_id = $request->vigencia_id;
        $cdp->secretaria_user_id = auth()->user()->id;
        $cdp->save();

        //SE CREA LA RELACION EN LA TABLA RUBROS CDP
        $rubrosCdp = new RubrosCdp();
        $rubrosCdp->cdp_id = $cdp->id;
        $rubrosCdp->rubro_id = $rubro->id;
        $rubrosCdp->dep_rubro_font_id = $depRubroFonts->id;
        $rubrosCdp->save();

        //SE CREA LA RELACION EN LA TABLA RUBROS CDP VALOR
        $rubrosCdpValor = new RubrosCdpValor();
        $rubrosCdpValor->valor = intval($request->valorPago);
        $rubrosCdpValor->valor_disp = 0;
        $rubrosCdpValor->fontsRubro_id  = $rubro->fontsRubro->first()->id;
        $rubrosCdpValor->cdp_id = $cdp->id;
        $rubrosCdpValor->rubrosCdp_id = $rubrosCdp->id;
        $rubrosCdpValor->fontsDep_id = $depRubroFonts->id;
        $rubrosCdpValor->save();

        //SE DESCUENTA EL DINERO CON EL QUE SE ESTA CREANDO EL CDP DEL RUBRO Y LA FUENTE DE LA DEP
        foreach ($cdp->rubrosCdpValor as $fuentes){
            $fontRubro = FontsRubro::findOrFail($fuentes->fontsRubro->id);
            $fontRubro->valor_disp = 0;
            $fontRubro->save();

            $depFont = DependenciaRubroFont::find($fuentes->fontsDep_id);
            $depFont->saldo = 0;
            $depFont->save();
        }

        $cdpArray = collect(['CDP' => $cdp, 'RCDPValue' => $rubrosCdpValor, 'RCDP' => $rubrosCdp]);

        return $cdpArray;
    }

    public function createRegistro($request, $cdp, $vigencia){
        //SE REALIZA LA BUSQUEDA DEL CODIGO QUE LE CORRESPONDE AL RP

        $allRegistros = Registro::orderBy('code','DESC')->first();
        $añoReg = Carbon::parse($allRegistros->created_at)->format('Y');
        if ($añoReg == $vigencia->vigencia) $numRP = $allRegistros->code + 1;
        else $numRP = 1;

        $registro = new Registro();
        $registro->code = $numRP;
        $registro->objeto = $request->conceptoOP;
        $registro->ff_expedicion = Carbon::today()->format('Y-m-d');
        $registro->valor = intval($request->valorPago);
        $registro->saldo = 0;
        $registro->val_total = intval($request->valorPago);
        $registro->iva = "0";
        $registro->persona_id = 75;
        $registro->tipo_doc = "Factura";
        $registro->secretaria_e = "3";
        $registro->ff_secretaria_e = Carbon::today()->format('Y-m-d');
        $registro->jefe_e = "3";
        $registro->ff_jefe_e = Carbon::today()->format('Y-m-d');
        $registro->observacion = "REGISTRO CREADO AUTOMATICAMENTE PARA EL PAGO DE LA DIAN";
        $registro->created_at = Carbon::today()->format('Y-m-d');
        $registro->save();

        //CREACION DE LA RELACION DE CDPS REGISTRO
        $cdpsRegistro = new CdpsRegistro();
        $cdpsRegistro->registro_id = $registro->id;
        $cdpsRegistro->cdp_id = $cdp['CDP']->id;
        $cdpsRegistro->valor = $registro->valor;
        $cdpsRegistro->save();

        //CREACIÓN DE LA RELACION DE CDPS REGISTRO VALOR
        $cdpsRegistroValor = new CdpsRegistroValor();
        $cdpsRegistroValor->valor = $registro->valor;
        $cdpsRegistroValor->valor_disp = 0;
        $cdpsRegistroValor->fontsRubro_id = $cdp['RCDPValue']->fontsRubro_id ;
        $cdpsRegistroValor->registro_id = $registro->id;
        $cdpsRegistroValor->cdp_id = $cdp['CDP']->id;
        $cdpsRegistroValor->rubro_id = $cdp['RCDP']->rubro_id;
        $cdpsRegistroValor->cdps_registro_id = $cdpsRegistro->id;
        $cdpsRegistroValor->save();

        //SE DESCUENTA EL DINERO DE LA FUENTE DEL RUBRO DEL CDP
        $rubCdpValor = RubrosCdpValor::find($cdp['RCDPValue']->id);
        $rubCdpValor->valor_disp = 0;
        $rubCdpValor->save();

        $registroArray = collect(['Registro' => $registro, 'CDPReg' => $cdpsRegistro, 'CDPRegValue' => $cdpsRegistroValor]);

        return $registroArray;
    }
}
