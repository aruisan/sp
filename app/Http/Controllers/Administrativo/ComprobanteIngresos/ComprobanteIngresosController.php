<?php

namespace App\Http\Controllers\Administrativo\ComprobanteIngresos;

use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\ComprobanteIngresos\CIRubros;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoIngresos;
use App\Model\Hacienda\Presupuesto\Register;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FileTraits;
use Session;


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
        $hijosDebito = PucAlcaldia::where('hijo', '1')->where('naturaleza','DEBITO')->orderBy('code','ASC')->get();

        return view('administrativo.comprobanteingresos.create', compact('vigencia','user_id',
        'hijosDebito'));
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
        }else $ruta = "";

        $countCI = ComprobanteIngresos::where('vigencia_id', $request->vigencia_id)->orderBy('id')->get()->last();
        if ($countCI == null)  $count = 0;
        else $count = $countCI->code;

        $comprobante = new ComprobanteIngresos();
        $comprobante->code = $count + 1;
        $comprobante->concepto = $request->concepto;
        $comprobante->valor = $request->valor;
        $comprobante->iva = $request->valorIva;
        $comprobante->val_total = $request->valor + $request->valorIva;
        $comprobante->estado = $request->estado;
        $comprobante->ff = $request->fecha;
        $comprobante->tipoCI = $request->tipoCI;
        $comprobante->cualOtroTipo = $request->cualOtroTipo;
        $comprobante->user_id = $request->user_id;
        $comprobante->vigencia_id = $request->vigencia_id;
        $comprobante->puc_alcaldia_id = $request->cuentaDeb;
        $comprobante->ruta = $ruta;
        $comprobante->save();

        Session::flash('success','El comprobante de ingreso se ha creado exitosamente');
        return redirect('/administrativo/CIngresos/show/'.$comprobante->id);
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
        $comprobante = ComprobanteIngresos::findOrFail($id);
        $comprobante->delete();

        Session::flash('error','Comprobante de Ingresos Borrado Correctamente');
        return redirect('../administrativo/CIngresos/'.$vigen);
    }
}
