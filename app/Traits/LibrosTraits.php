<?php
namespace App\Traits;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Impuestos\Muellaje;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Persona;
use App\Model\User;
use Carbon\Carbon;

Class LibrosTraits
{
    public function movAccountLibros($id, $fechaInicial, $fechaFinal){

        $rubroPUC = PucAlcaldia::find($id);
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $fechaIni = Carbon::parse($fechaInicial);
        $fechaFin = Carbon::parse($fechaFinal.'23:59:59');

        if($fechaIni->month >= 2) {
            $newSaldo = $this->validateBeforeMonths($fechaInicial, $rubroPUC);
            $total = $newSaldo['total'];
            $result[] = collect(['fecha' => $newSaldo['fecha'],
                'modulo' => '', 'debito' => '',
                'credito' => '', 'tercero' => '',
                'CC' => '', 'concepto' => 'SALDO HASTA EL MES '.$newSaldo['fecha'], 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                'total' => '$'.number_format($total,0), 'inicial' => $rubroPUC->saldo_inicial,
                'totDeb' => $totDeb, 'totCred' => $totCred,'pago_id' => '', 'pago_estado' => '']);
        }

        if (strlen($rubroPUC->code) == 4) return $this->findlvl3($rubroPUC, $fechaIni, $fechaFin, $total, $totDeb, $totCred);
        elseif (strlen($rubroPUC->code) == 6) return $this->findlvl4($rubroPUC, $fechaIni, $fechaFin, $total, $totDeb, $totCred);
        elseif (strlen($rubroPUC->code) == 10){
            if ($rubroPUC->id == 765){
                //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                foreach ($pagosFin as $pagoF){
                    foreach ($pagoF->orden_pago->descuentos as $descuento){
                        if ($descuento->valor > 0){
                            if ($descuento->desc_municipal_id != null){
                                //DESCUENTOS MUNICIPALES
                                if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                    $total = $total + $descuento->valor;
                                    $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                    $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                    $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                        'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                        'total' => '$'.number_format($total,0), 'from' => 5,
                                        'totDeb' => $totDeb, 'totCred' => $totCred]);
                                    //return $descuento->descuento_mun;
                                }
                            }
                        }
                    }
                }
            } else {
                //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                if (count($ordenPagosPUC) > 0) {
                    foreach ($ordenPagosPUC as $op_puc) {
                        if ($op_puc->ordenPago->estado == '1') {
                            if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                $total = $total + $op_puc->valor_debito;
                                $total = $total - $op_puc->valor_credito;
                                $totDeb = $totDeb + $op_puc->valor_debito;
                                $totCred = $totCred + $op_puc->valor_credito;
                                if (isset($op_puc->ordenPago->registros->persona)) {
                                    $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                    $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                } else {
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }
                                $result[] = collect(['fecha' => Carbon::parse($op_puc->ordenPago->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                    'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                    'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                    'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6]);

                            }
                        }
                    }
                }

                // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                $pagoBanks = PagoBanksNew::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                if (count($pagoBanks) > 0) {
                    foreach ($pagoBanks as $pagoBank) {
                        if ($pagoBank->pago->estado == 1) {
                            $total = $total + $pagoBank->debito;
                            $total = $total - $pagoBank->credito;
                            $pago = Pagos::find($pagoBank->pagos_id);
                            if (isset($pago->orden_pago->registros->persona)) {
                                $tercero = $pago->orden_pago->registros->persona->nombre;
                                $numIdent = $pago->orden_pago->registros->persona->num_dc;
                            } else {
                                $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                $numIdent = 800197268;
                            }

                            $totDeb = $totDeb + $pagoBank->debito;
                            $totCred = $totCred + $pagoBank->credito;
                            if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                            else $referencia = "Pago #" . $pago->code;
                            $result[] = collect(['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                'modulo' => $referencia, 'debito' => '$' . number_format($pagoBank->debito, 0),
                                'credito' => '$' . number_format($pagoBank->credito, 0), 'tercero' => $tercero,
                                'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                        }
                    }
                }

                //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                if (count($compsCont) > 0) {
                    foreach ($compsCont as $compCont) {
                        if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                            if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                $user = User::find($compCont->comprobante->persona_id);
                                $tercero = $user->name;
                                $numIdent = $user->email;
                            } else {
                                $persona = Persona::find($compCont->comprobante->persona_id);
                                $tercero = $persona->nombre;
                                $numIdent = $persona->num_dc;
                            }
                            //SE HACE LA BUSQUEDA DEL PAGO DEL IMPUESTO PARA LA CORRESPONDIENTE CONVERSION DE USD A COL
                            $strData = substr($compCont->comprobante->concepto, 0,8);
                            if ($strData == "MUELLAJE") {
                                $pos = strpos($compCont->comprobante->concepto, '#') + 1;
                                $tam = strlen($compCont->comprobante->concepto) - $pos;
                                $idImp = substr($compCont->comprobante->concepto, $pos, $tam);
                                $impuesto = \App\Model\Impuestos\Pagos::find($idImp);
                                $muellaje = Muellaje::find($impuesto->entity_id);
                                $compCont->debito = $muellaje->valorDolar * $muellaje->valorPago;
                            }
                            if ($compCont->cuenta_banco == $rubroPUC->id) {
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                    'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                            } else {
                                $total = $total + $compCont->debito;
                                $total = $total - $compCont->credito;
                                $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                    'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                    'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                    'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                    'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                            }
                        }
                    }
                }
            }

            if (isset($result)) return $result;
            else return [];

        }
    }

    public function findlvl4($cuenta, $fechaIni, $fechaFin, $total, $totDeb, $totCred){
        $rubrosPUC = PucAlcaldia::where('padre_id',$cuenta->id)->get();

        if ($rubrosPUC->count() >= 1) {
            foreach ($rubrosPUC as $rubroPUC) {
                if ($rubroPUC->id == 765){
                    //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                    $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                    foreach ($pagosFin as $pagoF){
                        foreach ($pagoF->orden_pago->descuentos as $descuento){
                            if ($descuento->valor > 0){
                                if ($descuento->desc_municipal_id != null){
                                    //DESCUENTOS MUNICIPALES
                                    if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                        $total = $total + $descuento->valor;
                                        $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                        $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                        $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                            'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0), 'from' => 5,
                                            'totDeb' => $totDeb, 'totCred' => $totCred]);
                                        //return $descuento->descuento_mun;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                    $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                    if (count($ordenPagosPUC) > 0) {
                        foreach ($ordenPagosPUC as $op_puc) {
                            if ($op_puc->ordenPago->estado == '1') {
                                if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                    $total = $total + $op_puc->valor_debito;
                                    $total = $total - $op_puc->valor_credito;
                                    $totDeb = $totDeb + $op_puc->valor_debito;
                                    $totCred = $totCred + $op_puc->valor_credito;
                                    if (isset($op_puc->ordenPago->registros->persona)) {
                                        $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                        $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                    } else {
                                        $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                        $numIdent = 800197268;
                                    }
                                    $result[] = collect(['fecha' => Carbon::parse($op_puc->ordenPago->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                        'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                        'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                        'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6,
                                        'totDeb' => $totDeb, 'totCred' => $totCred]);
                                }
                            }
                        }
                    }

                    $pagoBanks = PagoBanksNew::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                    if (count($pagoBanks) > 0) {
                        foreach ($pagoBanks as $pagoBank) {
                            if ($pagoBank->pago->estado == 1) {
                                $total = $total + $pagoBank->debito;
                                $total = $total - $pagoBank->credito;
                                $pago = Pagos::find($pagoBank->pagos_id);
                                if (isset($pago->orden_pago->registros->persona)) {
                                    $tercero = $pago->orden_pago->registros->persona->nombre;
                                    $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                } else {
                                    $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                    $numIdent = 800197268;
                                }

                                $totDeb = $totDeb + $pagoBank->debito;
                                $totCred = $totCred + $pagoBank->credito;
                                if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                                else $referencia = "Pago #" . $pago->code;
                                $result[] = collect(['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                    'modulo' => $referencia, 'debito' => '$' . number_format($pagoBank->debito, 0),
                                    'credito' => '$' . number_format($pagoBank->credito, 0), 'tercero' => $tercero,
                                    'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                    'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                    'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                            }
                        }
                    }

                    //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                    $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                    if (count($compsCont) > 0) {
                        foreach ($compsCont as $compCont) {
                            if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                                if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                    $user = User::find($compCont->comprobante->persona_id);
                                    $tercero = $user->name;
                                    $numIdent = $user->email;
                                } else {
                                    $persona = Persona::find($compCont->comprobante->persona_id);
                                    $tercero = $persona->nombre;
                                    $numIdent = $persona->num_dc;
                                }
                                //SE HACE LA BUSQUEDA DEL PAGO DEL IMPUESTO PARA LA CORRESPONDIENTE CONVERSION DE USD A COL
                                $strData = substr($compCont->comprobante->concepto, 0,8);
                                if ($strData == "MUELLAJE") {
                                    $pos = strpos($compCont->comprobante->concepto, '#') + 1;
                                    $tam = strlen($compCont->comprobante->concepto) - $pos;
                                    $idImp = substr($compCont->comprobante->concepto, $pos, $tam);
                                    $impuesto = \App\Model\Impuestos\Pagos::find($idImp);
                                    $muellaje = Muellaje::find($impuesto->entity_id);
                                    $compCont->debito = $muellaje->valorDolar * $muellaje->valorPago;
                                }
                                if ($compCont->cuenta_banco == $rubroPUC->id) {
                                    $total = $total + $compCont->debito;
                                    $total = $total - $compCont->credito;
                                    $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                        'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                        'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                        'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                        'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                } else {
                                    $total = $total + $compCont->debito;
                                    $total = $total - $compCont->credito;
                                    $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                        'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                        'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                        'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                        'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                }
                            }
                        }
                    }
                }
            }

            if (isset($result)) return $result;
            else return [];
        }
    }

    public function findlvl3($cuenta, $fechaIni, $fechaFin, $total, $totDeb, $totCred){
        $lv4 = PucAlcaldia::where('padre_id', $cuenta->id)->get();
        foreach ($lv4 as $item) {
            $rubrosPUC = PucAlcaldia::where('padre_id', $item->id)->get();
            if ($rubrosPUC->count() >= 1) {
                foreach ($rubrosPUC as $rubroPUC) {
                    if ($rubroPUC->id == 765){
                        //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                        $pagosFin = Pagos::where('estado','1')->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                        foreach ($pagosFin as $pagoF){
                            foreach ($pagoF->orden_pago->descuentos as $descuento){
                                if ($descuento->valor > 0){
                                    if ($descuento->desc_municipal_id != null){
                                        //DESCUENTOS MUNICIPALES
                                        if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                            $total = $total + $descuento->valor;
                                            $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                            $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                            $result[] = collect(['fecha' => Carbon::parse($pagoF->created_at)->format('d-m-Y'), 'modulo' => 'Pago #'.$pagoF->code, 'debito' => '$'.number_format(0,0),
                                                'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                'total' => '$'.number_format($total,0), 'from' => 5,
                                                'totDeb' => $totDeb, 'totCred' => $totCred]);
                                            //return $descuento->descuento_mun;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                        $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                        if (count($ordenPagosPUC) > 0) {
                            foreach ($ordenPagosPUC as $op_puc) {
                                if ($op_puc->ordenPago->estado == '1') {
                                    if ($op_puc->ordenPago->created_at >= $fechaIni and $op_puc->ordenPago->created_at <= $fechaFin) {
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        $totDeb = $totDeb + $op_puc->valor_debito;
                                        $totCred = $totCred + $op_puc->valor_credito;
                                        if (isset($op_puc->ordenPago->registros->persona)) {
                                            $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                            $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                        } else {
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($op_puc->ordenPago->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago #' . $op_puc->ordenPago->code,
                                            'debito' => '$' . number_format($op_puc->valor_debito, 0), 'credito' => '$' . number_format($op_puc->valor_credito, 0),
                                            'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre,
                                            'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto, 'total' => '$' . number_format($total, 0), 'from' => 6]);
                                    }
                                }
                            }
                        }

                        $pagoBanks = PagoBanksNew::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at', array($fechaIni, $fechaFin))->get();
                        if (count($pagoBanks) > 0) {
                            foreach ($pagoBanks as $pagoBank) {
                                if ($pagoBank->pago->estado == 1) {
                                    $total = $total + $pagoBank->debito;
                                    $total = $total - $pagoBank->credito;
                                    $pago = Pagos::find($pagoBank->pagos_id);
                                    if (isset($pago->orden_pago->registros->persona)) {
                                        $tercero = $pago->orden_pago->registros->persona->nombre;
                                        $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                    } else {
                                        $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                        $numIdent = 800197268;
                                    }

                                    $totDeb = $totDeb + $pagoBank->debito;
                                    $totCred = $totCred + $pagoBank->credito;
                                    if ($pago->type_pay == "CHEQUE") $referencia = "Pago #" . $pago->code . " - # Cheque " . $pago->num;
                                    else $referencia = "Pago #" . $pago->code;
                                    $result[] = collect(['fecha' => Carbon::parse($pago->created_at)->format('d-m-Y'),
                                        'modulo' => $referencia, 'debito' => '$' . number_format($pagoBank->debito, 0),
                                        'credito' => '$' . number_format($pagoBank->credito, 0), 'tercero' => $tercero,
                                        'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                        'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial,
                                        'totDeb' => $totDeb, 'totCred' => $totCred, 'pago_id' => $pagoBank->pagos_id, 'pago_estado' => $pago->estado]);
                                }
                            }
                        }

                        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp', array($fechaIni, $fechaFin))->get();
                        if (count($compsCont) > 0) {
                            foreach ($compsCont as $compCont) {
                                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id) {
                                    if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos") {
                                        $user = User::find($compCont->comprobante->persona_id);
                                        $tercero = $user->name;
                                        $numIdent = $user->email;
                                    } else {
                                        $persona = Persona::find($compCont->comprobante->persona_id);
                                        $tercero = $persona->nombre;
                                        $numIdent = $persona->num_dc;
                                    }
                                    //SE HACE LA BUSQUEDA DEL PAGO DEL IMPUESTO PARA LA CORRESPONDIENTE CONVERSION DE USD A COL
                                    $strData = substr($compCont->comprobante->concepto, 0,8);
                                    if ($strData == "MUELLAJE") {
                                        $pos = strpos($compCont->comprobante->concepto, '#') + 1;
                                        $tam = strlen($compCont->comprobante->concepto) - $pos;
                                        $idImp = substr($compCont->comprobante->concepto, $pos, $tam);
                                        $impuesto = \App\Model\Impuestos\Pagos::find($idImp);
                                        $muellaje = Muellaje::find($impuesto->entity_id);
                                        $compCont->debito = $muellaje->valorDolar * $muellaje->valorPago;
                                    }
                                    if ($compCont->cuenta_banco == $rubroPUC->id) {
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                            'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                            'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                    } else {
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #' . $compCont->comprobante->code, 'debito' => '$' . number_format($compCont->debito, 0),
                                            'credito' => '$' . number_format($compCont->credito, 0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code . ' - ' . $rubroPUC->concepto,
                                            'total' => '$' . number_format($total, 0), 'inicial' => $rubroPUC->saldo_inicial, 'totDeb' => $totDeb, 'totCred' => $totCred,
                                            'pago_id' => '', 'pago_estado' => '', 'CC_id' => $compCont->comprobante->id]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($result)) return $result;
        else return [];
    }


    public function validateBeforeMonths($lastDate, $rubroPUC){
        $today = Carbon::today();//2023-04-11
        $lastDate = Carbon::parse($lastDate." 23:59:59");//2023-2-1 23:59:59
        $fechaIni = Carbon::parse($today->year."-01-01");//2023-01-01
        $fechaFin = $lastDate->subDays(1);//2023-31-1 23:59:59
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $mes = $fechaFin->month.'-'.$today->year;//1-2023

        //SE AGREGAN LOS TOTALES DE LAS ORDENES DE PAGO
        $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        if (count($ordenPagosPUC) > 0) {
            foreach ($ordenPagosPUC as $op_puc) {
                if ($op_puc->ordenPago->estado == '1') {
                    $total = $total + $op_puc->valor_debito;
                    $total = $total - $op_puc->valor_credito;
                    $totDeb = $totDeb + $op_puc->valor_debito;
                    $totCred = $totCred + $op_puc->valor_credito;
                }
            }
        }

        $pagoBanks = PagoBanksNew::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        //select * from pago_banks where rubros_puc_id == 28 and created_at >= 2023-01-01 and created_at <= 2023-31-1
        //trae todos los pagos de bancos hechos entre una fecha inicial que seria el primero de enero del año actual a la fecha final
        // que seria el dia que hagan el descargue de el informe
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    $total = $total - $pagoBank->credito;
                    $total = $total + $pagoBank->debito;
                    $totDeb = $totDeb + $pagoBank->debito;
                    $totCred = $totCred + $pagoBank->credito;
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
        //aca coge todos los movimientos de las cuenta de bancos poara generar los libros y al igual que pahgo de bancos tiene una fecha final y una fecha inicial
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    //SE HACE LA BUSQUEDA DEL PAGO DEL IMPUESTO PARA LA CORRESPONDIENTE CONVERSION DE USD A COL
                    $strData = substr($compCont->comprobante->concepto, 0,8);
                    if ($strData == "MUELLAJE") {
                        $pos = strpos($compCont->comprobante->concepto, '#') + 1;
                        $tam = strlen($compCont->comprobante->concepto) - $pos;
                        $idImp = substr($compCont->comprobante->concepto, $pos, $tam);
                        $impuesto = \App\Model\Impuestos\Pagos::find($idImp);
                        $muellaje = Muellaje::find($impuesto->entity_id);
                        $compCont->debito = $muellaje->valorDolar * $muellaje->valorPago;
                    }
                    if ($compCont->cuenta_banco == $rubroPUC->id){
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    } else{
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    }
                }
            }
        }

        return collect(['total' => $total, 'fecha' => $mes]);
    }


    public function saldoActual($rubroPUC){
        $today = Carbon::today();//2023-04-11
        $lastDate = Carbon::parse($today->year."-12-31 23:59:59");
        $fechaIni = Carbon::parse($today->year."-01-01 00:00:01");
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $totCredComp[] = 0;
        $totDebComp[] = 0;

        //SE AGREGAN LOS VALORES DE LAS ORDENES DE PAGO
        $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $lastDate))->get();
        if (count($ordenPagosPUC) > 0) {
            foreach ($ordenPagosPUC as $op_puc) {
                if ($op_puc->ordenPago->estado == '1') {
                    $total = $total + $op_puc->valor_debito;
                    $total = $total - $op_puc->valor_credito;
                    $totCredComp[] = $op_puc->credito;
                    $totDebComp[] = $op_puc->debito;
                    $totDeb = $totDeb + $op_puc->valor_debito;
                    $totCred = $totCred + $op_puc->valor_credito;
                }
            }
        }

        $pagoBanks = PagoBanksNew::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $lastDate))->get();
        //select * from pago_banks where rubros_puc_id == 28 and created_at >= 2023-01-01 and created_at <= 2023-31-1
        //trae todos los pagos de bancos hechos entre una fecha inicial que seria el primero de enero del año actual a la fecha final
        // que seria el dia que hagan el descargue de el informe
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    $total = $total - $pagoBank->credito;
                    $total = $total + $pagoBank->debito;
                    $totDeb = $totDeb + $pagoBank->debito;
                    $totCred = $totCred + $pagoBank->credito;
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $lastDate))->get();
        //aca coge todos los movimientos de las cuenta de bancos poara generar los libros y al igual que pahgo de bancos tiene una fecha final y una fecha inicial
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    //SE HACE LA BUSQUEDA DEL PAGO DEL IMPUESTO PARA LA CORRESPONDIENTE CONVERSION DE USD A COL
                    $strData = substr($compCont->comprobante->concepto, 0,8);
                    if ($strData == "MUELLAJE") {
                        $pos = strpos($compCont->comprobante->concepto, '#') + 1;
                        $tam = strlen($compCont->comprobante->concepto) - $pos;
                        $idImp = substr($compCont->comprobante->concepto, $pos, $tam);
                        $impuesto = \App\Model\Impuestos\Pagos::find($idImp);
                        $muellaje = Muellaje::find($impuesto->entity_id);
                        $compCont->debito = $muellaje->valorDolar * $muellaje->valorPago;
                    }
                    $total = $total + $compCont->debito;
                    $total = $total - $compCont->credito;
                }
            }
        }

        //return collect(['total' => $total, 'cred' => array_sum($totCredComp), 'deb' => array_sum($totDebComp)]);
        return $total;
    }

}