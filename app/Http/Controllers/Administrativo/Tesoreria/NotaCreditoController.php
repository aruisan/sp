<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

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
    public function index($id)
    {
        dd("INDEX NOTA CREDITO", $id);
        $vigencia = Vigencia::findOrFail($id);
        if ($vigencia->tipo == 1){
            $notasT = NotaCredito::where('vigencia_id', $id)->where('estado','!=','3')->get();
            $notas = NotaCredito::where('vigencia_id', $id)->where('estado','3')->get();

            return view('administrativo.tesoreria.notacredito.index', compact('vigencia', 'notasT', 'notas'));
        } else {
            return back();
        }
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