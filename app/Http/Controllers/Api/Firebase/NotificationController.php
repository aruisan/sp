<?php

namespace App\Http\Controllers\Api\Firebase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Traits\ApiResponserTraits;

class NotificationController extends Controller
{
    use ApiResponserTraits;
    
    public function saveToken(Request $request)
    {
        JWTAuth::parseToken()->authenticate()->update(['device_token'=>$request->device_token]);
        return $this->successResponse('Token guardado Satisfactoriamente.');
    }
}
