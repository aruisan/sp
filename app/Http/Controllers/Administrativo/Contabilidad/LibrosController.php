<?php

namespace App\Http\Controllers\Administrativo\Contabilidad;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\Pagos;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use App\Model\Persona;
use Carbon\Carbon;
use Session;

class LibrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lv1 = PucAlcaldia::where('padre_id',0)->get();
        foreach ($lv1 as $item){
            $lv2 = PucAlcaldia::where('padre_id', $item->id )->get();
            foreach ($lv2 as $dato){
                $lv3 = PucAlcaldia::where('padre_id', $dato->id )->get();
                foreach ($lv3 as $object){
                    $result[] = $object;
                }
            }
        }

        return view('administrativo.contabilidad.libros.index',compact('result'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRubrosPUC(Request $request)
    {
        $lv4 = PucAlcaldia::where('padre_id', $request->id)->get();
        $total = 0;
        foreach ($lv4 as $item){
            $rubrosPUC = PucAlcaldia::where('padre_id',$item->id)->get();
            if ($rubrosPUC->count() >= 1){
                foreach ($rubrosPUC as $rubroPUC){

                    if ($request->id == 765){
                        //VALIDACION PARA LAS CUENTAS DE DESCUENTOS
                        $pagosFin = Pagos::where('estado','1')->get();
                        foreach ($pagosFin as $pagoF){
                            $añoPago = Carbon::parse($pagoF->ff_fin)->format('Y');
                            $añoActual = Carbon::today()->format('Y');
                            if ($añoPago == $añoActual){
                                foreach ($pagoF->orden_pago->descuentos as $descuento){
                                    if ($descuento->valor > 0){
                                        if ($descuento->desc_municipal_id != null){
                                            //DESCUENTOS MUNICIPALES
                                            if ($rubroPUC->code == $descuento->descuento_mun->codigo){
                                                $total = $total + $descuento->valor;
                                                $tercero = $pagoF->orden_pago->registros->persona->nombre;
                                                $numIdent = $pagoF->orden_pago->registros->persona->num_dc;
                                                $result[] = collect(['fecha' => Carbon::parse($pagoF->ff_fin)->format('d-m-Y'), 'modulo' => 'Pago', 'debito' => '$'.number_format(0,0),
                                                    'credito' => '$'.number_format($descuento->valor,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $descuento->descuento_mun->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                                    'total' => '$'.number_format($total,0)]);
                                                //return $descuento->descuento_mun;

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else{
                        //SE AÑADEN LOS VALORES DE LAS ORDENES DE PAGO AL LIBRO
                        $ordenPagosPUC = OrdenPagosPuc::where('rubros_puc_id', $rubroPUC->id)->get();
                        if (count($ordenPagosPUC) > 0){
                            foreach ($ordenPagosPUC as $op_puc){
                                if ($op_puc->ordenPago->estado === '1'){
                                    if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                        $total = $total + $op_puc->valor_debito;
                                        $total = $total - $op_puc->valor_credito;
                                        if (isset($op_puc->ordenPago->registros->persona)){
                                            $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                            $numIdent = $op_puc->ordenPago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago', 'debito' => '$'.number_format($op_puc->valor_debito,0),
                                            'credito' => '$'.number_format($op_puc->valor_credito,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0)]);
                                    }
                                }
                            }
                        }

                        // SE AÑADEN LOS VALORES DE LOS PAGOS AL LIBRO
                        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->get();
                        if (count($pagoBanks) > 0){
                            foreach ($pagoBanks as $pagoBank){
                                if ($pagoBank->pago->estado == 1){
                                    if (Carbon::parse($pagoBank->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                        $total = $total + $pagoBank->valor;
                                        $pago = Pagos::find($pagoBank->pagos_id);
                                        if (isset($pago->orden_pago->registros->persona)){
                                            $tercero = $pago->orden_pago->registros->persona->nombre;
                                            $numIdent = $pago->orden_pago->registros->persona->num_dc;
                                        } else{
                                            $tercero = 'DIRECCIÓN DE IMPUESTOS Y ADUANAS DIAN';
                                            $numIdent = 800197268;
                                        }
                                        $result[] = collect(['fecha' => Carbon::parse($pagoBank->created_at)->format('d-m-Y'), 'modulo' => 'Pago', 'debito' => '$'.number_format($pagoBank->valor,0),
                                            'credito' => '$'.number_format(0,0), 'tercero' => $tercero, 'CC' => $numIdent, 'concepto' => $pago->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0)]);
                                    }
                                }
                            }
                        }

                        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
                        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array(Carbon::today()->format('Y').'-01-01',
                            Carbon::today()->format('Y').'-12-31'))->get();
                        if (count($compsCont) > 0){
                            foreach ($compsCont as $compCont){
                                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                                    if ($compCont->comprobante->tipoCI == "Comprobante de Ingresos"){
                                        $user = User::find($compCont->comprobante->persona_id);
                                        $tercero = $user->name;
                                        $numIdent = $user->email;
                                    } else{
                                        $persona = Persona::find($compCont->comprobante->persona_id);
                                        $tercero = $persona->nombre;
                                        $numIdent = $persona->num_dc;
                                    }
                                    if ($compCont->cuenta_banco == $rubroPUC->id){
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0)]);
                                    } else{
                                        $total = $total + $compCont->debito;
                                        $total = $total - $compCont->credito;
                                        $result[] = collect(['fecha' => Carbon::parse($compCont->fechaComp)->format('d-m-Y'),
                                            'modulo' => 'Comprobante Contable #'.$compCont->comprobante->code, 'debito' => '$'.number_format($compCont->debito,0),
                                            'credito' => '$'.number_format($compCont->credito,0), 'tercero' => $tercero, 'CC' => $numIdent,
                                            'concepto' => $compCont->comprobante->concepto, 'cuenta' => $rubroPUC->code.' - '.$rubroPUC->concepto,
                                            'total' => '$'.number_format($total,0)]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
}
