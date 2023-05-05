<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\descuentos;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Tesoreria\descuentos\TesoreriaDescuentos;
use App\Model\Hacienda\Presupuesto\Vigencia;
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
    public function store(Request $request)
    {
        //
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
