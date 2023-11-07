<?php

namespace App\Http\Controllers\Api\Presupuesto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Hacienda\Presupuesto\PlantillaCuipo;
use App\Traits\ApiResponserTraits;
use App\Traits\FirebaseNotificationTraits;
Use App\Traits\ConteoTraits;
use PDF;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Administrativo\Registro\CdpsRegistro;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Administrativo\Cdp\RubrosCdp;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Model\Hacienda\Presupuesto\Level;
use App\Model\Hacienda\Presupuesto\Register;
use Carbon\Carbon;


class CdpController extends Controller
{
    use ApiResponserTraits,  FirebaseNotificationTraits;

    public function list(){
        $age_actual = 2023;//date("Y");
        //$cdps = Cdp::whereYear('created_at', '>=', $age_actual)->whereYear('created_at', '<=', $age_actual+1)->orderBy('created_at', 'desc')->get();
        $cdps = Cdp::whereYear('created_at', $age_actual)->orderBy('created_at', 'desc')->get();
        $new = collect();
        $old = collect();
        $roles = JWTAuth::parseToken()->authenticate()->getRoleNames()->toArray();

        if(in_array('Secretaria', $roles)){ 
            $cdps_new = $cdps->filter(function($c){
                return  $c['secretaria_e'] > 10;
            });
    
            $cdps_old = $cdps->filter(function($c){
                return  $c['secretaria_e'] != 0;
            });
        }

        if(in_array('Alcalde', $roles) || in_array('administrador', $roles)){
            $cdps_new = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] == 0;
            });
            
            $cdps_old = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] != 0;
            });
            //return response()->json([$cdps->count(), $cdps_new->count(), $cdps_old->count()]);
        }

        if(in_array('Jefe', $roles)){
            $cdps_new = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] == 3 && $c['jefe_e'] == 0;
            });
    
            $cdps_old = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] == 3 && $c['jefe_e'] != 0;
            });
        }


        foreach($cdps_new as $cdp):
            $new->push($this->estructura($cdp, $roles, FALSE)); 
        endforeach;

        foreach($cdps_old as $cdp):
            $old->push($this->estructura($cdp, $roles, TRUE)); 
        endforeach;

        return $this->successResponse(['old' => $old, 'new' => $new]);
    }

    private function estructura($cdp, $roles, $completo){
        $tipo = in_array('Secretaria', $roles) ? 'secretaria_e' : /**/ (in_array('Alcalde', $roles) ? 'alcalde_e' :  'jefe_e');

        return [
            "name" => $cdp->name,
            "valor" => $cdp->rubrosCdpValor->count() > 0 ? $cdp->rubrosCdpValor->sum('valor_disp') : $cdp->valor,
            "fecha" => $cdp->fecha,
            "id" => $cdp->id,
            "status" => $cdp[$tipo],//alcalde_e
            "pdf" =>  url(route($completo ? 'cpd-pdf-api' : 'cpd-pdf-borrador-api', [$cdp->id, $cdp->vigencia_id]))
        ];
    }

    public function updateStatus(Request $request){//alejandra, ////pachito, //moya
        $roles = JWTAuth::parseToken()->authenticate()->getRoleNames()->toArray();
        $tipo = in_array('Secretaria', $roles) ? 'secretaria_e' : /**/ (in_array('Alcalde', $roles) ? 'alcalde_e' :  'jefe_e');
        $cdps = collect();
        /*
        if(in_array('Secretaria', $user->getRoleNames()->toArray())){
            $tipo = 'secretaria_e';
        }else{
            $tipo = in_array('Alcalde', $user->getRoleNames()->toArray()) ? 'alcalde_e' : 'jefe_e';
        }
        */


        foreach($request->cdps as $item):
            $cdp = Cdp::find($item[0]);
            if(!is_null($cdp)):
                $cdp[$tipo]= $item[1];//alcalde_e $cdp->secretaria_e
                $cdp->save();
                $cdps->push($this->estructura($cdp, $roles, TRUE));
            endif;
        endforeach;

        if(in_array('Secretaria', $roles) || in_array('Alcalde', $roles)){
            $this->sendTokenMovil("Nuevo Cdp", "{$cdp->name}.", in_array('Secretaria', $roles) ? "Alcalde" : "Jefe");
        }

        return $this->successResponse($cdps);
    }


    public function pdf($id, $vigen)
    {
        $cdp = Cdp::findOrFail($id);
        if (TRUE){
            $all_rubros = Rubro::all();
            foreach ($all_rubros as $rubro){
                if ($rubro->fontsRubro->sum('valor_disp') != 0){
                    $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                    $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                    $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
                }
            }

            //codigo de rubros

            $V = $vigen;
            $vigencia_id = $V;
            $vigencia = Vigencia::find($vigencia_id);

            $ultimoLevel = Level::where('vigencia_id', $vigencia_id)->get()->last();
            //dd(Level::all());
            $rubroz = Rubro::where('vigencia_id', $vigencia_id)->get();
            
            $infoRubro = [];
            $fecha = \Carbon\Carbon::createFromTimeString($cdp->created_at);


            $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

            $pdf = PDF::loadView('administrativo.cdp.pdf', compact('cdp','rubros','valores','infoRubro', 'vigencia', 'dias', 'meses', 'fecha'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
            return $pdf->stream();
        } else {
           return response()->json('pailas');
        }
    }


    public function pdfBorrador($id, $vigen)
    {
        $roles = JWTAuth::parseToken()->authenticate()->roles;
        foreach ($roles as $role) $rol= $role->id;
        $cdp = Cdp::findOrFail($id);

        $all_rubros = Rubro::all();
        foreach ($all_rubros as $rubro){
            if ($rubro->fontsRubro->sum('valor_disp') != 0){
                $valFuente = FontsRubro::where('rubro_id', $rubro->id)->sum('valor_disp');
                $valores[] = collect(['id_rubro' => $rubro->id, 'name' => $rubro->name, 'dinero' => $valFuente]);
                $rubros[] = collect(['id' => $rubro->id, 'name' => $rubro->name]);
            }
        }

        //codigo de rubros

        $V = $vigen;
            $vigencia_id = $V;
        $vigens = Vigencia::findOrFail($vigencia_id);
        $vigencia = $vigens;
        $V = $vigens->id;
        $vigencia_id = $V;

        $conteoTraits = new ConteoTraits;
        $conteo = $conteoTraits->conteoCdps($vigens, $cdp->id);

        
        //NEW PRESUPUESTO
        $plantilla = PlantillaCuipo::where('id', '>', 317)->get();
        foreach ($plantilla as $data) {
            $rubro = Rubro::where('vigencia_id', $vigencia_id)->where('plantilla_cuipos_id', $data->id)->get();
            if ($data->id < '324') {
            } elseif (count($rubro) > 0) {
                if($rubro[0]->fontsRubro and $rubro[0]->tipo == "Funcionamiento"){
                    //SE VALIDA QUE EL RUBRO TENGA DINERO DISPONIBLE
                    foreach ($rubro[0]->fontsRubro as $fuentes){
                        foreach ($fuentes->dependenciaFont as $fontDep){
                            if (auth()->user()->dependencia_id == $fontDep->dependencia_id) $valDisp[] = $fontDep->saldo;
                        }
                    }
                    if (isset($valDisp) and array_sum($valDisp) > 0){
                        $infoRubro[] = ['id_rubro' => $rubro->first()->id ,'id' => '', 'codigo' => $rubro[0]->cod, 'name' => $rubro[0]->name, 'code' => $rubro[0]->cod];
                        unset($valDisp);
                    }
                }
            }
        }

        $fecha = Carbon::createFromTimeString($cdp->created_at);


        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $pdf = PDF::loadView('administrativo.cdp.pdfBorrador', compact('cdp','rubros','valores','rol','infoRubro', 'vigencia', 'dias', 'meses', 'fecha'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }
}
