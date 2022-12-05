<?php

namespace App\Http\Controllers\Api\Presupuesto;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Model\Administrativo\Cdp\Cdp;
use App\Traits\ApiResponserTraits;
use App\Traits\FirebaseNotificationTraits;

class CdpController extends Controller
{
    use ApiResponserTraits,  FirebaseNotificationTraits;

    public function list(){
        $age_actual = date("Y");
        $cdps = Cdp::whereYear('created_at', $age_actual)->get();
        $new = collect();
        $old = collect();
        $roles = JWTAuth::parseToken()->authenticate()->getRoleNames()->toArray();

        if(in_array('Secretaria', $roles)){ 
            $cdps_new = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 0;
            });
    
            $cdps_old = $cdps->filter(function($c){
                return  $c['secretaria_e'] != 0;
            });
        }

        if(in_array('Alcalde', $roles)){
            $cdps_new = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] == 0;
            });
    
            $cdps_old = $cdps->filter(function($c){
                return  $c['secretaria_e'] == 3 && $c['alcalde_e'] != 0;
            });
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
            $new->push($this->estructura($cdp, $roles)); 
        endforeach;

        foreach($cdps_old as $cdp):
            $old->push($this->estructura($cdp, $roles)); 
        endforeach;

        return $this->successResponse(['old' => $old, 'new' => $new]);
    }

    private function estructura($cdp, $roles){
        $tipo = in_array('Secretaria', $roles) ? 'secretaria_e' : /**/ (in_array('Alcalde', $roles) ? 'alcalde_e' :  'jefe_e');

        return [
            "name" => $cdp->name,
            "valor" => $cdp->valor,
            "fecha" => $cdp->fecha,
            "id" => $cdp->id,
            "status" => $cdp[$tipo],//alcalde_e
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
                $cdps->push($this->estructura($cdp, $roles));
            endif;
        endforeach;

        if(in_array('Secretaria', $roles) || in_array('Alcalde', $roles)){
            $this->sendTokenMovil("Nuevo Cdp", "{$cdp->name}.", in_array('Secretaria', $roles) ? "Alcalde" : "Jefe");
        }

        return $this->successResponse($cdps);
    }
}
