<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $except = [
        '/api/',
        '/api/getToken',
    ];
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {

            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
    public function handle($request, Closure $next)
    {

        if (!$this->inExceptArray($request)) {
            try {
                FacadesJWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {
                return response()->json([
                    'error' => '401 Unauthorized',
                    'msg' => $e->getMessage(),
                ], 401);
            }
        }

        return $next($request);
    }
}
