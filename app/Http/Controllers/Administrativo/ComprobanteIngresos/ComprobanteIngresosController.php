<?php

namespace App\Http\Controllers\Administrativo\ComprobanteIngresos;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\ComprobanteIngresos\CIRubros;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\FileTraits;
use Illuminate\Support\Facades\Auth;
use Session;
use PDF;

class ComprobanteIngresosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $vigencia = Vigencia::findOrFail($id);
        if ($vigencia->tipo == 1){
            $CIngresosT = ComprobanteIngresos::where('vigencia_id', $id)->where('estado','!=','3')->get();
            $CIngresos = ComprobanteIngresos::where('vigencia_id', $id)->where('estado','3')->get();

            return view('administrativo.comprobanteingresos.index', compact('vigencia', 'CIngresosT', 'CIngresos'));
        } else {
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vigencia = Vigencia::findOrFail($id);
        $user_id = auth()->user()->id;
        $lv1 = PucAlcaldia::where('padre_id', 2 )->get();
        foreach ($lv1 as $dato){
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) {
                $lv3 = PucAlcaldia::where('padre_id', $cuenta->id )->get();
                foreach ($lv3 as $hijo)  $hijosDebito[] = $hijo;
            }
        }
        $hijos = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();
        $rubI = Rubro::where('vigencia_id', $vigencia->id)->orderBy('cod','ASC')->get();
        $personas = Persona::all();

        foreach ($rubI as $rub){
            foreach ($rub->fontsRubro as $fuente){
                $rubrosIngresos[] = collect(['id' => $fuente->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                    $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description]);
            }
        }

        return view('administrativo.comprobanteingresos.create', compact('vigencia','user_id',
        'rubrosIngresos','hijos','personas','hijosDebito'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('file')) {
            $file = new FileTraits;
            $ruta = $file->File($request->file('file'), 'CertificadoIngresos');
        } else $ruta = "";

        $countCI = ComprobanteIngresos::where('vigencia_id', $request->vigencia_id)->orderBy('id')->get()->last();
        if ($countCI == null)  $count = 0;
        else $count = $countCI->code;

        $comprobante = new ComprobanteIngresos();
        $comprobante->code = $count + 1;
        $comprobante->concepto = $request->concepto;
        $comprobante->valor = $request->valor;
        $comprobante->iva = $request->valorIva;
        $comprobante->val_total = $request->valor + $request->valorIva;
        $comprobante->estado = '3';
        $comprobante->ff = $request->fecha;
        $comprobante->tipoCI = $request->tipoCI;
        $comprobante->cualOtroTipo = $request->cualOtroTipo;
        $comprobante->user_id = $request->user_id;
        $comprobante->vigencia_id = $request->vigencia_id;
        $comprobante->ruta = $ruta;
        $comprobante->responsable_id = Auth::user()->id;
        $comprobante->persona_id = $request->persona_id;
        $comprobante->save();

        //BANCO DEL COMPROBANTE CONTABLE
        $comprobanteMov = new ComprobanteIngresosMov();
        $comprobanteMov->comp_id = $comprobante->id;
        $comprobanteMov->fechaComp = $request->fecha;
        $comprobanteMov->cuenta_banco = $request->cuentaDeb;
        $comprobanteMov->debito = $request->debitoBanco;
        $comprobanteMov->credito = $request->creditoBanco;
        $comprobanteMov->save();

        //PUCs DEL COMPROBANTE CONTABLE
        for ($i = 0; $i < count($request->cuentaPUC); $i++){
            $comprobanteMov = new ComprobanteIngresosMov();
            $comprobanteMov->comp_id = $comprobante->id;
            $comprobanteMov->fechaComp = $request->fecha;
            $comprobanteMov->cuenta_puc_id = $request->cuentaPUC[$i];
            $comprobanteMov->debito = $request->debitoPUC[$i];
            $comprobanteMov->credito = $request->creditoPUC[$i];
            $comprobanteMov->save();
        }

        //RUBROS DE INGRESOS DEL COMPROBANTE CONTABLE
        if ( $request->tipoCI != "Transferencia"){
            for ($i = 0; $i < count($request->rubroIngresos); $i++){
                $comprobanteMov = new ComprobanteIngresosMov();
                $comprobanteMov->comp_id = $comprobante->id;
                $comprobanteMov->fechaComp = $request->fecha;
                $comprobanteMov->rubro_font_ingresos_id = $request->rubroIngresos[$i];
                $comprobanteMov->debito = $request->debitoIngresos[$i];
                $comprobanteMov->save();
            }
        }

        Session::flash('success','El comprobante de ingreso se ha creado exitosamente');
        return redirect('/administrativo/CIngresos/'.$request->vigencia_id);
    }

    public function edit($id){
        $comprobante = ComprobanteIngresos::find($id);
        $vigencia = Vigencia::findOrFail($comprobante->vigencia_id);
        $user_id = auth()->user()->id;
        $hijosDebito = PucAlcaldia::where('hijo', '1')->where('naturaleza','DEBITO')->orderBy('code','ASC')->get();
        $hijos = PucAlcaldia::where('hijo', '1')->orderBy('code','ASC')->get();
        $rubI = Rubro::where('vigencia_id', $vigencia->id)->orderBy('cod','ASC')->get();
        if ($comprobante->tipoCI == "Comprobante de Ingresos"){
            $user = User::find($comprobante->persona_id);
            $persona = $user;
            $persona->nombre = $user->name;
            $persona->num_dc = $user->email;
        } else $persona = Persona::find($comprobante->persona_id);

        foreach ($rubI as $rub){
            foreach ($rub->fontsRubro as $fuente){
                $rubrosIngresos[] = collect(['id' => $fuente->id, 'code' => $rub->cod, 'nombre' => $rub->name, 'fCode' =>
                    $fuente->sourceFunding->code, 'fName' => $fuente->sourceFunding->description]);
            }
        }

        return view('administrativo.comprobanteingresos.edit', compact('vigencia','user_id',
            'hijosDebito','rubrosIngresos','hijos','comprobante','persona'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\comprobante_egresos  $comprobante_egresos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comprobante = ComprobanteIngresos::findOrFail($id);
        $all_rubros = Rubro::where('vigencia_id',$comprobante->vigencia_id)->get();
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $vigens = Vigencia::findOrFail($comprobante->vigencia_id);
        $V = $vigens->id;
        $vigencia_id = $V;

        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipoIngresos::all();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if (count($rubro) > 0) {
                if($rubro[0]->fontsRubro){
                    //SE VALIDA QUE EL RUBRO TENGA DINERO DISPONIBLE
                    foreach ($rubro[0]->fontsRubro as $fuentes) $valDisp[] = $fuentes->valor_disp;
                    if (isset($valDisp) and array_sum($valDisp) > 0){
                        $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
                        unset($valDisp);
                    }
                }
            }
        }

        if (!isset($infoRubro)) $infoRubro = [];
        return view('administrativo.comprobanteingresos.show', compact('comprobante','rubros','valores','infoRubro','vigens'));
    }

    public function rubroStore(Request $request){

        $rubros = $request->rubro_id;
        if ($rubros != null){

            //dd($request, $rubros);
            $count = count($rubros);

            for($i = 0; $i < $count; $i++){

                $rubroSave = new CIRubros();
                $rubroSave->comprobante_ingreso_id = $request->comprobante_id;
                $rubroSave->rubro_id = $rubros[$i];
                $rubroSave->save();
            }

            Session::flash('success','Rubros asignados correctamente al comprobante de ingresos');
        }
        if (isset($request->fuente_id)){

            $fontsRubroId = $request->fuente_id;
            $valor = $request->valor;
            $count2 = count($fontsRubroId);

            for($i = 0; $i < $count2; $i++){

                $rubroUpdate = CIRubros::findOrFail($request->rubros_valor_id[$i]);
                $rubroUpdate->valor = $valor[$i];
                $rubroUpdate->fonts_rubro_id = $fontsRubroId[$i];
                $rubroUpdate->save();
            }

            Session::flash('success','Dinero asignado correctamente');
        }


        return  back();
    }

    public function rubroDelete($id){

        $rubro = CIRubros::find($id);
        $rubro->delete();
        Session::flash('error','Rubro eliminado correctamente del comprobante de ingresos');

    }

    public function estados($estado, $id){

        if ($estado == 3){

            $comprobante = ComprobanteIngresos::findOrFail($id);
            $valorAdd = $comprobante->rubros->sum('valor');
            if ($comprobante->valor == $valorAdd){
                $comprobante->estado = "3";
                $comprobante->save();

                Session::flash('success','Comprobante de Ingresos Finalizado Correctamente');

                return redirect('/administrativo/CIngresos/'.$comprobante->vigencia_id);
            } else {
                Session::flash('error','No se puede finalizar debido a que el valor asignado a los rubros no es el mismo valor del comprobante de ingresos');

                return back();
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\comprobante_egresos  $comprobante_egresos
     * @return \Illuminate\Http\Response
     */
    public function destroy($vigen, $id)
    {
        if (auth()->user()->id == 223){
            $comprobanteMovs = ComprobanteIngresosMov::where('comp_id', $id)->get();
            foreach ($comprobanteMovs as $mov) $mov->delete();

            $comprobante = ComprobanteIngresos::findOrFail($id);
            $comprobante->delete();
            return "OK";

        } else return "PERMISOS";
    }

    public function pdf($id)
    {
        $comprobante = ComprobanteIngresos::findOrFail($id);
        if ($comprobante->tipoCI == "Comprobante de Ingresos"){
            $user = User::find($comprobante->persona_id);
            $persona = $user;
            $persona->nombre = $user->name;
            $persona->num_dc = $user->email;
        } else $persona = Persona::find($comprobante->persona_id);

        $fecha = Carbon::createFromTimeString($comprobante->ff.' 00:00:00');
        $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        $pdf = PDF::loadView('administrativo.comprobanteingresos.pdf', compact('comprobante',
            'dias', 'meses', 'fecha','persona'))->setOptions(['images' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream();
    }
}
