<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Exports\UsersNOPagosImpuestosExport;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Impuestos\Muellaje;
use App\Model\Administrativo\ImpuestosPredial\Liquidador;
use App\Model\Impuestos\Comunicado;
use App\Model\Impuestos\ImpPredUVT;
use App\Model\Impuestos\ImpSalarioMin;
use App\Model\Impuestos\ImpUSD;
use App\Model\Impuestos\ImpUVT;
use App\Model\Impuestos\Pagos;
use App\Model\Impuestos\Predial;
use App\Model\Impuestos\PredialContribuyentes;
use App\Model\Impuestos\RIT;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class ImpAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersPredial = PredialContribuyentes::all();
        $pagos = Pagos::all();
        $today = Carbon::today()->format('Y-m-d');
        $usdDay = ImpUSD::where('fecha', $today)->first();
        if ($usdDay) $valorUSDToday = $usdDay->valor;
        else{
            $dataUSD = json_decode( file_get_contents('http://apilayer.net/api/live?access_key=c87dcbdc363ac7e963a3a8ac4f2ac02a&currencies=COP'), true );
            $saveUSD = new ImpUSD();
            $saveUSD->fecha = Carbon::createFromTimestamp($dataUSD['timestamp'])->format('Y-m-d');
            $saveUSD->valor = $dataUSD['quotes']['USDCOP'];
            $saveUSD->save();
            $valorUSDToday = $saveUSD->valor;
        }

        foreach ($pagos as $pagoMaked){
            if ($pagoMaked->modulo == "MUELLAJE") {
                $pagoMaked->valueCop = $valorUSDToday * $pagoMaked->valor;
                $pagoMaked->detalleBarco = Muellaje::find($pagoMaked->entity_id);
            }
        }

        $rits = RIT::all();
        $comunicados = Comunicado::all();
        $result = PucAlcaldia::where('id', 82 )->orWhere('id', 84)->get();

        $año = Carbon::today()->year;
        $uvts = ImpUVT::all();
        $usds = ImpUSD::orderBy('id', 'desc')->get();
        $smls = ImpSalarioMin::all();
        $pagosFinalizados = Pagos::where('estado','Pagado')->where('download', 1)->where('modulo','PREDIAL')->whereBetween('fechaPago',array($año.'-01-01', $año.'-12-31'))->with('user')->get();
        foreach ($pagosFinalizados as $pago){
            $pago->impPred = Predial::find($pago->entity_id);
            $pago->contribuyente = PredialContribuyentes::find($pago->impPred->imp_pred_contri_id);
        }
        return view('administrativo.impuestos.admin.index', compact('usersPredial','pagos','rits', 'comunicados','result'
        ,'pagosFinalizados','uvts','smls','usds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function makeComunicado()
    {
        $rits = RIT::all();
        return view('administrativo.impuestos.admin.createcomunicado', compact('rits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateComunicado(Request $request)
    {
        for ($i = 0; $i < count($request->users); $i++) {
            $comunicado = new Comunicado();
            $comunicado->estado = "Enviado";
            $comunicado->enviado = Carbon::today();
            $comunicado->comunicado_title = $request->titulo;
            $comunicado->comunicado_body = $request->mensaje;
            $comunicado->destinatario_id = $request->users[$i];
            $comunicado->remitente_id = auth()->user()->id;
            $comunicado->save();
        }

        Session::flash('success','Los comunicados han sido enviados exitosamente');
        return redirect('/administrativo/impuestos/comunicado/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showComunicado($id)
    {
        $comunicado = Comunicado::find($id);

        return view('administrativo.impuestos.admin.showcomunicado', compact('comunicado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editUser($id)
    {
        $user = PredialContribuyentes::find($id);

        return view('administrativo.impuestos.admin.userpredial.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $user = PredialContribuyentes::find($id);
        $user->email = $request->correo;
        $user->dir_predio = $request->dirPred;
        $user->otra_red= $request->otraRed;
        $user->dir_notificacion = $request->dirNoti;
        $user->municipio = $request->municipio;
        $user->whatsapp = $request->whatsapp;
        $user->facebook = $request->facebook;
        $user->cedCatastral = $request->cedCatastral;
        $user->matInmobiliaria = $request->matInmobiliaria;
        $user->numCatastral = $request->numCat;
        $user->numIdent = $request->numIdent;
        $user->contribuyente = $request->name;
        $user->hect = $request->hectareas;
        $user->metros = $request->mt2;
        $user->area = $request->aConst;
        $user->a2018 = $request->a2018;
        $user->a2019 = $request->a2019;
        $user->a2020 = $request->a2020;
        $user->a2021 = $request->a2021;
        $user->a2022 = $request->a2022;
        $user->a2023 = $request->a2023;
        $user->save();

        Session::flash('success','El contribuyente '.$user->contribuyente.' se ha actualziado exitosamente');
        return redirect('/administrativo/impuestos/admin');
    }
    public function noPay(){

        $noPagos = Pagos::where('estado','Generado')->where('modulo','!=','PREDIAL')->get();

        foreach ($noPagos as $item){
            $item->rit = RIT::where('user_id', $item->user->id)->first();
            if ($item->modulo == 'PREDIAL'){
                $pred = Predial::find($item->entity_id);
                $item->contribuyente = PredialContribuyentes::find($pred->imp_pred_contri_id);
            }
        }
        $fecha = Carbon::today();
        $fecha = $fecha->format('d-m-Y');

        $usersPred = PredialContribuyentes::all();
        foreach ($usersPred as $user){
            $impPred = Predial::where('imp_pred_contri_id', $user->id)->get();
            $uvts = ImpPredUVT::where('año', Carbon::today()->format('Y'))->get();
            $uvtSelect = ImpUVT::where('año', Carbon::today()->format('Y'))->first();
            $uvtPred = $user->a2023 / $uvtSelect->valor;
            foreach ($uvts as $index => $uvt){
                if ($uvt->condicion != null){
                    if ($uvt->uso == 1){
                        if ($uvtPred <= $uvt->condicion){
                            $tarifaxMil = $uvt->tarifa;
                            break;
                        }
                    } elseif ($uvt->uso == 2){
                        if($uvtPred <= $uvt->condicion & $uvtPred >= $uvts[$index-1]['condicion']){
                            $tarifaxMil = $uvt->tarifa;
                            break;
                        }
                    } elseif ($uvt->uso == 3){
                        if( $uvtPred >= $uvt->condicion){
                            $tarifaxMil = $uvt->tarifa;
                            break;
                        }
                    }
                }
            }
            if (count($impPred) > 0){
                $deuda = true;
                foreach ($impPred as $pred){
                    $impPagos = Pagos::where('modulo','PREDIAL')->where('entity_id', $pred->id)->get();
                    foreach ($impPagos as $impPago){
                        if ($impPago->estado == 'Pagado'){
                            if ($impPago->fechaPago <= '2023-06-30'){
                                $deuda = false;
                                break;
                            }
                        }
                    }
                }
                if ($deuda){
                    for($i = 0; $i < 6; $i++){
                        $año = 2018 + $i;
                        if ($user['a'.$año] != 0){
                            $tot = $user['a'.$año] * $tarifaxMil / 1000;
                            $tarifaBomb = 8;
                            if(Carbon::today()->format('Y') != $año) $tasaBombTot = $tot * 15 / 100;
                            else $tasaBombTot = $tot * $tarifaBomb / 100;

                            if(Carbon::today()->format('Y') != $año) $subTot =  $tasaBombTot + $tot;
                            else {
                                $suma = $tasaBombTot + $tot;
                                $subTot =  $suma / 2;
                            }

                            $intMora = $this->liquidar(Carbon::today(), $año, $subTot);

                            $tasaAmbiental = $tot * 0.01;
                            $totalAños[] = $subTot + $intMora + $tasaAmbiental;
                        }
                    }
                    $user->valorDeuda = array_sum($totalAños);
                    unset($totalAños);
                    $predNoPay[] = collect(['numCatastral' => $user->numCatastral, 'contribuyente' => $user->contribuyente,
                        'dir_predio' => $user->dir_predio, 'email' => $user->email, 'valorDeuda' => $user->valorDeuda]);
                }
            } else{
                for($i = 0; $i < 6; $i++){
                    $año = 2018 + $i;
                    if ($user['a'.$año] != 0){
                        $tot = $user['a'.$año] * $tarifaxMil / 1000;
                        $tarifaBomb = 8;
                        if(Carbon::today()->format('Y') != $año) $tasaBombTot = $tot * 15 / 100;
                        else $tasaBombTot = $tot * $tarifaBomb / 100;

                        if(Carbon::today()->format('Y') != $año) $subTot =  $tasaBombTot + $tot;
                        else {
                            $suma = $tasaBombTot + $tot;
                            $subTot =  $suma / 2;
                        }

                        $intMora = $this->liquidar(Carbon::today(), $año, $subTot);

                        $tasaAmbiental = $tot * 0.01;
                        $totalAños[] = $subTot + $intMora + $tasaAmbiental;
                    }
                }
                $user->valorDeuda = array_sum($totalAños);
                unset($totalAños);
                $predNoPay[] = collect(['numCatastral' => $user->numCatastral, 'contribuyente' => $user->contribuyente,
                    'dir_predio' => $user->dir_predio, 'email' => $user->email, 'valorDeuda' => $user->valorDeuda]);
            }
        }

        return Excel::download(new UsersNOPagosImpuestosExport($noPagos, $predNoPay), 'Informe Usuarios no Pago Impuestos '.$fecha.'.xlsx');
    }
    public function liquidar($fechaPago, $añoVencimiento, $subTotal){

        $mesPago = date('m', strtotime($fechaPago));
        $añoPago = date('Y', strtotime($fechaPago));
        $diaPago = date('d', strtotime($fechaPago));
        $añoActual = date('Y');

        if ($añoActual != $añoVencimiento){
            $liquidador = Liquidador::whereBetween('vencimiento',array($añoVencimiento.'-08-01', $fechaPago))->orderBy('id','DESC')->get();
            foreach ($liquidador as $item){
                $diasMes = date('d', strtotime($item->vencimiento));
                $porcent = $subTotal * floatval($item->valor) / 100;
                $interesMoraMeses[] = $porcent * $diasMes / 365;
            }

            $liquidadorLastMes = Liquidador::where('año', $añoPago)->where('mes',$mesPago)->get();
            if (count($liquidadorLastMes) > 0){
                $porcent = $subTotal * floatval($liquidadorLastMes[0]->valor) / 100;
                $interesMoraMeses[] = $porcent * $diaPago / 365;
            } else $interesMoraMeses[] = 0;

            return array_sum($interesMoraMeses);
        } else {
            return 0;
        }
    }
}
