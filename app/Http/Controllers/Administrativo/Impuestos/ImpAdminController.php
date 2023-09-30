<?php

namespace App\Http\Controllers\Administrativo\Impuestos;

use App\Exports\UsersNOPagosImpuestosExport;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Impuestos\Muellaje;
use App\Model\Impuestos\Comunicado;
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

        $noPagos = Pagos::where('estado','Generado')->get();
        foreach ($noPagos as $item){
            $item->rit = RIT::where('user_id', $item->user->id)->first();
            if ($item->modulo == 'PREDIAL'){
                $pred = Predial::find($item->entity_id);
                $item->contribuyente = PredialContribuyentes::find($pred->imp_pred_contri_id);
            }
        }
        $fecha = Carbon::today();
        $fecha = $fecha->format('d-m-Y');

        return Excel::download(new UsersNOPagosImpuestosExport($noPagos),
            'Informe Usuarios no Pago Impuestos '.$fecha.'.xlsx');

    }
}
