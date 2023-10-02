<?php

namespace App\Helpers;
use Carbon\Carbon;

class InstitutoHelper {
    static function lema(){  
    	return 'Unidos por un Trabajo Social';
    }

    static function secretaria($fecha){  
        if($fecha < '2020-02-01'):
            return 'Virginia Webster Archbold'; 
        elseif($fecha < '2021-01-01'):
            return 'Marhit May Jay';
        else:
            return 'Camila Nicolle Amador Hooker';
        endif;
    }  
}
