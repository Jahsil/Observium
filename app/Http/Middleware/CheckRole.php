<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request = $request->header('Authorization');
        $decodedToken = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        $userRole = $decodedToken->role;

        dd($userRole);

        return $next($request);
    }
}
