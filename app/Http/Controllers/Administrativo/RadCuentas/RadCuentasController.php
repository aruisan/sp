<?php

namespace App\Http\Controllers\Administrativo\RadCuentas;

use App\BPin;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\Pago\Pagos;
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
        $radCuentasHist = RadCuentas::where('vigencia_id', $id)->where('estado','!=','0')->get();
        $radCuentasPend = RadCuentas::where('vigencia_id', $id)->where('estado','0')->get();

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
        foreach ($registro->cdpRegistroValor as $cdpRegValue) {
            if ($cdpRegValue->cdps->tipo == "Inversion"){
                $bpinsCdpVal = BpinCdpValor::where('cdp_id', $cdpRegValue->cdps->id)->get();
                foreach ($bpinsCdpVal as $bpinCdp){
                    $DepRubFont = DependenciaRubroFont::find($bpinCdp->dependencia_rubro_font_id);
                    $cdpRegValue->cdps->bpin = BPin::where('cod_actividad', $bpinCdp->cod_actividad)->first();
                    $cdpRegValue->cdps->fuente = $DepRubFont->fontRubro->sourceFunding;
                    $cdpRegValue->cdps->dep = $DepRubFont->dependencia;
                    $cdpRegValue->cdps->rubro = $DepRubFont->fontRubro->rubro;
                    $cdps[] = $cdpRegValue->cdps;
                }
            } else{
                $rubsCdpVal = RubrosCdpValor::where('cdp_id', $cdpRegValue->cdps->id)->get();
                foreach ($rubsCdpVal as $rubCdp){
                    $DepRubFont = DependenciaRubroFont::find($rubCdp->fontsDep_id);
                    $cdpRegValue->cdps->fuente = $DepRubFont->fontRubro->sourceFunding;
                    $cdpRegValue->cdps->dep = $DepRubFont->dependencia;
                    $cdpRegValue->cdps->rubro = $DepRubFont->fontRubro->rubro;
                    $cdps[] = $cdpRegValue->cdps;
                }
            }
        }

        $ordenesPago = OrdenPagos::where('registros_id', $registro->id)->get();
        foreach ($ordenesPago as $ordenPago) $pagos[] = Pagos::where('orden_pago_id', $ordenPago->id)->get();

        if (!isset($pagos)) $pagos = [];

        return ['registro' => $registro,'cdps' => $cdps, 'ops' => $ordenesPago, 'pagos' => $pagos];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function radCuentasFirst(Request $request)
    {
        $radCuenta = new RadCuentas();
        $radCuenta->persona_id = $request->persona_id;
        $radCuenta->fecha_inicio = $request->fecha_inicio;
        $radCuenta->plazo_ejec = $request->plazo_ejecu_dias;
        $radCuenta->prorroga = $request->prorroga;
        $radCuenta->fecha_fin = $request->fecha_fin;
        $radCuenta->estado = '0';
        $radCuenta->registro_id  = $request->registro_id;
        $radCuenta->user_id = Auth::user()->id;
        $radCuenta->vigencia_id = $request->vigencia_id;
        $radCuenta->interventor_id = $request->interventor_id;
        $radCuenta->save();

        $radCuenta->code = $radCuenta->id;
        $radCuenta->save();

        $registro = Registro::find($request->registro_id);
        if ($request->tipo_contrato != "0") $registro->tipo_contrato = $request->tipo_contrato;
        if ($request->mod_seleccion != "CAMBIAR LA MODALIDAD") $registro->mod_seleccion = $request->mod_seleccion;
        $registro->ff_doc = $request->fecha_cont;
        $registro->save();

        $persona = Persona::find($request->persona_id);
        $persona->nombre = $request->contratista;
        $persona->num_dc  = $request->cedula;
        if ($request->regimen_tributario != "CAMBIAR EL REGIMEN TRIBUTARIO") $persona->regimen  = $request->regimen_tributario;
        $persona->reteFuente  = $request->retefuente;
        $persona->direccion  = $request->dir;
        $persona->email   = $request->email;
        $persona->telefono  = $request->cel;
        $persona->direccion  = $request->dir;
        $persona->telefono_fijo  = $request->telFijo;
        $persona->numero_cuenta_bancaria  = $request->cuentaBanc;
        if ($request->banco != "0") $persona->banco_cuenta_bancaria  = $request->banco;
        if ($request->tipo_cuenta != "CAMBIAR EL TIPO DE CUENTA") $persona->tipo_cuenta_bancaria  = $request->tipo_cuenta;
        $persona->save();

        return redirect('administrativo/radCuentas/'.$radCuenta->id.'/2');
    }

    public function pasos($id, $paso){
        $radCuenta = RadCuentas::find($id);
        $vigencia_id = $radCuenta->vigencia_id;
        $registro = Registro::where('id',$radCuenta->registro_id )->with('persona')->first();
        $allRPs = Registro::where('id','!=',$radCuenta->registro_id )->where('vigencia_id', $vigencia_id)->with('persona')->get();
        foreach ($registro->cdpRegistroValor as $cdpRegValue) {
            if ($cdpRegValue->cdps->tipo == "Inversion"){
                $bpinsCdpVal = BpinCdpValor::where('cdp_id', $cdpRegValue->cdps->id)->get();
                foreach ($bpinsCdpVal as $bpinCdp){
                    $DepRubFont = DependenciaRubroFont::find($bpinCdp->dependencia_rubro_font_id);
                    $cdpRegValue->cdps->bpin = BPin::where('cod_actividad', $bpinCdp->cod_actividad)->first();
                    $cdpRegValue->cdps->fuente = $DepRubFont->fontRubro->sourceFunding;
                    $cdpRegValue->cdps->dep = $DepRubFont->dependencia;
                    $cdpRegValue->cdps->rubro = $DepRubFont->fontRubro->rubro;
                    $cdps[] = $cdpRegValue->cdps;
                }
            } else{
                $rubsCdpVal = RubrosCdpValor::where('cdp_id', $cdpRegValue->cdps->id)->get();
                foreach ($rubsCdpVal as $rubCdp){
                    $DepRubFont = DependenciaRubroFont::find($rubCdp->fontsDep_id);
                    $cdpRegValue->cdps->fuente = $DepRubFont->fontRubro->sourceFunding;
                    $cdpRegValue->cdps->dep = $DepRubFont->dependencia;
                    $cdpRegValue->cdps->rubro = $DepRubFont->fontRubro->rubro;
                    $cdps[] = $cdpRegValue->cdps;
                }
            }
        }
        return view('administrativo.radcuentas.paso2', compact('vigencia_id','radCuenta',
            'registro','cdps','allRPs'));
    }

}
