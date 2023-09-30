<?php

namespace App\Http\Controllers\Administrativo\Contabilidad;

use App\Model\Administrativo\Contabilidad\LevelPUC;
use App\Model\Administrativo\Contabilidad\Puc;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Contabilidad\RubrosPuc;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteBalanceInicialExcExport;
use foo\bar;
use Illuminate\Http\Request;
use Session,Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */




    public function lvl($level)
    {
        $PUC = Puc::find('1');
        if ($PUC){
            $nivel = LevelPUC::where('puc_id', $PUC->id)->where('level', $level)->first();
            $niveles = LevelPUC::where('puc_id', $PUC->id)->get();
            $codes = RegistersPuc::where('level_puc_id', $nivel->id)->get();
            $conteo = RegistersPuc::where('level_puc_id', $nivel->id)->count();
            $rubros = RubrosPuc::all();

            if ($rubros){
                if($conteo == 0){
                    $fila = $nivel->rows;
                }else if($conteo >= $nivel->rows){
                    $fila = 0;
                }else if( $nivel->rows > $conteo){
                    $fila = $nivel->rows - $conteo;
                }

                foreach ($codes as $code){
                    foreach ($rubros as $rubro){
                        if ($PUC->levels == 5){
                            dd($rubro->register->code_padre->registers->code_padre->registers->code_padre->registers->code_padre->registers);
                        } elseif ($PUC->levels == 4){
                            if ($level == 1){
                                $padre = $rubro->register->code_padre->registers->code_padre->registers->code_padre->registers;
                                if ($code == $padre){
                                    $val_D = $rubro->op_puc->sum('valor_debito');
                                    $val_C = $rubro->op_puc->sum('valor_credito');
                                    $values[] = collect(['id_P' => $padre->id, 'v_C' => $val_C, 'v_D' => $val_D]);
                                    unset($val_D, $val_C);
                                }
                            } elseif ($level == 2) {
                                $padre = $rubro->register->code_padre->registers->code_padre->registers;
                                if ($code == $padre){
                                    $val_D = $rubro->op_puc->sum('valor_debito');
                                    $val_C = $rubro->op_puc->sum('valor_credito');
                                    $values[] = collect(['id_P' => $padre->id, 'v_C' => $val_C, 'v_D' => $val_D]);
                                    unset($val_D, $val_C);
                                }
                            } elseif ($level == 3) {
                                $padre = $rubro->register->code_padre->registers;
                                if ($code == $padre){
                                    $val_D = $rubro->op_puc->sum('valor_debito');
                                    $val_C = $rubro->op_puc->sum('valor_credito');
                                    $values[] = collect(['id_P' => $padre->id, 'v_C' => $val_C, 'v_D' => $val_D]);
                                    unset($val_D, $val_C);
                                }
                            } elseif ($level == 4) {
                                $padre = $rubro->register;
                                if ($code == $padre){
                                    $val_D = $rubro->op_puc->sum('valor_debito');
                                    $val_C = $rubro->op_puc->sum('valor_credito');
                                    $values[] = collect(['id_P' => $padre->id, 'v_C' => $val_C, 'v_D' => $val_D]);
                                    unset($val_D, $val_C);
                                }
                            }
                        } elseif ($PUC->levels == 3){
                            dd($rubro->register->code_padre->registers->code_padre->registers);
                        }
                    }
                    foreach ($values as $value){
                        if ($code->id == $value['id_P']) {
                            $Cred[] = $value['v_C'];
                            $Deb[] = $value['v_D'];
                            $id = $value['id_P'];
                        } else {
                            $Cred[] = 0;
                            $Deb[] = 0;
                            $id = $value['id_P'];
                        }
                    }
                    if ($code->id == $id){
                        $data[] = collect(['id' => $id, 'Cred' => array_sum($Cred), 'Deb' => array_sum($Deb)]);
                        unset($Cred, $Deb, $id);
                    } else {
                        $data[] = collect(['id' => $code->id, 'Cred' => 0, 'Deb' => 0]);
                    }
                }
                $lvl = $level;
               
            } else {
                Session::flash('error','Actualmente no existen rubros en el PUC. Se recomienda crearlos.');
                return back();
            }
        } else {
            Session::flash('error','Actualmente no existe un PUC para poder ver los informes. Se recomienda crearlo.');
            return back();
        }


        $meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio'];
        Session::put(auth()->id().'-mes-informe-contable-nivel', 1);
        //$pucs = PucAlcaldia::where('hijo','0')->where('padre_id',0)->take(3)->get();
        $pucs = $informe->datos->filter(function($p){ return is_null($p->padre); })->sortBy('puc_alcaldia.code');

        //dd($pucs->first()->hijos->map(function($e){ return $e->puc_alcaldia->code; }));
        //dd($pucs);
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        return view('administrativo.contabilidad.balances.prueba',compact('añoActual', 'mesActual', 'diaActual', 'pucs', 'meses', 'informe'));


        return view('administrativo.contabilidad.informes.index', compact('nivel', 'niveles', 'fila', 'codes','data','lvl'));
    }

    public function rubros($id){
        $PUC = Puc::findOrFail($id);
        $niveles = LevelPUC::where('puc_id', $id)->get();
        $codes = RubrosPuc::where('puc_id',$PUC->id)->get();

        foreach ($codes as $code){
            if ($code->op_puc->count() > 0){
                $data[] = collect(['id' => $code->id, 'Deb' => $code->op_puc->sum('valor_debito'), 'Cred' =>  $code->op_puc->sum('valor_credito')]);
            } else {
                $data[] = collect(['id' => $code->id, 'Deb' => 0, 'Cred' =>  0]);
            }
        }

        return view('administrativo.contabilidad.informes.indexR', compact( 'niveles', 'codes','data'));
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
