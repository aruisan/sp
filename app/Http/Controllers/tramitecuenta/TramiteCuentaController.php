<?php

namespace App\Http\Controllers\tramitecuenta;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TramiteCuenta;
use App\TramiteCuentaLog;
use App\RequisitoChequeo;
use App\ChequeoCuenta;
use App\AprobadorUser;
use App\AprobadorCuenta;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Correspondencia;
use App\Email;
use App\Model\Persona;

class TramiteCuentaController extends Controller
{
    public function index(){
        //{{Carbon\Carbon::now()->format("H:i")}} el dia {{Carbon\Carbon::now()->isoFormat("LL")}}
        $tramiteCuentas = TramiteCuenta::all();
        return view('tramiteCuentas.index', compact('tramiteCuentas'));
    }

    public function create(){
        $personas = Persona::get();
        return view('tramiteCuentas.create', compact('personas'));
    }

    public function store(Request $request){
         
        $this->validate($request, $this->arrayValidar($request->tipo_contrato));
        
        $create = new TramiteCuenta;
        $create->n_contrato = $request->n_contrato;
        $create->n_pago = $request->n_pago;
        $create->v_contrato = $request->v_contrato;
        $create->v_pago = $request->v_pago;
        $create->tipo_contrato = $request->tipo_contrato;
        $create->otro_tipo_contrato = $request->otro_tipo_contrato;
        $create->tipo_pago = $request->tipo_pago;
        $create->beneficiario_id = $request->beneficiario_id;
        $create->remitente_id = auth()->user()->id;
        $create->save();

        $this->chequearStore($create->id);
        $this->aprobadoresCuenta($create);

        return redirect()->route('tramites-cuentas.index')
                        ->with('success', "el tramite para la cuenta fue creado satisfactoriamente.");
    }

    public function edit($id){
        return view('tramiteCuentas.edit', ['item' => TramiteCuenta::find($id)]);
    }

    public function update(Request $request, $id){
        $this->validate($request, $this->arrayValidar($request->tipo_contrato));
        
        $update = TramiteCuenta::find($id);
        $create->n_contrato = $request->n_contrato;
        $create->n_pago = $request->n_pago;
        $create->v_contrato = $request->v_contrato;
        $create->v_pago = $request->v_pago;
        $update->tipo_contrato = $request->tipo_contrato;
        $update->otro_tipo_contrato = $request->otro_tipo_contrato;
        $update->tipo_pago = $request->tipo_pago;
        $update->beneficiario_id = $request->beneficiario_id;
        $update->remitente_id = Auth::user()->id;
        $update->save();

        return redirect()->route('tramites-cuentas.index')
                        ->with('success', "el tramite para la cuenta fue actualizado satisfactoriamente.");
    }

    public function chequearStore($id){
        $requisitos = RequisitoChequeo::all();
        foreach ($requisitos as  $value) {
            $create = new ChequeoCuenta;
            $create->tramite_cuenta_id = $id;
            $create->requisito_chequeo_id = $value->id;
            $create->save();
        }
    }

    public function chequearUpdate(Request $request){

        foreach ($request->id as $index => $id) {
            $update = ChequeoCuenta::find($id);
            $update->devolucion = $request->devolucion[$index];
            $update->observacion = $request->observacion[$index];
            $update->save();
        }

        foreach ($request->estado as $id) {
            $update = ChequeoCuenta::find($id);
            $update->estado = 'si';
            $update->save();
        }

        return redirect()->route('tramites-cuentas.index')
                        ->with('success', "Se ha actualizado el chequeo de tramites de cuentas satisfactoriamente.");
    }

    public function chequear($id){
        $tramiteCuenta = TramiteCuenta::find($id);
        return view('tramiteCuentas.chequear', compact('tramiteCuenta'));
    }

    public function validarEstadoChequeo($estados, $id){
        $clave = array_search($id, $estados);
        if(!$clave){
            return null;
        }else{
            return 'si';
        }
    }

    public function aprobadoresCuenta($cuenta){
        /*
        if($cuenta->tipo_contrato == 'Viaticos' || $cuenta->tipo_contrato == 'Nomina' || $cuenta->tipo_contrato == 'Pago de Servicios'){
            $aprobadores = AprobadorUser::where('grupo_aprobador_id', 1)->get();
        }else{
            $aprobadores = AprobadorUser::where('grupo_aprobador_id', 2)->get();
        }
        */
        $aprobadores = AprobadorUser::where('grupo_aprobador_id', 1)->get();
        foreach ($aprobadores as $value) {
            $create = new AprobadorCuenta;
            $create->aprobado_user_id = $value->id;
            $create->tramite_cuenta_id = $cuenta->id;
            $create->save();
        }
    }

    public function pdf($id){
        $tc = TramiteCuenta::find($id);
        $pdf = PDF::loadView('tramiteCuentas.pdf', compact('tc'))->setOptions(['images' => true,'isRemoteEnabled' => true])->save('tramites_cuentas_No_'.$tc->id.'.pdf');
            return $pdf->stream();
    }

    public function arrayValidar($tipo_contrato){
        if($tipo_contrato == 'Otros'){
            return [
                'beneficiario_id' => 'required',
                'n_contrato' => 'required',
                'otro_tipo_contrato' => 'required'
            ];
        }else{
            return [
                'beneficiario_id' => 'required',
                'n_contrato' => 'required'
            ];
        }
    }

    public function logs($id){
        return view('tramiteCuentas.logs', ['item' => TramiteCuenta::find($id)]);
    }

    public function aprobar($id){
        $update = AprobadorCuenta::find($id);
        $update->estado = 'Aprobado';
        $update->save();
        $fecha = Carbon::now('America/Bogota')->format('Y-m-d');
        $fecha = Carbon::now('America/Bogota')->addDay(10)->format('Y-m-d');
        /*
        $email = Email::create([
            'receptor_id' => $update->tramiteCuenta->remitente_id,
            'emisor_id' => Auth::user()->id,
            'signatario_id' => $update->tramiteCuenta->beneficiario_id,
            'referencia' => 'Aprobacion de el tramite con numero de contrato '.$update->tramiteCuenta->n_contrato,
            'objeto' => 'El tramite fue Aprobado por el '.Auth::user()->aprobadorUser->nombre,
            'observacion' => '',
            'fecha_entrega' => Carbon::now('America/Bogota')->format('Y-m-d'),
            'fecha_vencimiento' => Carbon::now('America/Bogota')->addDay(10)->format('Y-m-d')
        ]);
        $this->createLog($update->tramite_cuenta_id, $update->aprobadorUser->nombre, 'Aprobado');
        $update->tramiteCuenta->remitente->notify(new Correspondencia($email, 'email'));
        */
        return back();
    }

    public function aplazar(Request $request){
        $update = AprobadorCuenta::find($request->id);
        $update->estado = 'Aplazado';
        $update->save();
/*
        $email = Email::create([
            'receptor_id' => $update->tramiteCuenta->remitente_id,
            'emisor_id' => Auth::user()->id,
            'signatario_id' => $update->tramiteCuenta->beneficiario_id,
            'referencia' => 'Se Aplazo el tramite con numero de contrato '.$update->tramiteCuenta->n_contrato,
            'objeto' => $request->observacion,
            'observacion' => '',
            'fecha_entrega' => Carbon::now('America/Bogota')->format('Y-m-d'),
            'fecha_vencimiento' => Carbon::now('America/Bogota')->addDay(10)->format('Y-m-d')
        ]);

        $this->createLog($update->tramite_cuenta_id, $update->aprobadorUser->nombre, 'Aplazado', $request->observacion);
        $update->tramiteCuenta->remitente->notify(new Correspondencia($email, 'email'));
        */
        return back();
    }

    public function devolver(Request $request){

        $update = AprobadorCuenta::find($request->id);
        $update->estado = null;
        $update->recibido = null;
        $update->save();

        $array_anterior = AprobadorCuenta::where('id', '<', $update->id)->orderBy('id', 'asc')->get();
        $last = $array_anterior->last();
        $last->estado = null;
        $last->recibido = null;
        $last->save();
/*
        $email = Email::create([
            'receptor_id' => $update->tramiteCuenta->remitente_id,
            'emisor_id' => Auth::user()->id,
            'signatario_id' => $update->tramiteCuenta->beneficiario_id,
            'referencia' => 'Se devolvio el tramite con numero de contrato '.$update->tramiteCuenta->n_contrato,
            'objeto' => $request->observacion,
            'observacion' => '',
            'fecha_entrega' => Carbon::now('America/Bogota')->format('Y-m-d'),
            'fecha_vencimiento' => Carbon::now('America/Bogota')->addDay(10)->format('Y-m-d')
        ]);

        $this->createLog($update->tramite_cuenta_id, $update->aprobadorUser->nombre, 'Devuelto', $request->observacion);
        $update->tramiteCuenta->remitente->notify(new Correspondencia($email, 'email'));
        */
        return back();
    }


    public function updateRecibido($id){
        $update = AprobadorCuenta::find($id);
        $update->recibido = Carbon::now();
        $update->save();
        $this->createLog($update->tramite_cuenta_id, $update->aprobadorUser->nombre, 'Recibido');
        return back();
    }


    public function createLog($tramite_id, $rol, $accion, $observacion=Null){
        $create = new TramiteCuentaLog;
        $create->tramite_cuenta_id = $tramite_id;
        $create->accion = $accion;
        $create->rol = $rol;
        $create->observacion = $observacion;
        $create->fecha = Carbon::now('America/Bogota');
        $create->save();
    }
}
