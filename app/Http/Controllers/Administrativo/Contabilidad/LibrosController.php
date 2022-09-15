<?php

namespace App\Http\Controllers\Administrativo\Contabilidad;

use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;

class LibrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cuentasPUClvl3 = RegistersPuc::where('level_puc_id',3)->get();
        foreach ($cuentasPUClvl3 as $cuentaslvl3){
            $save = false;
            $cuentaslvl3->code = $cuentaslvl3->code_padre->registers->code_padre->registers->code.$cuentaslvl3->code_padre->registers->code.$cuentaslvl3->code;
            $hijos = RegistersPuc::where('register_puc_id', $cuentaslvl3->id)->get();
            if ($hijos->count() >= 1){
                foreach ($hijos as $hijo){
                    $hijo->code = $cuentaslvl3->code.$hijo->code;
                    $rubro = RubrosPuc::where('registers_puc_id',$hijo->id)->get();
                    if ($rubro->count() >= 1) $save = true;
                }
            }
            if ($save) $result[] = collect($cuentaslvl3);
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
        $padre = RegistersPuc::find($request->id);
        $padre->code = $padre->code_padre->registers->code_padre->registers->code.$padre->code_padre->registers->code.$padre->code;
        $hijos = RegistersPuc::where('register_puc_id', $request->id)->get();
        $total = 0;
        foreach ($hijos as $hijo){
            $hijo->code = $padre->code.$hijo->code;
            $rubrosPUC = RubrosPuc::where('registers_puc_id',$hijo->id)->get();
            if ($rubrosPUC->count() >= 1){
                //$result[] = collect($hijo);
                foreach ($rubrosPUC as $rubroPUC){
                    $rubroPUC->codigo = $hijo->code.$rubroPUC->codigo;
                    //$result[] = collect($rubroPUC);
                    if ($rubroPUC->op_puc->count() >= 1){
                        foreach ($rubroPUC->op_puc as $op_puc){
                            if (Carbon::parse($op_puc->created_at)->format('Y') == Carbon::today()->format('Y')) {
                                $total = $total + $op_puc->valor_debito;
                                $total = $total - $op_puc->valor_credito;
                                $tercero = $op_puc->ordenPago->registros->persona->nombre;
                                $result[] = collect(['fecha' => Carbon::parse($op_puc->created_at)->format('d-m-Y'), 'modulo' => 'Orden de Pago', 'debito' => '$'.number_format($op_puc->valor_debito,0),
                                    'credito' => '$'.number_format($op_puc->valor_credito,0), 'tercero' => $tercero, 'concepto' => $op_puc->ordenPago->nombre, 'cuenta' => $rubroPUC->codigo.' - '.$rubroPUC->nombre_cuenta,
                                    'total' => '$'.number_format($total,0)]);
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
}
