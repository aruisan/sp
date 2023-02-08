<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Tesoreria\NotaCredito;
use App\Http\Controllers\Controller;
use App\Model\Hacienda\Presupuesto\Vigencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class NotaCreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $añoActual = Carbon::today()->year;
        $notas = NotaCredito::where('año', $añoActual)->get();

        return view('administrativo.tesoreria.notacredito.index', compact('añoActual', 'notas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $añoActual = Carbon::today()->year;
        $hijosDebito = PucAlcaldia::where('hijo', '1')->where('naturaleza','DEBITO')->orderBy('code','ASC')->get();

        return view('administrativo.tesoreria.notacredito.create', compact('añoActual','hijosDebito'));
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
     * @param  \App\NotaCredito  $notaCredito
     * @return \Illuminate\Http\Response
     */
    public function show(NotaCredito $notaCredito)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NotaCredito  $notaCredito
     * @return \Illuminate\Http\Response
     */
    public function edit(NotaCredito $notaCredito)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NotaCredito  $notaCredito
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotaCredito $notaCredito)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NotaCredito  $notaCredito
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotaCredito $notaCredito)
    {
        //
    }
}
