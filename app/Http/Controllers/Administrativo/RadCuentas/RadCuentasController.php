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
use App\Model\Administrativo\RadCuentas\RadCuentasAdd;
use App\Model\Administrativo\RadCuentas\RadCuentasAnex;
use App\Model\Administrativo\RadCuentas\RadCuentasPago;
use App\Model\Administrativo\RadCuentas\RadCuentasPagoDesc;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Persona;
use App\Traits\ResourceTraits;
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
        $radCuentasHist = RadCuentas::where('vigencia_id', $id)->where('estado_rev','!=','0')->get();
        $radCuentasProceso = RadCuentas::where('vigencia_id', $id)->where('estado_elabor','1')->where('estado_rev','0')->get();
        $radCuentasPend = RadCuentas::where('vigencia_id', $id)->where('estado_elabor','0')->get();

        return view('administrativo.radcuentas.index', compact('radCuentasHist','radCuentasPend', 'id','radCuentasProceso'));
    }

    public function pdf($id){
        $radCuenta = RadCuentas::find($id);
        dd($id, $radCuenta);
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
        $radCuenta->estado_elabor = '0';
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
        if ($paso == 2){
            $vigencia_id = $radCuenta->vigencia_id;
            $registro = Registro::where('id',$radCuenta->registro_id )->with('persona')->first();
            $allRPs = Registro::where('id','!=',$radCuenta->registro_id )->where('vigencia_id', $vigencia_id)->with('persona')->get();
            $ordenesPago = OrdenPagos::where('registros_id', $registro->id)->where('estado','1')->get();
            $ordenesPagoAll = OrdenPagos::where('registros_id','!=',$registro->id)->where('estado','1')->get();
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
                'registro','cdps','allRPs','ordenesPago','ordenesPagoAll'));
        } elseif ($paso == 3){
            $vigencia_id = $radCuenta->vigencia_id;
            if ($radCuenta->pago == null) $radCuenta->pago = ['valor_pago' => 0];
            return view('administrativo.radcuentas.paso3', compact('vigencia_id','radCuenta'));
        } elseif($paso == 4) {
            $vigencia_id = $radCuenta->vigencia_id;
            return view('administrativo.radcuentas.paso4', compact('vigencia_id','radCuenta'));
        } else dd($radCuenta, $paso);
    }

    public function storeStep(Request $request, $step){
        $radCuenta = RadCuentas::find($request->radicacion_id);
        if ($step == 2){
            //STEP 2 - INFORMACION FINANCIERA
            //SE ACTUALIZA LA RADICACION
            $radCuenta->valor_ini = $radCuenta->registro->saldo;
            $radCuenta->valor_fin = $request->valor_fin_cont;
            $radCuenta->num_pagos = $request->num_pagos;
            $radCuenta->valor_mensual = $request->val_pago_men;
            $radCuenta->ing_retencion = $request->ing_base;
            $radCuenta->anticipo = $request->anticipo;
            $radCuenta->fecha_anticipo = $request->fecha_anticipo;
            $radCuenta->amortizacion = $request->amortizacion;
            $radCuenta->fecha_amort = $request->fecha_amorth;
            $radCuenta->save();

            //SI LLEVA ADICION DE RP LA RADICACIÓN
            if ($request->adicion_rp_id != 0){
                $registroAdd = Registro::find($request->adicion_rp_id);

                $radCuentaAdd = new RadCuentasAdd();
                $radCuentaAdd->valor = $registroAdd->saldo;
                $radCuentaAdd->registro_id  = $registroAdd->id;
                $radCuentaAdd->user_id = Auth::user()->id;
                $radCuentaAdd->rad_cuenta_id  = $radCuenta->id;
                $radCuentaAdd->save();
            }

            //SE ACTUALIZAN LAS ORDENES DE PAGO DEL RP CON LOS DATOS SUMINISTRADOS
            for($i = 0; $i < count($request->op_id); $i++){
                $ordenPago = OrdenPagos::find($request->op_id[$i]);
                $ordenPago->rad_cuenta_id  = $radCuenta->id;
                $ordenPago->periodo_pago  = $request->periodoPago[$i];
                $ordenPago->factura  = $request->factura[$i];
                $ordenPago->planilla  = $request->planilla[$i];
                $ordenPago->save();
            }

            //FALTA REGISTRAR LAS OP AGREGADAS

            return redirect('administrativo/radCuentas/'.$radCuenta->id.'/3');

        } elseif($step == 3) {

            if (!$request->netoPago){
                $radPago = new RadCuentasPago();
                $radPago->num_trabajadores = $request->num_trabajadores;
                $radPago->num_planilla = $request->num_planilla;
                $radPago->num_contratos = $request->num_contratos;
                $radPago->periodo_salud = $request->periodo_salud;
                $radPago->valor_salud = $request->valor_salud;
                $radPago->periodo_pension = $request->periodo_pension;
                $radPago->valor_pension = $request->valor_pension;
                $radPago->arl = $request->arl;
                $radPago->valor_arl = $request->valor_arl;
                $radPago->caja = $request->caja;
                $radPago->valor_caja = $request->valor_caja;
                $radPago->valor_pago = $request->valor_pago;
                $radPago->user_id = Auth::user()->id;
                $radPago->rad_cuenta_id  = $radCuenta->id;
                $radPago->save();

                return redirect('administrativo/radCuentas/'.$radCuenta->id.'/3');

            } else {
                //SE ACTUALIZA EL PAGO DE LA RADICACION CON LOS NUEVOS VALORES
                $radCuenta->pago->reteDIAN = $request->reteDIAN;
                $radCuenta->pago->reteDIANValue = $request->reteDIANValue;
                $radCuenta->pago->adulto = $request->adulto;
                $radCuenta->pago->adultoValue = $request->adultoValue;
                $radCuenta->pago->sobretasa = $request->sobretasa;
                $radCuenta->pago->sobretasaValue = $request->sobretasaValue;
                $radCuenta->pago->estampilla = $request->estampilla;
                $radCuenta->pago->estampillaValue = $request->estampillaValue;
                $radCuenta->pago->ica = $request->ica;
                $radCuenta->pago->icaValue = $request->icaValue;
                $radCuenta->pago->obraPub = $request->obraPub;
                $radCuenta->pago->obraPubValue = $request->obra_pubValue;
                $radCuenta->pago->totalDesc = $request->totalDesc;
                $radCuenta->pago->netoPago = $request->netoPago;
                $radCuenta->pago->save();

                if (isset($request->embargo)){
                    foreach ($request->embargo as $embargo){
                        $desc = new RadCuentasPagoDesc();
                        $desc->valor = $embargo;
                        $desc->type = 'EMBARGO';
                        $desc->rad_cuenta_pago_id = $radCuenta->pago->id;
                        $desc->user_id = Auth::user()->id;
                        $desc->save();
                    }
                }

                if (isset($request->libranza)){
                    foreach ($request->libranza as $libranza){
                        $desc = new RadCuentasPagoDesc();
                        $desc->valor = $libranza;
                        $desc->type = 'LIBRANZA';
                        $desc->rad_cuenta_pago_id = $radCuenta->pago->id;
                        $desc->user_id = Auth::user()->id;
                        $desc->save();
                    }
                }

                return redirect('administrativo/radCuentas/'.$radCuenta->id.'/4');
            }
        } elseif($step == 4) {

            if ($request->cdp){
                $file = new ResourceTraits;
                $resource = $file->resource($request->cdp, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CDP';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->cdpObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->crp){
                $file = new ResourceTraits;
                $resource = $file->resource($request->crp, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CRP';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->crpObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->CONTRATO){
                $file = new ResourceTraits;
                $resource = $file->resource($request->CONTRATO, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CONTRATO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->CONTRATOObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->planInv){
                $file = new ResourceTraits;
                $resource = $file->resource($request->planInv, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'PLAN DE INVERSIÓN';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->planInvObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->actaIni){
                $file = new ResourceTraits;
                $resource = $file->resource($request->actaIni, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ACTA DE INICIO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->actaIniObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->POLIZA){
                $file = new ResourceTraits;
                $resource = $file->resource($request->POLIZA, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'POLIZA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->POLIZAObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->aproPol){
                $file = new ResourceTraits;
                $resource = $file->resource($request->aproPol, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'APROBACION DE LA POLIZA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->aproPolObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->cedula){
                $file = new ResourceTraits;
                $resource = $file->resource($request->cedula, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CEDULA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->cedulaObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->oficioDian){
                $file = new ResourceTraits;
                $resource = $file->resource($request->oficioDian, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'OFICIO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->oficioDianObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->infEjec){
                $file = new ResourceTraits;
                $resource = $file->resource($request->infEjec, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'INFORME';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->infEjecObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->certCump){
                $file = new ResourceTraits;
                $resource = $file->resource($request->certCump, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CERTIFICADO CUMPLIMIENTO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->certCumpObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->actRec){
                $file = new ResourceTraits;
                $resource = $file->resource($request->actRec, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ACTA RECIBO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->actRecObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->actTerm){
                $file = new ResourceTraits;
                $resource = $file->resource($request->actTerm, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ACTA TERMINACION';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->actTermObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->actLiquid){
                $file = new ResourceTraits;
                $resource = $file->resource($request->actLiquid, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ACTA LIQUIDACION';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->actLiquidObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->actAutInt){
                $file = new ResourceTraits;
                $resource = $file->resource($request->actAutInt, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ACTA INTERVENTOR';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->actAutIntObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->segSocParaf){
                $file = new ResourceTraits;
                $resource = $file->resource($request->segSocParaf, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'SEGURIDAD SOCIAL';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->segSocParafObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->cuentaCobroFact){
                $file = new ResourceTraits;
                $resource = $file->resource($request->cuentaCobroFact, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CUENTA COBRO';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->cuentaCobroFactObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->certBanc){
                $file = new ResourceTraits;
                $resource = $file->resource($request->certBanc, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CERTIFICACION BANCARIA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->certBancObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->entradaAlmac){
                $file = new ResourceTraits;
                $resource = $file->resource($request->entradaAlmac, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'ENTRADA ALMACEN';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->entradaAlmacObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->RUT){
                $file = new ResourceTraits;
                $resource = $file->resource($request->RUT, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'RUT';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->RUTObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->pazySalvOff){
                $file = new ResourceTraits;
                $resource = $file->resource($request->pazySalvOff, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'PAZ Y SALVO OFICINA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->pazySalvOffObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->pagoSena){
                $file = new ResourceTraits;
                $resource = $file->resource($request->pagoSena, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'PAGO SENA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->pagoSenaObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->fotografias){
                $file = new ResourceTraits;
                $resource = $file->resource($request->fotografias, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'FOTOGRAFIAS';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->fotografiasObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->controlAssist){
                $file = new ResourceTraits;
                $resource = $file->resource($request->controlAssist, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'CONTROL ASISTENCA';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->controlAssistObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }
            if ($request->inscripRegTribut){
                $file = new ResourceTraits;
                $resource = $file->resource($request->inscripRegTribut, 'public/AnexosRadCuentas');
                $anexo = new RadCuentasAnex();
                $anexo->anexo = 'INCRIPCION REGIMEN';
                $anexo->resource_id = $resource;
                $anexo->observacion = $request->inscripRegTributObs;
                $anexo->estado = '0';
                $anexo->user_id = Auth::user()->id;
                $anexo->rad_cuenta_id  = $radCuenta->id;
                $anexo->save();
            }

            $radCuenta->estado_elabor = '1';
            $radCuenta->ff_fin_elaborador = Carbon::today();
            $radCuenta->estado_rev = '0';
            $radCuenta->save();

            Session::flash('success','La radicación de cuenta ha sido finalizada y enviada exitosamente.');
            return redirect('/administrativo/radCuentas/'.$radCuenta->vigencia_id);

        } else dd($request, $step);
    }

}
