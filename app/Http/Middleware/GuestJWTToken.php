<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Config;

class GuestJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('user-type') == 'member')
        {
            Config::set('auth.providers.users.model',\App\Models\Member::class);
            Config::set('jwt.user','App\Models\Member');
            Config::set('jwt.identifier','userid');
        }
        else
        {
            Config::set('auth.providers.users.model',\App\Models\Student::class);
            Config::set('jwt.user','App\Models\Student');
            Config::set('jwt.identifier','StudentID');
        }

        $token = JWTAuth::getToken();

        if(!$token)
        {
            return $next($request);
        }

        try
        {
            if (! $user = JWTAuth::toUser($token))
            {
                //return response()->json(['user_not_found'], 404);
                return response()->json(['code' => 200, 'message' => 'User Not Found'], 200);
            }
        }catch (JWTException $e)
        {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                //return response()->json(['token_expired'], $e->getStatusCode());
                return response()->json(['code' => 200, 'message' => 'Token Expired'], 200);
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                //return response()->json(['token_invalid'], $e->getStatusCode());
                return response()->json(['code' => 200, 'message' => 'Invalid Token'], 200);
            }
            else
            {
                //return response()->json(['error'=>'Token is required']);
                return response()->json(['code' => 200, 'message' => 'Token is required'], 200);
            }
        }
        return $next($request);
    }
}
