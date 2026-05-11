<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyGameToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-GAME-TOKEN');

        if (!$token || $token !== env('GAME_API_TOKEN')) {
            abort(403, 'Unauthorized game request');
        }
        
        return $next($request);
    }
}
