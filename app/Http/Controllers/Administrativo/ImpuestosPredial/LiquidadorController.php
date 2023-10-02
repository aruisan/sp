<?php

namespace App\Http\Controllers\Administrativo\ImpuestosPredial;

use App\Model\Administrativo\ImpuestosPredial\Liquidador;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Session;

class LiquidadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $liquidadores = Liquidador::orderBy('vencimiento','DESC')->get();
        return view('administrativo.impuestos-predial.liquidador.index', compact('liquidadores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrativo.impuestos-predial.liquidador.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateMes = Liquidador::where('año', Carbon::today()->Format('Y'))->where('mes',Carbon::today()->Format('m'))->get();
        if ($validateMes->count() > 0){
            Session::flash('error','Ya hay un registro almacenado con el mismo año y mes');
            return redirect('/administrativo/impuestospredial/liquidador');
        } else {
            $newMes = new Liquidador();
            $newMes->año = Carbon::today()->Format('Y');
            $newMes->mes = Carbon::today()->Format('m');
            $newMes->vencimiento = $request->vencimiento;
            $newMes->valor = $request->valor;
            $newMes->save();

            Session::flash('success','Se ha registrado correctamente el nuevo mes del liquidador');
            return redirect('/administrativo/impuestospredial/liquidador');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Liquidador  $liquidador
     * @return \Illuminate\Http\Response
     */
    public function destroy(Liquidador $liquidador)
    {
        $mes = Liquidador::find($liquidador);
        $mes->each->delete();

        Session::flash('error','Mes eliminado correctamente');
        return redirect('/administrativo/impuestospredial/liquidador');
    }

}
