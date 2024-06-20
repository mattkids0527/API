<?php

namespace App\Http\Controllers\Daily;

use App\Http\Controllers\Controller;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;

class DailyApiController extends Controller
{
    public function __construct()
    {
        // 
    }

    public function index(Request $request)
    {
        return response()->json('JWT API.');
    }
    public function getToken(Request $request)
    {
        $iden = $request->only(['account', 'password']);

        $token = JWTAuth::attempt($iden);

        if (!$token = JWTAuth::attempt($iden)) {
            return response()->json(['error' => '帳號密碼錯誤!'], 401);
        }

        return $this->respondWithToken($token);
    }
    public function revokeToken()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed => ' . $e
            ], 500);
        }

        return response()->json([
            'message' => 'Token Revoked.'
        ], 201);
    }


    public function getTokenExpir()
    {
        $payload = JWTAuth::getPayload(JWTAuth::getToken());

        return response()->json([
            'expir' => date("Y-m-d H:i:s", $payload['exp']),
        ]);
    }


    public function RefreshToken()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL()
        ]);
    }
}
