<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Assignguard extends BaseMiddleware
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = null)
    {
        if($guard != null){
            auth()->shouldUse($guard); //shoud you user guard / table
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token, true);
            $request->headers->set('Authorization', 'Bearer '.$token, true);
            try {
                $user = JWTAuth::parseToken()->authenticate();
                return $next($request);
            } catch (TokenExpiredException $e) {
                return  $this -> returnError('Unauthenticated user',401);
            } catch (JWTException $e) {

                return  $this -> returnError('token_invalid '.$e->getMessage(),400);
            }
        }

    }
}
