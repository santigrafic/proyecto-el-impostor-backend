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
        logger()->info('GAME TOKEN DEBUG', [
            'header' => $request->header('X-GAME-TOKEN'),
            'config' => config('services.game_token'),
        ]);

        $token = $request->header('X-GAME-TOKEN');

        if (!$token || $token !== config('services.game_token')) {
            return response()->json([
                'error' => 'Unauthorized game request',
                'debug' => [
                    'header' => $token,
                    'config' => config('services.game_token')
                ]
            ], 403);
        }

        return $next($request);
    }
}
