<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Tesoreria\NotaCredito;
use App\Http\Controllers\Controller;
use App\Model\Hacienda\Presupuesto\Rubro;
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
        $hijos = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();
        $vigenciaEgresos = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->first();
        $vigenciaIng = Vigencia::where('vigencia', $añoActual)->where('tipo', 1)->first();
        $rubrosEgresos = Rubro::where('vigencia_id', $vigenciaEgresos->id)->orderBy('cod','ASC')->get();
        $rubrosIngresos = Rubro::where('vigencia_id', $vigenciaIng->id)->orderBy('cod','ASC')->get();

        return view('administrativo.tesoreria.notacredito.create', compact('añoActual','hijos',
        'rubrosEgresos', 'rubrosIngresos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);

        $añoActual = Carbon::today()->year;

        if($request->hasFile('file')) {
            $file = new FileTraits;
            $ruta = $file->File($request->file('file'), 'NotaCredito');
        }else $ruta = "";

        $countNC = NotaCredito::where('año', $añoActual)->orderBy('id')->get()->last();
        if ($countNC == null)  $count = 0;
        else $count = $countNC->code;

        $nota = new NotaCredito();
        $nota->code = $count + 1;
        $nota->año = $añoActual;
        $nota->concepto = $request->valor;
        $nota->valor = $request->valor;
        $nota->iva = $request->valorIva;
        $nota->val_total = $request->valor + $request->valorIva;
        $nota->estado = $request->estado;
        $nota->ff = $request->fecha;
        $nota->tipoCI = $request->tipoCI;
        $nota->cualOtroTipo = $request->cualOtroTipo;
        $nota->user_id = $request->user_id;
        $nota->vigencia_id = $request->vigencia_id;
        $nota->puc_alcaldia_id = $request->cuentaDeb;
        $nota->ruta = $ruta;
        $nota->save();

        Session::flash('success','La nota credito se ha creado exitosamente');
        return redirect('/administrativo/tesoreria/notasCredito');
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
