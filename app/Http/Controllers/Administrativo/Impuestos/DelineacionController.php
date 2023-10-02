<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Impuestos\Delineacion;
use App\Model\Administrativo\Impuestos\DelineacionTitulares;
use App\Model\Administrativo\Impuestos\DelineacionVecinos;
use App\Model\Impuestos\Pagos;
use App\Traits\ResourceTraits;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;

class DelineacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delineaciones = Delineacion::orderBy('fecha','DESC')->get();
        if ($delineaciones){
            foreach ($delineaciones as $delineacion){
                $pago = Pagos::where('entity_id', $delineacion->id)->where('modulo','DELINEACIÓN')->first();
                if ($pago->estado == "Generado") $delineacionPend[] = $delineacion;
                else {
                    $delineacion->fechaPago = $pago->fechaPago;
                    $delineacion->rutaFile = $pago->Resource->ruta;
                    $delineacionPay[] = $delineacion;
                }
            }
        }
        if (!isset($delineacionPend)) $delineacionPend = [];
        if (!isset($delineacionPay)) $delineacionPay = [];
        $bancos = PucAlcaldia::where('padre_id', 8)->get();
        return view('administrativo.impuestos.delineacion.index', compact('delineacionPend','delineacionPay','bancos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $responsable = Auth::user()->name.' - '.Auth::user()->email;
        $tramite = "INICIAL";
        return view('administrativo.impuestos.delineacion.create',compact('responsable','tramite'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->tramite == "INICIAL"){

            $delineacion = new Delineacion();
            $delineacion->tramite = $request->tramite;
            $delineacion->tipoTramite = $request->tipoTramite;
            $delineacion->cualOtraActuacion = $request->cualOtraActuacion;
            $delineacion->modalidadLicenciaUrbanizacion = $request->modalidadLicenciaUrbanizacion;
            $delineacion->modalidadLicenciaSubdivision = $request->modalidadLicenciaSubdivision;
            $delineacion->modalidadLicenciaConstruccion = $request->modalidadLicenciaConstruccion;
            $delineacion->usos = $request->usos;
            $delineacion->cualOtroUso = $request->cualOtroUso;
            $delineacion->area = $request->area;
            $delineacion->tipoVivienda = $request->tipoVivienda;
            $delineacion->interesCultural = $request->interesCultural;
            $delineacion->dirActual = $request->dirActual;
            $delineacion->dirAnterior = $request->dirAnterior;
            $delineacion->matricula = $request->matricula;
            $delineacion->idCatastral = $request->idCatastral;
            $delineacion->clasificacionSuelo = $request->clasificacionSuelo;
            $delineacion->planimetria = $request->planimetria;
            $delineacion->cualotraPlanimetria = $request->cualotraPlanimetria;
            $delineacion->barrio = $request->barrio;
            $delineacion->vereda = $request->vereda;
            $delineacion->comuna = $request->comuna;
            $delineacion->sector = $request->sector;
            $delineacion->estrato = $request->estrato;
            $delineacion->corregimiento = $request->corregimiento;
            $delineacion->manzana = $request->manzana;
            $delineacion->lote = $request->lote;
            $delineacion->longNorte1 = $request->longNorte1;
            $delineacion->colindNorte1 = $request->colindNorte1;
            $delineacion->linderosNorte2 = $request->linderosNorte2;
            $delineacion->longNorte2 = $request->longNorte2;
            $delineacion->colindNorte2 = $request->colindNorte2;
            $delineacion->linderosNorte3 = $request->linderosNorte3;
            $delineacion->longNorte3 = $request->longNorte3;
            $delineacion->colindNorte3 = $request->colindNorte3;
            $delineacion->linderosNorte4 = $request->linderosNorte4;
            $delineacion->longNorte4 = $request->longNorte4;
            $delineacion->colindNorte4 = $request->colindNorte4;
            $delineacion->longSur1 = $request->longSur1;
            $delineacion->colindSur1 = $request->colindSur1;
            $delineacion->linderosSur2 = $request->linderosSur2;
            $delineacion->longSur2 = $request->longSur2;
            $delineacion->colindSur2 = $request->colindSur2;
            $delineacion->linderosSur3 = $request->linderosSur3;
            $delineacion->longSur3 = $request->longSur3;
            $delineacion->colindSur3 = $request->colindSur3;
            $delineacion->linderosSur4 = $request->linderosSur4;
            $delineacion->longSur4 = $request->longSur4;
            $delineacion->colindSur4 = $request->colindSur4;
            $delineacion->longOriente1 = $request->longOriente1;
            $delineacion->colindOriente1 = $request->colindOriente1;
            $delineacion->linderosOriente2 = $request->linderosOriente2;
            $delineacion->longOriente2 = $request->longOriente2;
            $delineacion->colindOriente2 = $request->colindOriente2;
            $delineacion->linderosOriente3 = $request->linderosOriente3;
            $delineacion->longOriente3 = $request->longOriente3;
            $delineacion->colindOriente3 = $request->colindOriente3;
            $delineacion->linderosOriente4 = $request->linderosOriente4;
            $delineacion->longOriente4 = $request->longOriente4;
            $delineacion->colindOriente4 = $request->colindOriente4;
            $delineacion->longOccidente1 = $request->longOccidente1;
            $delineacion->colindOccidente1 = $request->colindOccidente1;
            $delineacion->linderosOccidente2 = $request->linderosOccidente2;
            $delineacion->longOccidente2 = $request->longOccidente2;
            $delineacion->colindOccidente2 = $request->colindOccidente2;
            $delineacion->linderosOccidente3 = $request->linderosOccidente3;
            $delineacion->longOccidente3 = $request->longOccidente3;
            $delineacion->colindOccidente3 = $request->colindOccidente3;
            $delineacion->linderosOccidente4 = $request->linderosOccidente4;
            $delineacion->longOccidente4 = $request->longOccidente4;
            $delineacion->colindOccidente4 = $request->colindOccidente4;
            $delineacion->areaTotalPredios = $request->areaTotalPredios;
            $delineacion->notificar = $request->notificar;
            $delineacion->nameUrbanizador = $request->nameUrbanizador;
            $delineacion->ccUrbanizador = $request->ccUrbanizador;
            $delineacion->numMatUrbanizador = $request->numMatUrbanizador;
            $delineacion->fechaExpMatUrbanizador = $request->fechaExpMatUrbanizador;
            $delineacion->emailUrbanizador = $request->emailUrbanizador;
            $delineacion->telUrbanizador = $request->telUrbanizador;
            $delineacion->nameDir = $request->nameDir;
            $delineacion->ccDir = $request->ccDir;
            $delineacion->numMatDir = $request->numMatDir;
            $delineacion->fechaExpMatDir = $request->fechaExpMatDir;
            $delineacion->emailDir = $request->emailDir;
            $delineacion->telDir = $request->telDir;
            $delineacion->nameArq = $request->nameArq;
            $delineacion->ccArq = $request->ccArq;
            $delineacion->numMatArq = $request->numMatArq;
            $delineacion->fechaExpMatArq = $request->fechaExpMatArq;
            $delineacion->emailArq = $request->emailArq;
            $delineacion->telArq = $request->telArq;
            $delineacion->nameIngCivilDis = $request->nameIngCivilDis;
            $delineacion->ccIngCivilDis = $request->ccIngCivilDis;
            $delineacion->numMatIngCivilDis = $request->numMatIngCivilDis;
            $delineacion->fechaExpMatIngCivilDis = $request->fechaExpMatIngCivilDis;
            $delineacion->emailIngCivilDis = $request->emailIngCivilDis;
            $delineacion->telIngCivilDis = $request->telIngCivilDis;
            $delineacion->supervTecnicaIngCivilDis = $request->supervTecnicaIngCivilDis;
            $delineacion->nameDiseñadorElem = $request->nameDiseñadorElem;
            $delineacion->ccDiseñadorElem = $request->ccDiseñadorElem;
            $delineacion->numMatDiseñadorElem = $request->numMatDiseñadorElem;
            $delineacion->fechaExpMatDiseñadorElem = $request->fechaExpMatDiseñadorElem;
            $delineacion->emailDiseñadorElem = $request->emailDiseñadorElem;
            $delineacion->telDiseñadorElem = $request->telDiseñadorElem;
            $delineacion->nameIngCivilGeo = $request->nameIngCivilGeo;
            $delineacion->ccIngCivilGeo = $request->ccIngCivilGeo;
            $delineacion->numMatIngCivilGeo = $request->numMatIngCivilGeo;
            $delineacion->fechaExpMatIngCivilGeo = $request->fechaExpMatIngCivilGeo;
            $delineacion->emailIngCivilGeo = $request->emailIngCivilGeo;
            $delineacion->telIngCivilGeo = $request->telIngCivilGeo;
            $delineacion->supervTecnicaIngCivilGeo = $request->supervTecnicaIngCivilGeo;
            $delineacion->nameTopografo = $request->nameTopografo;
            $delineacion->ccTopografo = $request->ccTopografo;
            $delineacion->numMatTopografo = $request->numMatTopografo;
            $delineacion->fechaExpMatTopografo = $request->fechaExpMatTopografo;
            $delineacion->emailTopografo = $request->emailTopografo;
            $delineacion->telTopografo = $request->telTopografo;
            $delineacion->nameRevisor = $request->nameRevisor;
            $delineacion->ccRevisor = $request->ccRevisor;
            $delineacion->numMatRevisor = $request->numMatRevisor;
            $delineacion->fechaExpMatRevisor = $request->fechaExpMatRevisor;
            $delineacion->emailRevisor = $request->emailRevisor;
            $delineacion->telRevisor = $request->telRevisor;
            $delineacion->nameOtroProf1 = $request->nameOtroProf1;
            $delineacion->ccOtroProf1 = $request->ccOtroProf1;
            $delineacion->numMatOtroProf1 = $request->numMatOtroProf1;
            $delineacion->fechaExpMatOtroProf1 = $request->fechaExpMatOtroProf1;
            $delineacion->emailOtroProf1 = $request->emailOtroProf1;
            $delineacion->telOtroProf1 = $request->telOtroProf1;
            $delineacion->nameOtroProf2 = $request->nameOtroProf2;
            $delineacion->ccOtroProf2 = $request->ccOtroProf2;
            $delineacion->numMatOtroProf2 = $request->numMatOtroProf2;
            $delineacion->fechaExpMatOtroProf2 = $request->fechaExpMatOtroProf2;
            $delineacion->emailOtroProf2 = $request->emailOtroProf2;
            $delineacion->telOtroProf2 = $request->telOtroProf2;
            $delineacion->nameResponsable = $request->nameResponsable;
            $delineacion->ccResponsable = $request->ccResponsable;
            $delineacion->dirResponsable = $request->dirResponsable;
            $delineacion->emailResponsable = $request->emailResponsable;
            $delineacion->telResponsable = $request->telResponsable;
            $delineacion->notificarResponsable = $request->notificarResponsable;
            $delineacion->valorPago = $request->valorPago;
            ///
            if ($request->modalidadLicenciaConstruccion == "OBRA NUEVA"){
                $delineacion->tipoUsos = $request->tipoUsos;
                $delineacion->cualotroTipoUso = $request->cualotroTipoUso;
                $delineacion->medidasPasivas = $request->medidasPasivas;
                $delineacion->cualotraMedidaPasiva = $request->cualotraMedidaPasiva;
                $delineacion->medidasActivas = $request->medidasActivas;
                $delineacion->cualotraMedidaActiva = $request->cualotraMedidaActiva;
                $delineacion->materialidadMuroExt = $request->materialidadMuroExt;
                $delineacion->cualotroMuroExt = $request->cualotroMuroExt;
                $delineacion->materialidadMuroInt = $request->materialidadMuroInt;
                $delineacion->cualotroMuroInt = $request->cualotroMuroInt;
                $delineacion->materialidadCubierta = $request->materialidadCubierta;
                $delineacion->cualotroCub = $request->cualotroCub;
                $delineacion->relacionNorte = $request->relacionNorte;
                $delineacion->relacionSur = $request->relacionSur;
                $delineacion->relacionOriente = $request->relacionOriente;
                $delineacion->relacionOccidente = $request->relacionOccidente;
                $delineacion->declaracionMedidasAhorroAgua = $request->declaracionMedidasAhorroAgua;
                $delineacion->cualotroAhorroAgua = $request->cualotroAhorroAgua;
                $delineacion->zonificacionClimatica = $request->zonificacionClimatica;
                $delineacion->zonaClimatica = $request->zonaClimatica;
                $delineacion->cualotroZonificacionClimatica = $request->cualotroZonificacionClimatica;
                $delineacion->ahorroEsperadoAgua = $request->ahorroEsperadoAgua;
                $delineacion->ahorroEsperadoEnergia = $request->ahorroEsperadoEnergia;
                $delineacion->urbanismoPaisajismo = $request->urbanismoPaisajismo;
                $delineacion->zonasComunes = $request->zonasComunes;
                $delineacion->parqueaderos = $request->parqueaderos;
            }

            $delineacion->fecha = Carbon::today();
            $delineacion->funcionario_id = Auth::user()->id;

            $delineacion->save();

            $delineacionNumRef = Delineacion::find($delineacion->id);
            if (strlen($delineacionNumRef->id) < 5){
                $newValue = $delineacionNumRef->id;
                for ($i = 0; $i < 6 - strlen($delineacionNumRef->id); $i++) {
                    $newValue =  '0'.$newValue;
                }
            } else $newValue = $delineacionNumRef->id;
            $delineacionNumRef->numRegistroIngreso = Carbon::today()->format('Ymd').$newValue;
            $delineacionNumRef->save();

            //ALMACENAR LOS VECINOS
            if (count($request->dirPredVecino) > 0) {
                for ($i = 0; $i <= count($request->dirPredVecino) - 1; $i++) {
                    $vecinos = new DelineacionVecinos();
                    $vecinos->dirPredVecino = $request->dirPredVecino[$i];
                    $vecinos->dirCorrespVecino = $request->dirCorrespVecino[$i];
                    $vecinos->delineacion_id = $delineacion->id;
                    $vecinos->save();
                }
            }

            //ALMACENAR LOS TITULARES
            if (count($request->nameTit) > 0) {
                for ($i = 0; $i <= count($request->nameTit) - 1; $i++) {
                    $titulares = new DelineacionTitulares();
                    $titulares->nameTit = $request->nameTit[$i];
                    $titulares->ccTit = $request->ccTit[$i];
                    $titulares->telTit = $request->telTit[$i];
                    $titulares->emailTit = $request->emailTit[$i];
                    $titulares->delineacion_id = $delineacion->id;
                    $titulares->save();
                }
            }

            $pago = new Pagos();
            $pago->modulo = "DELINEACIÓN";
            $pago->entity_id = $delineacion->id;
            $pago->estado = "Generado";
            $pago->valor = $delineacion->valorPago;
            $pago->fechaCreacion = $delineacion->fecha;
            $pago->user_id = $delineacion->funcionario_id;
            $pago->save();

            Session::flash('success','Formato registrado exitosamente.');
            return redirect('/administrativo/impuestos/delineacion');
        } else{
            dd($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $delinacion = Delineacion::find($id);
        $pago = Pagos::where('entity_id',$id)->where('modulo','DELINEACIÓN')->first();
        if ($pago->user_pago_id) $pago->user_pago = User::find($pago->user_pago_id);
        $responsable = User::find($delinacion->funcionario_id);
        $vecinos = $delinacion->vecinos;
        $titulares = $delinacion->titulares;
        $tramite = 'REVALIDACIÓN';
        return view('administrativo.impuestos.delineacion.show',compact('responsable', 'delinacion','vecinos','titulares','tramite'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $delinacion = Delineacion::find($id);
        $user = User::find($delinacion->funcionario_id); Auth::user()->name.' - '.Auth::user()->email;
        $responsable = $user->name.' - '.$user->email;
        $tramite = "MODIFICACIÓN";
        $vecinos = $delinacion->vecinos;
        $titulares = $delinacion->titulares;
        return view('administrativo.impuestos.delineacion.create',compact('responsable','tramite', 'delinacion','vecinos','titulares'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function pay(Request $request)
    {
        if (!$request->hasFile('constanciaPago')){
            Session::flash('warning', 'Hay algun error en el archivo, intente de nuevo por favor.');
            return redirect('/administrativo/impuestos/delineacion');
        } else {

            $file = new ResourceTraits;
            $resource = $file->resource($request->constanciaPago, 'public/Impuestos/PagosDelineacion');

            $pago = Pagos::where('entity_id',$request->regId)->where('modulo','DELINEACIÓN')->first();
            $pago->estado = "Pagado";
            $pago->fechaPago = Carbon::today();
            $pago->resource_id = $resource;
            $pago->user_pago_id = Auth::user()->id;
            $pago->puc_alcaldia_id = $request->cuenta;
            $pago->save();

            Session::flash('success', 'Pago aplicado exitosamente.');
            return redirect('/administrativo/impuestos/delineacion');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteVecino($id)
    {
        $vecino = DelineacionVecinos::find($id);
        $vecino->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteTitular($id)
    {
        $titular = DelineacionTitulares::find($id);
        $titular->delete();
    }
}
