<?php

namespace App\Http\Controllers\Hacienda\Presupuesto;

use App\Model\Hacienda\Presupuesto\BudgetSection;
use App\Model\Hacienda\Presupuesto\Cpc;
use App\Model\Hacienda\Presupuesto\CpcsRubro;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\FontsVigencia;
use App\Model\Hacienda\Presupuesto\PublicPolitic;
use App\Model\Hacienda\Presupuesto\Sector;
use App\Model\Hacienda\Presupuesto\SourceFunding;
use App\Model\Hacienda\Presupuesto\Terceros;
use App\Model\Hacienda\Presupuesto\TipoNorma;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\VigenciaGasto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Hacienda\Presupuesto\Rubro;
use Session;

class CuipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($paso ,$vigencia_id)
    {
        if ($paso == "1"){
            $rubros = Rubro::where('vigencia_id',$vigencia_id)->with('cpcs')->paginate(25);
            $vigencia = Vigencia::findOrFail($vigencia_id);
            $CPCs = Cpc::select(['id','code','class'])->get();
            $CPCsRubro = CpcsRubro::all();
            //SE DEBE OCULTAR EL BOTON DE SIGUIENTE CUANDO EL USUARIO NO HA SELECCIONADO TODOS LOS CPCS A LOS RUBROS
            return view('hacienda.presupuesto.cuipo.index', compact('vigencia', 'rubros','CPCs','CPCsRubro','paso'));
        } elseif ($paso == "2") {
            $rubros = Rubro::where('vigencia_id',$vigencia_id)->with('fontsRubro')->paginate(20);
            $vigencia = Vigencia::findOrFail($vigencia_id);
            $terceros = Terceros::all();
            $tipoNormas = TipoNorma::all();
            $fuentes = SourceFunding::all();
            $fontRubro = FontsRubro::where('source_fundings_id','!=',null)->get();
            $publicPolitics = PublicPolitic::all();
            $allRub = Rubro::where('vigencia_id',$vigencia_id)->with('fontsRubro')->get();
            foreach ($allRub as $item) {
                if ($item->fontsRubro){
                    foreach ($item->fontsRubro as $itemFont) $value[] = $itemFont->valor;
                } else $value[] = 0;
            }
            if (!isset($value)){
                $value[] = null;
                unset($value[0]);
            }
            $maxValue = $vigencia->presupuesto_inicial - array_sum($value);
            return view('hacienda.presupuesto.cuipo.index', compact('vigencia', 'rubros','terceros','paso','vigencia','tipoNormas','fuentes','fontRubro','publicPolitics','maxValue'));
        } elseif ($paso == "3"){
            $rubros = Rubro::where('vigencia_id',$vigencia_id)->with('fontsRubro')->get();
            $vigencia = Vigencia::findOrFail($vigencia_id);
            $budgetSections = BudgetSection::all();
            $vigenciaGastos = VigenciaGasto::all();
            $sectors = Sector::all();
            return view('hacienda.presupuesto.cuipo.index', compact('vigencia', 'rubros','paso','budgetSections','vigenciaGastos','sectors'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCPC(Request $request)
    {
        foreach ($request->code as $item){
            $cpc = new CpcsRubro();
            $cpc->cpc_id = $item;
            $cpc->rubro_id = $request->rubroID;
            $cpc->save();
        }

        Session::flash('success','Se han asignado los CPCs al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/1/'.$request->vigencia_id);
    }

    /**
     * Remove the specified resource from cpcrubros.
     *
     * @param  int  $id
     * @param  int  $vigencia
     * @return \Illuminate\Http\Response
     */
    public function deleteCPCRubro($id, $vigencia)
    {
        $cpcRubroDelete = CpcsRubro::where('id',$id)->first();
        $cpcRubroDelete->delete();

        Session::flash('warning','Se ha eliminado el CPC del rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/1/'.$vigencia);
    }

    /**
     * Remove the specified resource from cpcrubros.
     *
     * @param  int  $idRubro
     * @param  int  $vigencia
     * @return \Illuminate\Http\Response
     */
    public function deleteAllCPCRubro($idRubro, $vigencia)
    {
        $cpcRubroDelete = CpcsRubro::where('rubro_id',$idRubro)->get();
        foreach ($cpcRubroDelete as $item) $item->delete();

        Session::flash('warning','Se ha eliminado todos los CPCs del rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/1/'.$vigencia);
    }

    /**
     * Store a newly source fundings in the rubro
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSourceFundings(Request $request)
    {
        if (isset($request->value)){
            $fontRubro = FontsRubro::findOrFail($request->idFontRubro);
            $fontRubro->valor = $request->value;
            $fontRubro->valor_disp = $request->value;
            $fontRubro->save();

            Session::flash('success','Se ha actualizado correctamente el valor de la fuente');
            return back();
        } else {
            foreach ($request->code as $item){
                $sourcefunding = new FontsRubro();
                $sourcefunding->source_fundings_id = $item;
                $sourcefunding->rubro_id = $request->rubroID;
                $sourcefunding->valor = 1;
                $sourcefunding->valor_disp = 1;
                $sourcefunding->save();
            }

            Session::flash('success','Se han asignado las fuentes de financiación al rubro correctamente');
            return back();
        }
    }

    /**
     * Remove the specified resource from fontrubros.
     *
     * @param  int  $id
     * @param  int  $vigencia
     * @return \Illuminate\Http\Response
     */
    public function deleteFontRubro($id, $vigencia)
    {
        $fontRubroDelete = FontsRubro::where('id',$id)->first();
        $fontRubroDelete->delete();

        Session::flash('warning','Se ha eliminado la fuente del rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/2/'.$vigencia);
    }

    /**
     * Remove the specified resource from cpcrubros.
     *
     * @param  int  $idRubro
     * @param  int  $vigencia
     * @return \Illuminate\Http\Response
     */
    public function deleteAllFontsRubro($idRubro, $vigencia)
    {
        $fontRubroDelete = FontsRubro::where('rubro_id',$idRubro)->get();
        foreach ($fontRubroDelete as $item) $item->delete();

        Session::flash('warning','Se ha eliminado todas las fuentes del rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/2/'.$vigencia);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveTipoNorma(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDTN)->get();
        $rubro[0]->tipo_normas_id = $request->codeTN;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado el tipo de norma al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/1/'.$request->vigencia_idTN);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveTercero(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDT)->get();
        $rubro[0]->terceros_id = $request->codeT;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado el tercero al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/2/'.$request->vigencia_idT);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePP(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDPP)->get();
        $rubro[0]->public_politics_id = $request->codePP;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado la politica pública al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/2/'.$request->vigencia_idPP);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveBS(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDBS)->get();
        $rubro[0]->budget_sections_id = $request->codeBS;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado la sección presupuestal al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/3/'.$request->vigencia_idBS);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveVG(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDVG)->get();
        $rubro[0]->vigencia_gastos_id = $request->codeVG;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado la vigencia gastos al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/3/'.$request->vigencia_idVG);
    }

    /**
     * Store a newly created resource of sectors in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSec(Request $request)
    {
        $rubro = Rubro::where('id',$request->rubroIDSec)->get();
        $rubro[0]->sectors_id = $request->codeSec;
        $rubro[0]->save();

        Session::flash('success','Se ha asignado el sector al rubro correctamente');
        return redirect('/presupuesto/rubro/CUIPO/3/'.$request->vigencia_idSec);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
