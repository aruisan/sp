<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\descuentos;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\descuentos\TesoreriaDescuentos;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Persona;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;

class TesoreriaDescuentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($vigencia_id)
    {
        $vigencia = Vigencia::find($vigencia_id);
        $pagos = TesoreriaDescuentos::where('vigencia_id', $vigencia_id)->get();
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $cuentas[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $cuentas[] = $cuenta;
        }

        return view('administrativo.tesoreria.descuentos.index', compact('pagos','vigencia_id','vigencia', 'cuentas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function movAccount(Request $request){

        $result = collect();
        $rubroPUC = PucAlcaldia::find($request->id);
        $cuentaPUC = PucAlcaldia::where('padre_id',660)->orWhere('padre_id',765)->get();

        foreach ($cuentaPUC as $cuenta){

            //CUENTA CORRESPONDIENTE AL DEBITO
            if ($cuenta->code == '243603') $idPadreDeb = 868; //honorarios ->  honorarios
            elseif ($cuenta->code == '243605') $idPadreDeb = 1029;
            elseif ($cuenta->code == '243606') $idPadreDeb = 1048;
            else $idPadreDeb = 869;
            $padreDeb = PucAlcaldia::find($idPadreDeb);

            $hijos = PucAlcaldia::where('padre_id', $cuenta->id)->get();
            foreach ($hijos as $hijo){
                $retefuenteCode = RetencionFuente::where('codigo', $hijo->code)->first();
                if ($retefuenteCode){
                    $descuentosOP = OrdenPagosDescuentos::where('retencion_fuente_id', $retefuenteCode->id)->get();
                    foreach ($descuentosOP as $descuento){
                        $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')->first();
                        if ($ordenPago){
                            if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $request->vigencia_id){
                                $mesOP = Carbon::parse($ordenPago->created_at)->month;
                                //SE VALIDA QUE LA ORDEN DE PAGO HAYA SIDO CREADA EN EL MISMO MES DE BUSQUEDA
                                if ($mesOP == $request->mes){
                                    $pagos = Pagos::where('orden_pago_id', $ordenPago->id)->where('estado','1')->get();
                                    foreach ($pagos as $pago){
                                        //SE DEBE VALIDAR SI UNA ORDEN DE PAGO TIENE 2 PAGOS SE MUESTRAN LOS DESCUENTOS DE LOS DOS PAGOS?
                                        if ($pago->banks->rubros_puc_id == $rubroPUC->id){
                                            $valueOP = $ordenPago->valor;
                                            $tableValues[] = collect(['code' => $retefuenteCode->codigo, 'concepto' => $retefuenteCode->concepto,
                                                'valorDesc' => $descuento->valor, 'cc' => $ordenPago->registros->persona->num_dc,
                                                'nameTer' => $ordenPago->registros->persona->nombre, 'valorDeb' => $valueOP,
                                                'idTercero' => $ordenPago->registros->persona->id, 'ordenPago' => '#'.$ordenPago->code.'- '.$ordenPago->nombre]);
                                            $valueCred[] = $valueOP;
                                            $valueDeb[] = $descuento->valor;
                                        }
                                    }
                                }
                            }

                        }
                    }
                }

                $descMunicipal = DescMunicipales::where('codigo', $hijo->code)->first();
                if ($descMunicipal){
                    $descuentosOP = OrdenPagosDescuentos::where('desc_municipal_id', $descMunicipal->id)->get();
                    foreach ($descuentosOP as $descuento){
                        $ordenPago = OrdenPagos::where('id', $descuento->orden_pagos_id)->where('estado', '1')->first();
                        if ($ordenPago){
                            if ($ordenPago->registros->cdpsRegistro->first()->cdp->vigencia_id == $request->vigencia_id){
                                $mesOP = Carbon::parse($ordenPago->created_at)->month;
                                //SE VALIDA QUE LA ORDEN DE PAGO HAYA SIDO CREADA EN EL MISMO MES DE BUSQUEDA
                                if ($mesOP == $request->mes){
                                    $pagos = Pagos::where('orden_pago_id', $ordenPago->id)->where('estado','1')->get();
                                    foreach ($pagos as $pago) {
                                        //SE DEBE VALIDAR SI UNA ORDEN DE PAGO TIENE 2 PAGOS SE MUESTRAN LOS DESCUENTOS DE LOS DOS PAGOS?
                                        if ($pago->banks->rubros_puc_id == $rubroPUC->id) {
                                            $tableValues[] = collect(['code' => $descMunicipal->codigo, 'concepto' => $descMunicipal->concepto,
                                                'valorDesc' => $descuento->valor, 'cc' => $ordenPago->registros->persona->num_dc,
                                                'nameTer' => $ordenPago->registros->persona->nombre, 'valorDeb' => $ordenPago->valor,
                                                'idTercero' => $ordenPago->registros->persona->id, 'ordenPago' => '#'.$ordenPago->code.'- '.$ordenPago->nombre]);
                                            $valueCred[] = $ordenPago->valor;
                                            $valueDeb[] = $descuento->valor;
                                        }
                                    }
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

        return $tableRT;

        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;

        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                        if (Carbon::parse($pagoBank->created_at)->format('m') == $request->mes){
                            $total = $total - $pagoBank->valor;
                            $pago = Pagos::find($pagoBank->pagos_id);
                            if (isset($pago->orden_pago->registros->persona)){
                                $tercero = $pago->orden_pago->registros->persona->nombre;
                                $numIdent = $pago->orden_pago->registros->persona->num_dc;
                            } else{
                                $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                $numIdent = 800197268;
                            }
                            $totDeb = $totDeb + 0;
                            $totCred = $totCred + $pagoBank->valor;
                            if ($pago->type_pay == "CHEQUE") $referencia = "Pago #".$pago->code." - # Cheque ".$pago->num;
                            else $referencia = "Pago #".$pago->code;
                            $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'),
                                'modulo' => $referencia, 'debito' => '$'.number_format(0,0),
                                'credito' => '$'.number_format($pagoBank->valor,0), 'tercero' => $tercero,
                                'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                                'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TesoreriaDescuentos  $tesoreriaDescuentos
     * @return \Illuminate\Http\Response
     */
    public function show(TesoreriaDescuentos $tesoreriaDescuentos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TesoreriaDescuentos  $tesoreriaDescuentos
     * @return \Illuminate\Http\Response
     */
    public function edit(TesoreriaDescuentos $tesoreriaDescuentos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TesoreriaDescuentos  $tesoreriaDescuentos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TesoreriaDescuentos $tesoreriaDescuentos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TesoreriaDescuentos  $tesoreriaDescuentos
     * @return \Illuminate\Http\Response
     */
    public function destroy(TesoreriaDescuentos $tesoreriaDescuentos)
    {
        //
    }
}
