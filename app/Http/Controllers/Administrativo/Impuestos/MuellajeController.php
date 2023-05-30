<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Impuestos\Muellaje;
use App\Model\Administrativo\Impuestos\MuellajeVehiculos;
use App\Model\Impuestos\Pagos;
use App\Model\User;
use App\Traits\ResourceTraits;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;

class MuellajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $atraques = Muellaje::orderBy('fecha','DESC')->get();
        if ($atraques){
            foreach ($atraques as $atraque){
                $pago = Pagos::where('entity_id', $atraque->id)->where('modulo','MUELLAJE')->first();
                if ($pago->estado == "Generado") $atraquesPend[] = $atraque;
                else {
                    $atraque->fechaPago = $pago->fechaPago;
                    $atraque->rutaFile = $pago->Resource->ruta;
                    $atraquesPay[] = $atraque;
                }
            }
        }
        if (!isset($atraquesPend)) $atraquesPend = [];
        if (!isset($atraquesPay)) $atraquesPay = [];
        $bancos = PucAlcaldia::where('padre_id', 8)->get();

        return view('administrativo.impuestos.muellaje.index', compact('atraquesPend','atraquesPay', 'bancos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $atraques = Muellaje::all()->unique('name');
        $responsable = Auth::user()->name.' - '.Auth::user()->email;
        return view('administrativo.impuestos.muellaje.create', compact('responsable','atraques'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $muellaje = new Muellaje();
        $muellaje->fecha = Carbon::today();
        $muellaje->name = $request->name;
        $muellaje->bandera = $request->bandera;
        $muellaje->tipo = $request->tipo;
        $muellaje->piesEslora = $request->piesEslora;
        $muellaje->tipoCarga = $request->tipoCarga;
        $muellaje->tonelajeCarga = $request->tonelajeCarga;
        $muellaje->tripulantes = $request->tripulantes;
        $muellaje->pasajeros = $request->pasajeros;
        $muellaje->sustanciasPeligrosas = $request->sustanciasPeligrosas;
        $muellaje->fechaPermiso = $request->fechaPermiso;
        $muellaje->titularPermiso = $request->titularPermiso;
        $muellaje->numIdent = $request->numIdent;
        $muellaje->nameCap = $request->nameCap;
        $muellaje->movilCap = $request->movilCap;
        $muellaje->nameCompany = $request->nameCompany;
        $muellaje->movilCompany = $request->movilCompany;
        $muellaje->emailCap = $request->emailCap;
        $muellaje->nameNaviera = $request->nameNaviera;
        $muellaje->NITNaviera = $request->NITNaviera;
        $muellaje->nameRep = $request->nameRep;
        $muellaje->idRep = $request->idRep;
        $muellaje->dirNotificacion = $request->dirNotificacion;
        $muellaje->municipio = $request->municipio;
        $muellaje->emailNaviera = $request->emailNaviera;
        $muellaje->nameRepPago = $request->nameRepPago;
        $muellaje->fechaAtraque = $request->fechaAtraque;
        $muellaje->fechaSalida = $request->fechaSalida;
        $muellaje->tarifa = $request->tarifa;
        $muellaje->horaIngreso = $request->horaIngreso;
        $muellaje->horaSalida = $request->horaSalida;
        $muellaje->valorDiario = $request->valorDiario;
        $muellaje->numTotalDias = $request->numTotalDias;
        $muellaje->valorPago = $request->valorPago;
        $muellaje->observaciones = $request->observaciones;
        $muellaje->funcionario_id = Auth::user()->id;
        $muellaje->save();

        for ($i = 0; $i < count($request->vehiculos); $i++) {
            if ($request->vehiculos[$i]){
                $newVehicle = new MuellajeVehiculos();
                $newVehicle->imp_id = $muellaje->id;
                $newVehicle->vehiculos = $request->vehiculos[$i];
                $newVehicle->claseVehiculo = $request->claseVehiculo[$i];
                $newVehicle->save();
            }
        }

        $muellajeNumRef = Muellaje::find($muellaje->id);
        if (strlen($muellajeNumRef->id) < 5){
            $newValue = $muellajeNumRef->id;
            for ($i = 0; $i < 6 - strlen($muellajeNumRef->id); $i++) {
                $newValue =  '0'.$newValue;
            }
        } else $newValue = $muellajeNumRef->id;
        $muellajeNumRef->numRegistroIngreso = Carbon::today()->format('Ymd').$newValue;
        $muellajeNumRef->save();

        $pago = new Pagos();
        $pago->modulo = "MUELLAJE";
        $pago->entity_id = $muellaje->id;
        $pago->estado = "Generado";
        $pago->valor = $muellaje->valorPago;
        $pago->fechaCreacion = $muellaje->fecha;
        $pago->user_id = $muellaje->funcionario_id;
        $pago->save();

        Session::flash('success', 'Registro de atraque presentado exitosamente.');

        return redirect('/administrativo/impuestos/muellaje');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $muellaje = Muellaje::find($id);
        $pago = Pagos::where('entity_id',$id)->where('modulo','MUELLAJE')->first();
        if ($pago->user_pago_id) $pago->user_pago = User::find($pago->user_pago_id);
        $responsable = User::find($muellaje->funcionario_id);

        return view('administrativo.impuestos.muellaje.show', compact('muellaje','pago','responsable'));
    }

    public function edit($id)
    {
        $muellaje = Muellaje::find($id);
        $atraques = Muellaje::all()->unique('name');
        $pago = Pagos::where('entity_id',$id)->where('modulo','MUELLAJE')->first();
        if ($pago->user_pago_id) $pago->user_pago = User::find($pago->user_pago_id);
        $responsable = User::find($muellaje->funcionario_id);

        return view('administrativo.impuestos.muellaje.edit', compact('muellaje','pago','responsable','atraques'));
    }

    /**
     * Send Atraque
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findAtraque($id, Request $request)
    {
        return Muellaje::find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        if (!$request->hasFile('constanciaPago')){
            Session::flash('warning', 'Hay algun error en el archivo, intente de nuevo por favor.');
            return redirect('/administrativo/impuestos/muellaje');
        } else {

            $file = new ResourceTraits;
            $resource = $file->resource($request->constanciaPago, 'public/Impuestos/PagosMuellaje');

            $pago = Pagos::where('entity_id',$request->regId)->where('modulo','MUELLAJE')->first();
            $pago->estado = "Pagado";
            $pago->fechaPago = Carbon::today();
            $pago->resource_id = $resource;
            $pago->user_pago_id = Auth::user()->id;
            $pago->puc_alcaldia_id = $request->cuenta;
            $pago->save();

            Session::flash('success', 'Pago aplicado exitosamente.');
            return redirect('/administrativo/impuestos/muellaje');

        }
    }


    public function update(Request $request, $id)
    {

        $muellaje = Muellaje::find($id);
        $muellaje->fecha = Carbon::today();
        $muellaje->name = $request->name;
        $muellaje->bandera = $request->bandera;
        $muellaje->tipo = $request->tipo;
        $muellaje->piesEslora = $request->piesEslora;
        $muellaje->tipoCarga = $request->tipoCarga;
        $muellaje->tonelajeCarga = $request->tonelajeCarga;
        $muellaje->tripulantes = $request->tripulantes;
        $muellaje->pasajeros = $request->pasajeros;
        $muellaje->sustanciasPeligrosas = $request->sustanciasPeligrosas;
        $muellaje->fechaPermiso = $request->fechaPermiso;
        $muellaje->titularPermiso = $request->titularPermiso;
        $muellaje->numIdent = $request->numIdent;
        $muellaje->nameCap = $request->nameCap;
        $muellaje->movilCap = $request->movilCap;
        $muellaje->nameCompany = $request->nameCompany;
        $muellaje->movilCompany = $request->movilCompany;
        $muellaje->emailCap = $request->emailCap;
        $muellaje->nameNaviera = $request->nameNaviera;
        $muellaje->NITNaviera = $request->NITNaviera;
        $muellaje->nameRep = $request->nameRep;
        $muellaje->idRep = $request->idRep;
        $muellaje->dirNotificacion = $request->dirNotificacion;
        $muellaje->municipio = $request->municipio;
        $muellaje->emailNaviera = $request->emailNaviera;
        $muellaje->nameRepPago = $request->nameRepPago;
        $muellaje->fechaAtraque = $request->fechaAtraque;
        $muellaje->fechaSalida = $request->fechaSalida;
        $muellaje->tarifa = $request->tarifa;
        $muellaje->horaIngreso = $request->horaIngreso;
        $muellaje->horaSalida = $request->horaSalida;
        $muellaje->valorDiario = $request->valorDiario;
        $muellaje->numTotalDias = $request->numTotalDias;
        $muellaje->valorPago = $request->valorPago;
        $muellaje->observaciones = $request->observaciones;
        $muellaje->funcionario_id = Auth::user()->id;
        $muellaje->save();

        $vehiculosSaved = MuellajeVehiculos::where('imp_id', $muellaje->id)->get();
        foreach ($vehiculosSaved as $vehSaved){
            $vehSaved->delete();
        }

        for ($i = 0; $i < count($request->vehiculos); $i++) {
            if ($request->vehiculos[$i]){
                $newVehicle = new MuellajeVehiculos();
                $newVehicle->imp_id = $muellaje->id;
                $newVehicle->vehiculos = $request->vehiculos[$i];
                $newVehicle->claseVehiculo = $request->claseVehiculo[$i];
                $newVehicle->save();
            }
        }

        $muellajeNumRef = Muellaje::find($muellaje->id);
        if (strlen($muellajeNumRef->id) < 5){
            $newValue = $muellajeNumRef->id;
            for ($i = 0; $i < 6 - strlen($muellajeNumRef->id); $i++) {
                $newValue =  '0'.$newValue;
            }
        } else $newValue = $muellajeNumRef->id;
        $muellajeNumRef->numRegistroIngreso = Carbon::today()->format('Ymd').$newValue;
        $muellajeNumRef->save();

        $pagoSaved = Pagos::where('modulo', "MUELLAJE")->where('entity_id', $muellaje->id)->first();
        $pagoSaved->delete();

        $pago = new Pagos();
        $pago->modulo = "MUELLAJE";
        $pago->entity_id = $muellaje->id;
        $pago->estado = "Generado";
        $pago->valor = $muellaje->valorPago;
        $pago->fechaCreacion = $muellaje->fecha;
        $pago->user_id = $muellaje->funcionario_id;
        $pago->save();

        Session::flash('success', 'Registro de atraque presentado exitosamente.');

        return redirect('/administrativo/impuestos/muellaje');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteVehiculo($id)
    {
        $vehiculo = MuellajeVehiculos::find($id);
        $vehiculo->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $vehiculosSaved = MuellajeVehiculos::where('imp_id', $id)->get();
        foreach ($vehiculosSaved as $vehSaved){
            $vehSaved->delete();
        }

        $pagoSaved = Pagos::where('modulo', "MUELLAJE")->where('entity_id', $id)->first();
        $pagoSaved->delete();

        $muellaje = Muellaje::find($id);
        $muellaje->delete();
    }
}
