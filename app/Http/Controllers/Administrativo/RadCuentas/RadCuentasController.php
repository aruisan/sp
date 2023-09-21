<?php

namespace App\Http\Controllers\Administrativo\RadCuentas;

use App\Model\Administrativo\RadCuentas\RadCuentas;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Persona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use PDF;

class RadCuentasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $radCuentasHist = RadCuentas::where('vigencia_id', $id)->get();
        $radCuentasPend = RadCuentas::where('vigencia_id', $id)->where('estado','1')->get();

        return view('administrativo.radcuentas.index', compact('radCuentasHist','radCuentasPend','id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $personas = Persona::all();
        $user = Auth::user();
        return view('administrativo.radcuentas.create', compact('id','personas','user'));
    }

    public function findDataPer(Request $request){
        $historyRad = RadCuentas::where('persona_id', $request->idPer)->where('vigencia_id', $request->vigencia_id)->get();
        $registros = Registro::where('saldo','>',0)->where('tipo_contrato','!=',20)->where('tipo_contrato','!=',22)
            ->where('jefe_e','3')->where('persona_id', $request->idPer)->where('vigencia_id', $request->vigencia_id)
            ->with('persona')->get();
        $data = ['history' => $historyRad, 'registros' => $registros];
        return $data;
    }

    public function findDataRP(Request $request){
        $registro = Registro::where('id',$request->idRP)->with('persona')->first();
        foreach ($registro->cdpRegistroValor as $cdpRegValue) $cdps[] = $cdpRegValue->cdps;

        return ['registro' => $registro,'cdp' => $cdps];
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
     * @param  \App\RadCuentas  $radCuentas
     * @return \Illuminate\Http\Response
     */
    public function show(RadCuentas $radCuentas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RadCuentas  $radCuentas
     * @return \Illuminate\Http\Response
     */
    public function edit(RadCuentas $radCuentas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RadCuentas  $radCuentas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RadCuentas $radCuentas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RadCuentas  $radCuentas
     * @return \Illuminate\Http\Response
     */
    public function destroy(RadCuentas $radCuentas)
    {
        //
    }
}
