<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Model\User;
use Illuminate\Http\Response;
use App\Traits\ApiResponserTraits;

class AuthController extends Controller
{
    use ApiResponserTraits;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse('El correo o la contraseña son incorrectos', Response::HTTP_UNAUTHORIZED);
        }

        return $this->successResponse($this->respondWithToken($token));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        JWTAuth::parseToken()->authenticate()->update(['device_token'=> NULL]);
        auth()->logout();

        return $this->successResponse('has cerrado sesion.');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function refresh()
    {
        return $this->successResponse($this->respondWithToken(JWTAuth::parseToken()->refresh()));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->successResponse(JWTAuth::parseToken()->authenticate());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 600
        ]);
    }

}
