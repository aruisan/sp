<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\descuentos;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
