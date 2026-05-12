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
        'env' => env('GAME_API_TOKEN'),
    ]);

    $token = $request->header('X-GAME-TOKEN');

    if (!$token || $token !== env('GAME_API_TOKEN')) {
        return response()->json([
            'error' => 'Unauthorized game request',
            'debug' => [
                'header' => $token,
                'env' => env('GAME_API_TOKEN')
            ]
        ], 403);
    }

    return $next($request);
}
}
