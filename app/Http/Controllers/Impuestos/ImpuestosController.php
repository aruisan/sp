<?php

namespace App\Http\Controllers\Impuestos;
use App\Http\Controllers\Controller;
use App\Model\Impuestos\Ciuu;
use App\Model\Impuestos\Comunicado;
use App\Model\Impuestos\ImpSalarioMin;
use App\Model\Impuestos\ImpUVT;
use App\Model\Impuestos\PredialContribuyentes;
use App\Model\User;
use Illuminate\Http\Request;
use App\Traits\NaturalezaJuridicaTraits;

use Illuminate\Support\Facades\Auth;
use Session;
use PDF;
use Carbon\Carbon;

class ImpuestosController extends Controller
{
    use NaturalezaJuridicaTraits;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $comunicados = Comunicado::where('destinatario_id', $user->id)->where('estado','Enviado')->get();
        $numComunicados = $comunicados->count();
        $rit = $user->rit;
        $contribuyente = PredialContribuyentes::where('email',$user->email)->get();
        //SE FIJA EL AÑO 2023
        //$añoActual = Carbon::today()->format('Y');
        $añoActual = 2023;
        $uvt = ImpUVT::where('año', $añoActual)->first();
        $sml = ImpSalarioMin::whereBetween('fecha',array($añoActual.'-01-01', $añoActual.'-12-31'))->first();

        return view('impuestos.menu',compact('rit', 'numComunicados', 'contribuyente','uvt',
        'sml','añoActual'));
    }

    /**
     * Generate PDF from the RIT.
     *
     * @return \Illuminate\Http\Response
     */
    public function pdfRIT($user_id){
        $user = User::find($user_id);
        $rit = $user->rit;
        $rit->natJuridiContri = $this->nameNaturalezaJuridica($rit->natJuridiContri);
        $rit->tipSociedadContri = $this->nameTipoSociedad($rit->tipSociedadContri);
        $rit->tipEntidadContri = $this->nameTipoEntidad($rit->tipEntidadContri);
        $rit->claEntidadContri = $this->nameClaseEntidad($rit->claEntidadContri);
        $actividades = $rit->actividades;

        if (count($rit->actividades) > 0){
            foreach ($actividades as $actividad){
                $ciuu = Ciuu::find($actividad->codCIIU);
                $actividad['code'] = $ciuu->code_ciuu;
                $actividad['description'] = $ciuu->description;
            }
        }
        $establecimientos = $rit->establecimientos;
        $pdf = PDF::loadView('impuestos.rit.pdf', compact('rit','actividades','establecimientos'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    /**
     * Download Files to help.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($file){
        $pathtoFile = storage_path().'/impuestos/'.$file;
        return response()->download($pathtoFile);
    }

}
