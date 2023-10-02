<?php
namespace App\Traits;

use App\Model\Hacienda\Presupuesto\Vigencia;

Class ConteoTraits
{
	public function conteoCdps($vigencia, $cdp_id){
        foreach ($vigencia->cdps as $key => $cdps) {
            if($cdps->id == $cdp_id){
                return $key+1;
            }
        }
    }
}
